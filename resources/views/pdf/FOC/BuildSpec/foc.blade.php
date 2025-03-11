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
        .terms {
            clear: both;
        }

        ul.custom-dash {
            list-style: none;
            /* Remove default list styling */
            padding-left: 0;
            /* Remove padding */
        }

        ul.custom-dash li {
            position: relative;
            /* Allows absolute positioning inside */
            padding-left: 20px;
            /* Space for the dash */
        }

        ul.custom-dash li::before {
            content: "-";
            /* The dash */
            position: absolute;
            /* Position it absolutely within the li */
            left: 0;
            /* Align to the left */
        }
    </style>
</head>

<body>

    @php
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
    @if (count($sortQuotation) != 0)
        <div class="pdf-content">
            @foreach ($sortQuotation as $index => $item)
                <div key="{{ $item['section_id'] }}">
                    <table border="1" style="border-collapse: collapse; border-color: transparent; width: 100%;">
                        @if (isset($item['emptyAOWData']) || count($item['hasAOWData']) != 0)
                            {{-- <thead> --}}
                            <tr>
                                <td style="width: 50px;">
                                    <span class="ft-b-14 ">{{ chr(65 + $index) }}</span>
                                </td>
                                <td colspan="3" class="section-name">
                                    <span class="ft-b-14 underline">{{ $item['section_name'] }}</span>
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
                                        <span>{{ chr(65 + $index) }} . {{ $countIndex }}</span>
                                    </td>
                                    <td style="width: 600px;" class="ft-12"> <!-- Adjust the width as needed -->
                                        <span class="aow-item">{!! formatText($emptyAOW['name']) !!}</span>
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
                                <thead>
                                    <tr class="aow-name">
                                        <td></td>
                                        <td colspan="3" class="ft-b-12">
                                            <div>
                                                <span class="underline">{{ $value['area_of_work_name'] }}</span>
                                            </div>
                                        </td>
                                    </tr>
                                </thead>
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
                                            <span>{{ chr(65 + $index) }} . {{ $countIndex }}</span>
                                        </td>
                                        <td style="width: 600px;" class="ft-12">
                                            <!-- Adjust the width as needed -->
                                            <span class="aow-item">{!! formatText($hasAOW['name']) !!}</span>
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
                                    <td colspan="2"></td>
                                    @if ($item['hasAOWData'][0]['area_of_work_items'][0]['calculation_type'] != 'NORMAL')
                                        <td style="width: 100px;" align="center" class="ft-12">
                                            Total Amount</td>
                                        <!-- Adjust width -->

                                        <td style="width: 100px;" align="right" class="ft-12">
                                            {{ calculateTotalAmountForEachSections($item['section_id'], $quotationList) }}
                                        </td>
                                    @endif
                                </tr>
                            @endif
                        </table>
                    @endif



                </div>
            @endforeach
            <table class="avoid-break" style="width:100%;font-size:12px;">
                <tbody>
                    <tr>
                        <td colspan="4" align="left" style="width: 60%">{!! $quotationList->disclaimer !!} </td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
            <table class="avoid-break" style="width:100%">

                <tbody class="ft-b-12">
                    <tr>
                        <td colspan="4" align="right">Total For The Above Mentioned Renovation Works :</td>
                        <td style="width: 100px;" align="right">FOC</td>
                    </tr>
                </tbody>
            </table>

            <div class="signature-section avoid-break">
                <table class="ft-12 avoid-break" style="float: left;">
                    <tbody>
                        <tr>
                            <td colspan="2">
                                <div>Yours faithfully</div>
                                <div>Sales Rep.Signature</div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <img src="{{ 'data:image/png;base64,' . $quotationData['saleperson_signature'] }}"
                                    style="height:100px">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">{{ $quotationData['companies']['name'] }}</td>
                        </tr>
                        <tr>
                            <td>Name </td>
                            <td>: {{ $quotationData['signed_saleperson'] }}</td>
                        </tr>
                        <tr>
                            <td>DESIGNATION </td>
                            <td>: {{ $quotationData['rank'] }}</td>
                        </tr>
                        <tr>
                            <td>MOBILE </td>
                            <td>: {{ $quotationData['signed_sale_ph'] }}</td>
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
                                            <div> I/We Confirm Our Acceptance</div>
                                            <div>Client Signature</div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <img src="{{ 'data:image/png;base64,' . $customer['customer_signature'] }}"
                                                style="height:100px">
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

            <div class="terms page">
                <div class="ft-12">
                    <span class="underline">Terms & Conditions</span>
                </div>
                <div>
                    <ul class="ft-12 custom-dash">
                        <li>
                            All quotes above are valid for thirty (30) days from the date of the quotation. If
                            acceptance is received after this period has
                            lapsed, the work may be required to be requoted
                        </li>
                        <li>
                            Full payment to “Buildspec Construction Pte Ltd” upon work completion
                        </li>
                        <li>
                            Payment Term: NET30. Invoices that are not paid by the Client within 30 days (or the
                            period specified on the invoice) will be
                            deemed a Payment Default.

                        </li>
                        <li>
                            Payment defaults that are 30 days or more overdue may be handed over to a debt collection
                            agency or solicitor
                        </li>
                        <li>
                            Any costs incurred by Buildspec Construction Pte Ltd in obtaining payment of outstanding
                            invoices including legal costs and/or collection agency fees will be the sole responsibility
                            of the Client.
                        </li>
                        <li>
                            We reserve the right to void the warranty if payment is not received in full
                        </li>
                        <li>
                            Any items not quoted herein will be consider variation and will be charge accordingly
                        </li>
                        <li>
                            Client to provide water, electrical and storage to us at no cost & access for working
                            vehicles
                        </li>
                        <li>
                            All quotes are subjected to prevailing GST
                        </li>
                        <li>
                            Commencement of work will be scheduled 1-3 weeks after receipt of your Work/Purchase Order
                            and/or your acceptance in
                            this quote
                        </li>
                        <li>
                            The Client may at its option cancel the Works, or any part thereof, at any time. The Client
                            will be liable to pay
                            for materials and services provided to the date of cancellation.
                        </li>
                        <li>
                            Buildspec Construction Pte Ltd may at its option cancel the Works, or any part thereof, at
                            any time if the work is not started.
                        </li>
                    </ul>
                    <br />
                    <div class="ft-12">
                        We appreciate your time and consideration. Should you have any question or require further
                        details, please feel free to reach out, We look forward to the opportunity to work with you on
                        this project.
                    </div>
                </div>
            </div>
        </div>
    @endif
</body>

</html>
