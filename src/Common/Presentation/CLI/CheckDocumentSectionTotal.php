<?php

namespace Src\Common\Presentation\CLI;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Src\Company\Document\Infrastructure\EloquentModels\RenovationItemsEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\RenovationSectionsEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\RenovationDocumentsEloquentModel;

class CheckDocumentSectionTotal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:document_section_total';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check consistency of section totals and document totals for quotations created since 2024-01-01';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }


    public function handle()
    {
        $this->info("Checking section totals and document totals for quotations created since 2024-01-01");
        $this->newLine(2);

        $excelData = [];

        $documents = RenovationDocumentsEloquentModel
            ::whereDate('created_at', '>=', '2024-01-01')
            ->where('type', 'QUOTATION')
            ->select('id', 'total_amount', 'special_discount_percentage','signed_by_salesperson_id','updated_by_user','customer_signature','project_id')
            ->with(['salesperson.user', 'updatedUsers', 'projects.company'])
            ->get();


        $bar = $this->output->createProgressBar(count($documents));
        $bar->start();

        foreach ($documents as $document) {


            $bar->advance();

            // $this->info("Document ID: {$document->id}, Original Total Amount: {$document->total_amount}");


            $sections = RenovationSectionsEloquentModel
                ::where('document_id', $document->id)
                ->select('id', 'total_price', 'calculation_type')
                ->get();


            $sumOfCalculatedSectionTotals = 0;
            foreach ($sections as $section) {
                if ($section->calculation_type === 'LUMP_SUM') {

                    $sumOfCalculatedSectionTotals += $section->total_price;
                } else {

                    $sumOfCalculatedSectionTotals += RenovationItemsEloquentModel
                        ::where('renovation_document_id', $document->id)
                        ->where('renovation_item_section_id', $section->id)
                        ->where('is_FOC',0)
                        ->sum(DB::raw('quantity * price'));
                }
            }


            $renovationSectionItemTotal = $sumOfCalculatedSectionTotals;



            $gstPrice = $document?->projects?->company?->gst ?? 9;



            $specailDiscountAmount = $renovationSectionItemTotal * ($document->special_discount_percentage / 100);
            $gstAmount = ($renovationSectionItemTotal - $specailDiscountAmount) * ($gstPrice / 100);





            $renovationDocumentTotal = $document->total_amount - $gstAmount + $specailDiscountAmount;





            $sumOfStoredSectionTotals = $sections->sum('total_price');


            $sectionDifference = $sumOfCalculatedSectionTotals - $sumOfStoredSectionTotals;


            // $this->table(
            //     ['Renovation Document Total','Stored Section Totals', 'Calculated Section  Items Totals', 'Difference'],
            //     [[
            //         $renovationDocumentTotal,
            //         $sumOfStoredSectionTotals,
            //         $sumOfCalculatedSectionTotals,
            //         $sectionDifference
            //     ]]
            // );

            $excelData[] = [
                    'document_id' => $document->id,
                    'Renovation Document Total' => $renovationDocumentTotal,
                    'Stored Section Totals' => $sumOfStoredSectionTotals,
                    'Calculated Section  Items Totals' => $sumOfCalculatedSectionTotals,
                    'Difference' => $sectionDifference,
                    'signed_by_salesperson'  => $document?->salesperson?->user?->email ?? "",
                    'Update Users' => $document?->updatedUsers?->email ?? "",
                    'Customer Signed' => empty($document->customer_signature) ? "UnSigned" : "Signed",

            ];

            // $this->info("Original Document Total: {$document->total_amount}");
            // $this->info("Calculated Document Total (after gst and discount price): {$renovationDocumentTotal}");


            // if ($document->total_amount != $renovationDocumentTotal) {
            //     $this->warn("Document Total Discrepancy: " . ($sectionDifference));
            // }

            // $this->info('------------------------------------------');
            // $this->newLine(2);
        }


        $bar->finish();

        $this->info(json_encode($excelData));
        $this->info('Document total count' . $documents->count());
    }
}
