<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        * {
            font-family: sans-serif;
            margin: 0;
            font-size: 12px;
            /* Use the custom font, fallback to sans-serif if unavailable */
        }

        .footer-style {
            background: #1A4323;
            padding: 18px 20px 18px 40px;
            width: 88%;
        }

        .text-white {
            color: #fff;
        }

        .text-green {
            color: #76997B !important;
        }

        .ft-12 {
            font-size: 12px;
        }

        .ft-14 {
            font-size: 14px;
        }

        .ft-header {
            font-weight: bold;
            font-size: 22px;
        }

        .bg-color-1 {
            background: #EBECDE;
            height: 100px;
            width: 2%;
        }

        .bg-color-2 {
            background: #999F83;
            height: 100px;
        }
        .border-b {
            border-bottom: 1px solid black !important;
            /* padding: 5px 0; */
        }
    </style>
</head>

<body style="padding-top: 10px;">
    {{-- <div style="width: 100%; overflow: hidden;">
        <table style="width: 49%; float: left;">
            <tbody style="float: left; margin-left: 40px;">
                <tr>
                    <td colspan="2">
                        <div>Yours faithfully</div>
                        <div>Sales Rep. Signature</div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <img src="{{ 'data:image/png;base64,' . $quotationData['saleperson_signature'] }}"
                            style="height:100px" class="twp-image border-b">
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="ft-b"><b>{{ $quotationData['companies']['name'] }}</b></td>
                </tr>
                <tr>
                    <td colspan="2" class="ft-b"><b>{{ $quotationData['signed_saleperson'] }}</b></td>
                </tr>
            </tbody>
        </table>
    
        @if (!empty($quotationData['customer_signature']))
            @foreach ($quotationData['customer_signature'] as $customer)
                <table style="width: 49%; float: right;">
                    <tbody style="float: right; margin-right: 40px;">
                        <tr>
                            <td colspan="2">
                                <div>Price, Layout, Terms & Conditions</div>
                                <div>Agreed & Accepted By :</div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <img src="{{ 'data:image/png;base64,' . $customer['customer_signature'] }}"
                                    style="height:100px;" class="twp-image border-b">
                            </td>
                        </tr>

                        <tr>
                            <td colspan="2" class="ft-b"><b>Name / Signature</b> </td>
                        </tr>
                        <tr>
                            <td>NRIC No. : </td>
                            <td class="border-b" style="padding-right: 100px;">{{ $customer['customer']['customers']['nric'] }}</td>
                        </tr>
                        <tr>
                            <td>DATE : </td>
                            <td class="border-b" style="padding-right: 100px;">{{ $quotationData['signed_date'] }}</td>
                        </tr>
                    </tbody>
                </table>
            @endforeach
        @elseif(!empty($customers_array))
            @foreach ($customers_array as $customer)
                <table style="width: 49%; float: right;">
                    <tbody style="float: right; margin-right: 40px;">
                        <tr>
                            <td colspan="2">
                                <div>Price, Layout, Terms & Conditions</div>
                                <div>Agreed & Accepted By :</div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <div style="height:100px;width:200px;" class="border-b">
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="ft-b">Name / Signature </td>
                        </tr>
                        <tr>
                            <td>NRIC No. : </td>
                            <td class="border-b" style="padding-right: 100px;">{{ $customer['customers']['nric'] }}</td>
                        </tr>
                        <tr>
                            <td>DATE : </td>
                            <td class="border-b" style="padding-right: 100px;">{{ $quotationData['created_at'] }}</td>
                        </tr>
                    </tbody>
                </table>
            @endforeach
        @endif
    </div> --}}
    
    <div style="width: 100%; margin-top: 20px;">
        <div class="footer-style" style="float: left;">
            <table style="width: 100%;" class="text-white">
                <tr>
                    <td style="width: 450px;">
                        <span class="ft-header"
                            style="padding-bottom: 5px; margin-bottom: 0;">{{ $quotationData['companies']['name'] }}</span><br />
                        <span class="ft-14 text-green">{{ $quotationData['companies']['main_office'] }}</span>
                    </td>
                    <td>
                        <img style="float: left;padding-right: 10px;" src="{{ public_path() . '/images/mail_supa.png' }}"
                            height="25" />
                        <div style="margin-top: 6px;" class="text-green ft-14">
                            {{ $quotationData['companies']['email'] }}
                        </div>
                    </td>
                    <td><img style="float: left;padding-right: 10px;" src="{{ public_path() . '/images/phone_supa.png' }}"
                            height="25" />
                        <div style="margin-top: 6px;" class="text-green ft-14">
                            {{ '+65' . ' ' . $quotationData['companies']['tel'] }}
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        <div class="bg-color-2">
            <div class="bg-color-1" style="float: left;"></div>
        </div>
    </div>
    
</body>

</html>
