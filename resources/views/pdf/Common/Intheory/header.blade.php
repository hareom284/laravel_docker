<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=\, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link type="text/css" media="all" rel="stylesheet" href="{{ public_path('css/font_style.css') }}">
    <style>
        .type {
            display: inline-block;
            border: 2px solid #000;
            padding: 8px 70px;
            font-weight: bold;
            margin-bottom: 3px;
        }

        .company {
            padding: 0px 50px;
        }

        .custom-hr {
            border: none;
            /* Remove the default border */
            height: 2px;
            /* Set the height (thickness) of the line */
            background-color: black;
            /* Set the color of the line */
            margin: 10px 0;
            /* Optional: add some vertical spacing */
        }

        .small-text {
            font-size: 12px;
        }

        .style-text{
            text-decoration: underline;
            font-style: italic;
        }

        .bold {
            font-weight: bold;
        }

        .text-uppercase {
            text-transform: uppercase !important;
        }

        .mb-5 {
            margin-bottom: 5px;;
        }

    </style>

</head>

<body>
    <!-- Header -->
    <table style="width: 100%;" class="small-text">
        <tr>
            <td style="width: 30%";><img src="{{ 'data:image/png;base64,' . $quotationData['companies']['company_logo'] }}" style="width: auto;height:150px"></td>
            <td style="width: 30%";>
                
            </td>
            <td style="width: 60%; padding-top: 50px;"; align="right">
                <div class="mb-5 text-uppercase">
                    <span class="ft-b bold text-uppercase small-text" style="text-align: right;">{{ $quotationData['companies']['main_office'] }}</span>
                </div>
                <div class="mb-5 text-uppercase">
                    <span class="text-uppercase small-text"><span class="ft-b bold text-uppercase" style="text-align: right;"><span>TEL | </span>+{{ $quotationData['companies']['tel'] }}</span>
                </div>
                <div class="mb-5 text-uppercase">
                    <span class="text-uppercase small-text"><span class="ft-b bold text-uppercase" style="text-align: right;">EMAIL | </span>{{ $quotationData['companies']['email'] }}</span>
                </div>
                <div class="mb-5 text-uppercase">
                    <span style="text-align: right;" class="text-upppercase small-text"><span class="bold text-uppercase">WEB | </span> www.intheorydesign.sg</span>
                </div>
                <div class="mb-5 text-uppercase">
                    <span style="text-align: right;" class="text-upppercase small-text"><span class="bold text-uppercase">CO.REG NO | </span> {{ $quotationData['companies']['reg_no'] }}</span>
                </div>
            </td>
        </tr>
    </table>
</body>

</html>
