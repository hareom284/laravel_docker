<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        * {
            font-family: sans-serif;
            /* Use the custom font, fallback to sans-serif if unavailable */
        }

        .header-section {
            padding-top: 10px;
        }

        .right-header {
            float: right;
        }

        .left-header {
            float: left;
        }

        .logo-section {
            margin: 0 auto;
            text-align: center;
        }

        .miracle-padding {
            padding-bottom: 30px;
        }

        .twp-padding {
            padding-bottom: 15px;
        }

        .hide_header_and_footer {
            display: none;
        }

        .header-name {
            clear: both;
        }

        .header-name {
            clear: both;
        }

        .ft-b {
            font-weight: bold;
        }

        .border-bottom {
            border-bottom: 2px solid black;
        }
    </style>
</head>

<body onload="subst()">

    <div class="header-section">
        @php
            $headerText = $header ?? '';
            $parts = $headerText ? explode('Dear Sir/Mdm,', $headerText) : [];
        @endphp
        <div>
            <table style="width: 100%;">
                <tr>
                    <td style="width: 60%">
                        <img src="{{ 'data:image/png;base64,' . $companies['company_logo'] }}"
                            style="width: auto;height:150px">
                    </td>
                    <td style="width: 40%" align="right">
                        <span>{{ $company_name }}</span><br />
                        <span>Co. Reg. No. {{ $company_reg_no }}</span><br />
                        <span>{{ $company_address }}</span>
                    </td>
                </tr>
            </table>
        </div>

        <div class="logo-section">
            <table style="width: 100%">
                <td align="right">
                    <h2><i style="font-weight: bold;">INVOICE/RECEIPT No. :</i></h2>
                </td>
                <td class="border-bottom" align="center">
                    <h2><b>{{ $customerPaymentInvNo }}</b></h2>
                </td>
            </table>
        </div>
        <br />
        <br />
        <div class="header">
            <table style="width: 100%;" cellpadding="10">
                <tr>
                    <td style="width: 15%">
                        <span>Name :</span>
                    </td>
                    <td style="width: 45%" class="border-bottom" colspan="2">
                        @if (count($customers_array) > 1)
                            <span>
                                {{ implode(
                                    ' / ',
                                    array_map(function ($customer) {
                                        return $customer['name_prefix'] . ' ' . $customer['first_name'] . ' ' . $customer['last_name'];
                                    }, $customers_array),
                                ) }}
                            </span>
                        @else
                            @foreach ($customers_array as $customer)
                                <span>{{ $customer['name_prefix'] . ' ' . $customer['first_name'] . ' ' . $customer['last_name'] }}</span>
                            @endforeach
                        @endif
                    </td>
                    <td style="width: 10%"></td>
                    <td style="width: 10%"></td>
                    <td style="width: 20%"></td>
                </tr>
                <tr>
                    <td style="width: 15%">
                        <span>Job Site :</span>
                    </td>
                    <td style="width: 45%" class="border-bottom" colspan="2">
                        @if (isset($properties))
                            {{ $properties['block_num'] . ' ' . $properties['street_name'] }}
                            {{ $properties['unit_num'] ? ' #' : '' }}
                            {{ $properties['unit_num'] }}
                            {{ $properties['postal_code'] ? 'S(' . $properties['postal_code'] . ')' : '' }}
                        @endif
                    </td>
                    <td style="width: 10%"></td>
                    <td style="width: 10%" align="right">Date :</td>
                    <td style="width: 20%" class="border-bottom">
                        @if (isset($created_at))
                            {{ $created_at }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="width: 15%">
                        <span>Contact No :</span>
                    </td>
                    <td style="width: 100px;" class="border-bottom">
                        @if (count($customers_array) > 1)
                            <span>{{ implode(' / ', array_column($customers_array, 'contact_no')) }}</span>
                        @else
                            @foreach ($customers_array as $customer)
                                <span>{{ $customer['contact_no'] }}</span>
                            @endforeach
                        @endif
                    </td>
                    <td style="width: 50px"></td>
                    <td style="width: 15%;" align="right">In Charge :</td>
                    <td colspan="2" class="border-bottom">
                        @if (count($salepersons) > 1)
                            <span>{{ implode(' / ', array_column($salepersons, 'full_name')) }}</span>
                        @else
                            @foreach ($salepersons as $saleperson)
                                <span>{{ $saleperson['full_name'] }}</span>
                            @endforeach
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="width: 15%">
                        <span>Project No :</span>
                    </td>
                    <td style="width: 100px;" class="border-bottom"> {{ $document_agreement_no }}</td>
                    <td style="width: 50px"></td>
                    <td colspan="3" align="center"><span style="text-decoration: underline;">AMOUNT S$</span></td>
                </tr>
                <tr>
                    <td style="width: 15%"></span></td>
                    <td style="width: 100px;"></td>
                    <td style="width: 50px"></td>
                    <td colspan="3">
                        <table style="width: 100%">
                            <tr>
                                <td>Contract Amount</td>
                                <td align="center" style="min-width: 100px;"><span></span></td>
                            </tr>
                            <tr>
                                <td>Variation Order</td>
                                <td align="center" class="border-bottom"><span></span></td>
                            </tr>
                            <tr>
                                <td>TOTAL</td>
                                <td align="center" class="border-bottom">$ <span></span></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>
