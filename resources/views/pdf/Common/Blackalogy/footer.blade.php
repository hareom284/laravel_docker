<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=\, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link type="text/css" media="all" rel="stylesheet" href="{{ public_path('css/font_style.css') }}">
    <style>
        .small-text {
            font-size: 15px;
        }

        .bold-underline {
            font-weight: bold;
            text-decoration: underline;
        }

        .footer-style {
            height: 150px;
            font-size: 12px;
            width: 100%;
            /* padding-bottom: 20px; */

        }
        .footer-style td {
            vertical-align:top;
            width: 100%;

        }
        .footer-style pre {
            white-space: pre-wrap;
            /* margin: 0; */
        }
    </style>

</head>

<body>
    <!-- Footer -->
    <table style="width: 100%;" class="small-text">
        <tr>
            <td style="width: 70%;">
                <div>
                    <p class="ft-b ft-i">Payment Methods for all progressive and final payment</p>
                    <ol>
                        <li>Cheques and Cashier Order to be made payable to <span class="bold-underline">BLACKALOGY INTERIOR PTE LTD</span></li>
                        <li>Bank Transfer to DBS BANK LTD <span class="bold-underline">8852-1581-0684</span></li>
                        <li>Paynow Transfer to UEN: <span class="bold-underline">202340184M</span></li>
                    </ol>
                </div>
            </td>
            <td style="width: 30%; text-align: center;"><img src="{{ public_path() . '/images/sidac.png' }}" height="70" /></td>
        </tr>
    </table>
    <div style="width: 100%; text-align: center;" class="small-text">
        <p class="ft-underline">Please confirm acceptance of quote and payment details by signing and dating below</p>
    </div>
    <!-- Signature -->
    <table style="width: 100%;" class="small-text">
        <tr>
            <td style="width: 50%; padding: 0px 30px;">
                <div>
                    @if(count($quotationData['customer_signature']) > 0)
                    <img src="{{ 'data:image/png;base64,' . $quotationData['customer_signature'][0]['customer_signature'] }}"
                        style="height:100px;">
                    @else
                    <img style="height:100px;">
                    @endif                 
                </div>
                <hr>
                <table style="width: 100%;">
                    <tr>
                        <td style="width: 30%;">Customer Name</td>
                        <td style="width: 70%;"> {{ $quotationData['customers']['name'] }} </td>
                    </tr>
                    <tr>
                        <td style="width: 30%;">Contact No</td>
                        <td style="width: 70%;"> {{ $quotationData['customers']['contact_no'] }} </td>
                    </tr>
                    <tr>
                        <td style="width: 30%;">NRIC</td>
                        <td style="width: 70%;"> {{ $quotationData['customers']['nric'] }} </td>
                    </tr>
                </table>
            </td>
            <td style="width: 50%; padding: 0px 30px;">
                <div>
                    <img src="{{ 'data:image/png;base64,' . $quotationData['saleperson_signature'] }}"
                                    style="height:100px;">
                </div>
                <hr>
                <table style="width: 100%;">
                    <tr>
                        <td style="width: 30%;">Designer Name</td>
                        <td style="width: 70%;"> {{ $quotationData['signed_saleperson'] }} </td>
                    </tr>
                    <tr>
                        <td style="width: 30%;">Contact No</td>
                        <td style="width: 70%;">{{ $quotationData['signed_sale_ph'] }}</td>
                    </tr>
                    <tr>
                        <td style="width: 30%;">ID Registry No</td>
                        <td style="width: 70%;">{{ $quotationData['salepersonRegistryNo'] }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    {{-- Footer Text --}}
    <table class="footer-style">
        <tr>
            <td align="center">
                <pre>{{ $quotationData['footer'] }}</pre>
            </td>
        </tr>
    </table>
</body>

</html>
