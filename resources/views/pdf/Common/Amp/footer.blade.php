<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <script>
        function subst() {
            var vars = {};
            var x = document.location.search.substring(1).split('&');
            for (var i in x) {
                var z = x[i].split('=', 2);
                vars[z[0]] = unescape(z[1]);
            }
            var x = ['frompage', 'topage', 'page', 'webpage', 'section', 'subsection', 'subsubsection'];
            for (var i in x) {
                var y = document.getElementsByClassName(x[i]);
                for (var j = 0; j < y.length; ++j) y[j].textContent = vars[x[i]];
            }
        }
    </script>
    <style>
        .text-xl {
            font-size: 19px !important;
        }

        * {
            font-family: sans-serif;
            /* Use the custom font, fallback to sans-serif if unavailable */
        }

        .page-number {
            text-align: center;
            margin: 0 auto;
            width: 100%;
        }

        .ft-bold {
            font-weight: bold;
        }

        .border-b {
            border-bottom: 1px solid black;
        }

        /* .footer-info {
            color: #345f85;
            position: absolute;
            bottom: 0;
            width: 90%;
            text-align: center;
            margin-bottom: 35px;
        } */
        .footer-info {
            color: #345f85;
            position: absolute;
            bottom: 0;
            right: 0;
            width: auto;
            text-align: right;
            margin-right: 20px;
            margin-bottom: 35px;
        }
    </style>
</head>

<body onload="subst()">
    <div class="signature-section avoid-break" style="position: relative;">
        <table class="ft-12 avoid-break" style="float: left; margin-bottom: 65px">
            <tbody>
                <tr>
                    <td>
                        <div class="ft-bold">Confirmed & Agreed By: </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <img src="{{ 'data:image/png;base64,' . $quotationData['saleperson_signature'] }}"
                            style="height:100px; border-bottom: 2px dotted black;" class="border-b">
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="ft-bold">Client's Signature</span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="ft-bold">NRIC:</span> {{ $quotationData['customers']['nric'] ?? 'N/A' }}
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class="ft-bold">Date:</span> {{ $quotationData['signed_date'] }}
                    </td>
                </tr>
            </tbody>
        </table>
        <div style="float: right; margin-bottom: 65px" class="avoid-break">
            @if ($quotationData['already_sign'])
                @foreach ($quotationData['customer_signature'] as $customer)
                    <table class="ft-12" style="float: right;">
                        <tbody>
                            <tr>
                                <td colspan="2" style="float: right">
                                    <div class="ft-bold">Best Regards,</div>
                                    <div class="ft-bold" style="visibility: hidden;">Best Regards</div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <img src="{{ 'data:image/png;base64,' . $customer['customer_signature'] }}"
                                        style="height:100px; border-bottom: 2px dotted black;" class="border-b">
                                </td>
                            </tr>
                            <tr>
                                <td style="float: right" class="ft-bold">
                                    {{ $quotationData['customers']['name'] }}
                                </td>
                            </tr>
                            <tr>
                                <td style="float: right" class="ft-bold">
                                    {{ $quotationData['companies']['name'] }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                @endforeach
            @else
                <div style="height: 200px; width:200px;"></div>
            @endif

        </div>
    </div>
    <div class="footer-info">
        <div>
            <span class="company-name">{{ $quotationData['companies']['name'] ?? 'Company Name' }}</span><br>
            <span class="company-address">{{ $quotationData['companies']['main_office'] ?? 'Company Address' }}</span>
        </div>
        <div>
            <span class="contact-info">
                Phone: {{ $quotationData['companies']['contact_number'] ?? 'N/A' }} |
                Email: {{ $quotationData['companies']['email'] ?? 'N/A' }}
            </span>
        </div>
    </div>
    <div style="width: 100%; text-align: center; clear: both;">
        <span class="page text-xl"></span>
    </div>
</body>

</html>
