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
            <div class="top-header">
                <div class="left-section">
                    <img src="{{ public_path() . '/images/artdecor.png' }}" height="150" />
                </div>
                <div class="right-section text-center" style="padding-top:30px;">
                    <div>{{ $quotationData['companies']['name'] }}</div>
                    <div style="width: 90%">{{ $quotationData['companies']['main_office'] }}</div>
                    <div>Tel: (65){{ $quotationData['companies']['tel'] }} Tel1 :
                        (65){{ $quotationData['companies']['fax'] }}</div>
                    <div>Email: {{ $quotationData['companies']['email'] }}</div>
                    <div>Website: www.artdecordesign.net</div>
                </div>
            </div>
            <div class="bottom-header">
                <div class="doc-type">
                    <span class="underline ft-b-16">QUOTATION</span>
                </div>
                <br />
                <div class="detail">
                    <table>
                        <tr>
                            <td>Attn To :</td>
                            <td style="width: 450px;">
                                @foreach ($quotationData['customers_array'] as $customer)
                                    <div style="padding-left: 5px;">
                                        {{ $customer['name_prefix'] . ' ' . $customer['first_name'] . ' ' . $customer['last_name'] }}
                                    </div>
                                @endforeach
                            </td>
                            <td>Quotation Reference No:</td>
                            <td align="right">{{ $quotationData['project']['agreement_no'] }}</td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top;">Site Address :</td>
                            <td style="vertical-align: top;">
                                @if (isset($quotationData['properties']))
                                    {{-- <div>{{ $quotationData['properties']['block_num'] }}</div>
                                <div>{{ $quotationData['properties']['street_name'] }}</div>
                                <div>{{ $quotationData['properties']['unit_num'] }}</div> --}}
                                    {{ $quotationData['properties']['block_num'] . ' ' . $quotationData['properties']['street_name'] . ' ' . '#' . $quotationData['properties']['unit_num'] }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>Contact No :</td>
                            <td>H/P : {{ $quotationData['customers']['contact_no'] }}</td>
                        </tr>
                        <tr>
                            <td>Date :</td>
                            <td>
                                @if (isset($quotationData['signed_date']))
                                    <span>{{ convertDate($quotationData['signed_date']) }}</span>
                                @else
                                    <span>{{ convertDate($quotationData['created_at']) }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>Email :</td>
                            <td>{{ $quotationData['customers']['email'] }}</td>
                        </tr>
                        @if ($quotationData['customers']['customer_type'] == 'commerical')
                            <tr>
                                <td style="width: 100px;">Company Name :</td>
                                <td>{{ $quotationData['customers']['company_name'] }}</td>
                            </tr>
                        @endif

                        <tr>
                            <td style="height: 5px;"></td>
                        </tr>
                        <tr>
                            <td>Designer :</td>
                            <td>{{ $quotationData['signed_saleperson'] }}</td>
                        </tr>
                        <tr>
                            <td>Contact :</td>
                            <td>H/P : {{ $quotationData['signed_sale_ph'] }}</td>
                        </tr>
                        <tr>
                            <td>Email :</td>
                            <td>{{ $quotationData['signed_sale_email'] }}</td>
                        </tr>
                    </table>
                    <br />
                    <br />
                    <div class="ft-b-12">RE:<span class="underline">Quotation for Renovation Works @
                            @if (isset($quotationData['properties']))
                                {{ $quotationData['properties']['block_num'] . ' ' . $quotationData['properties']['street_name'] . ' ' . '#' . $quotationData['properties']['unit_num'] }}
                            @endif
                        </span>
                    </div>
                </div>
            </div>

        </div>
        <br />
        <div class="content-section">
            <div class="content">
                <table border="1" style="border-collapse: collapse; border-color: transparent; width: 100%;"
                    class="ft-12">
                    <tr class="bg-gray ft-b">
                        <td class="section-name" style="width: 50px;padding: 10px;">
                            <span style="padding-left: 10px">ITEM</span>
                        </td>
                        <td style="padding: 10px;">DESCRIPTION</td>
                        <td align="center" style="padding: 10px;">QTY</td>
                        <td align="center" style="padding: 10px;">PRICE</td>
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
                                    <td align="center" style="vertical-align: top;width: 50px;padding: 4px;"
                                        class="ft-12">
                                        <span> {{ $countIndex }}</span>
                                    </td>
                                    <td style="padding: 4px 4px 4px 10px;" class="ft-12">
                                        <!-- Adjust the width as needed -->
                                        <span class="line-sp aow-item">{!! formatText($emptyAOW['name']) !!}</span>
                                    </td>

                                    <td style="padding: 4px;" align="center" class="ft-12">
                                        {{ calculateMeasurement($emptyAOW) }}
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
                                    <td class="ft-12" style="padding: 10px;">
                                        <div>
                                            <span class="ft-bold">{{ $value['area_of_work_name'] }} </span>
                                        </div>
                                    </td>
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
                                            <span> {{ $countIndex }} </span>
                                        </td>
                                        <td style="padding: 4px 4px 4px 10px;" class="ft-12">
                                            <!-- Adjust the width as needed -->
                                            <span class="line-sp aow-item">{!! formatText($hasAOW['name']) !!}</span>
                                        </td>

                                        <td style="padding: 4px;" align="center" class="ft-12">
                                            {{ calculateMeasurement($hasAOW) }}
                                        </td>

                                        <td style="padding: 4px;" align="center" class="ft-12">
                                            {{ calculateTotalPrice($hasAOW) }}</td>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                            @if (calculateTotalAmountForEachSections($item['section_id'], $quotationList))
                                @if ($item['hasAOWData'][0]['area_of_work_items'][0]['calculation_type'] != 'NORMAL')
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td style="width: 80px;" align="center" class="ft-b-12">
                                            Sub-Total</td>
                                        <!-- Adjust width -->

                                        <td align="center" class="ft-b-12">
                                            {{ calculateTotalAmountForEachSections($item['section_id'], $quotationList) }}
                                        </td>
                                    </tr>
                                @endif
                            @endif
                        @endif
                    @endforeach
                    <tr class="clear-border">
                        <td colspan="4" style="height: 10px;">
                        </td>
                    </tr>
                    <tr class="clear-border">
                        <td colspan="2" align="right">TOTAL :</td>
                        <td></td>
                        <td style="width: 100px;" align="right">
                            <table style="width: 100%" class="total-pri">
                                <tr>
                                    <td style="padding-left:35px;" class="border-b">$</td>
                                    <td align="right" class="border-b">
                                        {{ number_format($total_prices['total_all_amount'], 2, '.', ',') }}
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>
                    @if ($total_prices['discount_percentage'] != 0)
                        <tr class="clear-border">
                            <td colspan="2" align="right">GOODWILL DISCOUNT :</td>
                            <td></td>
                            <td style="width: 100px;" align="right">
                                <table style="width: 100%" class="total-pri">
                                    <tr>
                                        <td style="padding-left:35px;" class="border-b">$</td>
                                        <td align="right" class="border-b">
                                            {{ number_format($total_prices['total_all_amount'] - $total_prices['total_special_discount'], 2, '.', ',') }}
                                        </td>
                                    </tr>
                                </table>

                            </td>
                        </tr>

                        <tr class="clear-border">
                            <td colspan="2" align="right">SUBTOTAL :</td>
                            <td></td>
                            <td style="width: 100px;" align="right">
                                <table style="width: 100%" class="total-pri">
                                    <tr>
                                        <td style="padding-left:35px;" class="border-b">$</td>
                                        <td align="right" class="border-b">
                                            {{ number_format($total_prices['total_special_discount'], 2, '.', ',') }}
                                        </td>
                                    </tr>
                                </table>

                            </td>
                        </tr>
                    @endif

                    @if ($total_prices['gst_percentage'] != 0)
                        <tr class="clear-border">
                            <td colspan="2" align="right">GST ( {{ $total_prices['gst_percentage'] }}% ) :
                            </td>
                            <td></td>
                            <td style="width: 100px;" align="right">
                                <table style="width: 100%" class="total-pri">
                                    <tr>
                                        <td style="padding-left:35px;" class="border-b">$</td>
                                        <td align="right" class="border-b">
                                            {{ number_format($total_prices['total_gst'], 2, '.', ',') }}
                                        </td>
                                    </tr>
                                </table>

                            </td>
                        </tr>
                    @endif
                    <tr class="clear-border">
                        <td colspan="2" align="right">GRAND TOTAL :</td>
                        <td></td>
                        <td style="width: 100px;" align="right">
                            <table style="width: 100%" class="total-pri">
                                <tr>
                                    <td style="padding-left:35px;" class="border-b">$</td>
                                    <td align="right" class="border-b">
                                        {{ number_format($total_prices['total_inclusive'], 2, '.', ',') }}
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>
                </table>

                </table>
            </div>
        </div>

        <div class="footer-section ft-12 page">
            <div class="footer-detail">
                <p>
                <div>
                    <span class="ft-b-12">Term and condition:</span>
                    It is agreed that any claim or dispute with respect to the performance of the contract does not
                    allow you or your
                    company to withhold due payment or any part thereof.
                </div>
                <div>
                    We reserve the right to suspend and / or terminate all works for non-payment within the said
                    credit
                    term without
                    advance notice.
                </div>
                <br />
                <div>
                    <span class="ft-b-12">ARTDECOR DESIGN STUDIO PTE LTD</span> (hereunder called the Company)
                    shall
                    have
                    exclusive right to the
                    works
                </div>
                <div>
                    You or your company shall not assign any part of the Contract covered under this
                    quotation<br /><br />
                    <span class="ft-b-12">Ref : {{ $quotationData['project']['agreement_no'] }} dated @if (isset($quotationData['signed_date']))
                            <span>{{ $quotationData['signed_date'] }}</span>
                        @else
                            <span>{{ $quotationData['created_at'] }}</span>
                        @endif to any person (s) without written consent to the company </span>
                </div>
                <div>
                    Any alteration or additional works involved and are not included in the specification or
                    description
                    of works,
                </div>
                <div>
                    invoices will then be rendered to you accordingly.
                </div>
                <div>
                    Utilities such as water, electricity and storage area to be provided by client free of charge
                </div>
                <div>
                    All the above stated products will remain as the property of Artdecor Design Studio Pte Ltd
                    until
                    payment is fully paid
                </div>
                <br />
                <div>
                    @include('pdf.Common.Artdecor.termsOfPaymentTermComponent')
                    
                </div>
                <div>
                    Down payment is not refundable upon cancellation of contract
                </div>
                <br />
                <div>
                    We trust that the above is in good order and looking forward to your favourable reply. Please do
                    not
                    hesitate to contact the undersigned if you require any clarification or information.
                </div>
                <br />
                <div>
                    For Bank Transfer:
                    <div>
                        <span class="ft-b">Company Name:</span> Artdecor Design Studio Pte Ltd
                    </div>
                    <div>

                        <span class="ft-b">Bank / Account No.:</span> UOB 3953064381
                    </div>
                    <div>
                        <span class="ft-b">Bank / Branch Code :</span> 7375 / 447
                    </div>
                    <div>
                        <span class="ft-b">Paynow :</span> 200822091C
                    </div>
                    <div style="padding: 15px 0">
                        <img src="{{ public_path() . '/images/paynow.png' }}" height="150" />
                    </div>
                    @include('pdf.Common.Artdecor.paymentTermsComponent')
                    <br />
                </div>
                </p>
            </div>
            <div class="signature-section">
                <div>
                    Thank You<br />
                    Best regards<br />
                    Yours Sincerely
                </div>
                <table class="left-section">
                    <tr>
                        <td>ARTDECOR DESIGN STUDIO PTE LTD </td>
                    </tr>
                    <tr>
                        <td>
                            <img src="{{ 'data:image/png;base64,' . $quotationData['saleperson_signature'] }}"
                                style="height:100px;border-bottom:1px solid black;">
                        </td>

                    </tr>
                    <tr>
                        <td>
                            {{ $quotationData['signed_saleperson'] }}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Designation : {{ $quotationData['rank'] }}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            H/P : {{ $quotationData['signed_sale_ph'] }}
                        </td>
                    </tr>
                </table>
                @if (!empty($quotationData['customer_signature']))
                    @foreach ($quotationData['customer_signature'] as $customer)
                        <table style="padding-left: 20px;">
                            <tr>
                                <td>Acknowledged & Accepted By</td>
                            </tr>
                            <tr>
                                <td>
                                    <img src="{{ 'data:image/png;base64,' . $customer['customer_signature'] }}"
                                        style="height:100px;border-bottom:1px solid black;" class="border-b">
                                </td>
                            </tr>
                            <tr>
                                <td>Agreed and Accepted by</td>
                            </tr>
                            <tr>
                                <td>
                                    Name :
                                    {{ $customer['customer']['name_prefix'] . ' ' . $customer['customer']['first_name'] . ' ' . $customer['customer']['last_name'] }}
                                </td>
                            </tr>
                            <tr>
                                <td>NRIC : {{ $customer['customer']['customers']['nric'] }}</td>
                            </tr>
                            <tr>
                                <td>
                                    Date : @if (isset($quotationData['signed_date']))
                                        <span>{{ convertDate($quotationData['signed_date']) }}</span>
                                    @else
                                        <span>{{ convertDate($quotationData['created_at']) }}</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    @endforeach
                @elseif(!empty($customers_array))
                    @foreach ($customers_array as $customer)
                        <table style="padding-left: 20px;">
                            <tr>
                                <td>Acknowledged & Accepted By</td>
                            </tr>
                            <tr>
                                <td>
                                    <div style="height:100px;width:200px;border-bottom:1px solid black;"
                                        class="border-b"></div>
                                </td>
                            </tr>
                            <tr>
                                <td>Agreed and Accepted by</td>
                            </tr>
                            <tr>
                                <td>
                                    Name :
                                    {{ $customer['name_prefix'] . ' ' . $customer['first_name'] . ' ' . $customer['last_name'] }}
                                </td>
                            </tr>
                            <tr>
                                <td>NRIC : {{ $customer['customers']['nric'] }}</td>
                            </tr>
                            <tr>
                                <td>
                                    Date : @if (isset($quotationData['signed_date']))
                                        <span>{{ convertDate($quotationData['signed_date']) }}</span>
                                    @else
                                        <span>{{ convertDate($quotationData['created_at']) }}</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    @endforeach
                @endif

            </div>
        </div>
    </div>
</body>

</html>
