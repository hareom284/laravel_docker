<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    {{-- <link href="https://fonts.cdnfonts.com/css/helvetica-neue-5" rel="stylesheet"> --}}
    <link type="text/css" media="all" rel="stylesheet" href="{{ public_path('css/font.css') }}">
    <link type="text/css" media="all" rel="stylesheet" href="{{ public_path('css/pdf.css') }}">
    <link type="text/css" media="all" rel="stylesheet" href="{{ public_path('css/common.css') }}">

    <style>
        th,
        td {
            border: 1px solid transparent;
            padding: 4px;
        }

        table {
            border-collapse: collapse;
            font-size: 12px;

        }

        tr {
            /* page-break-inside: avoid; */
        }

        .border-b td {
            border-bottom: 1px solid black;
            padding: 10px 0;
        }

        .alone-border-b {
            border-bottom: 1px solid black;
            padding: 10px 0;

        }

        .border-t-b {
            border-top: 1px solid black !important;
            border-bottom: 1px solid black !important;
            padding: 10px 0;
        }

        .border-t td {
            border-top: 1px solid black;
            padding: 20px 0;
        }

        * {
            font-family: 'HelveticaNeue-Thin' !important;
            /* Use the custom font, fallback to sans-serif if unavailable */
        }

        .terms {
            /* padding-top: 60px; */
            clear: both;
        }

        .term-list li {
            padding-top: 10px;
            padding-bottom: 10px;
        }

        .term-list li::before {
            counter-increment: item;
            /* Increment the counter */
            content: counter(item) '. ' !important;
            /* Display the counter and close with a parenthesis */
            position: absolute;
            /* Position the pseudo-element */
            left: 0;
            /* Align to the left */
        }

        .page {
            page-break-before: always !important;
        }

        .page-after {
            page-break-after: always !important;
        }

        .summary {
            padding-bottom: 30px;
            padding-top: 15px;
        }

        .quotation-section {
            padding-top: 15px;
        }
    </style>
</head>

