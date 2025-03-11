<style>
    .header-section {
        padding-bottom: 140px;
    }

    .right-header {
        float: right;
    }

    .left-header {
        float: left;
    }

    .twp-padding {
        padding-bottom: 15px;
    }

    .header-name {
        clear: both;
    }

    .header-name {
        clear: both;
    }

    .ft-b {
        font-weight: bold;
    }

    .header-center {
        margin: 0 auto;
        text-align: center;
    }

    .ft-16 {
        font-size: 16px !important;
    }

    .ft-12 {
        font-size: 12px !important;
    }
</style>
<div class="header-section">
    <div class="header-center">
        <p class="ft-16 ft-b">{{ $document_type }}</p>
    </div>
    <div style="padding-top:10px;font-size:12px;clear:both;" class="twp-padding">
        <table class="left-header">
            <tbody>
                <tr>
                    <td colspan="3" class="ft-b ft-12">Biz Reg No. @if (isset($quotationData['companies']['gst_reg_no']) && $quotationData['companies']['gst_reg_no'] != '')
                            {{ $quotationData['companies']['gst_reg_no'] }}
                        @else
                            {{ $quotationData['companies']['reg_no'] }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td colspan="3" style="height: 15px;"></td>
                </tr>
                <tr>
                    <td colspan="3" class="ft-b ft-12">Prepared for:</td>
                </tr>
                <tr>
                    <td colspan="3" style="height: 15px;"></td>
                </tr>

                @if ($quotationData['customers']['customer_type'] == 'commerical')
                    <tr>
                        <td colspan="3" class="ft-b ft-12">{{ $quotationData['customers']['company_name'] }}</td>
                    </tr>
                @endif
                @foreach ($quotationData['customers_array'] as $customer)
                    <tr>
                        @if (isset($quotationData['properties']))
                            <td colspan="3" class="ft-12">
                                {{ $quotationData['properties']['block_num'] . ' ' . $quotationData['properties']['street_name'] }}
                            </td>
                        @endif
                    </tr>
                    <tr>
                        <td colspan="3" class="ft-12">CP:
                            {{ $customer['name_prefix'] . ' ' . $customer['first_name'] . ' ' . $customer['last_name'] }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="ft-12">{{ $customer['contact_no'] }}</td>
                    </tr>
                    <tr>
                        <td colspan="3" class="ft-12">{{ $customer['email'] }}</td>
                    </tr>
                    <tr>
                        <td colspan="3" style="height: 20px;"></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <table class="right-header">
            <tbody>
                <tr>
                    <td colspan="3" class="ft-b ft-12" align="right">Date</td>
                </tr>
                <tr>
                    <td colspan="3" class="ft-12" align="right">
                        @if (isset($quotationData['signed_date']))
                            <span>{{ convertDate($quotationData['signed_date']) }}</span>
                        @else
                            <span>{{ convertDate($quotationData['created_at']) }}</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td colspan="3" style="height: 15px;"></td>
                </tr>
                <tr>
                    <td colspan="3" class="ft-b ft-12" align="right">Quotation No.</td>
                </tr>
                <tr>
                    <td colspan="3" class="ft-12" align="right">{{ $quotationData['document_agreement_no'] }}</td>
                </tr>
                <tr>
                    <td colspan="3" style="height: 15px;"></td>
                </tr>
                <tr>
                    <td colspan="3" class="ft-b ft-12" align="right">Quote Valid Till</td>
                </tr>
                <tr>
                    <td colspan="3" class="ft-12" align="right"></td>
                </tr>
                <tr>
                    <td colspan="3" style="height: 20px;"></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
