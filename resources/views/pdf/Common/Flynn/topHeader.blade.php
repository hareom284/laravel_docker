<style>
    .right-header {
        float: right;
    }

    .left-header {
        float: left;
    }
</style>
<div style="clear: both;">
    <div class="left-header">
        <table>
            <tr>
                <th align="left">Attn:</th>
                <td>
                    @if (count($customers_array) > 1)
                        <span>
                            {{ implode(
                                ' & ',
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
                <th align="left">HP:</th>
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
                <th align="left">Address:</th>
                <td>
                    @if (isset($quotationData['properties']))
                        {{ $quotationData['properties']['block_num'] . ' ' . $quotationData['properties']['street_name'] . ' ' . '#' . $quotationData['properties']['unit_num'] }}
                    @endif
                </td>
            </tr>
            <tr>
                <th align="left">E-mail</th>
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
        </table>
    </div>
    <div class="right-header" style="width: 250px;">
        <table border="1" style="border-collapse: collapse; border-color: black; width: 100%;">
            <tr>
                <th>Date:</th>
                @if (isset($quotationData['signed_date']))
                    <td align="center">{{ $quotationData['signed_date'] }}</td>
                @else
                    <td align="center">{{ $quotationData['created_at'] }}</td>
                @endif
            </tr>
            <tr>
                <th>Ref:</th>
                <td align="center">{{ $quotationData['document_agreement_no'] }}</td>
            </tr>
            <tr>
                <th colspan="2">Project Designer:</th>
            </tr>
            <tr>
                <td colspan="2" align="center">{{ $quotationData['signed_saleperson'] }}</td>
            </tr>
            <tr>
                <td colspan="2" align="center">{{ $quotationData['signed_sale_ph'] }}</td>
            </tr>
        </table>
    </div>
</div>
