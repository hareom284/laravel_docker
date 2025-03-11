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
    </style>
</head>
<body>
    <div class="header-section" style="padding: 0 30px 30px 30px;">
        <p style="text-align: center; text-decoration: underline; font-size: 40px; font-weight: bold;">Invoice</p>
        <table border="1" style="border-collapse: collapse; border-color: black; width: 100%;font-size: 16px; margin-top: 30px;">
            <tr>
                <td style="width: 50%; padding: 10px;">
                    <span>Attention:</span>
                    @foreach ($customers_array as $customer)
                    <span>{{ $customer['name_prefix'] . ' ' . $customer['last_name'] . ' ' . $customer['first_name'] }}</span>
                    @endforeach
                </td>
                <td style="width: 50%; padding: 10px;"><span>Date: </span><span>{{ $payment_date }}</span></td>
            </tr>
            <tr>
                <td style="width: 50%; padding: 10px;"><span>Address:</span><span>{{ $address }}</span></td>
                <td style="width: 50%; padding: 10px;"><span>Invoice #:</span><span>{{ $customerPaymentInvNo }}</span></td>
            </tr>
            <tr>
                <td style="width: 50%; padding: 10px;">
                    <span>Tel:</span>
                    @foreach ($customers_array as $customer)
                    <span>{{ $customer['contact_no'] }}</span>
                    @endforeach
                </td>
                <td style="width: 50%; padding: 10px;">
                    <span>Reference PO Number:</span>
                    <span>{{ $agreementNo }}</span>
                </td>
            </tr>
        </table>

        <table border="1"  style="border-collapse: collapse; border-color: black; width: 100%;font-size: 16px; margin-top: 30px;">
            <tr>
                <td style="width: 35%; padding: 7px;">DESIGNER IN-CHARGE</td>
                <td style="width: 30%; padding: 7px;">CONTACT NO</td>
                <td style="width: 30%; padding: 7px;">EMAIL</td>
            </tr>
            @foreach ($salepersons as $saleperson)
            <tr>
                <td style="width: 35%; padding: 7px;">{{ $saleperson['full_name'] }}</td>
                <td style="width: 30%; padding: 7px;">{{ $saleperson['contact_no'] }}</td>
                <td style="width: 30%; padding: 7px;">{{ $saleperson['email'] }}</td>
            </tr>
            @endforeach
        </table>

        <p style="margin-top: 30px;">Project Address: {{ $address }}</p>
        
        <table border="1"  style="border-collapse: collapse; border-color: transparent; width: 100%;font-size: 16px; margin-top: 30px;">
            <tr style="border-bottom: 2px solid black; padding-bottom: 5px;">
                <td style="width: 5%; padding: 7px;">No.</td>
                <td style="width: 80%; padding: 7px;">Description</td>
                <td style="width: 15%; padding: 7px;">Amount (SGD)</td>
            </tr>
            <tr>
                <td style="width: 5%; padding: 7px;">1.</td>
                <td style="width: 80%; padding: 7px;">{{ $description }}</td>
                <td style="width: 15%; padding: 7px;" align="right">${{ addCommaToThousand($amount) }}</td>
            </tr>
            <tr>
                <td style="width: 5%; padding: 7px;"></td>
                <td style="width: 80%; padding: 7px;">Renovation Works as Stated in Attached Contract Details</td>
                <td style="width: 15%; padding: 7px;"></td>
            </tr>
            <tr>
                <td style="width: 5%; padding: 7px;"></td>
                <td style="width: 80%; padding: 7px;">Quotation Ref: {{ $agreementNo }}</td>
                <td style="width: 15%; padding: 7px;"></td>
            </tr>
            @for ($i = 0; $i <= 5; $i++)
            <tr>
                <td style="width: 5%; padding: 7px;"></td>
                <td style="width: 80%; padding: 7px;"></td>
                <td style="width: 15%; padding: 7px;"></td>
            </tr>
            @endfor
            <tr style="border-bottom: 2px solid black; border-top: 2px solid black; padding-bottom: 5px;">
                <td style="width: 5%; padding: 7px;"></td>
                <td style="width: 80%; padding: 7px;" align="right">TOTAL : </td>
                <td style="width: 15%; padding: 7px;" align="right">${{ addCommaToThousand($amount) }}</td>
            </tr>
        </table>

        <div style="margin-top: 30px;">
            <p>Make all cheques payable to: The Metis Designers Firm LLP</p>
            <p>Alternatively you may PayNow to: Company UEN – T20LL1942A</p>
            <p style="text-align:center">OCBC Current Account – 601-404460-001</p>
        </div>
        <p style="margin-top: 30px; text-align: right;text-decoration: underline;">{{ $company_name }}</p>
    </div>
</body>
</html>
