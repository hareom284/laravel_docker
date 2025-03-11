<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=\, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Customer Invoice</title>
    <link type="text/css" media="all" rel="stylesheet" href="{{ public_path('css/pdf.css') }}">
    <link type="text/css" media="all" rel="stylesheet" href="{{ public_path('css/font_style.css') }}">
    <style>
        @page {
            margin: 0; /* Ensures the background color covers the entire page */
            size: A4; /* Defines the size of the page */
        }
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            width: 100%;
            /* background-color: #ECF0F1; */
        }
        .qr-code {
            margin: 20px 0;
            padding: 20px 0;
        }
        .header-section {
            padding-top: 30px;
            padding-right: 30px;
            padding-left: 30px;
            color: black;
        }
        .footer-style {
            position: fixed;
            bottom: 200px;
            text-align: center;
            font-size: 12px;
        }

        .footer-style span {
            display: inline-block; /* Makes the span behave like a block for better centering */
            vertical-align: middle; /* Ensures vertical alignment within the container */
        }

        .watermark {
            position: fixed;
            top: 30%;
            left: 25%;
            -webkit-transform: rotate(-45deg);
            -ms-transform: rotate(-45deg);
            font-size: 200px;
            color: rgba(0, 0, 0, 0.1);
            white-space: nowrap;
            pointer-events: none;
            z-index: -1;
            font-family: Arial, sans-serif;
            font-weight: bold;
        }
    </style>