<body>
    @php
        $originalIndex = 0;
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
        function calculateQuantity($section_id, $quotationList)
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

        function getUnitPrice($item)
        {
            $result = $item['price'] == 0 ? '' : '$' . ' ' . $item['price'];
            return preg_replace('/\B(?=(\d{3})+(?!\d))/', ',', $result);
        }
    @endphp
    @if (count($sortQuotation) != 0)
        <div class="pdf-content">
            <div class="summary">
                <table border="1" style="border-collapse: collapse; border-color: transparent; width: 100%;">
                    @foreach ($sortQuotation as $index => $item)
                        @if (isset($item['emptyAOWData']) || count($item['hasAOWData']) != 0)
                            <tr class="ft-12">
                                <td colspan="3" style="width:350px;vertical-align: top;">
                                    <span class="ft-12">{{ $item['section_name'] }}</span>
                                </td>
                                <td colspan="3" align="center" class="ft-12"
                                    style="width: 250px;vertical-align: top;">
                                    1
                                </td>
                                <td align="right" style="vertical-align: top;">
                                    {{ calculateTotalAmountForEachSections($item['section_id'], $quotationList) }}
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </table>
                <table border="1" style="border-collapse: collapse; border-color: transparent; width: 100%;">
                    <tr class="ft-12">
                        <td colspan="5"></td>
                        <td align="right">Subtotal :</td>
                        <td style="width: 100px;" align="right">
                            ${{ number_format($total_prices['total_all_amount'], 2, '.', ',') }}
                        </td>
                    </tr>
                    <tr class="ft-12">
                        <td colspan="5"></td>
                        <td align="right" class="alone-border-b" style="width: 100px;">Discount :</td>
                        <td align="right" class="alone-border-b">
                            - </td>
                    </tr>
                    <tr>
                        <td colspan="7" style="height: 10px;"></td>
                    </tr>
                    <tr class="ft-12">
                        <td colspan="5"></td>
                        <td align="right">Final Amount S$ :</td>
                        <td style="width: 100px;" align="right">
                            <table>
                                <tr>
                                    <td class="border-t-b ft-12">
                                        FOC</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                <div class="signature-section avoid-break">
                    <table class="ft-12 avoid-break" style="float: left;">
                        <tbody>
                            <tr>
                                <td colspan="2">
                                    <div>Prepared By</div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <img src="{{ 'data:image/png;base64,' . $quotationData['saleperson_signature'] }}"
                                        style="height:100px;border-bottom:1px solid black;">
                                </td>
                            </tr>
                            <tr>
                                <td>Name </td>
                                <td>: {{ $quotationData['signed_saleperson'] }}</td>
                            </tr>
                            <tr>
                                <td colspan="2"> {{ $quotationData['rank'] }}</td>
                            </tr>
                            <tr>
                                <td>DATE </td>
                                <td>: {{ $quotationData['created_at'] }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <div style="float: right;" class="avoid-break">
                        @if ($quotationData['already_sign'])
                            @foreach ($quotationData['customer_signature'] as $customer)
                                <table class="ft-12 avoid-break">
                                    <tbody>
                                        <tr>
                                            <td colspan="2">
                                                <div> Acctepted By</div>

                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">
                                                <img src="{{ 'data:image/png;base64,' . $customer['customer_signature'] }}"
                                                    style="height:100px;border-bottom:1px solid black;">
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>Name </td>
                                            <td>:
                                                {{ $customer['customer']['name_prefix'] . ' ' . $customer['customer']['first_name'] . ' ' . $customer['customer']['last_name'] }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>NRIC </td>
                                            <td>: {{ $customer['customer']['customers']['nric'] }}</td>
                                        </tr>
                                        <tr>
                                            <td>DATE </td>
                                            <td>: {{ $quotationData['signed_date'] }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            @endforeach
                        @endif

                    </div>
                </div>
            </div>
            <div class="page quotation-section">
                @foreach ($sortQuotation as $index => $item)
                    <div key="{{ $item['section_id'] }}">
                        <table border="1"
                            style="border-collapse: collapse; border-color: transparent; width: 100%;">
                            @if (isset($item['emptyAOWData']) || count($item['hasAOWData']) != 0)
                                {{-- <thead> --}}
                                <tr>
                                    <td colspan="3" class="section-name">
                                        <span class="ft-12 underline">{{ $item['section_name'] }}</span>
                                    </td>
                                    <td colspan="3" class="section-name" align="right">
                                        <span
                                            class="ft-12 underline">{{ calculateTotalAmountForEachSections($item['section_id'], $quotationList) }}</span>
                                    </td>
                                </tr>
                                {{-- </thead> --}}
                            @endif
                            @if (getDescription($item['section_id'], $quotationList))
                                <tr>
                                    <!-- Adjust width -->

                                    <td colspan="6" class="ft-b-12">
                                        {{ '( ' . getDescription($item['section_id'], $quotationList) . ' )' }}
                                    </td>
                                </tr>
                            @endif
                        </table>


                        @if (isset($item['emptyAOWData']))
                            <table border="1"
                                style="border-collapse: collapse; border-color: transparent; width: 100%;">

                                @foreach ($item['emptyAOWData'] as $emptyAOW)
                                    @php
                                        // Increment or initialize the count for the section_id
                                        // if (isset($originalIndex[$item['section_id']])) {
                                        //     $originalIndex[$item['section_id']]++;
                                        // } else {
                                        //     $originalIndex[$item['section_id']] = 1;
                                        // }
                                        $originalIndex++;

                                        $countIndex = str_pad($originalIndex, 2, '0', STR_PAD_LEFT);
                                    @endphp


                                    <tr key="{{ $emptyAOW['id'] }}">
                                        <td style="vertical-align: top;width: 50px;" class="ft-12">
                                            <span> {{ $countIndex }}</span>
                                        </td>
                                        <td style="width: 100">

                                        </td>
                                        <td style="width: 300px;" class="ft-12">

                                            <span class="aow-item">{!! formatText($emptyAOW['name']) !!}</span>
                                        </td>
                                        <td align="center" class="ft-12" style="width: 100px;vertical-align: top;">
                                            {{ calculateMeasurement($emptyAOW) }}</td>
                                        <td align="center" class="ft-12" style="width: 50px;vertical-align: top;">
                                            {{ $emptyAOW['quantity'] ? $emptyAOW['quantity'] : null }}</td>
                                        <td align="right" class="ft-12" style="width: 100px;vertical-align: top;">
                                            {{ getUnitPrice($emptyAOW) }}
                                        </td>
                                        <td align="right" class="ft-12" style="vertical-align: top;">
                                            {{ calculateTotalPrice($emptyAOW) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        @endif

                        @if (count($item['hasAOWData']) != 0)
                            @foreach ($item['hasAOWData'] as $value)
                                <table border="1"
                                    style="border-collapse: collapse; border-color: transparent; width: 100%;">
                                    @foreach ($value['area_of_work_items'] as $hasAOW)
                                        @php
                                            // if (isset($originalIndex[$item['section_id']])) {
                                            //     $originalIndex[$item['section_id']]++;
                                            // } else {
                                            //     $originalIndex[$item['section_id']] = 1;
                                            // }
                                            // $countIndex = $originalIndex[$item['section_id']];

                                            $originalIndex++;
                                            $countIndex = str_pad($originalIndex, 2, '0', STR_PAD_LEFT);
                                        @endphp

                                        <tr key="{{ $hasAOW['id'] }}">
                                            <td style="vertical-align: top;width: 50px;" class="ft-12">
                                                <span> {{ $countIndex }}</span>
                                            </td>
                                            <td style="width: 100px;vertical-align: top;" class="ft-12">
                                                <span>{{ $value['area_of_work_name'] }}</span>
                                            </td>
                                            <td style="width: 300px;" class="ft-12">

                                                <span class="aow-item">{!! formatText($hasAOW['name']) !!}</span>
                                            </td>
                                            <td align="center" class="ft-12"
                                                style="width: 100px;vertical-align: top;">
                                                {{ calculateMeasurement($hasAOW) }}</td>
                                            <td align="center" class="ft-12"
                                                style="width: 50px;vertical-align: top;">
                                                {{ $hasAOW['quantity'] ? $hasAOW['quantity'] : null }}</td>
                                            <td align="right" class="ft-12"
                                                style="width: 100px;vertical-align: top;">
                                                {{ getUnitPrice($hasAOW) }}
                                            </td>
                                            <td align="right" class="ft-12" style="vertical-align: top;">
                                                {{ calculateTotalPrice($hasAOW) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </table>
                            @endforeach
                        @endif



                    </div>
                @endforeach
            </div>
            <div class="remark">
                <table class="avoid-break" style="width:100%;">
                    <tbody>
                        <tr class="border-t">
                            <td colspan="5" align="left" style="width: 60%">{{ $quotationList->disclaimer }}
                            </td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            {{-- <div class="total-section">
                <table class="avoid-break" style="width:100%">

                    <tbody class="ft-b-12">
                        <tr>
                            <td colspan="5" align="right">Total For The Above Mentioned Renovation Works :</td>
                            <td style="width: 100px;" align="right">
                                FOC </td>
                        </tr>

                    </tbody>
                </table>
            </div> --}}
            <div class="signature-section avoid-break">
                <table class="ft-12 avoid-break" style="float: left;">
                    <tbody>
                        <tr>
                            <td colspan="2">
                                <div>Prepared By</div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <img src="{{ 'data:image/png;base64,' . $quotationData['saleperson_signature'] }}"
                                    style="height:100px;border-bottom:1px solid black;">
                            </td>
                        </tr>
                        <tr>
                            <td>Name </td>
                            <td>: {{ $quotationData['signed_saleperson'] }}</td>
                        </tr>
                        <tr>
                            <td colspan="2"> {{ $quotationData['rank'] }}</td>
                        </tr>
                        <tr>
                            <td>DATE </td>
                            <td>: {{ $quotationData['created_at'] }}</td>
                        </tr>
                    </tbody>
                </table>
                <div style="float: right;" class="avoid-break">
                    @if ($quotationData['already_sign'])
                        @foreach ($quotationData['customer_signature'] as $customer)
                            <table class="ft-12 avoid-break">
                                <tbody>
                                    <tr>
                                        <td colspan="2">
                                            <div> Acctepted By</div>

                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <img src="{{ 'data:image/png;base64,' . $customer['customer_signature'] }}"
                                                style="height:100px;border-bottom:1px solid black;">
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>Name </td>
                                        <td>:
                                            {{ $customer['customer']['name_prefix'] . ' ' . $customer['customer']['first_name'] . ' ' . $customer['customer']['last_name'] }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>NRIC </td>
                                        <td>: {{ $customer['customer']['customers']['nric'] }}</td>
                                    </tr>
                                    <tr>
                                        <td>DATE </td>
                                        <td>: {{ $quotationData['signed_date'] }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        @endforeach
                    @endif

                </div>
            </div>

            <div class="terms avoid-break">
                <table class="page">
                    <tbody>
                        <tr>
                            <td>
                                <h3>TERMS AND CONDITIONS</h3>
                                <div class="list">
                                    <ol class="term-list ft-12">
                                        <li>After customer confirmation, cancellation of order will not be accepted.
                                        </li>
                                        <li>
                                            10% deposit upon Commencement of Design<br />
                                            40% upong commencement of Site Works<br />
                                            25% upon Commencement of Carpentry Works<br />
                                            20% Upon Delivery of Carpentry On-Site<br />
                                            5% upon Joint Inspection prior defects make good<br />
                                        </li>
                                        <li>
                                            Any balance amount shall be paid in FULL prior to project schedule
                                            milestones
                                        </li>
                                        <li>
                                            We retain ownership and property rights on the goods until the full
                                            payment is settled by the Customer
                                        </li>
                                        <li>
                                            All confirmed items are not returnable or exchangeable.
                                        </li>
                                        <li>
                                            Seller's obligation to supply item(s) ordered is subject to the
                                            availability of such items
                                        </li>
                                        <li>
                                            Complaints of defective goods/items must be reported within the
                                            same day they were delivered. No complaints shall be entertained
                                            after the date of delivery
                                        </li>
                                        <li>
                                            Delivery service is within Singapore ONLY.

                                        </li>
                                        <li>
                                            Warranty for site works 12 months from date of Joint Inspection
                                        </li>
                                        <li>
                                            All Payment payable to <span
                                                class="ft-b">{{ $quotationData['companies']['name'] }}</span>
                                        </li>
                                    </ol>
                                </div>
                            </td>
                            <td>
                                <img src="{{ public_path() . '/images/xcepcion_img.png' }}" height="400" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</body>

</html>
