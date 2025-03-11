<div class="signature-section">
    <table style="float: left;">
        <tbody>
            <tr>
                <td colspan="2">
                    <div>Yours faithfully</div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <img src="{{ 'data:image/png;base64,' . $quotationData['saleperson_signature'] }}"
                        style="height:100px" class="twp-image">
                </td>
            </tr>
            <tr>
                <td colspan="2">{{ $quotationData['signed_saleperson'] }}</td>
            </tr>
        </tbody>
    </table>
    <div style="float: right;">
        @if (!empty($quotationData['customer_signature']))
            @foreach ($quotationData['customer_signature'] as $customer)
                <table>
                    <tbody>
                        <tr>
                            <td colspan="2">
                                <div>Confirmed & Agreed</div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <img src="{{ 'data:image/png;base64,' . $customer['customer_signature'] }}"
                                    style="height:100px;" class="twp-image">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">Signature & company stamp</td>
                        </tr>
                    </tbody>
                </table>
            @endforeach
        @elseif(!empty($customers_array))
            @foreach ($customers_array as $customer)
                <table>
                    <tbody>
                        <tr>
                            <td colspan="2">
                                <div>Confirmed & Agreed</div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <div style="height:100px;width:200px;" class="border-b">
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">Signature & company stamp</td>
                        </tr>
                    </tbody>
                </table>
            @endforeach
        @endif
    </div>
</div>
