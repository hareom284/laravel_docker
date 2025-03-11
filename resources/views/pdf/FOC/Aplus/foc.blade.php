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
        .right-section {
            float: right;
            font-size: 13px !important;
            width: 50%;
        }

        .left-section {
            float: left;
        }

        .text-center {
            text-align: center;
        }

        .doc-type {
            margin: 0 auto;
            width: 100%;
            text-align: center;
        }

        .bottom-header {
            clear: both;
        }

        .detail th,
        .detail td {
            border: 1px solid black;
        }

        .detail table {
            border: 1px solid black !important;
            font-size: 12px;
            width: 100%;

        }

        .footer-section {
            padding: 100px;
        }

        .payment-percentage table {
            border: 1px solid black !important;
            border-collapse: collapse;
            font-size: 12px;
            width: 100%;
        }

        .payment-percentage th,
        .payment-percentage td {
            border: 1px solid black !important;
        }

        .percentage-total {
            width: 100px;
        }

        .percentage-total table {
            border-collapse: collapse;
            font-size: 12px;
        }

        .percentage-total th,
        .percentage-total td {
            border: 1px solid transparent !important;
        }

        .page {
            /* overflow: hidden; */
            page-break-before: always;
        }

        .content table {
            border-collapse: collapse;
            font-size: 12px;
        }

        .content th,
        .content td {
            border: 2px solid black !important;
        }

        .bg-gray {
            background: #d9d9d9;

        }

        .clear-border td {
            border: 1px solid transparent !important;
        }

        .total-pri .border-b {
            border-bottom: 1px solid black !important;
            padding-bottom: 5px;
            /* padding: 5px 0; */
        }

        .ft-12 {
            font-size: 14px !important;
        }

        .ft-b-14 {
            font-size: 14px !important;
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
                $formattedDate = $date->format('jS F Y'); // 'jS' for day with ordinal suffix, 'F' for full month name
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
                        return '$ ' . number_format($total_price, 2);
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

                $final_result = $totalAmount == 0 ? '' : '$' . ' ' . $totalAmountFormatted;

                return $items['is_FOC'] ? 'FOC' : preg_replace('/\B(?=(\d{3})+(?!\d))/', ',', $final_result);
            } else {
                return $items['is_FOC'] ? 'FOC' : '';
            }
        }
    @endphp
    <div>
        <div class="header-section">
            @include('pdf.Common.Aplus.header')
            @include('pdf.Common.Aplus.bottomHeader')
        </div>
        <br />
        <div class="content-section">
            <div class="content">
                <table border="2" style="border-collapse: collapse; border-color: black; width: 100%;"
                    class="ft-12">
                    @foreach ($sortQuotation as $index => $item)
                        @if (count($item['hasAOWData']) != 0)
                            <tr>
                                <td align="center" class="section-name" style="padding:  10px;" colspan="4">
                                    <span class="ft-b-14">{{ $item['section_name'] }}</span>
                                </td>
                            </tr>
                            @if (getDescription($item['section_id'], $quotationList))
                                <tr>
                                    <td align="center" style="padding: 10px;" colspan="4">
                                        <p class="ft-b-14" style="padding: 0; margin: 0;">
                                            {{ '( ' . getDescription($item['section_id'], $quotationList) . ' )' }}
                                        </p>
                                    </td>
                                </tr>
                            @endif
                        @endif

                        @if (count($item['hasAOWData']) != 0)
                            @foreach ($item['hasAOWData'] as $value)
                                @foreach ($value['area_of_work_items'] as $hasAOW)
                                    <tr key="{{ $hasAOW['id'] }}">
                                        <td style="padding: 10px;width: 75%" class="ft-12" colspan="3">
                                            <span class="line-sp aow-item">{!! formatText($hasAOW['name']) !!}</span>
                                        </td>
                                        <td style="padding: 10px;width: 25%;" class="ft-12">
                                            {{ calculateMeasurement($hasAOW) }}
                                        </td>
                                        @if (!empty($hasAOW['items']))
                                            @foreach ($hasAOW['items'] as $subItem)
                                                @include('pdf.FOC.Aplus.foc_subitems', [
                                                    'item' => $subItem,
                                                    'level' => 1,
                                                ])
                                            @endforeach
                                        @endif
                                    </tr>
                                @endforeach
                            @endforeach
                        @endif
                    @endforeach
                </table>
            </div>
            <div style="height: 10px;"></div>
            <div class="summary-section">
                <table border="2" style="border-collapse: collapse; width: 100%;" class="ft-12">
                    <tr>
                        <td style="padding: 10px; width: 25%;" class="ft-b-14">Man Power: </td>
                        <td style="padding: 10px; width: 25%;"></td>
                        <td style="padding: 10px; width: 25%;" class="ft-b-14">Est. Floor Area (SQFT)</td>
                        <td style="padding: 10px; width: 25%;"></td>
                    </tr>
                    <tr>
                        <td class="ft-b-14" style="padding: 10px;">Quotation Amount:</td>
                        <td style="padding: 10px;">
                            FOC
                        </td>
                        <td class="ft-b-14" style="padding: 10px;">Credit Terms:</td>
                        <td align="center" class="ft-b-14" style="padding: 10px;"></td>
                    </tr>
                </table>
            </div>
            <div class="ft-12 avoid-break">
                @include('pdf.Common.Aplus.noteAndDisclaimerComponent')
            </div>
            <div class="signature-section">
                @include('pdf.Common.Aplus.signatureComponent')
            </div>
        </div>
    </div>
</body>

</html>
