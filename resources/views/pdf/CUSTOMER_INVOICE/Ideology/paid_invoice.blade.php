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

        .border-bottom {
            border-bottom: 2px solid black;
        }

        .overlay-text {
            position: absolute;
            top: 25%;
            left: 20%;
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
        <table border="1px" style="border-collapse: collapse;width: 100%;">
            <tr>
                <td align="center" style="width: 60%;" colspan="2">DESCRIPTION</td>
                <td align="center" style="width: 20%;">COLLECTED S$</td>
                <td align="center" style="width: 20%;">INVOICE AMT S$</td>
            </tr>
            <tr>
                <td colspan="4"></td>
            </tr>
            <tr style="border-bottom: 0;">
                <td colspan="2" style="border-bottom: 0;">
                    <h4>BEING PAYMENT RECEIVED FOR THE ABOVE JOB SITE.</h4>
                </td>
                <td style="border-bottom:0;"></td>
                <td style="border-bottom:0;"></td>
            </tr>
            <tr>
                <td align="center" style="border-right: 0;border-top:0; height: 250px;vertical-align: top;">
                    {{ $type }}</td>
                <td style="border-left: 0;border-top:0;vertical-align: top;">Received on</td>
                <td align="center" style="border-top:0;vertical-align: top;">{{ addCommaToThousand($netAmountTaxable) }}
                </td>
                <td align="center" style="border-top:0;vertical-align: top;">{{ addCommaToThousand($netAmountTaxable) }}
                </td>
            </tr>
            <tr>
                <td colspan="2" align="right">
                    <h4>AMOUNT COLLECTED</h4>
                </td>
                <td style="padding: 0 10px; border: 2px solid black;">
                    <div style="float: left;">$</div>
                    <div style="float: right;">{{ addCommaToThousand($netAmountTaxable) }}</div>
                </td>
                <td></td>
            </tr>
            <tr>
                <td colspan="3" align="right" style="border-left: 0; border-bottom: 0">
                    <h4>INVOICE TOTAL = </h4>
                </td>
                <td style="padding: 0 10px; border: 2px solid black;">
                    <div style="float: left;">$</div>
                    <div style="float: right;">{{ addCommaToThousand($netAmountTaxable) }}</div>
                </td>
            </tr>
        </table>

        <table>
            <tr>
                <td style="width: 60%">
                    <p>We thank you for the opportunity to the above project.</p>
                    <br />
                    <p style="font-weight: bold;">
                        All payment made payable in Singapore Dollars<br />
                        Company Name : Ideology Interior Pte Ltd<br />
                        Account Number : UOB 348-318-414-6<br />
                        Bank Code : 7375<br />
                        Bank Swift Code : UOVBSGSG<br />
                    </p>
                </td>
                <td style="width: 50px;"></td>
                <td style="width: 40%" align="center">
                    <p>IDEOLOGY INTERIOR PTE LTD</p>
                    <div style="height: 100px; width: 250px; border-bottom: 1px solid black;"></div>
                    <p style="text-align: center;">Authorised Signature</p>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
