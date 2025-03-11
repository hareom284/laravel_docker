<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<style>

</style>

<body>
    <table style="width: 100%;" class="small-text">
        <tr>
            <td style="width: 70%;">
                <div>
                    <table style="width: 100%;">
                        <tr>
                            <td>Client: @if (count($customers_array) > 1)
                                    <span>
                                        {{ implode(
                                            ' & ',
                                            array_map(function ($customer) {
                                                return $customer['first_name'] . ' ' . $customer['last_name'];
                                            }, $customers_array),
                                        ) }}
                                    </span>
                                @else
                                    @foreach ($customers_array as $customer)
                                        <span>{{ $customer['first_name'] . ' ' . $customer['last_name'] }}</span>
                                    @endforeach
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>Address:
                                {{ $quotationData['properties']['block_num'] . ' ' . $quotationData['properties']['street_name'] . ' ' . '#' . $quotationData['properties']['unit_num'] }}
                            </td>
                        </tr>
                        <tr>
                            <td>Contact: @if (count($customers_array) > 1)
                                    <span>{{ implode(' / ', array_column($customers_array, 'contact_no')) }}</span>
                                @else
                                    @foreach ($customers_array as $customer)
                                        <span>{{ $customer['contact_no'] }}</span>
                                    @endforeach
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>Email Address: @if (count($customers_array) > 1)
                                    <span>{{ implode(' / ', array_column($customers_array, 'email')) }}</span>
                                @else
                                    @foreach ($customers_array as $customer)
                                        <span>{{ $customer['email'] }}</span>
                                    @endforeach
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
            <td style="width: 30%;">
                <div>
                    <table style="width: 100%;">
                        <tr>
                            <td style="width: 30%">PRJ No:</td>
                            <td style="width: 70%" align="right">{{ $quotationData['document_agreement_no'] }}</td>
                        </tr>
                        <tr>
                            <td style="width: 30%">Sales:</td>
                            <td style="width: 70%" align="right">{{ $quotationData['signed_saleperson'] }}</td>
                        </tr>
                        <tr>
                            <td colspan="2" style="height: 20px;"></td>
                        </tr>
                        <tr>
                            <td style="width: 30%">Date:</td>
                            @if (isset($quotationData['signed_date']))
                                <td style="width: 70%" align="right">{{ $quotationData['signed_date'] }}</td>
                            @else
                                <td style="width: 70%" align="right">{{ $quotationData['created_at'] }}</td>
                            @endif
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <span>
                    {{ $quotationData['header'] }}
                </span>
            </td>
        </tr>
    </table>
</body>

</html>
