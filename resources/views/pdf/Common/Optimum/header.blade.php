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
    <div style="padding-bottom: 40px;">
        <div style="padding-top:10px;font-size:12px;">
            <table style="border: 1px solid black;">
                <tbody>
                    <tr>
                        <td style="width: 200px;"><img style="width: 200px; height: 200px;" src="{{ public_path() . '/images/optimum_logo.jpeg' }}" /></td>
                        <td style="width: 350px;"></td>
                        <td style="font-size: 30px; font-weight: bold;">Quotation</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="quotation-header">
            <table style="width: 100%;">
                <tbody>
                    <tr>
                        <td colspan="4" style="height: 15px;"></td>
                    </tr>
                    <tr>
                        <td>
                            <div style="float: left;">Client's Full NRIC Name: </div>
                            <div style="width:300px;">
                                <div style="padding-left: 15px;">
                                @foreach ($customers_array as $customer)
                                    <span>{{ $customer['name_prefix'] . ' ' . $customer['last_name'] . ' ' . $customer['first_name'] }}</span>
                                @endforeach
                                </div>
                            </div>
                        </td>
                        <td align="left">
                            <div>Date :</div>
                        </td>
                        <td>
                            <div style="background-color: #eee; padding: 3px;">
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
                            <div style="float: left;">Client's Contact Number: </div>
                            <div>
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
                        <td align="left">
                            <div>Project Ref: </div>
                        </td>
                        <td>
                            <div style="background-color: #eee; padding: 3px;">
                                {{ $document_agreement_no }}
                            </div>
                        </td>
                        <td style="width: 60px"></td>
                    </tr>
                    <tr>
                        <td colspan="4" style="height: 5px;"></td>
                    </tr>
                    <tr>
                        <td>
                            <div style="float: left;">Client's Email: </div>
                            <div>
                                @foreach ($customers_array as $customer)
                                    <div style="padding-left: 15px;">
                                        {{ $customer['email'] }}
                                    </div>
                                @endforeach
                            </div>
                        </td>
                        <td align="left">
                            <div>Designer:</div>
                        </td>
                        <td>
                            <div style="background-color: #eee; padding: 3px;">
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
                            <div style="float: left;">Renovating Address: </div>
                            <div>
                                <span style="padding-left: 10px;">
                                    @if (isset($properties))
                                        {{ $properties['block_num'] . ' ' . $properties['street_name'] . ' ' . '#' . $properties['unit_num'] }}
                                    @endif
                                </span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
