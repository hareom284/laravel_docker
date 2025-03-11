<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=\, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link type="text/css" media="all" rel="stylesheet" href="{{ public_path('css/metis.css') }}">
    <link type="text/css" media="all" rel="stylesheet" href="{{ public_path('css/common.css') }}">
    <style>
        .bg-gray {
            background: #345f85;
            color: white;

        }

        .border-b {
            border-bottom: 1px solid black !important;
            /* padding: 5px 0; */
        }

        .border-t {
            border-top: 1px solid black !important;
            /* padding: 5px 0; */
        }

        .line-sp {
            line-height: 1.5 !important;
        }

        .ft-b-12 {
            font-size: 12px !important;
        }

        .ft-12 {
            font-size: 12px !important;
        }
    </style>
</head>

<body>

    @php
        $originalIndex = [];

        function calculateByPercent($totalAmount, $percent)
        {
            $amount = $totalAmount * ($percent / 100);
            return number_format($amount, 2, '.', ',');
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
    @if (count($sortQuotation) != 0)
        <div class="pdf-content">
            <div class="avoid-break">
                <table border="1" style="border-collapse: collapse; border-color: transparent; width: 100%;"
                    class="ft-12">
                    <tr class="bg-gray">
                        <td colspan="1" class="section-name" style="width: 50px;">
                            <span style="padding-left: 10px">Item</span>
                        </td>
                        <td colspan="1">Description</td>
                        <td colspan="2" align="right" style="padding-right: 10px">Amount</td>
                    </tr>
                </table>
                <br />
                @foreach ($sortQuotation as $index => $item)
                    <div key="{{ $item['section_id'] }}" class="avoid-break">
                        <table border="1"
                            style="border-collapse: collapse; border-color: transparent; width: 100%;">
                            @if (isset($item['emptyAOWData']) || count($item['hasAOWData']) != 0)
                                {{-- <thead> --}}
                                <tr>
                                    <td colspan="4" style="height: 5px;"></td>
                                </tr>
                                <tr class="bg-gray">
                                    <td style="width: 50px;">
                                        <span class="ft-b-14 ">{{ chr(65 + $index) }}</span>
                                    </td>
                                    <td colspan="3" class="section-name">
                                        <span class="ft-b-14">{{ $item['section_name'] }}</span>
                                    </td>
                                </tr>
                                {{-- </thead> --}}
                            @endif
                            @if (getDescription($item['section_id'], $quotationList))
                                <tr>
                                    <!-- Adjust width -->
                                    <td style="width: 50px;">
                                        <span class="ft-b-14 "></span>
                                    </td>
                                    <td colspan="3" class="ft-b-12">
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
                                        if (isset($originalIndex[$item['section_id']])) {
                                            $originalIndex[$item['section_id']]++;
                                        } else {
                                            $originalIndex[$item['section_id']] = 1;
                                        }
                                        $countIndex = $originalIndex[$item['section_id']];
                                    @endphp


                                    <tr key="{{ $emptyAOW['id'] }}">
                                        <td style="vertical-align: top;width: 50px;" class="ft-12">
                                            <span> {{ chr(65 + $index) }} . {{ $countIndex }} </span>
                                        </td>
                                        <td style="width: 600px;" class="ft-12"> <!-- Adjust the width as needed -->
                                            <span class="line-sp aow-item">{!! formatText($emptyAOW['name']) !!}</span>
                                        </td>
                                        <td style="width: 100px;" align="center" class="ft-12">
                                            {{ calculateMeasurement($emptyAOW) }}</td>
                                        <!-- Adjust width -->

                                        <td style="width: 100px;" align="right" class="ft-12">
                                            {{ calculateTotalPrice($emptyAOW) }}</td>
                                        <!-- Adjust width -->
                                    </tr>
                                @endforeach
                            </table>
                        @endif

                        @if (count($item['hasAOWData']) != 0)
                            @foreach ($item['hasAOWData'] as $value)
                                <table border="1"
                                    style="border-collapse: collapse; border-color: transparent; width: 100%;">
                                    <tbody>
                                        <tr>
                                            <td colspan="4" style="height: 5px;"></td>
                                        </tr>
                                        <tr class="aow-name">
                                            <td></td>
                                            <td colspan="3" class="ft-b-12">
                                                <div>
                                                    <span class="underline">{{ $value['area_of_work_name'] }}</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
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
                                            <td style="vertical-align: top;width: 50px;" class="ft-12">
                                                <span> {{ chr(65 + $index) }} . {{ $countIndex }} </span>
                                            </td>
                                            <td style="width: 600px;" class="ft-12">
                                                <!-- Adjust the width as needed -->
                                                <span class="line-sp aow-item">{!! formatText($hasAOW['name']) !!}</span>
                                            </td>
                                            <td style="width: 100px;" align="center" class="ft-12">
                                                {{ calculateMeasurement($hasAOW) }}</td>
                                            <!-- Adjust width -->

                                            <td style="width: 100px;" align="right" class="ft-12">
                                                {{ calculateTotalPrice($hasAOW) }}
                                            </td>
                                            <!-- Adjust width -->
                                        </tr>
                                    @endforeach
                                </table>
                            @endforeach
                            <table border="1"
                                style="border-collapse: collapse; border-color: transparent; width: 100%;">

                                @if (calculateTotalAmountForEachSections($item['section_id'], $quotationList))
                                    <tr>
                                        <td colspan="4" style="height: 15px;"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"></td>

                                        <td style="width: 100px;" align="center" class="ft-b-12">
                                            Sub Total:</td>
                                        <!-- Adjust width -->

                                        <td style="width: 100px;" align="right" class="ft-b-12 border-b border-t">
                                            {{ calculateTotalAmountForEachSections($item['section_id'], $quotationList) }}
                                        </td>

                                    </tr>
                                    <tr>
                                        <td colspan="4" style="height: 5px;"></td>
                                    </tr>
                                @endif
                            </table>
                        @endif



                    </div>
                @endforeach
            </div>
            <table class="avoid-break" style="width:100%;font-size:12px;">
                <tbody>
                    <tr>
                        <td colspan="4" align="left" style="width: 60%">{{ $quotationList->disclaimer }} </td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
            <div class="summary-section page">
                <table style="border-collapse: collapse; border-color: transparent; width: 100%;font-size:12px;">
                    <thead>
                        <tr class="bg-gray">
                            <td style="width: 50px;"></td>
                            <td colspan="2">Summary</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="4" style="height: 5px;"></td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sortQuotation as $index => $item)
                            @if (isset($item['emptyAOWData']) || count($item['hasAOWData']) != 0)
                                <tr>
                                    <td style="width: 50px;">
                                        <span class="ft-b-14 ">{{ chr(65 + $index) }}</span>
                                    </td>
                                    <td colspan="2" class="section-name">
                                        <span class="ft-b-14">{{ $item['section_name'] }}</span>
                                    </td>
                                    <td align="right">
                                        {{ calculateTotalAmountForEachSections($item['section_id'], $quotationList) }}
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>

                </table>
                <table class="avoid-break" style="width:100%">

                    <tbody class="ft-b-12">


                        <tr>
                            <td colspan="4" align="right">Good Will Discount :</td>
                            <td style="width: 100px;" align="right" class="border-b">
                                ${{ number_format($total_prices['only_discount_amount'], 2, '.', ',') }}</td>
                        </tr>


                        <tr>
                            <td colspan="4" align="right">Total Contract Sum :</td>
                            <td style="width: 100px;" align="right" class="border-b">
                                ${{ number_format($total_prices['total_special_discount'], 2, '.', ',') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    @endif

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
                <span class="ft-b-12">AMP DESIGN STUDIO PTE LTD</span> (hereunder called the Company)
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
                All the above stated products will remain as the property of Amp Design Studio Pte Ltd
                until
                payment is fully paid
            </div>
            <br />
            <div>
                @include('pdf.Common.Amp.termsOfPaymentTermComponent')

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
                    <span class="ft-b">Company Name:</span> Amp Design Studio Pte Ltd
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
                @include('pdf.Common.Amp.paymentTermsComponent')
                <br />
            </div>
            </p>
        </div>
    </div>
</body>

</html>
