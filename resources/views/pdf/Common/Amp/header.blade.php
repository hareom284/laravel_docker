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
            font-size: 20px !important;
        }

        .ft-bold {
            font-weight: bold;
            color: white
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
            background: #345f85;
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
            <img src="{{ public_path() . '/images/amp_logo.png' }}" />
        </div>
        <div class="quotation-header">
            <table style="width: 100%;">
                <tbody>
                    <tr>
                        <td colspan="4" class="bg-gray-center" align="center">
                            @if ($doc_type == 'QUOTATION' && $already_sign && isset($customers))
                                <span style="padding: 8px;" class="ft-bold">Invoice</span>
                            @elseif($doc_type == 'QUOTATION')
                                <span style="padding: 8px;" class="ft-bold">Quotation</span>
                            @elseif($doc_type == 'VARIATIONORDER')
                                <span style="padding: 8px;" class="ft-bold">Variation Order</span>
                            @elseif($doc_type == 'CANCELLATION')
                                <span style="padding: 8px;" class="ft-bold">Cancellation</span>
                            @elseif($doc_type == 'INVOICE')
                                <span style="padding: 8px;" class="ft-bold">Invoice</span>
                            @else
                                <span style="padding: 8px;" class="ft-bold">FOC</span>
                            @endif

                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" style="height: 15px;"></td>
                    </tr>
                    <tr>
                        <td>
                            <div style="float: left;">Subject</div>
                            <div style="padding-left:100px;width:300px;">
                                <div style="float: left;">: </div>
                                <div style="padding-left: 15px;">
                                    INVOICE For Interior Design and fitting out For Project Site As Stated Below
                                </div>
                            </div>
                        </td>
                        <td align="right">
                            @if ($doc_type == 'QUOTATION' && $already_sign && isset($customers))
                                {{-- <div>QUOTATION :</div> --}}
                                <div>INVOICE :</div>
                            @else
                                <div>QUOTATION :</div>
                                {{-- <div>INVOICE :</div> --}}
                            @endif
                        </td>
                        <td>
                            <div>
                                {{ $project['invoice_no'] }}
                            </div>
                        </td>
                        <td style="width: 60px"></td>
                    </tr>
                    <tr>
                        <td colspan="4" style="height: 5px;"></td>
                    </tr>
                    <tr>
                        <td>
                            <div style="float: left;">Attn</div>
                            <div style="padding-left:100px;width:300px;">
                                <div style="float: left;">: </div>
                                @if (count($customers_array) > 1)
                                    <span style="padding-left: 15px;">
                                        {{ implode(
                                            ' & ',
                                            array_map(function ($customer) {
                                                return $customer['name_prefix'] . ' ' . $customer['first_name'] . ' ' . $customer['last_name'];
                                            }, $customers_array),
                                        ) }}
                                    </span>
                                @else
                                    @foreach ($customers_array as $customer)
                                        <span
                                            style="padding-left: 15px;">{{ $customer['name_prefix'] . ' ' . $customer['first_name'] . ' ' . $customer['last_name'] }}</span>
                                    @endforeach
                                @endif
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
                        <td style="width: 60px"></td>
                    </tr>
                    <tr>
                        <td colspan="4" style="height: 5px;"></td>
                    </tr>
                    <tr>
                        <td>
                            <div style="float: left;">Address</div>
                            <div style="padding-left:100px;">:
                                <span style="padding-left: 10px;">
                                    @if (isset($properties))
                                        {{ $properties['block_num'] . ' ' . $properties['street_name'] . ' ' . '#' . $properties['unit_num'] }}
                                    @endif
                                </span>
                            </div>
                        </td>
                        <td align="right">
                            <div>From :</div>
                        </td>
                        <td>
                            <div>
                                {{ $signed_saleperson }}
                            </div>
                        </td>
                        <td style="width: 60px"></td>
                    </tr>
                    <tr>
                        <td colspan="4" style="height: 5px;"></td>
                    </tr>
                    <tr>
                        <td>
                            <div style="float: left;">Contact Number</div>
                            <div style="padding-left:100px;">:
                                @if (count($customers_array) > 1)
                                    <span
                                        style="padding-left: 10px;">{{ implode(' / ', array_column($customers_array, 'contact_no')) }}</span>
                                @else
                                    @foreach ($customers_array as $customer)
                                        <span style="padding-left: 10px;">{{ $customer['contact_no'] }}</span>
                                    @endforeach
                                @endif
                            </div>
                        </td>
                        <td align="right">
                            <div>H/P :</div>
                        </td>
                        <td>
                            <div>{{ $signed_sale_ph }}</div>
                        </td>
                        <td style="width: 60px"></td>

                    </tr>
                    <tr>
                        <td colspan="4" style="height: 5px;"></td>
                    </tr>
                    <tr>
                        <td>
                            <div style="float: left;">Email</div>
                            <div style="padding-left:100px;">
                                <div style="float: left;">: </div>
                                @foreach ($customers_array as $customer)
                                    <div style="padding-left: 15px;">
                                        {{ $customer['email'] }}
                                    </div>
                                @endforeach
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" style="height: 5px;"></td>
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
                        <tr>
                            <td colspan="4" style="height: 5px;"></td>
                        </tr>
                    @endif
                    @if (isset($header))
                        <tr>
                            <td colspan="4">
                                {{ $header }}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" style="height: 5px;"></td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
