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
        @endphp
        @if ($folder_name == 'Miracle')
            <div class="logo-section">
                <img src="{{ 'data:image/png;base64,' . $companies['company_logo'] }}" style="width: auto;height:150px">
                <div>{{ $companies['name'] }}</div>
            </div>
        @elseif ($folder_name == 'Henglai')
            <div class="logo-section">
                <img src="{{ 'data:image/png;base64,' . $companies['company_logo'] }}" style="width: auto;height:150px">
            </div>
        @elseif($folder_name == 'Molecule')
            <div>
                @if ($companies['name'] === 'Molecule Interior Design Pte Ltd')
                <div class="logo-section">
                    <img src="{{ public_path() . '/images/molecule_with_hdb.png' }}"
                        style="width: auto;height:150px">
                </div>
                @else
                <div class="left-header">
                    <img src="{{ 'data:image/png;base64,' . $companies['company_logo'] }}"
                        style="width: auto;height:150px">
                </div>
                <div class="right-header">
                    <img src="{{ public_path() . '/images/molecule_logos.png' }}" style="width: auto;height:150px">
                </div>
                @endif
                
            </div>
        @elseif($folder_name == 'AcCarpentry')
            <div class="logo-section">
                <img src="{{ 'data:image/png;base64,' . $companies['company_logo'] }}" style="width: 100%;height:200px">
            </div>
            <br/>
            <br/>
        @elseif($folder_name == 'Intereno')
            <div>
                <img src="{{ 'data:image/png;base64,' . $companies['company_logo'] }}" style="width: 60%;height:150px;padding-bottom: 80px;">
            </div>
        @else
            <div>
                <img src="{{ 'data:image/png;base64,' . $companies['company_logo'] }}" style="width: auto;height:100px">
            </div>
        @endif
        @if ($folder_name == 'AcCarpentry')
        <div style="padding-top:10px;font-size:12px;clear:both;padding-left: 30px; padding-right: 30px;"
        class="{{ $folder_name == 'Miracle' ? 'miracle-padding' : 'twp-padding' }}">
        @else
        <div style="padding-top:10px;font-size:12px;clear:both;"
        class="{{ $folder_name == 'Miracle' ? 'miracle-padding' : 'twp-padding' }}">
        @endif
            <table class="left-header">
                <tbody>
                    <tr>
                        <td align="right">OUR REF</td>
                        <td>:</td>
                        <td>{{ $project['agreement_no'] }}</td>
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
                        {{-- <td>{{ $project['agreement_no'] . '/QO' . $version_num }}</td> --}}
                        <td>{{ $document_agreement_no }}</td>
                    </tr>
                    <tr style="height: 10px"></tr>
                    @if ($customers['customer_type'] == 'commerical')
                        <tr>
                            <td align="right" style="vertical-align: top;">COMPANY NAME</td>
                            <td style="vertical-align: top;">:</td>
                            <td>
                                {{ $customers['company_name'] }}
                            </td>

                        </tr>
                    @endif
                    <tr>
                        <td align="right" style="vertical-align: top;">ATTN</td>
                        <td style="vertical-align: top;">:</td>
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
                        <td align="right">ADD</td>
                        <td>:</td>
                        @if (isset($properties))
                            <td>{{ $properties['block_num'] . ' ' . $properties['street_name'] }}
                            </td>
                        @endif
                    </tr>
                    @if (isset($properties['unit_num']))
                        <tr>
                            <td align="right"></td>
                            <td>:</td>
                            @if (isset($properties))
                                <td>{{ $properties['unit_num'] ? '#' : '' }} {{ $properties['unit_num'] }}
                                </td>
                            @endif
                        </tr>
                    @endif
                    <tr>
                        <td align="right">SPORE</td>
                        <td>:</td>
                        @if (isset($properties))
                            <td>{{ $properties['postal_code'] }}</td>
                        @endif
                    </tr>
                    <tr>
                        <td align="right" style="vertical-align: top;">MOBILE</td>
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
                    @if ($folder_name == 'Jream')
                        <tr>
                            <td colspan="3" class="ft-b">{{ $companies['name'] }}</td>
                        </tr>
                        @if ($companies['reg_no'])
                            <tr>
                                <td colspan="3"><span class="ft-b">ROC:</span> {{ $companies['reg_no'] }}</td>
                            </tr>
                        @elseif ($companies['gst_reg_no'])
                            <tr>
                                <td colspan="3"><span class="ft-b">ROC:</span> {{ $companies['gst_reg_no'] }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td colspan="3"><span class="ft-b">Office:</span> {{ $companies['main_office'] }}</td>
                        </tr>
                        <tr>
                            <td colspan="3"><span class="ft-b">Contact:</span> {{ $companies['tel'] }}</td>
                        </tr>
                        @if ($companies['hdb_license_no'])
                            <tr>
                                <td colspan="3"><span class="ft-b">HDB LICENSE NO:</span>
                                    {{ $companies['hdb_license_no'] }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td colspan="3"><span class="ft-b">PAGES:</span>
                                <span class="page"></span> of <span class="topage"></span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" style="height: 20px;"></td>
                        </tr>
                    @endif
                    @if ($folder_name != 'Paddry' && $folder_name != 'Jream')
                        @if ($companies['reg_no'])
                            <tr>
                                <td align="right">REG NO</td>
                                <td>:</td>
                                <td>{{ $companies['reg_no'] }}</td>
                            </tr>
                        @endif
                        @if ($companies['gst_reg_no'] && $folder_name != 'Intereno' && $folder_name != 'AcCarpentry')
                            <tr>
                                <td align="right">GST REG NO</td>
                                <td>:</td>
                                <td>{{ $companies['gst_reg_no'] }}</td>
                            </tr>
                        @endif
                        @if ($companies['hdb_license_no'] && $folder_name != 'Intereno' && $folder_name != 'AcCarpentry')
                            <tr>
                                <td align="right" style="white-space: nowrap;">HDB LICENSE NO</td>
                                <td>:</td>
                                <td>{{ $companies['hdb_license_no'] }}</td>
                            </tr>
                        @endif
                    @endif
                    @if ($folder_name != 'Jream')
                        <tr>
                            <td align="right">PAGES</td>
                            <td>:</td>
                            <td><span class="page"></span> of <span class="topage"></span></td>
                        </tr>
                    @endif
                    @if ($folder_name == 'Henglai' && $project_status == 'InProgress' && $doc_type == 'QUOTATION')
                    <tr>
                        <td align="right" colspan="3">
                            <h1 style="text-decoration: underline">INVOICE</h1>
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    @if ($folder_name == 'AcCarpentry')
    <div style="padding-bottom:30px;padding-top:5px;padding-left: 30px; padding-right:30px;" class="header-name">
        <div class="show-page"></div>
        <pre style="font-size:12px;">{{ $header }}</pre>
    </div>
    @else
    <div style="padding-bottom:30px;padding-top:5px;" class="header-name">
        <div class="show-page"></div>
        <pre style="font-size:12px;">{{ $header }}</pre>
    </div>
    @endif
</body>

</html>
