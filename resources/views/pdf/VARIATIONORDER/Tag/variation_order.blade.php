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
    @endphp
    <div>
        <div class="content-section">
            <div class="content">
                <table border="1" style="border-collapse: collapse; border-color: transparent; width: 100%;">
                    <thead>
                        <tr class="head-row">
                            <th style="width: 5%">S/N</th>
                            <th style="width: 15%">Location</th>
                            <th align="left" style="width: 50%;">Description</th>
                            <th style="width: 5%">QTY</th>
                            <th style="width: 5%">UOM</th>
                            <th style="width: 10%">Unit Price($)</th>
                            <th style="width: 10%">Total($)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sortQuotation as $index => $item)
                            @if (isset($item['emptyAOWData']) || count($item['hasAOWData']) != 0)
                                <tr class="bg-gray">
                                    <td align="center">
                                        <span class="ft-b">{{ $index + 1 }}</span>
                                    </td>
                                    <td colspan="5" align="center">
                                        <span class="ft-b">{{ $item['section_name'] }}</span>
                                    </td>
                                    <td align="center" class="ft-b">
                                        {{ calculateTotalAmountForEachSections($item['section_id'], $quotationList) }}
                                    </td>
                                </tr>
                                @if (getDescription($item['section_id'], $quotationList))
                                    <tr>
                                        <td class="ft-i-11" colspan="7">
                                            {{ getDescription($item['section_id'], $quotationList) }}
                                        </td>
                                    </tr>
                                @endif
                            @endif

                            @if (count($item['hasAOWData']) != 0)
                                @foreach ($item['hasAOWData'] as $value)
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
                                            <td align="center">
                                                <span> {{ $countIndex }}</span>
                                            </td>
                                            <td align="center">
                                                {{ $value['area_of_work_name'] }}
                                            </td>
                                            <td>
                                                <!-- Adjust the width as needed -->
                                                <span class="line-sp aow-item">{!! formatText($hasAOW['name']) !!}</span>
                                            </td>

                                            <td align="center">
                                                {{ $hasAOW['quantity'] }}
                                            </td>
                                            <td align="center">
                                                {{ $hasAOW['measurement'] }}
                                            </td>
                                            <td align="center">
                                                {{ $hasAOW['price'] }}
                                            </td>
                                            <td align="center">
                                                {{ calculateTotalPrice($hasAOW) }}</td>
                                            </td>
                                            @if (!empty($hasAOW['items']))
                                                @php
                                                    $index = 1;
                                                @endphp
                                                @foreach ($hasAOW['items'] as $subItem)
                                                    @php
                                                        $subCountIndex = $countIndex . '.' . $index;
                                                    @endphp
                                                    @include('pdf.Common.Tag.subitems', [
                                                        'item' => $subItem,
                                                        'countIndex' => $subCountIndex,
                                                        'level' => 1,
                                                    ])
                                                    @php
                                                        $index++;
                                                    @endphp
                                                @endforeach
                                            @endif
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="7" style="height: 10px;"></td>
                                    </tr>
                                @endforeach
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <br />
        @include('pdf.VARIATIONORDER.Tag.variationSummaryComponent')
        <br />
        @include('pdf.Common.Tag.disclaimerComponent')
        <br />
        @include('pdf.Common.Tag.signatureComponent')

    </div>
</body>

</html>
