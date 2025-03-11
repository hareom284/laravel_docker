<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link type="text/css" media="all" rel="stylesheet" href="{{ public_path('css/font.css') }}">

    <style>
        * {
            font-family: sans-serif;
            /* Use the custom font, fallback to sans-serif if unavailable */
        }

        .text-3xl {
            font-size: 36px !important;
        }

        .text-2xl {
            font-size: 30px !important;
        }

        .text-xl {
            font-size: 16px !important;
        }

        .ft-bold {
            font-weight: bold;
        }

        .right-header {
            float: right;
        }

        .left-header {
            float: left;
            width: 50%;
        }

        .quotation-header: {
            padding-top: 30px;
        }

        .text-md {
            font-size: 16px !important;
        }

        th,
        td {
            border: 1px solid transparent;
            /* padding: 4px; */
        }

        table {
            border-collapse: collapse;
            font-size: 12px;
        }

        .border-b {
            border-bottom: 1px solid black;
            /* padding: 5px 0; */
        }

        .py-10 {
            padding: 10px 0;
        }

        .bg-gray-center {
            background: #d9d9d9;

        }
    </style>
</head>

<body>
    @php
        function convertDate($dateString)
        {
            $originalFormat = 'd/m/Y';
            $date = DateTime::createFromFormat($originalFormat, $dateString);
            if ($date) {
                $formattedDate = $date->format('d M Y');
                return $formattedDate;
            } else {
                return '';
            }
        }

        function getDateTwoWeeksLaterFormatted($dateString, $inputFormat = 'd/m/Y', $outputFormat = 'd M Y')
        {
            $date = DateTime::createFromFormat($inputFormat, $dateString);
            if (!$date || $date->format($inputFormat) !== $dateString) {
                return '';
            }
            $date->add(new DateInterval('P2W'));
            return $date->format($outputFormat);
        }
    @endphp
    <div>
        <div style="padding-top:10px;font-size:12px;">
            <table class="left-header">
                <tbody>
                    <tr>
                        <td>
                            <span class="text-xl ft-bold">{{ $companies['name'] }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-xl">U.E.N: {{ $companies['reg_no'] }}</td>
                    </tr>
                    <tr>
                        <td style="height: 15px;"></td>
                    </tr>
                    <tr>
                        <td style="height: 10px;" class="text-xl">
                            @if ($companies['main_office'])
                                <div>{{ $companies['main_office'] }}</div>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="height: 15px;"></td>

                    </tr>
                    <tr>
                        <td class="text-xl">Tel: +65{{ $companies['tel'] }}</td>
                    </tr>
                    <tr>

                        <td class="text-xl">Website: www.luxcraft.sg</td>
                    </tr>
                    <tr>

                        <td class="text-xl">Email: {{ $companies['email'] }}</td>
                    </tr>
                    <tr>
                        <td style="height: 15px;"></td>

                    </tr>
                </tbody>
            </table>
            <table class="right-header">
                <tbody>
                    <tr>
                        <td align="right">
                            {{-- <img src="{{ public_path() . '/images/metis_logo.png' }}" height="300" /> --}}
                            <img src="{{ 'data:image/png;base64,' . $companies['company_logo'] }}" height="200">
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="quotation-header">
            <table style="width: 100%;">
                <tbody>
                    <tr>
                        <td colspan="4" class="bg-gray-center" align="center">
                            @if ($doc_type == 'QUOTATION')
                                <span style="padding: 8px;" class="ft-bold">Quotation</span>
                            @elseif($doc_type == 'VARIATIONORDER')
                                <span style="padding: 8px;" class="ft-bold">Variation Order</span>
                            @elseif($doc_type == 'CANCELLATION')
                                <span style="padding: 8px;" class="ft-bold">Cancellation</span>
                            @else
                                <span style="padding: 8px;" class="ft-bold">FOC</span>
                            @endif

                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" style="height: 15px;"></td>
                    </tr>
                    <tr>
                        <td>
                            <div style="float: left;">Attention</div>
                            <div style="padding-left:100px;width:400px;">
                                <div style="float: left;">: </div>
                                @foreach ($customers_array as $customer)
                                    <div style="padding-left: 18px;">
                                        {{ $customer['name_prefix'] . ' ' . $customer['first_name'] . ' ' . $customer['last_name'] }}
                                    </div>
                                @endforeach
                            </div>
                        </td>
                        <td align="right">
                            <div>Date :</div>
                        </td>
                        <td>
                            <div>
                                @if (isset($signed_date))
                                    <span>{{ convertDate($signed_date) }}</span>
                                @else
                                    <span>{{ convertDate($created_at) }}</span>
                                @endif
                            </div>
                        </td>

                    </tr>
                    <tr>
                        <td>
                            <div style="float: left;">Contact Number</div>
                            <div style="padding-left:100px;">:
                                <span style="padding-left: 10px;">
                                    {{ '+65' . $customers['contact_no'] }}
                                </span>
                            </div>
                        </td>
                        <td align="right">
                            <div>Quotation Reference :</div>
                        </td>

                        <td>
                            <div>{{ $project['agreement_no'] }}</div>
                        </td>


                    </tr>
                    <tr>
                        <td>
                            <div style="float: left;">Project Address</div>
                            <div style="padding-left:100px;">:
                                <span style="padding-left: 10px;">
                                    @if (isset($properties))
                                        {{ $properties['block_num'] . ' ' . $properties['street_name'] . ' ' . '#' . $properties['unit_num'] }}
                                    @endif
                                </span>
                            </div>
                        </td>
                        <td align="right">
                            <div>Invoice Number :</div>
                        </td>
                        <td>
                            <div>
                                {{ $project['invoice_no'] }}
                            </div>
                        </td>

                    </tr>
                    <tr>
                        <td>
                            <div style="float: left;">Email Address</div>
                            <div style="padding-left:100px;">:
                                @if (isset($properties))
                                    <span style="padding-left: 10px;">{{ $customers['email'] }}</span>
                                @endif
                            </div>
                        </td>
                        <td align="right">
                            <div>Designer :</div>
                        </td>
                        <td>
                            <div>
                                <div>
                                    {{ $signed_saleperson }}
                                </div>
                            </div>
                        </td>

                    </tr>
                    @if ($customers['customer_type'] == 'commerical')
                        <td>
                            <div style="float: left;">Company Name</div>
                            <div style="padding-left:100px;">:
                                @if (isset($properties))
                                    <span style="padding-left: 10px;">{{ $customers['company_name'] }}</span>
                                @endif
                            </div>
                        </td>
                    @endif


                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
