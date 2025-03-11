<style>
    .right-header {
        float: right;
    }

    .left-header {
        float: left;
    }
    .right-header th, .right-header td, .left-header th, .left-header td {
        padding: 8px 0;
    }
    .ft-24{
        font-size: 24px;
    }
</style>
<div style="clear: both;">
    <div>
        <table style="width: 100%;">
           <tr>
            <td align="right">
                <table>
                    <tr>
                        <td class="ft-24 ft-b">{{$type}}</td>
                    </tr>
                    <tr>
                        <td class="ft-12">{{ $quotationData['document_agreement_no'] }}</td>
                    </tr>
                </table>
            </td>
           </tr>
        </table>
    </div>
    <div>
        <div class="left-header">
            <table class="ft-12">
                <tr>
                    <th align="right">Customer</th>
                    <td style="width: 18px;"></td>
                    <td style="width: 200px;">
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
                    <th align="right">NRIC</th>
                    <td style="width: 18px;"></td>
                    <td>
                        @if (count($customers_array) > 1)
                            <span>
                                {{ implode(
                                    ' & ',
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
                <tr>
                    <th align="right">Mobile</th>
                    <td style="width: 18px;"></td>
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
                    <th align="right">Email</th>
                    <td style="width: 18px;"></td>
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
        <div class="right-header">
            <table class="ft-12">
                <tr>
                    <th align="right">Order Type</th>
                    <td style="width: 18px;"></td>
                    <td>New</td>
                </tr>
                <tr>
                    <th align="right">Date</th>
                    <td style="width: 18px;"></td>
                    @if (isset($quotationData['signed_date']))
                        <td>{{ $quotationData['signed_date'] }}</td>
                    @else
                        <td>{{ $quotationData['created_at'] }}</td>
                    @endif
                </tr>
                <tr>
                    <th align="right" style="vertical-align: top;">Billing Address</th>
                    <td style="width: 18px;"></td>
                    <td style="white-space: nowrap">
                        @if (isset($quotationData['properties']))
                            {{ $quotationData['properties']['block_num'] . ' ' . $quotationData['properties']['street_name'] }}<br />
                            {{ '#' . $quotationData['properties']['unit_num'] }}<br/>
                            {{ 'Singapore' . ' ' . $quotationData['properties']['postal_code'] }}<br/>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th align="right" style="vertical-align: top;">Job Site Address</th>
                    <td style="width: 18px;"></td>
                    <td style="white-space: nowrap">
                        @if (isset($quotationData['properties']))
                            {{ $quotationData['properties']['block_num'] . ' ' . $quotationData['properties']['street_name'] }}<br />
                            {{ '#' . $quotationData['properties']['unit_num'] }}<br/>
                            {{ 'Singapore' . ' ' . $quotationData['properties']['postal_code'] }}<br/>
                        @endif
                    </td>
                </tr>
    
            </table>
        </div>
    </div>
</div>
