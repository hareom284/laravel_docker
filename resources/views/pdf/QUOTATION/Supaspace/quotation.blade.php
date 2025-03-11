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
            border: 1px solid black !important;
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
            font-size: 12px !important;
        }

        .ft-b-12 {
            font-size: 12px !important;
        }
        .text-italic {
            font-style: italic;
        }
        .top-align {
            vertical-align: top;
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
                $formattedDate = $date->format('F j, Y');
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
                        return number_format($total_price, 2);
                    } else {
                        return '-';
                    }
                }
            }
            return '-'; // Return 0 if section_id not found
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
    <div style="padding: 0 50px 50px 50px;">
        <div class="header-name">
            <pre style="font-size:12px;">{{ $quotationData['header'] }}</pre>
        </div>
        <div class="content-section">
            <div>
                <div class="summary-section">
                    <table style="border-collapse: collapse; border-color: transparent; width: 100%;font-size:12px;">
                        <tbody class="term-of-pay">
                            <tr class="bg-gray">
                                <th colspan="2" align="left" style="padding: 5px 10px 5px 35px;">Summary</th>
                                <th colspan="2" align="left" style="padding: 5px 10px 5px 35px;">Cost</th>
                            </tr>
                            @foreach ($sortQuotation as $index => $item)
                                @if (isset($item['emptyAOWData']) || count($item['hasAOWData']) != 0)
                                    <tr>
                                        <td style="width: 30px;" class="top-align" align="center"><span>{{ chr(65 + $index) }}</span></td>
                                        <th align="left">
                                            <span>{{ $item['section_name'] }}</span>
                                        </th>
                                        <td align="right">
                                            {{ calculateTotalAmountForEachSections($item['section_id'], $quotationList) }}
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                            @if ($total_prices['discount_percentage'] != 0)
                                <tr>
                                    <td colspan="2" align="right">Good Will Discount :</td>
                                    <td style="width: 100px;" align="right" class="border-tb">
                                        ${{ number_format($total_prices['only_discount_amount'], 2, '.', ',') }}</td>
                                </tr>
                            @endif
                            <tr>
                                <td colspan="2" align="right">Total :</td>
                                <td style="width: 100px;" align="right" class="border-tb">
                                    ${{ number_format($total_prices['total_special_discount'], 2, '.', ',') }}</td>
                            </tr>
                        </tbody>
                        <tbody class="note text-italic">
                            <tr>
                                <td class="top-align" align="center"></td>
                                <td colspan="2">
                                    * This quotation <span class="ft-b">does not include electrical works</span> unless otherwise stated
                                </td>
                            </tr>
                            <tr>
                                <td class="top-align" align="center"></td>
                                <td colspan="2">
                                    * Any renovation, security deposits and/or bonds required by various agencies for the course of
                                    renovation to be borne by Client.
                                </td>
                            </tr>
                            <tr>
                                <td class="top-align" align="center"></td>
                                <td colspan="2">
                                    * Any items not stated in our Contract will be charged accordingly as Variation Order.
                                </td>
                            </tr>
                            <tr>
                                <td class="top-align" align="center"></td>
                                <td colspan="2">
                                    * Any items not stated in our Contract will be charged accordingly as Variation Order
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3" style="height: 5px;"></td>
                            </tr>
                        </tbody>
                        <tbody class="term-of-pay">
                            <tr class="bg-gray">
                                <th colspan="3" align="left" style="padding: 5px 10px 5px 35px;">TERMS OF PAYMENT</th>
                            </tr>
                            <tr>
                                <td style="width: 30px;" class="top-align" align="center"></td>
                                <th align="left">
                                    10% - Upon Confirmation
                                </th>
                                <td align="right">
                                    ${{ calculateByPercent($total_prices['total_special_discount'], 10) }}
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 30px;" class="top-align" align="center"></td>
                                <th align="left">
                                    40% - Before Commencement of Works
                                </th>
                                <td align="right">
                                    ${{ calculateByPercent($total_prices['total_special_discount'], 40) }}
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 30px;" class="top-align" align="center"></td>
                                <th align="left">
                                    30% - Upon Completion of Wet Works
                                </th>
                                <td align="right">
                                    ${{ calculateByPercent($total_prices['total_special_discount'], 30) }}
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 30px;" class="top-align" align="center"></td>
                                <th align="left">
                                    15% - Before lnstallation Of Carpentry Works
                                </th>
                                <td align="right">
                                    ${{ calculateByPercent($total_prices['total_special_discount'], 15) }}
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 30px;" class="top-align" align="center"></td>
                                <th align="left">
                                    5% - Upon Completion Of Works
                                </th>
                                <td align="right">
                                    ${{ calculateByPercent($total_prices['total_special_discount'], 5) }}
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3" align="right" class="ft-b" style="padding-top: 5px;">Grand Total: <span class="border-tb"
                                        style="margin-left: 30px;">${{ number_format($total_prices['total_special_discount'], 2, '.', ',') }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3" style="height: 5px;"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="content page">
                <table border="1" style="border-collapse: collapse; border-color: transparent; width: 100%;"
                    class="ft-12">
                    <tr class="bg-gray ft-b">
                        <td class="section-name" style="width: 50px;padding: 10px;">
                            <span style="padding-left: 10px">ITEM</span>
                        </td>
                        <td style="padding: 10px;">DESCRIPTION</td>
                        <td align="center" style="padding: 10px;">QTY</td>
                        <td align="center" style="padding: 10px;min-width: 100px;">UNIT RATE</td>
                        <td align="center" style="padding: 10px;min-width: 100px;">AMOUNT(S$)</td>
                    </tr>
                    @foreach ($sortQuotation as $index => $item)
                        @if (isset($item['emptyAOWData']) || count($item['hasAOWData']) != 0)
                            <tr class="bg-gray">
                                <td style="padding:  10px;vertical-align:top;" align="center">
                                    <span class="ft-b-14 underline">{{ chr(65 + $index) }}</span>
                                </td>
                                <td class="section-name" style="padding:  10px;">
                                    <span class="ft-b-14 underline">{{ $item['section_name'] }}</span>
                                    @if (getDescription($item['section_id'], $quotationList))
                                        <p class="pb-0 mb-0">
                                            {{ '( ' . getDescription($item['section_id'], $quotationList) . ' )' }}
                                        </p>
                                    @endif
                                </td>
                                <td></td>
                                <td style="padding: 4px;" align="center" class="ft-b">Sub-Total:</td>
                                <td style="padding: 4px;" align="center">
                                    {{ '$' . ' ' . calculateTotalAmountForEachSections($item['section_id'], $quotationList) }}
                                </td>
                            </tr>
                        @endif

                        @if (count($item['hasAOWData']) != 0)
                            @foreach ($item['hasAOWData'] as $value)
                                <tr class="aow-name">
                                    <td></td>
                                    <td class="ft-12" style="padding: 10px;">
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
                                        <td align="center" style="vertical-align: top;width: 50px;padding: 4px;"
                                            class="ft-12">
                                            <span> {{ $countIndex }}</span>
                                        </td>
                                        <td style="padding: 4px 4px 4px 10px;" class="ft-12">
                                            <!-- Adjust the width as needed -->
                                            <span class="line-sp aow-item">{!! formatText($hasAOW['name']) !!}</span>
                                        </td>

                                        <td style="padding: 4px;" align="center" class="ft-12">
                                            {{ calculateMeasurement($hasAOW) }}
                                        </td>
                                        <td style="padding: 4px;" align="center" class="ft-12">
                                            @if ($hasAOW['price'] != 0)
                                                {{ '$' . ' ' . $hasAOW['price'] }}
                                            @endif
                                        </td>
                                        <td style="padding: 4px;" align="center" class="ft-12">
                                            {{ calculateTotalPrice($hasAOW) }}</td>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        @endif
                    @endforeach
                    <tr>
                        <td colspan="4" align="right" class="ft-b" style="padding: 6px;">Subtotal</td>
                        <td align="right" class="ft-b" style="padding: 6px;">
                            ${{ number_format($total_prices['total_all_amount'], 2, '.', ',') }}
                        </td>
                    </tr>
                    @if ($total_prices['discount_percentage'] != 0)
                        <tr>
                            <td colspan="4" align="right" class="ft-b" style="padding: 6px;">Less
                                {{ $total_prices['discount_percentage'] }}% Discount</td>
                            <td align="right" class="ft-b" style="padding: 6px;">
                                -${{ number_format($total_prices['total_all_amount'] - $total_prices['total_special_discount'], 2, '.', ',') }}
                            </td>
                        </tr>
                    @endif
                    <tr class="bg-gray">
                        <td colspan="4" align="right" class="ft-b" style="padding: 6px;">Grand Total
                        </td>
                        <td align="right" class="ft-b" style="padding: 6px;">
                            ${{ number_format($total_prices['total_special_discount'], 2, '.', ',') }}
                        </td>
                    </tr>
                </table>

                </table>
            </div>

            <div class="footer-section ft-12">
                <div class="footer-detail">
                    @include('pdf.Common.Supaspace.termsAndConditions')
                </div>
                @include('pdf.Common.Supaspace.signatureComponent')
            </div>
        </div>
    </div>
</body>

</html>
