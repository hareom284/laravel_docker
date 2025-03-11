<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        * {
            font-family: sans-serif;
            font-size: 12px;
            /* Use the custom font, fallback to sans-serif if unavailable */
        }

        .full-table {
            width: 100%;
        }

        .border-bottom {
            border-bottom: 3px solid black;
        }

        .uppercase{
            text-transform: uppercase;
        }
    </style>
</head>

<body>

    <div>
        <div style="position: relative;padding-top: 50px;">
            <div style="position: absolute; top: 30px;left: 482px;"><span style="font-size: 14px; font-weight:bold;" class="uppercase">{{ $companies['name'] }}</span></div>
            <table class="full-table border-bottom">
                <tr>
                    <td style="width: 20%"></td>
                    <td rowspan="5" align="center" style="vertical-align: middle;">
                        <img src="{{ 'data:image/png;base64,' . $companies['company_logo'] }}" height="100" />
                        <div>Company Reg No: {{ $companies['reg_no'] }}</div>
                    </td>
                    <td style="vertical-align: top;">
                        <img src="{{ public_path() . '/images/location.png' }}" height="20" />
                    </td>
                    <td style="vertical-align: top;">
                        <div>100G Pasir Panjang Road </div>
                        <div>Interlocal Centre #01-18</div>
                        <div>Singapore 118523</div>
                    </td>
                    <td style="width: 20%"></td>
                </tr>
                <tr>
                    <td colspan="4" style="height: 5px;"></td>
                </tr>
                <tr>
                    <td style="width: 20%"></td>
                    <td style="vertical-align: top;">
                        <img src="{{ public_path() . '/images/phone.png' }}" height="20" />
                    </td>
                    <td>+65 {{ $companies['tel'] }}</td>
                    <td style="width: 20%"></td>
                </tr>
                <tr>
                    <td colspan="4" style="height: 5px;"></td>
                </tr>
                <tr>
                    <td style="width: 20%"></td>
                    <td style="vertical-align: top;">
                        <img src="{{ public_path() . '/images/mail.png' }}" height="20" />
                    </td>
                    <td>{{ $companies['email'] }}</td>
                    <td style="width: 20%"></td>
                </tr>
            </table>
            <table class="full-table">
                <tr>
                    <th align="left" style="width: 13%;">Quotation No:</th>
                    <th align="left" style="width: 15%">{{ $document_agreement_no }}</th>
                    <th align="left" style="width: 10%;">Project No:</th>
                    <th align="left" style="width: 11%;">{{ $project['agreement_no'] }}</th>
                    <th align="right">Date :
                        @if (isset($signed_date))
                            <span>{{ $signed_date }}</span>
                        @else
                            <span>{{ $created_at }}</span>
                        @endif
                    </th>
                </tr>
            </table>
            <table class="full-table">
                <tr>
                    <td colspan="4" style="height: 10px;"></td>
                </tr>
                <tr>
                    <td style="width: 5%">To:</td>
                    <td colspan="3">
                        <span>
                            @if (isset($properties))
                                {{ $properties['block_num'] . ' ' . $properties['street_name'] . ' ' . '#' . $properties['unit_num'] . ' ' }}
                                {{ $properties['postal_code'] ? 'S(' . $properties['postal_code'] . ')' : '' }}
                            @endif
                        </span>
                    </td>
                </tr>
                <tr>
                    <td style="width: 5%">Attn:</td>
                    <td colspan="3">
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
                </tr>
                <tr>
                    <td colspan="2" style="width: 25%;">Mobile No.
                        @if (count($customers_array) > 1)
                            <span>{{ implode(' / ', array_column($customers_array, 'contact_no')) }}</span>
                        @else
                            @foreach ($customers_array as $customer)
                                <span>{{ $customer['contact_no'] }}</span>
                            @endforeach
                        @endif
                    </td>
                    <td style="width: 5%;">Email:</td>
                    <td>
                        @if (count($customers_array) > 1)
                            <span>{{ implode(' / ', array_column($customers_array, 'email')) }}</span>
                        @else
                            @foreach ($customers_array as $customer)
                                <span>{{ $customer['email'] }}</span>
                            @endforeach
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="width: 5%">Nric:</td>
                    <td colspan="3">
                        @if (count($customers_array) > 1)
                            <span>
                                {{ implode(
                                    ' / ',
                                    array_map(function ($customer) {
                                        return $customer['customers']['nric'];
                                    }, $customers_array),
                                ) }}
                            </span>
                        @else
                            @foreach ($customers_array as $customer)
                                <span>{{ $customer['customers']['nric'] }}</span>
                            @endforeach
                        @endif
                    </td>
                </tr>
            </table>
            <table class="full-table">
                <tr>
                    <th colspan="3" style="height: 10px;"></th>
                </tr>
                <tr>
                    <th align="left">Re:</th>
                    <th style="width: 3.5%;"></th>
                    <th align="left">Renovation Works</th>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>
