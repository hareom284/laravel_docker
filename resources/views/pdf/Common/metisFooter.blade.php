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
    <div class="signature-section avoid-break" style="position: relative;">
        <table class="ft-12 avoid-break" style="float: left;">
            <tbody>
                <tr>
                    <td colspan="2">
                        <div class="text-xl ft-bold">{{ $quotationData['signed_saleperson'] }}</div>
                        <div class="text-xl ft-bold">{{ $quotationData['rank'] }}</div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <img src="{{ 'data:image/png;base64,' . $quotationData['saleperson_signature'] }}"
                            style="height:100px" class="border-b">
                    </td>
                </tr>
            </tbody>
        </table>
        <div style="float: right;" class="avoid-break">
            @if ($quotationData['already_sign'])
                @foreach ($quotationData['customer_signature'] as $customer)
                    <table class="ft-12" style="float: right;">
                        <tbody>
                            <tr>
                                <td colspan="2">
                                    <div class="text-xl ft-bold">Read and Agreed</div>
                                    <div class="text-xl ft-bold" style="visibility: hidden;">Read and Agreed</div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <img src="{{ 'data:image/png;base64,' . $customer['customer_signature'] }}"
                                        style="height:100px" class="border-b">
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
    <br />
    <br />
    <div style="width: 100%;text-align:center;clear:both;">
        <span class="page text-xl"></span>
    </div>
</body>

</html>
