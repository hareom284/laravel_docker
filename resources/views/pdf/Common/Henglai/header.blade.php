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
            padding-bottom: 140px;
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
    </style>
</head>

<body onload="subst()">

    <div class="header-section">
        @php
            $headerText = $header ?? '';
            $parts = $headerText ? explode('Dear Sir/Mdm,', $headerText) : [];

            function convertDate($dateString)
            {
                $originalFormat = 'd/m/Y';
                $date = DateTime::createFromFormat($originalFormat, $dateString);
                if ($date) {
                    $formattedDate = $date->format('j M Y');
                    return $formattedDate;
                } else {
                    return '';
                }
            }
        @endphp
        <div class="logo-section">
            <img src="{{ 'data:image/png;base64,' . $companies['company_logo'] }}" style="width: auto;height:150px">
        </div>

        <table class="left-header">
            <tbody>
                <tr>
                    <td colspan="3">Customer Information:</td>
                </tr>
                <tr>
                    <td style="vertical-align: top;">Name:</td>
                    <td style="vertical-align: top;width: 10px;"></td>
                    @if ($enable_show_last_name_first == 'true')
                        <td>
                            @if (count($customers_array) > 1)
                                <span>
                                    {{ implode(
                                        ' / ',
                                        array_map(function ($customer) {
                                            return $customer['name_prefix'] . ' ' . $customer['last_name'] . ' ' . $customer['first_name'];
                                        }, $customers_array),
                                    ) }}
                                </span>
                            @else
                                @foreach ($customers_array as $customer)
                                    <span>{{ $customer['name_prefix'] . ' ' . $customer['last_name'] . ' ' . $customer['first_name'] }}</span>
                                @endforeach
                            @endif

                        </td>
                    @else
                        <td>
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
                    @endif
                </tr>
                <tr>
                    <td>Address:</td>
                    <td></td>
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
                            <td>
                                {{ $properties['unit_num'] ? '#' : '' }}
                                {{ $properties['unit_num'] }}
                                {{ $properties['postal_code'] ? 'S(' . $properties['postal_code'] . ')' : '' }}
                            </td>
                        @endif
                    </tr>
                @endif
                <tr>
                    <td style="vertical-align: top;">H/P:</td>
                    <td style="vertical-align: top;"></td>
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
                    <td colspan="3">Order Information:</td>
                </tr>
            </tbody>
        </table>
        <table class="right-header">
            <tbody>
                <tr>
                    <td>No:</td>
                    <td></td>
                    <td>{{ $document_agreement_no }}</td>
                </tr>
                <tr>
                    <td colspan="3" style="height: 20px;"></td>
                </tr>
                <tr>
                    <td>Date:</td>
                    <td></td>
                    @if (isset($signed_date))
                        <td>{{ convertDate($signed_date) }}</td>
                    @else
                        <td>{{ convertDate($created_at) }}</td>
                    @endif
                </tr>
                <tr>
                    <td colspan="3" style="height: 20px;"></td>
                </tr>
            </tbody>
        </table>
    </div>
    </div>

    <div style="padding-bottom:30px;padding-top:5px;" class="header-name">
        <div class="show-page"></div>
        <pre style="font-size:12px;">{{ $header }}</pre>
    </div>
</body>

</html>
