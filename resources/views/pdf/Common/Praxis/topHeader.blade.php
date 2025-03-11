<style>
    .right-header {
        float: right;
    }

    .left-header {
        float: left;
    }
</style>
<table border="1" style="border-collapse: collapse; border-color: transparent; width: 100%;">
    <tr style="background: #000;color: #fff">
        <td colspan="6" align="center" style="vertical-align: top;width: 120px;height: 15px;" class="ft-b">  
        </td>
    </tr>
</table>
<br/>
<div style="clear: both;">
    <div class="left-header">
        <table>
            <tr>
                <td>
                    <div class="logo-section" style="height: 140px;">
                        <img src="{{ 'data:image/png;base64,' . $quotationData['companies']['company_logo'] }}" style="width: auto; height: 100%; max-height: 150px;">
                    </div>
                </td>
                <td style="vertical-align: bottom;">
                    <p>{{ $quotationData['companies']['main_office'] }}</p>
                    <p>Singapore 208357</p>
                    <p>UEN: @if (isset($quotationData['companies']['gst_reg_no']) && $quotationData['companies']['gst_reg_no'] != '')
                        {{ $quotationData['companies']['gst_reg_no'] }}
                    @else
                        {{ $quotationData['companies']['reg_no'] }}
                    @endif</p>
                </td>
            </tr>
        </table>
        <table style="margin-top: 15px;">
            <tr>
                <th align="right">Client Name:</th>
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
                <th align="right">Site Address:</th>
                <td>
                    @if (isset($quotationData['properties']))
                        {{ $quotationData['properties']['block_num'] . ' ' . $quotationData['properties']['street_name'] . ' ' . '#' . $quotationData['properties']['unit_num'] }}
                    @endif
                </td>
            </tr>
            <tr>
                <th align="right">Postal Code:</th>
                <td>
                    @if (isset($quotationData['properties']))
                        {{ $quotationData['properties']['postal_code'] }}
                    @endif
                </td>
            </tr>
            <tr>
                <th align="right">Contact No:</th>
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
                <th align="right">Email:</th>
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
        <table style="margin-top: 15px;">
            <tr>
                <th align="right">Date:</th>
                @if (isset($quotationData['signed_date']))
                    <td>{{ $quotationData['signed_date'] }}</td>
                @else
                    <td>{{ $quotationData['created_at'] }}</td>
                @endif
            </tr>
            <tr>
                <th align="right">Quotation No:</th>
                <td>
                    {{ $quotationData['document_agreement_no'] }}
                </td>
            </tr>
            <tr>
                <th align="right">Valid Till:</th>
                <td></td>
            </tr>
            <tr>
                <th align="right">Project Manager:</th>
                <td>{{ $quotationData['signed_saleperson'] }}</td>
            </tr>
        </table>
    </div>
</div>
