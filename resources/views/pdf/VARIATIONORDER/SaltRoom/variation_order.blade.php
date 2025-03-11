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
        .company-name {
            font-weight: bold;
            font-size: 20px;
            padding-right: 15px;
        }

        .type {
            font-weight: bold;
            font-size: 35px;
            position: absolute;
            bottom: 0;
            /* Align to the bottom */
            right: 0;
            /* Align to the right */
            padding: 25px;
            /* Optional: Add padding if necessary */
        }

        .custom-hr {
            margin: 0px 20px;
        }
    </style>

</head>

<body>
    @php
        function getQty($items)
        {
            if ($items['calculation_type'] == 'NORMAL') {
                return $items['is_FOC'] ? 'FOC' : $items['quantity'];
            } else {
                return $items['is_FOC'] ? 'FOC' : 'L umps ump';
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

        function calculateTotalAmountForEachSections($section_id, $quotationList)
        {
            foreach ($quotationList->section_total_amount as $item) {
                if ($item->section_id == $section_id) {
                    $total_price = (float) $item->total_price;

                    if ($total_price != 0) {
                        return number_format($total_price, 2, '.', ',');
                    } else {
                        return 0;
                    }
                }
            }
            return 0; // Return 0 if section_id not found
        }

        function calculateByPercent($totalAmount, $percent)
        {
            $amount = $totalAmount * ($percent / 100);
            return number_format($amount, 2, '.', ',');
        }

        function removingPTab($data)
        {
            $data = str_replace('<p>', '<span>', $data);
            $data = str_replace('</p>', '</span>', $data);

            return $data;
        }

    @endphp


    <!--   Header -->
    <div>
        <table style="width: 100%;">
            <tr>
                <td style="width: 50%;">
                    <div><img src="{{ 'data:image/png;base64,' . $quotationData['companies']['company_logo'] }}"
                            style="width: auto;height:150px"></div>
                    <div>
                        <span class="company-name">{{ $quotationData['companies']['name'] }}</span>
                        <span>{{ $quotationData['companies']['gst_reg_no'] }}</span>
                    </div>
                    <div>
                        <p>{{ $quotationData['companies']['main_office'] }}</p>
                        <p></p>
                        <p>E: {{ $quotationData['companies']['email'] }}</p>
                    </div>
                </td>
                <td style="width: 50%; position: relative;">
                    <div class="type">
                        <span>{{ $quotationData['doc_type'] }}</span>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <hr class="custom-hr">
    <!-- Header Info -->
    <div>
        <table style="width: 100%;">
            <tr>
                <td style="width: 40%;">
                    <table style="width: 100%;">
                        <tr>
                            <td colspan="2">TO CLIENT</td>
                        </tr>
                        <tr>
                            <td style="width: 15%;">Name</td>
                            <td style="width: 85%;">:<span
                                    style="margin-left: 10px;">{{ $quotationData['customers']['name'] }}</span></td>
                        </tr>
                        <tr>
                            <td style="width: 15%;">Contact</td>
                            <td style="width: 85%;">:<span
                                    style="margin-left: 10px;">{{ $quotationData['customers']['contact_no'] }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 15%;">Email</td>
                            <td style="width: 85%;">:<span
                                    style="margin-left: 10px;">{{ $quotationData['customers']['email'] }}</span></td>
                        </tr>
                        <tr>
                            <td style="width: 15%;">Address</td>
                            <td style="width: 85%;">:<span
                                    style="margin-left: 10px;">{{ $quotationData['properties']['block_num'] . ' ' . $quotationData['properties']['street_name'] . ' ' . '#' . $quotationData['properties']['unit_num'] }}</span>
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="width: 60%; text-align: right;">
                    <table style="width: 100%;">
                        <tr>
                            <td style="width: 50%; text-align: right;">Reference No :</td>
                            <td style="width: 50%; text-align: right;"><span
                                    style="margin-left: 10px;">{{ $quotationData['document_agreement_no'] }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 50%; text-align: right;">Date :</td>
                            <td style="width: 50%; text-align: right;"><span style="margin-left: 10px;">{{ $quotationData['signed_date'] }}</span></td>
                        </tr>
                        <tr>
                            <td style="width: 50%; text-align: right;">Designer :</td>
                            <td style="width: 50%; text-align: right;"><span
                                    style="margin-left: 10px;">{{ $quotationData['signed_saleperson'] }}</span></td>
                        </tr>
                        <tr>
                            <td style="width: 50%; text-align: right;">Contact No :</td>
                            <td style="width: 50%; text-align: right;"><span
                                    style="margin-left: 10px;">{{ $quotationData['signed_sale_ph'] }}</span></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    {{-- Content --}}
    <div>
        <table style="width: 100%;" cellspacing="0" cellpadding="0">
            <tr>
                <td style="width: 5%;"></td>
                <td style="width: 55%"></td>
                <td style="text-align: center; width: 10%;">QTY</td>
                <td style="text-align: center; width: 10%;">AMOUNT</td>
            </tr>
            @if (count($sortQuotation) != 0)
                @foreach ($sortQuotation as $sectionIndex => $section)
                    <tr>
                        <td
                            style="font-weight: bold; text-align: center; background-color: #D3D3D3; border: 1px solid #D3D3D3;">
                            {{ $sectionIndex + 1 . '.' }}</td>
                        <td colspan="3"
                            style="font-weight: bold; background-color: #D3D3D3; border: 1px solid #D3D3D3;">
                            {{ $section['section_name'] }}</td>
                    </tr>
                    @php
                        // Initialize an item counter for each section
                        $itemCounter = 1;
                    @endphp
                    @foreach ($section['hasAOWData'] as $aowIndex => $aow)
                        <tr>
                            <td style="padding-top: 8px;"></td>
                            <td colspan="3" style="font-weight: bold; padding-top: 8px;">
                                {{ $aow['area_of_work_name'] }}</td>
                        </tr>
                        @foreach ($aow['area_of_work_items'] as $itemIndex => $item)
                            <tr>
                                <!-- Use the item counter to create continuous numbering across Area of Work (AOW) -->
                                <td style="padding-bottom: 8px; text-align: center;vertical-align: top;">
                                    {{ $sectionIndex + 1 . '.' . $itemCounter }}
                                </td>
                                <td style="padding-bottom: 8px;" class="aow-item">{!! formatText($item['name']) !!}</td>
                                <td style="text-align: center; padding-bottom: 8px;">{{ getQty($item) }}</td>
                                <td style="text-align: center; padding-bottom: 8px;">{{ calculateTotalPrice($item) }}
                                </td>
                                @if (!empty($item['items']))
                                    @foreach ($item['items'] as $subItem)
                                        @include('pdf.VARIATIONORDER.SaltRoom.variation_order_subitems', [
                                            'item' => $subItem,
                                            'level' => 1,
                                        ])
                                    @endforeach
                                @endif
                            </tr>
                            <tr>
                                <td style="height: 8px;"></td>
                            </tr>
                            @php
                                // Increment the item counter for continuous numbering
                                $itemCounter++;
                            @endphp
                        @endforeach
                    @endforeach
                @endforeach
            @endif
        </table>
    </div>

    <!--   Summary -->
    <div>
        <table style="width: 100%;" cellpadding="0" cellspacing="0">
            <tr>
                <td style="font-weight: bold; text-align: left; background-color: #D3D3D3; border: 1px solid #D3D3D3;">
                    Summary</td>
                <td
                    style="font-weight: bold; text-align: center; background-color: #D3D3D3; border: 1px solid #D3D3D3;">
                </td>
            </tr>
            @if (count($sortQuotation) != 0)
                @foreach ($sortQuotation as $sectionIndex => $section)
                    <tr>
                        <td style="padding-top: 10px;">{{ $sectionIndex + 1 }}. {{ $section['section_name'] }}</td>
                        <td style="text-align: right; padding-top: 10px;">$
                            {{ calculateTotalAmountForEachSections($section['section_id'], $quotationList) }}</td>
                    </tr>
                @endforeach
            @endif
        </table>
        <div style="margin-top: 100px; margin-bottom: 100px;"></div>
        <table style="width: 50%; margin-left: auto; margin-right: 0;" cellpadding="0" cellspacing="0">
            <tr>
                <td style="text-align: right; padding-top: 5px; padding-bottom: 5px;">Total</td>
                <td style="text-align: right; padding-top: 5px; padding-bottom: 5px;">$
                    {{ number_format($total_prices['total_all_amount'], 2, '.', ',') }}</td>
            </tr>
            <tr>
                <td style="text-align: right; padding-top: 5px; padding-bottom: 5px;">Discount</td>
                <td style="text-align: right; padding-top: 5px; padding-bottom: 5px;">$
                    {{ number_format($total_prices['only_discount_amount'], 2, '.', ',') }}</td>
            </tr>
            <tr>
                <td
                    style="text-align: right; border-top: 1px solid #D3D3D3; border-bottom: 1px solid #D3D3D3; padding-top: 5px; padding-bottom: 5px;">
                    Sub Total</td>
                <td
                    style="text-align: right; border-top: 1px solid #D3D3D3; border-bottom: 1px solid #D3D3D3; padding-top: 5px; padding-bottom: 5px;">
                    $ {{ number_format($total_prices['total_special_discount'], 2, '.', ',') }}</td>
            </tr>
        </table>
        <br>
        <table style="width: 100%;" cellpadding="0" cellspacing="0">
            <tr>
                <td style="font-weight: bold; text-align: left; background-color: #D3D3D3; border: 1px solid #D3D3D3;">
                    PAYMENT MILESTONES</td>
                <td
                    style="font-weight: bold; text-align: center; background-color: #D3D3D3; border: 1px solid #D3D3D3;">
                </td>
            </tr>
            <tr>
                <td style="padding-top: 5px; padding-bottom: 5px;">20% Deposit upon confirmation of contract
                </td>
                <td style="text-align: right; padding-top: 5px; padding-bottom: 5px;">$
                    {{ calculateByPercent($total_prices['total_inclusive'], 20) }}</td>
            </tr>
            <tr>
                <td style="padding-top: 5px; padding-bottom: 5px;">45% Upon commencement of work</td>
                <td style="text-align: right; padding-top: 5px; padding-bottom: 5px;">$
                    {{ calculateByPercent($total_prices['total_inclusive'], 45) }}</td>
            </tr>
            <tr>
                <td style="padding-top: 5px; padding-bottom: 5px;">30% Upon fabrication of carpenter work at factory
                </td>
                <td style="text-align: right; padding-top: 5px; padding-bottom: 5px;">$
                    {{ calculateByPercent($total_prices['total_inclusive'], 30) }}</td>
            </tr>
            <tr>
                <td style="padding-top: 5px; padding-bottom: 5px;">5% Upon practical completion
                </td>
                <td style="text-align: right; padding-top: 5px; padding-bottom: 5px;">$
                    {{ calculateByPercent($total_prices['total_inclusive'], 5) }}</td>
            </tr>
            <tr>
                <td style="text-align: right; padding-top: 5px; padding-bottom: 5px; padding-right: 20px;">Total
                </td>
                <td
                    style="text-align: right; padding-top: 5px; padding-bottom: 5px; border-top: 1px solid #D3D3D3; border-bottom: 1px solid #D3D3D3;">
                    $ {{ number_format($total_prices['total_inclusive'], 2, '.', ',') }} </td>
            </tr>
        </table>
        <table style="width: 100%;">
            <tr>
                <td>{!! $quotationData['terms'] !!}</td>
            </tr>
        </table>
        <table style="width: 100%;">
            <tr>
                <td style="width: 50%; vertical-align: top;">
                    <p>Prepared and Approved by:</p>
                    <div>
                        <img src="{{ 'data:image/png;base64,' . $quotationData['saleperson_signature'] }}" style="height:100px;">
                    </div>
                    <div>
                        <span>{{ $quotationData['signed_saleperson'] }}</span><br>
                        <span>Design Manager</span>
                    </div>
                </td>
                <td style="width: 50%; text-align: right; vertical-align: top;">
                    <p>Received and Accepted by:</p>
                    <div>
                        @if (count($quotationData['customer_signature']) > 0)
                            <img src="{{ 'data:image/png;base64,' . $quotationData['customer_signature'][0]['customer_signature'] }}" style="height:100px;">
                        @else
                            <img style="height:100px;">
                        @endif
                    </div>
                    <div>
                        <span>Client: {{ $quotationData['customers']['name'] }}</span>
                    </div>
                </td>
            </tr>
        </table>
</body>

</html>
