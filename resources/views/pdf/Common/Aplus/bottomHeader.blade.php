<style>
    .bottom-header table td {
        padding: 10px;
        border-width: 2px;
    }

    .email-link {
        text-decoration: underline;
        color: #2973c0;
        font-weight: bold;
    }

    .no-border td {
        padding: 0 !important;
        border-left: 1px solid transparent !important;
        border-right: 1px solid transparent !important;
        height: 10px;
    }

    .yearly-contract {
        border-bottom: 3px solid red;
        color: red;
        width: 385px;
    }
</style>
<div class="bottom-header ft-12">
    <h2 class="yearly-contract">A+ Daily – Quotation (Yearly Contract)</h2>
    <table border="1" style="border-collapse: collapse;  width: 100%;">
        <tr>
            <td class="ft-b-14">Company Name:</td>
            <td colspan="3">{{ $quotationData['companies']['name'] }}</td>
        </tr>
        <tr>
            <td class="ft-b-14">Company Address:</td>
            <td colspan="3">{{ $quotationData['companies']['main_office'] }}</td>
        </tr>
        <tr>
            <td style="vertical-align: top;" class="ft-b-14">Site Address:</td>
            <td colspan="3" style="vertical-align: top;">
                @if (isset($quotationData['properties']))
                    {{ $quotationData['properties']['block_num'] . ' ' . $quotationData['properties']['street_name'] . ' ' . '#' . $quotationData['properties']['unit_num'] }}
                @endif
            </td>
        </tr>
        <tr>
            <td class="ft-b-14">Contact Person:</td>
            <td>
                @foreach ($quotationData['customers_array'] as $customer)
                    <div style="padding-left: 5px;">
                        {{ $customer['name_prefix'] . ' ' . $customer['first_name'] . ' ' . $customer['last_name'] }}
                    </div>
                @endforeach
            </td>
            <td class="ft-b-14">Contact No:</td>
            <td>
                @foreach ($quotationData['customers_array'] as $customer)
                    <div style="padding-left: 5px;">
                        {{ $customer['contact_no'] }}
                    </div>
                @endforeach
            </td>
        </tr>
        <tr>
            <td class="ft-b-14">Email Address:</td>
            <td colspan="3" align="center" style="text-align: center;">
                @foreach ($quotationData['customers_array'] as $customer)
                    <div style="padding-left: 5px;" class="email-link">
                        {{ $customer['email'] }}
                    </div>
                @endforeach
            </td>
        </tr>
        <tr class="no-border">
            <td colspan="4"></td>
        </tr>
        <tr>
            <td class="ft-b-14">Prepared By:</td>
            <td>{{ $quotationData['signed_saleperson'] }}</td>
            <td class="ft-b-14">Contact No:</td>
            <td>{{ $quotationData['signed_sale_ph'] }}</td>
        </tr>
        <tr>
            <td class="ft-b-14">Email Address:</td>
            <td colspan="3" class="email-link ft-b-14" align="center" style="text-align: center;">
                {{ $quotationData['signed_sale_email'] }}</td>
        </tr>
        <tr class="no-border">
            <td colspan="4"></td>
        </tr>
        <tr>
            <td class="ft-b-14">
                Proposed No. of Days <br />Per Week:
            </td>
            <td colspan="3" class="email-link ft-b-14" align="center" style="text-align: center;"></td>
        </tr>
        <tr>
            <td class="ft-b-14">
                Timing:
            </td>
            <td colspan="3" class="email-link ft-b-14"></td>
        </tr>
        <tr class="no-border">
            <td colspan="4"></td>
        </tr>
        <tr>
            <td class="ft-b-14">Quotation Date:</td>
            <td>{{ convertDate($quotationData['signed_date']) }}</td>
            <td class="ft-b-14">Quotation No:</td>
            <td>{{ $quotationData['document_agreement_no'] }}</td>
        </tr>
        <tr>
            <td class="ft-b-14">Commencement Date:</td>
            <td></td>
            <td class="ft-b-14">Contract Period:</td>
            <td></td>
        </tr>
    </table>
</div>
