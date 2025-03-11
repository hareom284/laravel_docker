<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=\, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link type="text/css" media="all" rel="stylesheet" href="{{ public_path('css/pdf.css') }}">
    <link type="text/css" media="all" rel="stylesheet" href="{{ public_path('css/font_style.css') }}">

    <style>
        .qr-code {
            margin: 20px 0;
            padding: 20px 0;
        }

        .overlay-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 72px;
            color: red;
            opacity: 0.5;
            font-weight: bold;
            z-index: 1;
            pointer-events: none;
        }

        .content {
            position: relative;
        }
    </style>
</head>

<body style="padding-top: 20px;">
    <div class="content">
        <div class="overlay-text">PAID</div>
        <div class="header-section" style="margin-bottom: 15px; padding-right: 20px; padding-left: 20px;">
            <table>
                <tr>
                    <td style="width: 90px;" class="ft-b-16">Date :</td>
                    <td class="ft-b-12">
                        <span>{{ $payment_date }}</span>
                    </td>
                    <td style="width: 90px;" class="ft-b-16"></td>
                    <td style="width: 90px;" class="ft-b-16"></td>
                    <td style="width: 90px;" class="ft-b-16">Ref:</td>
                    <td class="ft-b-12">
                        <span>{{ $customerPaymentInvNo }}</span>
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align: top; width: 90px;" class="ft-b-16">Name :</td>
                    <td style="vertical-align: top; width: 200px;" class="ft-b-12">
                        @foreach ($customers_array as $customer)
                            <span>{{ $customer['name_prefix'] . ' ' . $customer['last_name'] . ' ' . $customer['first_name'] }}</span>
                        @endforeach
                    </td>

                </tr>
                <tr>
                    <td style="vertical-align: top; width: 90px;" class="ft-b-16">Address :</td>
                    <td style="vertical-align: top; width: 200px;" class="ft-b-12">
                        <span>{{ $address }}</span>
                    </td>
                <tr>
                    <td style="vertical-align: top; width: 90px;" class="ft-b-16">HP :</td>
                    <td style="vertical-align: top; width: 200px;" class="ft-b-12">
                        @foreach ($customers_array as $customer)
                            <span>{{ $customer['contact_no'] }}</span>
                        @endforeach
                    </td>
                </tr>
            </table>
        </div>

        <div class="" style="padding-right: 20px; padding-left: 20px;">
            <div>
                <div style="margin: 0 !important; padding: 5px 0 5px 0 !important; line-height: 1;">
                    <p class="ft-b-16" style="margin: 0; padding: 0;">Customer Invoice</p>       
                </div>
                <table style="border-collapse: collapse; border-color:transparent; width: 100%; margin-bottom: 20px; margin-top: 50px !important;">
                    <tr style="margin-top: 0 important;">
                        <td style="width: 30px;"></td>
                        <td style="vertical-align: top;width: 55px; padding-left: 50px; margin-right: 5px;" class="ft-b-12">
                            <span>{{ $type }}</span>
                        </td>
                        <td style="width: 80px;"></td>
                        <td style="width: 80px;" class="ft-b-12">
                            $ {{ addCommaToThousand($netAmountTaxable) }}
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30px;"></td>
                        <td style="width: 30px;"></td>
                        <td style="width: 30px;"></td>
                        <td style="width: 30px;"></td>
                    </tr>
                    <tr>
                        <td style="width: 30px;"></td>
                        <td style="width: 30px;"></td>
                        <td style="width: 30px;"></td>
                        <td style="width: 30px;"></td> 
                    </tr>
                    <tr>
                        <td style="width: 30px;"></td>
                        <td style="width: 30px;"></td>
                        <td style="width: 30px;"></td>
                        <td style="width: 30px;"></td>
                    </tr>
                    <tr style="margin-top: 0 important;">
                        <td style="width: 30px;"></td>
                        <td style="vertical-align: top;width: 55px; padding-left: 50px; margin-right: 5px;"></td>
                        <td style="width: 10px;" class="ft-b-12">
                            <span>9% GST:</span>
                        </td>
                        <td style="width: 30px;" class="ft-b-12">
                            $ {{ addCommaToThousand($gst) }}
                        </td>
                    </tr>
                    <tr style="margin-top: 0 important;">
                        <td style="width: 30px;"></td>
                        <td style="vertical-align: top;width: 55px; padding-left: 50px; margin-right: 5px;"></td>
                        <td style="width: 10px;" class="ft-b-12">
                            <span>Total:</span>
                        </td>
                        <td style="width: 30px;" class="ft-b-12">
                            $ {{ addCommaToThousand($amount) }}
                        </td>
                    </tr>
                </table>
            </div>
            <p>All cheques must be crossed & made payable to: <strong>TID PLUS DESIGN PTE LTD</strong>, <br>
                transfer to: <strong>OCBC 551-720972-001</strong> or PayNow to UEN No: <strong>200408517Z</strong> or scan through QR code.</p>

            <div class="qr-code" align="left">
                <img src="{{ public_path() . '/images/tidplusqr.png' }}" height="150" />
            </div>
        </div>
    </div>
</body>
</html>
