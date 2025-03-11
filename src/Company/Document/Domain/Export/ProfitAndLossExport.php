<?php

namespace Src\Company\Document\Domain\Export;

use stdClass;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Concerns\FromCollection;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Src\Company\Project\Application\Repositories\Eloquent\ProjectRepository;
use Src\Company\System\Infrastructure\EloquentModels\GeneralSettingEloquentModel;

class ProfitAndLossExport implements FromCollection, WithStyles, WithTitle
{
    private $projectRepository;
    private $project;
    private $totalExpenseType;
    private $totalCostOfSalesByExpenseType;
    private $boldRows = [];

    public function __construct(ProjectRepository $projectRepository, $projectId)
    {
        $this->projectRepository = $projectRepository;
        $this->project = $this->projectRepository->getProjectById($projectId);
        $this->totalExpenseType = 0;
        $this->totalCostOfSalesByExpenseType = 0;
    }

    public function collection()
    {
        $data = collect();
        $this->generateHeaderSection($data);
        $totalRevenue = $this->generateRevenueSection($data);
        $this->generateCostOfSalesSection($data, $totalRevenue);
        // $this->generateBackupSection($data);

        return $data;
    }

    public function title(): string
    {
        return 'Profit And Loss Report';
    }

    public function styles(Worksheet $sheet)
    {
        // Merge cells for headers and set them bold and center-aligned
        $sheet->mergeCells('A1:J1');
        $sheet->mergeCells('A2:J2');
        $sheet->mergeCells('A3:J3');
        $sheet->mergeCells('A4:J4');
        $sheet->mergeCells('A5:J5');

        // Header Rows
        $sheet->getStyle('A1:J5')->getFont()->setBold(true);
        $sheet->getStyle('A1:J5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A5:J5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("B7:J7")->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('A4:C4')->getAlignment()->setWrapText(true);

        $sheet->getStyle('B5:C100') // Adjust the cell range as needed
            ->getNumberFormat()
            ->setFormatCode('"S$" #,##0.00;[Red]"-S$" #,##0.00');

        // Set text alignment for columns B and C
        $sheet->getStyle('I5:J100') // Apply to specific rows if needed
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // Section Row Pointers
        $rowPointer = 7; // Start styling from this row after headers

        // Style for 'Revenue' section
        $sheet->getStyle("A{$rowPointer}:J{$rowPointer}")->getFont()->setBold(true)->setSize(9);
        $sheet->getStyle("A{$rowPointer}:J{$rowPointer}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setWrapText(true);
        $rowPointer += 1;
        $sheet->getStyle("A{$rowPointer}:J{$rowPointer}")->getFont()->setBold(true)->setSize(8);
        $rowPointer += 3;
        $rowPointer += ($this->project?->saleReport?->customer_payments?->count() ?? 0 + 1); // Increment row pointer after Revenue section

        $sheet->getStyle("I$rowPointer")->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle("I$rowPointer")->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
        $rowPointer += 1;

        // Style for 'Cost of Sales' section
        $sheet->getStyle("I{$rowPointer}:J{$rowPointer}")->getFont()->setBold(true);
        $rowPointer += ($this->project?->supplierCostings->count() ?? 0 + 1); // Increment row pointer after Cost of Sales section
        $rowPointer += 2;
        foreach ($this->boldRows as $row) {
            $sheet->getStyle("I$row")->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getStyle("I$row")->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);
            $rowPointer += 1;
        }
        // Total Cost of sales
        $sheet->getStyle("I{$rowPointer}:J{$rowPointer}")->getFont()->setBold(true);
        // $rowPointer += 3;

        // $sheet->getStyle("I$rowPointer")->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN);
        // $sheet->getStyle("I$rowPointer")->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);

        // Style for 'Gross Profit'
        // $sheet->getStyle("A{$rowPointer}:C{$rowPointer}")->getFont()->setBold(true);
        // $rowPointer += 1;

        // // Style for 'Other Revenue' section
        // $sheet->getStyle("A{$rowPointer}")->getFont()->setBold(true);
        // $rowPointer += 1; // Increment row pointer after Other Revenue section

        // // Total Other Income
        // $sheet->getStyle("A{$rowPointer}:J{$rowPointer}")->getFont()->setBold(true);
        // $rowPointer += 1; // Increment row pointer after Other Revenue section
        // $sheet->getStyle("I$rowPointer:J$rowPointer")->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN);

        // // Style for 'Expenses' section
        // $sheet->getStyle("A{$rowPointer}")->getFont()->setBold(true);
        // $rowPointer += collect($this->getSalePersons())->count() + 1;

        // // Total Expenses
        // $sheet->getStyle("A{$rowPointer}")->getFont()->setBold(true);
        // $rowPointer += 1; // Increment row pointer after Expenses section
        // $sheet->getStyle("I$rowPointer:J$rowPointer")->getBorders()->getTop()->setBorderStyle(Border::BORDER_THIN);
        // $sheet->getStyle("I$rowPointer:J$rowPointer")->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN);

        // // Style for 'Profit/Loss'
        // $sheet->getStyle("I{$rowPointer}:J{$rowPointer}")->getFont()->setBold(true);

        // General Formatting
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle("A8:J$lastRow")->getFont()->setSize(8);

        // Set alignment for the first column
        $sheet->getStyle("A8:J$lastRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle("I11:J$lastRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // Set Font Family
        $sheet->getStyle("A1:J$lastRow")->getFont()->setBold(true)->setName('Arial');
        $sheet->getStyle("B11:H$lastRow")->getFont()->setBold(false)->setName('Arial');
        $sheet->getStyle("A3:C6")->getFont()->setBold(true)->setSize(10)->setName('Arial');
        $sheet->getColumnDimension('A')->setWidth(35);
        $sheet->getColumnDimension('B')->setWidth(10);
        $sheet->getColumnDimension('C')->setWidth(8);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(18);
        $sheet->getColumnDimension('F')->setWidth(38);
        $sheet->getColumnDimension('G')->setWidth(45);
        $sheet->getColumnDimension('H')->setWidth(20);
        $sheet->getColumnDimension('I')->setWidth(15);
        $sheet->getColumnDimension('J')->setWidth(15);

        return [
            'A1:C2' => ['font' => ['size' => 14]],
            'A8:J' . $lastRow => ['font' => ['size' => 8]],
        ];
    }

    private function getFormattedAddress()
    {
        $properties = $this->project->properties;
        return $properties->block_num . " " . $properties->street_name . ($properties->unit_num ? " #" . $properties->unit_num : "") . ",  S" . $properties->postal_code;
    }

    private function getCustomerNames()
    {
        return $this->project->customersPivot()
            ->select('first_name', 'last_name')
            ->get()
            ->map(fn ($c) => "{$c->first_name} {$c->last_name}")
            ->implode(' / ');
    }

    private function generateRevenueSection($data)
    {
        $saleReport = $this->project?->saleReport;
        $customerPayments = $saleReport?->customer_payments;
        $gstRate = $this->project->company->gst;
        $data->push(['Ordinary Income/Expenses']);
        $data->push(['   Revenue']);
        $data->push(['      Sales - Renovation']);
        $totalRevenue = 0;
        if (!empty($customerPayments)) {
                foreach ($customerPayments as $payment) {
                    $gstAmount = $payment->amount * ($gstRate / (100 + $gstRate)); // Calculate GST component.
                    $netAmount = $payment->amount - $gstAmount;
        Log::info('gst rate is ' . $gstAmount);

                    $data->push([
                        '',
                        Carbon::parse($payment->created_at)->format('d/m/Y'),
                        'Invoice',
                        $payment->invoice_no,
                        $this->getCustomerNames(),
                        $this->project->agreement_no. ' '. $this->getFormattedAddress(),
                        $payment->description,
                        'Trade Receivables',
                        number_format($netAmount, 2),
                        number_format($netAmount, 2),
                    ]);
                    $totalRevenue += $netAmount;
                }
        } else {
            $data->push(['', '', '', '', '', '', '', '', '0.00', '0.00']);
        }

        $data->push([
            '      Total for Sales Renovation', 
            '', '', '', '', '', '', '', 
            'S$ ' . number_format($totalRevenue, 2), 
            ''
        ]);
        $data->push([
            '   Total for Revenue', 
            '', '', '', '', '', '', '', 
            'S$ ' . number_format($totalRevenue, 2), 
            ''
        ]);

        return $totalRevenue;
    }

    private function generateHeaderSection($data)
    {
        $salePersons = $this->getSalePersons();
        $projectStartDate = Carbon::parse($this->project->expected_date_of_completion)->format('j F');
        $projectEndDate = Carbon::parse($this->project->completed_date)->format('j F, Y');

        $formattedDateRange = "$projectStartDate - $projectEndDate";
        // Header rows
        $data->push([$this->project->company?->name]);
        $data->push(['Profit and Loss Detail']);
        $data->push([$formattedDateRange]);
        if (!empty($salePersons)) {
            $data->push(['Sales person: ' . collect($salePersons)->pluck('name')->implode(', ')]);
        }

        $data->push([$this->getFormattedAddress()]);
        $data->push(['', '', '', '', '', '', '', '', '', '', ]);
        $data->push(['', 'Date', 'Transaction Type', 'No.', 'Name', 'Class', 'Memo/Description', 'Split', 'Amount', 'Balance', ]);

    }

    private function generateCostOfSalesSection($data, $totalRevenue)
    {
        $supplierCostings = $this->project->supplierCostings;
        $totalCost = $this->addGroupedCostOfSales($data, $supplierCostings);
        $data->push(['   Total Cost of Sales', '', '', '', '', '', '', '', 'S$ ' . number_format($totalCost, 2)]);
        $data->push([]);

        $netIncome = $totalRevenue - $totalCost;
        $data->push([
            'Net Income',
            '', '', '', '', '', '', '', 
            ($netIncome < 0 ? '-S$ ' : 'S$ ') . number_format(abs($netIncome), 2),
            ''
        ]);
    }

    private function addGroupedCostOfSales(&$data, $supplierCostings)
    {
        $totalCost = 0;
        $rowPointer = $data->count() + 1; // Start from the current data count + 1
        $data->push(['   Cost of Sales']);
        $gstRate = $this->project->company->gst_rate;
        $rowPointer++;
        $isQBOIntegrated = config('quickbooks.qbo_integration');

        // Group supplierCostings by vendor_invoice_expense_type_id
        $groupedCostings = $supplierCostings->groupBy(function ($cost) use ($isQBOIntegrated) {
            if ($isQBOIntegrated) {
                return $cost->quick_book_expense_id ?? 'null';
            } else {
                return $cost->vendor_invoice_expense_type_id ?? 'null';
            }
        });

        foreach ($groupedCostings as $expenseTypeId => $costs) {
            $this->totalExpenseType += 1;
            $expenseTypeName = $isQBOIntegrated ?
                ($costs->first()->qboExpenseType->name ?? 'None Project Related Expense Type') :
                ($costs->first()->expenseType->name ?? 'None Project Related Expense Type');

            $data->push(["      $expenseTypeName"]);
            $rowPointer++;
            $expenseTotal = 0;
            foreach ($costs as $cost) {
                $gstAmount = $cost->is_gst_inclusive ? $cost->gst_value : 0;
                $netAmount = $cost->payment_amt - $gstAmount;
                $this->totalCostOfSalesByExpenseType += 1;
                $data->push([
                    '',
                    Carbon::parse($cost->invoice_date)->format('d/m/Y'),
                    'Bill',
                    $cost->invoice_no,
                    $cost->vendor?->vendor_name,
                    $this->project->agreement_no . ' ' . $this->getFormattedAddress(),
                    $cost->description,
                    'Trade and other payables',
                    'S$ ' . number_format($netAmount, 2),
                    'S$ ' . number_format($netAmount, 2),
                ]);
                $rowPointer++;
                $expenseTotal += $netAmount;
            }

            // Add subtotal for the expense type
            $data->push(["      Total for $expenseTypeName", '', '', '', '', '', '', '', 'S$ ' . number_format($expenseTotal, 2)]);
            $this->applyBorderStyle($rowPointer); // Apply bold style to the subtotal row
            $rowPointer++;
            $totalCost += $expenseTotal;
        }
        $this->applyBorderStyle($rowPointer);
        return $totalCost;
    }

    private function applyBorderStyle($rowPointer)
    {
        // Apply bold style to the row (logic will be handled in the `styles` method)
        // You can store the rows requiring bold styling in an array or dynamically apply styles in the `styles` method.
        $this->boldRows[] = $rowPointer;
    }

    private function getSalePersons()
    {
        $saleperson = [];
        $minCommission = null;
        $equalCommission = null;
        $referral_commission = GeneralSettingEloquentModel::where('setting', 'referral_commission')->first();
        $company_earning_percentage = GeneralSettingEloquentModel::where('setting', 'company_earning_percentage')->first();
        $totalCommissionBase = $referral_commission ? $referral_commission->value : 0;
        $salespersonCount = count($this->project->salespersons);
        $totalRankPercent = 0;

        if ($salespersonCount > 1) {
            // Calculate total rank percentage for all salespersons if there are more than 1
            foreach ($this->project->salespersons as $salesperson) {
                $totalRankPercent += $salesperson->staffs->rank->commission_percent;
            }
        }

        foreach ($this->project->salespersons as $salesperson) {
            $obj = new stdClass();
            $obj->company_earning_percentage = $company_earning_percentage->value;
            $obj->name = $salesperson->first_name . ' ' . $salesperson->last_name;
            if ($this->project->contactUser) {
                if ($salespersonCount == 1) {
                    $obj->commission = $totalCommissionBase;
                } else {
                    $commissionPercent = $salesperson->staffs->rank->commission_percent;

                    if ($totalRankPercent > 0) {
                        $obj->commission = round(($commissionPercent / $totalRankPercent) * $totalCommissionBase);
                    } else {
                        $obj->commission = round($totalCommissionBase / $salespersonCount);
                    }
                }
            } else {
                $totalCommissionBase = 100;
                if ($salespersonCount == 1) {
                    if ($this->project?->saleReport?->or_issued && $this->project?->saleReport?->or_issued > 0) {
                        $obj->commission = $totalCommissionBase - $this->project->saleReport->or_issued;
                    } else {
                        $obj->commission = $totalCommissionBase;
                    }
                } else {
                    $commissionPercent = $salesperson->staffs->rank->commission_percent;

                    if ($totalRankPercent > 0) {
                        $obj->commission = round(($commissionPercent / $totalRankPercent) * $totalCommissionBase);
                    } else {
                        $obj->commission = round($totalCommissionBase / $salespersonCount);
                    }
                }

                if ($minCommission === null || $obj->commission < $minCommission) {
                    $minCommission = $obj->commission;
                    if ($this->project?->saleReport?->or_issued && $this->project?->saleReport?->or_issued > 0 && $totalRankPercent > 100) {
                        $obj->commission -= $this->project->saleReport->or_issued;
                    }
                }
                // $obj->commission = round(intval($salesperson->staffs->rank->commission_percent));
            }

            array_push($saleperson, $obj);
        }

        return $saleperson;
    }

    private function generateBackupSection($data)
    {
        // Gross Profit
        // $grossProfit = $totalRevenue - $totalCost;
        // $data->push([
        //     '   Gross Profit', 
        //     ($grossProfit < 0 ? '-S$ ' : 'S$ ') . number_format(abs($grossProfit), 2),
        //     ($grossProfit < 0 ? '-S$ ' : 'S$ ') . number_format(abs($grossProfit), 2)
        // ]);
        // $data->push([]);

        // // Other Revenue
        // $data->push(['   Other Revenue']);
        // $rebate = $supplierCostings?->sum('discount_amt');
        // $data->push(['      Rebates', number_format($rebate, 2), number_format($rebate, 2)]);
        // $data->push([
        //     '   Total Other Income', 
        //     ($rebate < 0 ? '-S$ ' : 'S$ ') . number_format(abs($rebate), 2),
        //     ($rebate < 0 ? '-S$ ' : 'S$ ') . number_format(abs($rebate), 2)
        // ]);
        // $data->push([]);

        // // Expenses Section
        // $data->push(['   Expenses']);
        // if (!empty($salePersons)) {
        //     $gstPercentage = $this->project->company->gst_reg_no ? $this->project->company->gst : 0;
        //     $amountBeforeGst = $gstPercentage == 0 ? 0 : $saleReport?->total_sales * $gstPercentage / ( 100 + $gstPercentage );
        //     $plAmount = 
        //         $saleReport?->total_sales - 
        //         (
        //             $amountBeforeGst + 
        //             $this->project->supplierCostings?->sum('payment_amt') ?? 0 + 
        //             $saleReport->special_discount ?? 0
        //         );
        //     $totalSalePersonCommission = 0;
        //     foreach ($salePersons as $saleperson) {
        //         $adjustedplAmount = $plAmount * ((100 - $saleperson->company_earning_percentage ?? 0) / 100);
        //         $salePersonCommission = $adjustedplAmount * ( $saleperson->commission / 100 );

        //         $data->push(['      Sales person commission', 
        //         number_format($salePersonCommission, 2), 
        //         number_format($salePersonCommission, 2)]);
        //         $totalSalePersonCommission += $salePersonCommission;
        //     }
        // } else {
        //     $data->push(['   Cost of Sales', '0.00', '0.00']);
        // }

        // $totalExpense = collect($salePersons)->sum('commission');
        // $data->push(['   Total Expenses', 'S$ ' . number_format($totalSalePersonCommission, 2), 'S$ ' . number_format($totalSalePersonCommission, 2)]);
        // $data->push([]);

        // // Profit/Loss
        // $profitLoss = ($grossProfit + $rebate) - $totalSalePersonCommission;
        // $data->push([
        //     '   Profit/Loss', 
        //     ($profitLoss < 0 ? '-S$ ' : 'S$ ') . number_format(abs($profitLoss), 2),
        //     ($profitLoss < 0 ? '-S$ ' : 'S$ ') . number_format(abs($profitLoss), 2)
        // ]);
    }
}