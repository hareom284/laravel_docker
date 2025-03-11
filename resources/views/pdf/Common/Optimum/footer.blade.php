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
    </style>
</head>

<body onload="subst()">
<div class="signature-section">
    <table class=" avoid-break" style="float: left;">
        <tbody>
            <tr>
                <td>
                    <img src="{{ 'data:image/png;base64,' . $quotationData['saleperson_signature'] }}"
                        style="height:80px" class="twp-image">
                    <p>_____________________________</p>
                </td>
            </tr>
            <tr>
                <td align="left" style="font-size: 12px;">{{ $quotationData['signed_saleperson'] }} / Signature</td>
            </tr>
        </tbody>
    </table>
    <div style="float: right;" class="avoid-break">
        @if (!empty($quotationData['customer_signature']))
            @foreach ($quotationData['customer_signature'] as $customer)
                <table>
                    <tbody>
                        <tr>
                            <td>
                                <img src="{{ 'data:image/png;base64,' . $customer['customer_signature'] }}"
                                    style="height:80px;" class="twp-image">
                                <p>_____________________________</p>
                            </td>
                        </tr>
                        <tr>
                            <td style="font-size: 12px;">
                            {{ $customer['customer']['name_prefix'] . ' ' . $customer['customer']['first_name'] . ' ' . $customer['customer']['last_name'] }} / Signature
                            </td>
                        </tr>
                    </tbody>
                </table>
            @endforeach
        @elseif(!empty($customers_array))
            @foreach ($customers_array as $customer)
                <table>
                    <tbody>
                        <tr>
                            <td>
                                <div style="height:80px;width:200px;">
                                <img src="{{ 'data:image/png;base64,' . $customer['customer_signature'] }}"
                                style="height:50px;" class="twp-image">
                                </div>
                                <p>_____________________________</p>
                            </td>
                        </tr>

                        <tr>
                            <td align="left" style="font-size: 12px;">
                                {{ $customer['name_prefix'] . ' ' . $customer['first_name'] . ' ' . $customer['last_name'] }} / Signature
                            </td>
                        </tr>
                    </tbody>
                </table>
            @endforeach
        @else
        <table>
            <tbody>
                <tr>
                    <td>
                        <div style="height:80px;width:200px;">
                        </div>
                        <p>_____________________________</p>
                    </td>
                </tr>
                <tr>
                    <td style="font-size: 12px;">
                       {{ $quotationData['customers']['name'] }} / Signature
                    </td>
                </tr>
            </tbody>
        </table>
        @endif
    </div>
</div>
<div style="text-align: center; clear:both; font-size: 12px;">
    <p>UOB ACCOUNT : 353-310-524-4</p>
    <p style="padding-bottom: 0; margin-bottom: 0;">Optimum Interior Pte Ltd (UEN 202341627W)</p>
    <p>Northview Bizhub, 6 Yishun Industrial St 1 #06-13 Singapore 768090 | 9109 1687 | alvin@optimum-Interior.com</p>
</div>
</body>

</html>
