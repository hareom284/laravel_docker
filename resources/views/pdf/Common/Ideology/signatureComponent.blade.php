<div class="signature-section">
    <div style="float: left"></div>
    <div style="float: right;" class="avoid-break">
        @if (!empty($quotationData['customer_signature']))
            <table border="1" style="border-collapse: collapse;">
                <tr>
                    @foreach ($quotationData['customer_signature'] as $customer)
                        <td style="width: 250px;padding: 5px;">
                            <p>Owner's Signature/NRIC: </p>
                            <img src="{{ 'data:image/png;base64,' . $customer['customer_signature'] }}"
                                style="height:100px;" class="twp-image">
                        </td>
                    @endforeach
                    <td style="width: 250px;padding: 5px;">
                        <p>Sales Signature/NRIC:</p>
                        <div style="float: left;">
                            <img src="{{ 'data:image/png;base64,' . $quotationData['saleperson_signature'] }}"
                            style="height:100px;" class="twp-image">
                        </div>
                        <div style="float: right;">
                            <img src="{{ 'data:image/png;base64,' . $quotationData['companies']['company_stamp'] }}"
                            style="height:100px; width:100px;" class="twp-image">
                        </div>
                    </td>
                </tr>
            </table>
        @elseif(!empty($customers_array))
            <table border="1" style="border-collapse: collapse;">
                <tr>
                    @foreach ($customers_array as $customer)
                        <td style="width: 250px;padding: 5px;">
                            <p>Owner's Signature/NRIC: {{ $customer['customers']['nric'] }}</p>
                            <div style="height:100px;width:250px;" class="border-b">
                            </div>
                        </td>
                    @endforeach
                    <td style="width: 250px;padding: 5px;">
                        <p>Sales Signature/NRIC: </p>
                        <img src="{{ 'data:image/png;base64,' . $quotationData['saleperson_signature'] }}"
                            style="height:100px" class="twp-image">
                    </td>
                </tr>
            </table>
        @endif
    </div>
</div>
