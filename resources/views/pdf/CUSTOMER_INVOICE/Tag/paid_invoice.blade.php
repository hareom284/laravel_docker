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
        .qr-code {
            margin: 20px 0;
            padding: 20px 0;
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
    <div class="header-section">
        <div style="width: 100%; text-align: center;">
            <p style="font-weight: bold; font-size: 30px; margin: 0;">TAX INVOICE</p>
        </div>
        <table style="width: 100%; font-size: 16px; padding: 20px;">
            <tr>
                <td style="font-size: 16px;">
                    <table>
                        <tr>
                            <td style="width: 100px;">Invoice :</td>
                            <td>
                                <span>{{ $customerPaymentInvNo }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top; width: 90px;">Date :</td>
                            <td style="vertical-align: top; width: 200px;">
                                <span>{{ $payment_date }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top; width: 90px;">To :</td>
                            <td style="vertical-align: top; width: 200px;">
                                @foreach ($customers_array as $customer)
                                    <span>{{ $customer['name_prefix'] . ' ' . $customer['last_name'] . ' ' . $customer['first_name'] }}</span>
                                @endforeach
                                <br><span>{{ $address }}</span>
                            </td>
                        <tr>
                            <td style="vertical-align: top; width: 150px; font-weight: bold;">Project Code :</td>
                            <td style="vertical-align: top; width: 200px; font-weight: bold;">
                                <span>{{ $agreementNo }}</span>
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="font-size: 15px; padding-left: 130px;">
                    Payment Terms: <br>
                    10% Contract Confrimation <br>
                    40% Before Commencement of Work <br>
                    45% Before Carpentry Confrimation <br>
                    5% Upon Completion of Works
                </td>
            </tr>
        </table>
    </div>
    <div class="" style="padding-right: 20px; padding-left: 20px; font-size: 15px;">
        <div>
            <div style="margin: 0 !important; padding: 5px 0 5px 0 !important; line-height: 1;">
                <p class="ft-b-16" style="margin: 0; padding: 0;">RE: Renovation Work</p>
            </div>
            <table style="border-collapse: collapse; border-color:transparent; width: 100%; margin-bottom: 20px;">
                <tr style="margin-top: 0 important;">
                    <td style="width: 30px; border: 2px solid black; border-right: none; padding: 10px;">S/N</td>
                    <td
                        style="vertical-align: top;width: 280px; margin-right: 5px; border: 2px solid black; border-right: none; border-left: none; padding: 10px;">
                        <span>Description</span>
                    </td>
                    <td
                        style="width: 50px; text-align:center; border: 2px solid black; border-right: none; border-left: none; padding: 10px;">
                        Contract Price</td>
                    <td
                        style="width: 30px; text-align:center; border: 2px solid black; border-right: none; border-left: none; padding: 10px;">
                        QTY</td>
                    <td
                        style="width: 50px; text-align:center; border: 2px solid black; border-left: none; padding: 10px;">
                        TOTAL
                    </td>
                </tr>
                <tr style="margin-top: 0 important;">
                    <td style="width: 30px; border: 2px solid black; border-right: none; padding: 10px;">1.</td>
                    <td
                        style="vertical-align: top;width: 280px; text-align: left; line-height:20px; padding-left: 50px; margin-right: 5px; border: 2px solid black; border-right: none; border-left: none; padding: 10px;">
                        <span>{{ $description }}</span>
                    </td>
                    <td
                        style="width: 50px;text-align:center; border: 2px solid black; border-right: none; border-left: none; padding: 10px;">
                        $ {{ addCommaToThousand($contractPrice) }}</td>
                    <td
                        style="width: 30px;text-align:center; border: 2px solid black; border-right: none; border-left: none; padding: 10px;">
                        1</td>
                    <td
                        style="width: 50px;text-align:right; border: 2px solid black; border-left: none; padding: 10px;">
                        $ {{ addCommaToThousand($contractPrice) }}
                    </td>
                </tr>
                <tr style="margin-top: 0 important;">
                    <td style="width: 50px; text-align: center;  border: 2px solid black;" colspan="4">
                        <span style="font-size: 10px;">E.& O. E. </span><span> Sub Total:</span>
                    </td>
                    <td style="width: 50px; border: 2px solid black; padding: 10px; text-align: right;">
                        $ {{ addCommaToThousand($contractPrice) }}
                    </td>
                </tr>
                <tr>
                    <td style="width: 30px; padding: 10px;"></td>
                    <td style="width: 30px; padding: 10px;"></td>
                    <td style="width: 30px; padding: 10px;"></td>
                    <td style="width: 30px; padding: 10px;"></td>
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
                    <td style="width: 50px; padding: 5px;"></td>
                    <td style="width: 30px; padding: 5px;"></td>
                    <td style="vertical-align: top;width: 55px; padding-left: 50px; margin-right: 5px; padding: 5px;">
                    </td>
                    <td style="width: 160px; padding: 5px; text-align: right;">
                        <span>{{ $type }}:</span>
                    </td>
                    <td style="width: 30px; text-align: center; padding: 5px; text-align: right;">
                        {{-- $ {{ addCommaToThousand($amount) }} --}}
                        $ {{ addCommaToThousand($netAmountTaxable) }}
                    </td>
                </tr>
                <tr style="margin-top: 0 important;">
                    <td style="width: 30px; padding: 5px;"></td>
                    <td style="width: 30px; padding: 5px;"></td>
                    <td style="vertical-align: top;width: 55px; padding-left: 50px; margin-right: 5px; padding: 5px;">
                    </td>
                    <td style="width: 150px; padding: 5px; text-align: right;">
                        <span>9% GST:</span>
                    </td>
                    <td style="width: 30px; text-align: right; padding: 5px;">
                        $ {{ addCommaToThousand($gst) }}
                    </td>
                </tr>
                <tr style="margin-top: 0 important;">
                    <td style="width: 30px; padding: 5px;"></td>
                    <td style="width: 30px; padding: 5px;"></td>
                    <td style="vertical-align: top;width: 55px; padding-left: 50px; margin-right: 5px; padding: 5px;">
                    </td>
                    <td style="width: 150px; font-weight: bold; padding: 5px; text-align: right;">
                        <span>Grand Total:</span>
                    </td>
                    <td style="width: 30px; text-align:right; font-weight: bold; padding: 5px;">
                        $ {{ addCommaToThousand($amount) }}
                    </td>
                </tr>
                <tr>
                    <td style="width: 30px;"></td>
                    <td style="width: 30px;"></td>
                    <td style="width: 30px;"></td>
                    <td style="width: 30px;"></td>
                    <td style="width: 30px; ;"></td>
                </tr>
            </table>
        </div>
        <p style="margin-bottom: 50px;">*Total amount of <span style="font-weight: bold;">Singapore Dollars : SGD
                {{ addCommaToThousand($amount) }}</span></p>
        <ol style="list-style-type: decimal;">
            <li>Kindly make payment <strong>ASAP (Unless specified above **)</strong> from the date of this invoice.
                Failing which, interest at 12% per
                annum shall be imposed on any unpaid amount until full payment is received.</li>
            <li>
                Payment(s) via cheque should be made crossed and payable to <strong>TAG Studio Pte Ltd</strong><br />
                made via bank transfer, please notify us via e-mail to: agnes@techart.com.sg & shalene@techart.com.sg
                <br />
                <table style="width: 65%;">
                    <tr>
                        <td style="vertical-align: top;">
                            <strong>Bank details:</strong>
                        </td>
                        <td style="vertical-align: top;">
                            <strong>Name of Bank:</strong><br />
                            <strong>Bank Account No.:</strong><br />
                            <strong>Bank Code:</strong><br />
                            <strong>Branch Code:</strong><br />
                            <strong>Swift Code:</strong>
                        </td>
                        <td style="vertical-align: top;">
                            <strong>DBS BANK</strong><br />
                            <strong>054-904736-8</strong><br />
                            <strong>7171</strong><br />
                            <strong>054</strong><br />
                            <strong>DBSS SG SG</strong>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">Payment(s) VIA PAYNOW UEN NO. 201502086C</td>
                    </tr>
                </table>
            </li>
            <li>Acceptance and confirmation on the above works. Subject to TAG STUDIO PTE LTD Term & Conditions.</li>
        </ol>
    </div>
    <div style="padding-right: 20px; padding-left: 20px; margin-top: 15px;">
        <table>
            <tbody>
                <tr>
                    <td>
                        <img src="" style="height:100px;" class="twp-image">
                    </td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">
                        TAG Studio Pte Ltd
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</body>

</html>
