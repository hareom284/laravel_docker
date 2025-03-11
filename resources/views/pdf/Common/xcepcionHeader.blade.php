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
            font-family: 'HelveticaNeue-Thin' !important;
        }

        .text-3xl {
            font-size: 36px !important;
        }

        .text-2xl {
            font-size: 30px !important;
        }

        .text-xl {
            font-size: 22px !important;
        }

        .ft-bold {
            font-weight: bold;
        }

        .right-header {
            float: right;
        }

        .left-header {
            float: left;
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
            padding: 4px;
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

        .text-light-gray {
            color: #c3c1c2;
        }

        .thin-text {
            font-family: 'HelveticaNeue-Thin' !important;
        }

        .ls-md {
            letter-spacing: 1.5px;
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
                            <span class="text-2xl">xcepcion studio</span>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                    </tr>
                    <tr class="thin-text ft-bold">
                        <td>uen/gst:
                            @if (isset($companies['gst_reg_no']) && $companies['gst_reg_no'] != '')
                                {{ $companies['gst_reg_no'] }}
                            @else
                                {{ $companies['reg_no'] }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="height: 10px;"></td>
                    </tr>
                </tbody>
            </table>
            <table class="right-header">
                <tbody>
                    <tr>
                        <td align="right" class="thin-text ft-bold">{{ $companies['name'] }}</td>
                    </tr>
                    <tr style="height: 30px">
                        <td></td>
                    </tr>
                    <tr>
                        <td align="right" class="thin-text ft-bold">
                            <div>
                                e:{{ $companies['email'] }}
                            </div>
                            <div>
                                m: {{ $companies['tel'] }}
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="quotation-header">
            <table style="width: 100%;">
                <tbody>
                    <tr>
                        <td colspan="2" class="border-b" style="height: 40px">
                            @if ($doc_type == 'QUOTATION')
                                <span style="padding: 8px 8px 20px 0;"
                                    class="ft-bold text-md thin-text ls-md">QUOTATION</span>
                            @elseif($doc_type == 'VARIATIONORDER')
                                <span style="padding: 8px 8px 20px 0;" class="ft-bold text-md thin-text ">VARIATION
                                    ORDER</span>
                            @elseif($doc_type == 'CANCELLATION')
                                <span style="padding: 8px 8px 20px 0;"
                                    class="ft-bold text-md thin-text ls-md">CANCELLATION</span>
                            @else
                                <span style="padding: 8px 8px 20px 0;"
                                    class="ft-bold text-md thin-text ls-md">FOC</span>
                            @endif

                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                    </tr>
                    <tr>
                        <td>
                            <div style="float: left;">Customer</div>
                            <div style="padding-left:140px;width:300px;">
                                <div style="float: left;">: </div>
                                @foreach ($customers_array as $customer)
                                    <div style="padding-left: 5px;">
                                        {{ $customer['name_prefix'] . ' ' . $customer['first_name'] . ' ' . $customer['last_name'] }}
                                    </div>
                                @endforeach
                            </div>
                        </td>
                        <td>
                            <div style="float: left;padding-left:150px;">Reference No.</div>
                            <div style="padding-left:250px;"> <span style="padding:0 0 0 20px;">:</span>
                                {{ $project['agreement_no'] }}</div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div style="float: left;">Address</div>
                            <div style="padding-left:140px;">:
                                @if (isset($properties))
                                    {{ $properties['block_num'] . ' ' . $properties['street_name'] }}
                                @endif
                            </div>
                        </td>
                        <td>
                            <div style="float: left;padding-left:150px;">Quotation Date</div>
                            <div style="padding-left:250px;"><span style="padding:0 0 0 20px;">:</span>
                                @if (isset($signed_date))
                                    <span>{{ convertDate($signed_date) }}</span>
                                @else
                                    <span>{{ convertDate($created_at) }}</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <div style="float: left;padding-left:150px;">Quotation Expiry</div>
                            <div style="padding-left:250px;"><span style="padding:0 0 0 20px;">:</span>
                                {{ getDateTwoWeeksLaterFormatted($created_at) }}</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="height: 10px;"></td>
                    </tr>
                    <tr>
                        <td>
                            <div style="float: left;">Contact</div>
                            <div style="padding-left:140px;">:
                                @if (isset($properties))
                                    <span>{{ $customers['contact_no'] }}</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div style="float: left;padding-left:150px;">Customer Code</div>
                            <div style="padding-left:250px;">
                                <div style="float: left;"><span style="padding:0 4px 0 20px;">:</span></div>
                                @foreach ($customer_ids as $customer_id)
                                    <div style="padding-left: 6px;">
                                        {{ 'XC-' . $customer_id['id'] }}
                                    </div>
                                @endforeach
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div style="float: left;">Email</div>
                            <div style="padding-left:140px;">: {{ $customers['email'] }}</div>
                        </td>
                        <td>
                            <div style="float: left;padding-left:150px;">Salesperson</div>
                            <div style="padding-left:250px;"><span style="padding:0 0 0 20px;">:</span>
                                XC-{{ $saleperson_id }}</div>
                        </td>
                    </tr>

                    @if ($customers['customer_type'] == 'commerical')
                        <tr>
                            <td>
                                <div style="float: left;">Company Name</div>
                                <div style="padding-left:140px;">: {{ $customers['company_name'] }}</div>
                            </td>
                            <td></td>
                        </tr>
                    @endif
                    <tr>
                        <td colspan="2" class="border-b"></td>
                    </tr>

                </tbody>
            </table>
            <table style="width:100%;">
                <thead>
                    <tr>
                        <td class="border-b py-10" style="width: 58px;"><span>S/N</span></td>
                        <td class="border-b py-10" style="width: 108px;"><span>ITEM CODE</span></td>
                        <td class="border-b py-10" style="width: 308px;"><span>ITEM DESCRIPTION</span></td>
                        <td class="border-b py-10" align="center" style="width: 108px;"></td>
                        <td class="border-b py-10" align="center" style="width: 58px;">QTY</td>
                        <td class="border-b py-10" align="right" style="width: 108px;"></td>
                        <td class="border-b py-10" align="right">Sub-Total (S$)</td>
                    </tr>
                </thead>

            </table>
        </div>
    </div>
</body>

</html>
