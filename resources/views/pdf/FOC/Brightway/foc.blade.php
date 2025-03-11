<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link type="text/css" media="all" rel="stylesheet" href="{{ public_path('css/font_style.css') }}">
    <link type="text/css" media="all" rel="stylesheet" href="{{ public_path('css/common.css') }}">
    <style>
        .right-section {
            float: right;
            width: 40%;
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
            border: 1px solid transparent;
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
            border-style: dotted !important;
        }

        .content th,
        .content td {
            border: 1px solid black !important;
            border-style: dotted !important;
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
            font-size: 12px !important;
        }

        .ft-b-12 {
            font-size: 12px !important;
        }


        .vertical-text-wrapper {
            display: table;
            vertical-align: bottom;
            height: 100%;

        }

        .vertical-text {
            transform: rotate(-270deg);
            -webkit-transform: rotate(-270deg);
            width: 20px;
        }

        .sgd-text {
            color: blue;
        }

        .total-section {
            width: 100%;
        }

        .total-section td,
        .total-section th {
            border: 1px solid transparent !important;
        }

        .percentage-section th,
        .percentage-section td,
        .percentage-section tr {
            border: none !important;
            padding: 4px;
        }

        .text-blue {
            color: blue;
        }

        .underline-blue {
            border-bottom: 2px solid blue;
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
                    $final_result = $item['is_fixed_measurement'] ? $item['measurement'] : $item['measurement'];
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

        function getDescription($section_id, $quotationList)
        {
            foreach ($quotationList->section_total_amount as $item) {
                if ($item->section_id == $section_id) {
                    return $item->section_description;
                }
            }
        }
    @endphp
    <div class="container">
        <div class="header" style="clear: both;">
            @include('pdf.Common.Brightway.header',[
                'doc_type' => 'FOC'
            ])
        </div>
        <div class="content-section">
            <div class="content">
                <table border="1" style="border-collapse: collapse; border-color: transparent; width: 100%;"
                    class="ft-12">
                    <tr class="ft-b">
                        <td class="section-name" style="width: 20px;padding: 10px;height: 100px;" rowspan="2">
                            NO
                        </td>
                        <td style="padding: 10px;" rowspan="2" colspan="2">ITEM</td>
                        <td align="center" rowspan="2">
                            <div class="vertical-text-wrapper">
                                <div class="vertical-text">QTY</div>
                            </div>
                        </td>
                        <td align="center" style="padding: 10px;height: 25px;">UNIT PRICE</td>
                        <td align="center" style="padding: 10px;height: 25px;">AMOUNT</td>
                    </tr>
                    <tr>
                        <td align="center" class="sgd-text">$-SGD</td>
                        <td align="center" class="sgd-text">$-SGD</td>
                    </tr>
                    @foreach ($sortQuotation as $index => $item)
                        @if (isset($item['emptyAOWData']) || count($item['hasAOWData']) != 0)
                            <tr>
                                <th style="padding: 4px;" align="center">
                                    <span class="ft-14">{{ chr(65 + $index) }}</span>
                                </th>
                                <td class="section-name" style="padding: 4px;" colspan="2">
                                    <span class="ft-14">{{ $item['section_name'] }}</span>
                                </td>
                                <td></td>
                                <td></td>
                                <td></td>

                            </tr>
                        @endif
                        @if (getDescription($item['section_id'], $quotationList))
                            <tr>
                                <!-- Adjust width -->
                                <td style="width: 20px;">
                                    <span class="ft-12 "></span>
                                </td>
                                <td class="ft-12" style="padding: 4px;width: 70%;" colspan="2">
                                    {{ '( ' . getDescription($item['section_id'], $quotationList) . ' )' }}
                                </td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        @endif
                        @if (isset($item['emptyAOWData']))
                            @foreach ($item['emptyAOWData'] as $emptyAOW)
                                @php
                                    // Increment or initialize the count for the section_id
                                    if (isset($originalIndex[$item['section_id']])) {
                                        $originalIndex[$item['section_id']]++;
                                    } else {
                                        $originalIndex[$item['section_id']] = 1;
                                    }
                                    $countIndex = $originalIndex[$item['section_id']];
                                @endphp


                                <tr key="{{ $emptyAOW['id'] }}">
                                    <th align="center" style="vertical-align: top;width: 20px;padding: 4px;"
                                        class="ft-12">
                                        <span> {{ $countIndex }}</span>
                                    </th>
                                    <td style="padding: 4px;" class="ft-12" colspan="2">
                                        <!-- Adjust the width as needed -->
                                        <span class="line-sp aow-item">{!! formatText($emptyAOW['name']) !!}</span>
                                    </td>

                                    <th style="padding: 4px;" align="center" class="ft-12">
                                        {{ $emptyAOW['quantity'] }}
                                    </th>

                                    <td style="padding: 4px;" align="center" class="ft-12">
                                        {{ calculateTotalPrice($emptyAOW) }}</td>
                                    </td>
                                    <td style="padding: 4px;" align="center" class="ft-12">
                                        {{ calculateTotalPrice($emptyAOW) }}</td>
                                    </td>
                                </tr>
                            @endforeach
                        @endif

                        @if (count($item['hasAOWData']) != 0)
                            @foreach ($item['hasAOWData'] as $value)
                                <tr class="aow-name">
                                    <td></td>
                                    <td class="ft-12" style="padding: 4px;" colspan="2">
                                        <div>
                                            <span class="ft-bold">{{ $value['area_of_work_name'] }} </span>
                                        </div>
                                    </td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>

                                @foreach ($value['area_of_work_items'] as $hasAOW)
                                    @php
                                        if (isset($originalIndex[$item['section_id']])) {
                                            $originalIndex[$item['section_id']]++;
                                        } else {
                                            $originalIndex[$item['section_id']] = 1;
                                        }
                                        $countIndex = $originalIndex[$item['section_id']];
                                    @endphp
                                    <tr key="{{ $hasAOW['id'] }}">
                                        <th align="center" style="vertical-align: top;width: 20px;padding: 4px;"
                                            class="ft-12">
                                            <span> {{ $countIndex }}</span>
                                        </th>
                                        <td style="padding: 4px;width: 70%;" class="ft-12" colspan="2">
                                            <!-- Adjust the width as needed -->
                                            <span class="line-sp aow-item">{!! formatText($hasAOW['name']) !!}</span>
                                        </td>

                                        <th style="padding: 4px;" align="center" class="ft-12">
                                            @if ($hasAOW['quantity'] != 0)
                                                {{ $hasAOW['quantity'] }}
                                            @endif
                                        </th>

                                        <td style="padding: 4px;" align="right" class="ft-12">
                                            @if ($hasAOW['price'] != 0)
                                                {{ '$' . ' ' . $hasAOW['price'] }}
                                            @endif
                                        </td>
                                        </td>
                                        <td style="padding: 4px;" align="right" class="ft-12">
                                            {{ calculateTotalPrice($hasAOW) }}</td>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach

                            @if (calculateTotalAmountForEachSections($item['section_id'], $quotationList))
                                @if ($item['hasAOWData'][0]['area_of_work_items'][0]['calculation_type'] != 'NORMAL')
                                    <tr>
                                        <td></td>
                                        <td colspan="2"></td>
                                        <td></td>
                                        <td align="center" class="ft-b-12">
                                            Sub-Total
                                        </td>
                                        <!-- Adjust width -->

                                        <td align="right" class="ft-b-12">
                                            {{ calculateTotalAmountForEachSections($item['section_id'], $quotationList) }}
                                        </td>
                                    </tr>
                                @endif
                            @endif
                        @endif
                    @endforeach
                    <tr>
                        <td style="height: 15px;"></td>
                        <td colspan="2" style="height: 15px;"></td>
                        <td style="height: 15px;"></td>
                        <td style="height: 15px;"></td>
                        <td style="height: 15px;"></td>
                    </tr>
                    <tr>
                        <td style="height: 15px;"></td>
                        <td colspan="2" style="height: 15px;"></td>
                        <td style="height: 15px;"></td>
                        <td style="height: 15px;"></td>
                        <td style="height: 15px;"></td>
                    </tr>
                    <tr>
                        <td style="height: 15px;"></td>
                        <td colspan="2" style="height: 15px;"></td>
                        <td style="height: 15px;"></td>
                        <td style="height: 15px;"></td>
                        <td style="height: 15px;"></td>
                    </tr>
                    @include('pdf.FOC.Brightway.focSummaryComponent')
                </table>
                @include('pdf.Common.Brightway.percentageComponent')
            </div>
        </div>
        <div class="footer">
            @include('pdf.Common.Brightway.footer')
        </div>
    </div>
</body>

</html>