</head>
<body>
    @if ($status == 1)
        <div class="watermark">PAID</div>
    @endif
    @php
        $tableRows = [
            [
                'qty' => '20.00',
                'description' => 'Areca Pain',
                'unit_price' => '$2.50',
                'line_total' => '$50.00'
            ],
            [
                'qty' => '20.00',
                'description' => 'Areca Pain',
                'unit_price' => '$2.50',
                'line_total' => '$50.00'
            ],
            [
                'qty' => '20.00',
                'description' => 'Areca Pain',
                'unit_price' => '$2.50',
                'line_total' => '$50.00'
            ],
            [
                'qty' => '20.00',
                'description' => 'Areca Pain',
                'unit_price' => '$2.50',
                'line_total' => '$50.00'
            ],
            [
                'qty' => '20.00',
                'description' => 'Areca Pain',
            ]
        ];
    @endphp
    
    <div class="header-section" style="padding: 30px;">
        <table style="width: 100%; font-size: 16px;;">
            <tr>
                <td style="font-size: 16px;">
                    <table style="padding-left: 10px;">
                        <tr>
                            <td style="vertical-align: top; width: 40px;">To :</td>
                            <td style="vertical-align: top; width: 200px;">
                                @foreach ($customers_array as $customer)
                                <span>{{ $customer['name_prefix'] . ' ' . $customer['last_name'] . ' ' . $customer['first_name'] }}</span>
                                @endforeach
                                <br><span>{{ $address }}</span>
                                @foreach ($customers_array as $customer)
                                <span>{{ $customer['contact_no'] }}</span>
                                @endforeach
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="font-size: 15px; padding-left: 330px;">
                    <table>
                        <tr>
                            <td style="vertical-align: top; width: 90px; text-align: right;">Date :</td>
                            <td style="vertical-align: top; width: 200px; text-align: right;">
                                <span>{{ $payment_date }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 100px; text-align: right;">Invoice #:</td>
                            <td style="text-align: right;">
                                <span>{{ $customerPaymentInvNo }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top; width: 90px; text-align: right;">Customer ID :</td>
                            <td style="vertical-align: top; width: 200px; text-align: right;">
                                <span>{{ $agreementNo }}</span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
    <div class="" style="font-size: 15px; padding: 30px;">
        <div>
            <table style="border-collapse: collapse; border-color: transparent; width: 100%;font-size: 15px;">
                <tr style="background-color: rgb(139, 143, 143); border-radius: 50px;">
                    <td style="width: 20%; padding: 8px;">Salesperson</td>
                    <td style="width: 40%; padding: 8px;">Job</td>
                    <td style="width: 20%; padding: 8px;">Payment Terms</td>
                    <td style="width: 20%; padding: 8px;" align="right">Due Date</td>
                </tr>
                @foreach ($salepersons as $saleperson)
                <tr style="background-color: #fff;">
                    <td style="width: 20%; padding: 8px;">{{ $saleperson['full_name'] }}</td>
                    <td style="width: 45%; padding: 8px;">{{ $saleperson['rank_name'] }}</td>
                    <td style="width: 20%; padding: 8px;">Due uppon receipt</td>
                    <td style="width: 15%; padding: 8px;" align="right">{{ $payment_date }}</td>
                </tr>
                @endforeach
            </table>
        </div>
        <div style="margin-top: 40px;">
            <table style="border-collapse: collapse; border-color: transparent; width: 100%;font-size: 15px;">
                <tr style="background-color: rgb(139, 143, 143); border-radius: 50px;">
                    <td style="width: 20%; padding: 8px;">Qty</td>
                    <td style="width: 40%; padding: 8px;">Description</td>
                    <td style="width: 20%; padding: 8px;" align="right">Unit Price</td>
                    <td style="width: 20%; padding: 8px;" align="right">Line Total</td>
                </tr>
                <tr style="border-radius: 50px;">
                    <td style="width: 20%; padding: 8px;">1.00</td>
                    <td style="width: 40%; padding: 8px;">{{ $description }}</td>
                    <td style="width: 20%; padding: 8px;" align="right">${{ addCommaToThousand($amount) }}</td>
                    <td style="width: 20%; padding: 8px;" align="right">${{ addCommaToThousand($amount) }}</td>
                </tr>
                <tr style="background-color: #fff;">
                    <td style="width: 20%; padding: 15px;"></td>
                    <td style="width: 40%; padding: 15px;"></td>
                    <td style="width: 20%; padding: 15px;"></td>
                    <td style="width: 20%; padding: 15px;"></td>
                </tr>
                <tr style="background-color: transparent;">
                    <td style="width: 20%; padding: 15px;"></td>
                    <td style="width: 40%; padding: 15px;"></td>
                    <td style="width: 20%; padding: 15px;"></td>
                    <td style="width: 20%; padding: 15px;"></td>
                </tr>
                <tr style="background-color: #fff;">
                    <td style="width: 20%; padding: 15px;"></td>
                    <td style="width: 40%; padding: 15px;"></td>
                    <td style="width: 20%; padding: 15px;"></td>
                    <td style="width: 20%; padding: 15px;"></td>
                </tr>
                <tr style="background-color: transparent;">
                    <td style="width: 20%; padding: 15px;"></td>
                    <td style="width: 40%; padding: 15px;"></td>
                    <td style="width: 20%; padding: 15px;"></td>
                    <td style="width: 20%; padding: 15px;"></td>
                </tr>
                <tr style="">
                    <td style="width: 20%; padding: 8px;"></td>
                    <td style="width: 40%; padding: 8px;"></td>
                    <td style="width: 20%; padding: 8px; background-color: #fff; border-top: 1px solid #000;" align="right">Subtotal</td>
                    <td style="width: 20%; padding: 8px; background-color: #fff; border-top: 1px solid #000;" align="right">${{ addCommaToThousand($amount) }}</td>
                </tr>
                @if ($gst > 0)
                <tr style="">
                    <td style="width: 20%; padding: 8px;"></td>
                    <td style="width: 40%; padding: 8px;"></td>
                    <td style="width: 20%; padding: 8px;" align="right">Sales Tasx</td>
                    <td style="width: 20%; padding: 8px;" align="right">${{ $gst }}</td>
                </tr>
                @endif
                <tr style="">
                    <td style="width: 20%; padding: 8px;"></td>
                    <td style="width: 40%; padding: 8px;"></td>
                    <td style="width: 20%; padding: 8px; background-color: #fff; border-top: 1px solid #000;" align="right">Total</td>
                    <td style="width: 20%; padding: 8px; background-color: #fff; border-top: 1px solid #000;" align="right">${{ $netAmountTaxable }}</td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
