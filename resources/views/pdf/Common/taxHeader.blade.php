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
            padding-bottom: 170px;
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
    </style>
</head>

<body onload="subst()">
    <div class="header-section">

        @if ($folder_name == 'Miracle')
            <div class="logo-section">
                <img src="{{ 'data:image/png;base64,' . $companies['company_logo'] }}" style="width: 200px;height:200px">
                <div>{{ $companies['name'] }}</div>
            </div>
        @else
            <div>
                <img src="{{ 'data:image/png;base64,' . $companies['company_logo'] }}" style="width: 100px;height:100px">
            </div>
        @endif

        <div style="padding-top:10px;font-size:12px;">
            <table class="left-header">
                <tbody>
                    <tr>
                        <td align="right">OUR REF</td>
                        <td>:</td>
                        <td>{{ $our_ref }}</td>
                    </tr>
                    <tr>
                        <td align="right">DATE</td>
                        <td>:</td>
                        @if (isset($signed_date))
                            <td>{{ $signed_date }}</td>
                        @else
                            <td>{{ $created_at }}</td>
                        @endif
                    </tr>
                    <tr>
                        <td align="right">AGR</td>
                        <td>:</td>
                        <td>{{ $agr }}</td>
                    </tr>
                    <tr>
                        <td align="right" style="vertical-align: top;">ATTN</td>
                        <td style="vertical-align: top;">:</td>
                        @if ($enable_show_last_name_first == 'true')
                            <td>
                                @foreach ($customers_array as $customer)
                                    <div>
                                        {{ $customer['name_prefix'] . ' ' . $customer['last_name'] . ' ' . $customer['first_name'] }}
                                    </div>
                                @endforeach
                            </td>
                        @else
                            <td>
                                @foreach ($customers_array as $customer)
                                    <div>
                                        {{ $customer['name_prefix'] . ' ' . $customer['first_name'] . ' ' . $customer['last_name'] }}
                                    </div>
                                @endforeach
                            </td>
                        @endif
                    </tr>
                    <tr>
                        <td align="right">ADD</td>
                        <td>:</td>
                        @if (isset($properties))
                            <td>{{ $properties['street_name'] }}
                            </td>
                        @endif
                    </tr>
                    <tr>
                        <td align="right">SPORE</td>
                        <td>:</td>
                        @if (isset($properties))
                            <td>{{ $properties['postal_code'] }}</td>
                        @endif
                    </tr>
                    <tr>
                        <td align="right">MOBILE</td>
                        <td>:</td>
                        @if (isset($properties))
                            <td>{{ $customers['contact_no'] }}</td>
                        @endif
                    </tr>
                    @if ($folder_name == 'Miracle')
                        <tr>
                            <td align="right">EMAIL</td>
                            <td>:</td>

                            <td>{{ $customers['email'] }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>
            <table class="right-header">
                <tbody>
                    <tr>
                        <td align="right">REG NO</td>
                        <td>:</td>
                        <td>{{ $companies['reg_no'] }}</td>
                    </tr>
                    <tr>
                        <td align="right">GST REG NO</td>
                        <td>:</td>
                        <td>{{ $companies['gst_reg_no'] }}</td>
                    </tr>
                    <tr>
                        <td align="right" style="white-space: nowrap;">HDB LICENSE NO</td>
                        <td>:</td>
                        <td>{{ $companies['hdb_license_no'] }}</td>
                    </tr>

                    <tr>
                        <td align="right">PAGES</td>
                        <td>:</td>
                        <td><span class="page"></span> of <span class="topage"></span></td>
                    </tr>
                </tbody>
            </table>

        </div>

    </div>
</body>

</html>
