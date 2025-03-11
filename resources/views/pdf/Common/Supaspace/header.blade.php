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
            padding-left: 50px;
            padding-right: 50px;
            /* padding-bottom: 100px; */
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
            padding-left: 50px;
            padding-right: 50px;
            padding-top: 15px;
        }

        .ft-b {
            font-weight: bold;
        }

        .ft-14 {
            font-size: 14px !important;
        }

        .underline {
            text-decoration: underline;
        }

        .text-center {
            text-align: center;
        }

        .logo {
            padding: 50px 50px 0 20px;
        }

        .center {
            width: 100%;
            margin: 0 auto;
            text-align: center;
        }

        .blue {
            color: blue;
        }
    </style>
</head>

<body onload="subst()">

    <div class="logo">
        <img src="{{ 'data:image/png;base64,' . $companies['company_logo'] }}" style="width: auto;height:140px">
    </div>
    <div class="header-section">
        @php
            $headerText = $header ?? '';
            $parts = $headerText ? explode('Dear Sir/Mdm,', $headerText) : [];

        @endphp
        <div class="center">
            <span class="ft-b ft-14 underline">{{ $doc_type }}</span>
        </div>
        <div style="padding-top:15px;font-size:12px;clear:both;" class="twp-padding">
            <table class="left-header">
                <tbody>
                    <tr>
                        <td>Prepared for</td>
                        <td>:</td>
                        <td></td>
                    </tr>

                    <tr>
                        <td style="vertical-align: top;padding-left: 10px;">Name</td>
                        <td style="vertical-align: top;">:</td>
                        @if ($enable_show_last_name_first == 'true')
                            <td>
                                @if (count($customers_array) > 1)
                                    <span class="ft-b">
                                        {{ implode(
                                            ' / ',
                                            array_map(function ($customer) {
                                                return $customer['name_prefix'] . ' ' . $customer['last_name'] . ' ' . $customer['first_name'];
                                            }, $customers_array),
                                        ) }}
                                    </span>
                                @else
                                    @foreach ($customers_array as $customer)
                                        <span
                                            class="ft-b">{{ $customer['name_prefix'] . ' ' . $customer['last_name'] . ' ' . $customer['first_name'] }}</span>
                                    @endforeach
                                @endif

                            </td>
                        @else
                            <td>
                                @if (count($customers_array) > 1)
                                    <span class="ft-b">
                                        {{ implode(
                                            ' / ',
                                            array_map(function ($customer) {
                                                return $customer['name_prefix'] . ' ' . $customer['first_name'] . ' ' . $customer['last_name'];
                                            }, $customers_array),
                                        ) }}
                                    </span>
                                @else
                                    @foreach ($customers_array as $customer)
                                        <span class="ft-b">{{ $customer['name_prefix'] . ' ' . $customer['first_name'] . ' ' . $customer['last_name'] }}</span>
                                    @endforeach
                                @endif

                            </td>
                        @endif

                    </tr>
                    <tr>
                        <td style="padding-left: 10px;">Site Address</td>
                        <td>:</td>
                        @if (isset($properties))
                            <td>{{ $properties['block_num'] . ' ' . $properties['street_name'] }}
                            </td>
                        @endif
                    </tr>
                    @if (isset($properties['unit_num']))
                        <tr>
                            <td></td>
                            <td></td>
                            @if (isset($properties))
                                <td>{{ $properties['unit_num'] ? '#' : '' }} {{ $properties['unit_num'] }}
                                </td>
                            @endif
                        </tr>
                    @endif
                    @if (isset($properties['postal_code']))
                        <tr>
                            <td></td>
                            <td></td>
                            @if (isset($properties))
                                <td>Singapore {{ $properties['postal_code'] }}</td>
                            @endif
                        </tr>
                    @endif
                    <tr>
                        <td style="vertical-align: top;padding-left: 10px;">H/P</td>
                        <td style="vertical-align: top;">:</td>
                        @if (isset($customers_array))
                            <td>
                                @if (count($customers_array) > 1)
                                    <span>{{ implode(' / ', array_column($customers_array, 'contact_no')) }}</span>
                                @else
                                    @foreach ($customers_array as $customer)
                                        <span>{{ $customer['contact_no'] }}</span>
                                    @endforeach
                                @endif
                            </td>
                        @endif
                    </tr>

                    <tr>
                        <td style="padding-left: 10px;">Email</td>
                        <td>:</td>
                        <td><span class="underline blue">{{ $customers['email'] }}</span></td>
                    </tr>

                </tbody>
            </table>
            <table class="right-header">
                <tbody>
                    @if ($companies['reg_no'])
                        <tr>
                            <th align="right">Biz Reg No.</th>
                            <td></td>
                            <td align="right" class="ft-b">
                                @if (isset($companies['gst_reg_no']) && $companies['gst_reg_no'] != '')
                                    {{ $companies['gst_reg_no'] }}
                                @else
                                    {{ $companies['reg_no'] }}
                                @endif
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <td align="right">Date</td>
                        <td>:</td>
                        @if (isset($signed_date))
                            <td align="right">{{ convertDate($signed_date) }}</td>
                        @else
                            <td align="right">{{ convertDate($created_at) }}</td>
                        @endif
                    </tr>
                    <tr>
                        <td align="right" style="vertical-align: top;">Agreement No.</td>
                        <td style="vertical-align: top;">:</td>
                        {{-- <td>{{ $project['agreement_no'] . '/QO' . $version_num }}</td> --}}
                        <td align="right">
                            <div class="ft-b"
                                style="width: 100px; overflow-wrap: break-word; word-break: break-word; white-space: normal;">
                                {{ $document_agreement_no }}
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" style="height: 10px;"></td>
                    </tr>
                    <tr>
                        <td align="right">Served By</td>
                        <td>:</td>
                        <td align="right" class="ft-b">{{ $signed_saleperson }}</td>
                    </tr>
                    <tr>
                        <td align="right">H/P</td>
                        <td>:</td>
                        <td align="right">{{ $signed_sale_ph }}</td>
                    </tr>
                    <tr>
                        <td align="right">Page/s</td>
                        <td></td>
                        <td align="right"><span class="page"></span> of <span class="topage"></span></td>
                    </tr>

                </tbody>
            </table>
        </div>
    </div>
    <div class="header-name">
        <div class="show-page"></div>
        {{-- <pre style="font-size:12px;">{{ $header }}</pre> --}}
    </div>
    <br/>
</body>

</html>
