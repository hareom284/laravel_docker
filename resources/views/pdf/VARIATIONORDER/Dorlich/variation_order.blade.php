<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=\, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link type="text/css" media="all" rel="stylesheet" href="{{ public_path('css/font_style.css') }}">
    <link type="text/css" media="all" rel="stylesheet" href="{{ public_path('css/common.css') }}">
    <style>
        .pdf-section-contents {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000;
        }

        .pdf-section-contents th {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;

        }

        .pdf-section-contents td {
            border-right: 1px solid #000;
            border-left: 1px solid #000;
            padding: 4px;
            text-align: left;
        }

        .pdf-section-contents th {
            background-color: #f2f2f2;
        }

        /* Fixed widths for columns */
        .pdf-section-contents th:first-child,
        .pdf-section-contents td:first-child {
            width: 5%;
            /* Fixed width for No. */
        }

        .pdf-section-contents th:nth-child(2),
        .pdf-section-contents td:nth-child(2) {
            width: 80%;
            /* Description column takes up most of the width */
        }

        .pdf-section-contents th:last-child,
        .pdf-section-contents td:last-child {
            width: 15%;
            /* Fixed width for Amount */
        }

        .pdf-section-contents .aow {
            text-decoration: underline;
            font-weight: bold;
            padding: 10px 5px;
        }

        .amount td {
            border: 0;
        }

        .small-text {
            font-size: 15px;
        }
    </style>

</head>

<body class="small-text">

    @php
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

    @if (count($sortQuotation) != 0)
        <div class="avoid-break">
            @include('pdf.Common.Dorlich.topHeader', [
                'type' => 'VARIATION ORDER',
            ])
            <!-- Content -->
            <table class="pdf-section-contents">
                <thead>
                    <tr>
                        <th>S/N</th>
                        <th style="text-align: left;" align="left">Description</th>
                        <th style="min-width: 60px;">Qty</th>
                        <th style="min-width: 100px;">Unit Price</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                @foreach ($sortQuotation as $sectionIndex => $section)
                    <tbody>
                        <tr>
                            <td></td>
                            <td class="ft-b"> {{ $section['section_name'] }} </td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        @foreach ($section['hasAOWData'] as $aowIndex => $aow)
                            <tr>
                                <td></td>
                                <td class="aow"> {{ $aow['area_of_work_name'] }} </td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            @foreach ($aow['area_of_work_items'] as $itemIndex => $item)
                                <tr>
                                    <td style="text-align: center;"> {{ $itemIndex + 1 }}</td>
                                    <td class="aow-item">{!! formatText($item['name']) !!}</td>
                                    <td style="text-align: center;">{{ calculateMeasurement($item) }}</td>
                                    <td style="text-align: right;">
                                        @if ($item['price'] != '0')
                                            $ {{ $item['price'] }}
                                        @endif
                                    </td>
                                    <td style="text-align: right;">
                                        {{ calculateTotalPrice($item) }}
                                    </td>
                                    @if (!empty($item['items']))
                                        @foreach ($item['items'] as $subItem)
                                            @include('pdf.Common.Dorlich.subitems', [
                                                'item' => $subItem,
                                                'level' => 1,
                                            ])
                                        @endforeach
                                    @endif
                                </tr>
                            @endforeach
                        @endforeach
                        @if ($sectionIndex == count($sortQuotation) - 1)
                            <tr style="border: 1px solid #000;">
                                <td colspan="4" style="text-align: right;">TOTAL</td>
                                <td>
                                    {{ number_format($total_prices['total_all_amount'], 2, '.', ',') }}
                                </td>
                            </tr>
                            @if ($total_prices['discount_percentage'] != 0)
                                <tr style="border: 1px solid #000;">
                                    <td colspan="4" style="text-align: right;" class="ft-i">SPECIAL DISCOUNT </td>
                                    <td>
                                        {{ number_format($total_prices['total_all_amount'] - $total_prices['total_special_discount'], 2, '.', ',') }}
                                    </td>
                                </tr>
                            @endif
                            <tr style="border: 1px solid #000;">
                                <td colspan="4" style="text-align: right;" class="ft-b">GRAND TOTAL</td>
                                <td>
                                    {{ number_format($total_prices['total_special_discount'], 2, '.', ',') }}
                                </td>
                            </tr>
                        @endif
                    </tbody>
                @endforeach
            </table>
            @include('pdf.Common.Dorlich.termsAndConditionComponent', [
                'terms' => $quotationList->terms,
            ])
            @include('pdf.Common.Dorlich.signatureComponent')

        </div>
    @endif

</body>

</html>
