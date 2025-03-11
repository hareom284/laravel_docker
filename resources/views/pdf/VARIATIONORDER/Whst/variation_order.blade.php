<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link type="text/css" media="all" rel="stylesheet" href="{{ public_path('css/pdf.css') }}">
    <link type="text/css" media="all" rel="stylesheet" href="{{ public_path('css/common.css') }}">
    <style>
        .content table {
            border-collapse: collapse;
            font-size: 12px;
        }

        .content th,
        .content td {
            border: 1px solid black !important;
            padding: 5px;
        }

        .head-row th {
            border: 2px solid black !important;
            background-color: #D9D9D9;
        }

        .grand-total td {
            border: 2px solid black !important;
        }

        .page {
            /* overflow: hidden; */
            page-break-before: always;
        }
    </style>
</head>

<body>
    @php
        function convertDate($dateString)
        {
            $originalFormat = 'd/m/Y';
            $date = DateTime::createFromFormat($originalFormat, $dateString);
            if ($date) {
                $formattedDate = $date->format('d M Y');
                return $formattedDate;
            } else {
                return '';
            }
        }

        function calculateByPercent($totalAmount, $percent)
        {
            $amount = $totalAmount * ($percent / 100);
            return number_format($amount, 2, '.', ',');
        }

        $originalIndex = [];

        function calculateMeasurement($item)
        {
            if (isset($item['calculation_type'])) {
                $final_result = '';

                if ($item['quantity'] != 0) {
                    $final_result = $item['is_fixed_measurement']
                        ? $item['measurement']
                        : $item['quantity'] . ' ' . $item['measurement'];
                } else {
                    $final_result = '';
                }

                return $final_result;
            } else {
                return '';
            }
        }
        function calculateTotalAmountForEachSections($section_id, $quotationList)
        {
            foreach ($quotationList->section_total_amount as $item) {
                if ($item->section_id == $section_id) {
                    $total_price = (float) $item->total_price;

                    if ($total_price != 0) {
                        return '' . number_format($total_price, 2);
                    } else {
                        return 0;
                    }
                }
            }
            return 0; // Return 0 if section_id not found
        }

        function getDescription($section_id, $quotationList)
        {
            foreach ($quotationList->section_total_amount as $item) {
                if ($item->section_id == $section_id) {
                    return $item->section_description;
                }
            }
        }
        function calculateTotalPrice($items)
        {
            if ($items['calculation_type'] == 'NORMAL') {
                $final_result = '';
                $totalAmount = floatval($items['quantity']) * floatval($items['price']);

                $totalAmountFormatted = number_format($totalAmount, 2);

                $final_result = $totalAmount == 0 ? '' : '' . ' ' . $totalAmountFormatted;

                return $items['is_FOC'] ? 'FOC' : preg_replace('/\B(?=(\d{3})+(?!\d))/', ',', $final_result);
            } else {
                return $items['is_FOC'] ? 'FOC' : '';
            }
        } 
        $globalIndex = 0;
    @endphp
    <div>
        @include('pdf.Common.Whst.topHeaderComponent', [
            'type' => 'Variation Order'
        ])

        <div class="content-section" style="clear: both;padding-top: 20px;">
            <div class="content">
                <table class="avoid-break" style="border-collapse: collapse; border-color: transparent; width: 100%;">
                    <thead>
                        <tr class="head-row">
                            <th align="left" style="border: 0.1px solid #ccc !important; padding: 5px; width: 5%">Sr
                            </th>
                            <th align="left" style="border: 0.1px solid #ccc !important; padding: 5px; width: 40%;">
                                Description</th>
                            <th align="left" style="border: 0.1px solid #ccc !important; padding: 5px; width: 10%;">
                                Design Area</th>
                            <th align="left" style="border: 0.1px solid #ccc !important; padding: 5px; width: 10%;">
                                Item Group</th>
                            <th align="right" style="border: 0.1px solid #ccc !important; padding: 5px; width: 8%;">
                                Quantity</th>
                            @if ($settings['enable_show_selling_price'] == 'true')
                                <th align="right" style="border: 0.1px solid #ccc !important; padding: 5px; width: 8%">
                                    Rate</th>
                            @endif
                            <th align="right" style="border: 0.1px solid #ccc !important; padding: 5px; width: 8%">
                                Amount</th>
                        </tr>
                    </thead>
                    {{-- <tbody>
                        @foreach ($sortQuotation as $index => $item)

                            @if (count($item['hasAOWData']) != 0)
                                @foreach ($item['hasAOWData'] as $value)
                                    @foreach ($value['area_of_work_items'] as $hasAOW)
                                        @php
                                            $globalIndex++;
                                            if (isset($originalIndex[$item['section_id']])) {
                                                $originalIndex[$item['section_id']]++;
                                            } else {
                                                $originalIndex[$item['section_id']] = 1;
                                            }
                                            $countIndex = $originalIndex[$item['section_id']];
                                        @endphp
                                    @include('pdf.Common.Whst.aowItemComponent')
                                    @endforeach
                                @endforeach
                            @endif
                        @endforeach
                    </tbody> --}}
                </table>
                @foreach ($sortQuotation as $index => $item)
                    <table class="avoid-break  {{ $item['is_page_break'] ? 'page' : '' }}"
                        style="border-collapse: collapse; border-color: transparent; width: 100%;">
                        <tbody>
                            @if (count($item['hasAOWData']) != 0)
                                @foreach ($item['hasAOWData'] as $value)
                                    @foreach ($value['area_of_work_items'] as $hasAOW)
                                        @php
                                            $globalIndex++;
                                            if (isset($originalIndex[$item['section_id']])) {
                                                $originalIndex[$item['section_id']]++;
                                            } else {
                                                $originalIndex[$item['section_id']] = 1;
                                            }
                                            $countIndex = $originalIndex[$item['section_id']];
                                        @endphp
                                        @include('pdf.Common.Whst.aowItemComponent')
                                    @endforeach
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                @endforeach
            </div>
        </div>
        <br />
        @include('pdf.VARIATIONORDER.Whst.variationSummaryComponent')
        <br />
        @include('pdf.Common.Whst.disclaimerComponent')
        <br />
        <div class="page">
            @include('pdf.Common.Whst.termsOfPaymentComponent', [
                'terms' => $quotationList->terms,
            ])
        </div>
        <br />
        <hr style="width: 100%;">
        <br />
        @include('pdf.Common.Whst.paymentMilestonesComponent')
        <br />
        @include('pdf.Common.Whst.signatureComponent')

    </div>
</body>

</html>
