<style>
    .right-header {
        float: right;
    }

    .left-header {
        float: left;
    }
</style>
<div>
    <div style="clear: both">
        <p style="font-size: 28px;"><span class="ft-b underline">{{ $type }}</span></p>
    </div>
    <div style="clear: both;">
        <div class="left-header">
            <table>
                <tr>
                    <td><span class="ft-b underline">Client</span></td>
                </tr>
                <tr>
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
                    <td>
                        @if (isset($quotationData['properties']))
                            <span>{{ $quotationData['properties']['block_num'] . ' ' . $quotationData['properties']['street_name'] . ' ' . '#' . $quotationData['properties']['unit_num'] }}</span><br />
                            <span>Singapore {{ $quotationData['properties']['postal_code'] }}</span>
                        @endif
                    </td>
                </tr>
            </table>
            <br />
        </div>
        <div class="right-header" style="width: 270px;">
            <table>
                <tr>
                    <td style="min-width: 100px;" align="right">Quotation No :</td>
                    <td style="min-width: 100px;">{{ $quotationData['document_agreement_no'] }}</td>
                </tr>
                <tr>
                    <td style="height: 10px;"></td>
                </tr>
                <tr>
                    <td align="right">Date :</td>
                    @if (isset($quotationData['signed_date']))
                        <td align="right">{{ $quotationData['signed_date'] }}</td>
                    @else
                        <td align="right">{{ $quotationData['created_at'] }}</td>
                    @endif
                </tr>
            </table>
        </div>
    </div>
    <div style="clear: both">
        <p class="ft-b" style="font-size: 18px;"><span class="underline">Re: Supply of signages</span></p>
        <p>Thank you for inviting us to quote and we are please to submit our quotation as follows:</p>
    </div>
</div>
