<style>
    .border-b {
        border-bottom: 1px solid black !important;
        /* padding: 5px 0; */
    }
</style>
<div class="signature-section">
    <table class=" avoid-break" style="float: left;">
        <tbody>
            <tr>
                <td colspan="2">
                    <div>Yours faithfully</div>
                    <div>Sales Rep.Signature</div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    @if (!empty($quotationData['saleperson_signature']))
                        <img src="{{ 'data:image/png;base64,' . $quotationData['saleperson_signature'] }}"
                            style="height:100px" class="twp-image border-b">
                    @else
                        <div style="height:100px;width:200px;" class="border-b">
                        </div>
                    @endif
                </td>
            </tr>
            <tr>
                <td colspan="2" class="ft-b">{{ $quotationData['companies']['name'] }}</td>
            </tr>
            <tr>
                <td colspan="2" class="ft-b">{{ $quotationData['signed_saleperson'] }}</td>
            </tr>

        </tbody>
    </table>
    <div style="float: right;" class="avoid-break">
        @if (!empty($quotationData['customer_signature']))
            @foreach ($quotationData['customer_signature'] as $customer)
                <table class="{{ $current_folder_name == 'Twp' ? 'twp-float-left' : '' }}">
                    <tbody>
                        <tr>
                            <td colspan="2">
                                <div>Price, Layout, Terms & Conditions</div>
                                <div>Agreed & Accepted By :</div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <img src="{{ 'data:image/png;base64,' . $customer['customer_signature'] }}"
                                    style="height:100px;" class="twp-image border-b">
                            </td>
                        </tr>

                        <tr>
                            <td colspan="2" class="ft-b">Name / Signature </td>
                        </tr>
                        <tr>
                            <td>NRIC No. : </td>
                            <td class="border-b" style="padding-right: 100px;">
                                {{ $customer['customer']['customers']['nric'] }}</td>
                        </tr>
                        <tr>
                            <td>DATE : </td>
                            <td class="border-b" style="padding-right: 100px;">{{ $quotationData['signed_date'] }}</td>
                        </tr>
                    </tbody>
                </table>
            @endforeach
        @elseif(!empty($customers_array))
            @foreach ($customers_array as $customer)
                <table class="{{ $current_folder_name == 'Twp' ? 'twp-float-left' : '' }}">
                    <tbody>
                        <tr>
                            <td colspan="2">
                                <div>Price, Layout, Terms & Conditions</div>
                                <div>Agreed & Accepted By :</div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <div style="height:100px;width:200px;" class="border-b">
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="ft-b">Name / Signature </td>
                        </tr>
                        <tr>
                            <td>NRIC No. : </td>
                            <td class="border-b" style="padding-right: 100px;">{{ $customer['customers']['nric'] }}</td>
                        </tr>
                        <tr>
                            <td>DATE : </td>
                            <td class="border-b" style="padding-right: 100px;">{{ $quotationData['created_at'] }}</td>
                        </tr>
                    </tbody>
                </table>
            @endforeach
        @endif
    </div>
</div>
