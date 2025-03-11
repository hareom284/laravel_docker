<div class="signature-section" style="font-size:12px; margin-top: 100px;">
    <table style="float: left;">
        <tbody>
            <tr>
                <td colspan="2">
                    <div>Your Sincerely,</div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <img src="{{ 'data:image/png;base64,' . $quotationData['saleperson_signature'] }}"
                        style="height:100px" class="twp-image">
                </td>
            </tr>
            <tr>
                <td colspan="2" class="ft-b">{{ $quotationData['companies']['name'] }}</td>
            </tr>
            <tr>
                <td>Name:</td>
                <td>{{ $quotationData['signed_saleperson'] }}</td>
            </tr>
            <tr>
                <td>Mobile No:</td>
                <td>{{ $quotationData['signed_sale_ph'] }}</td>
            </tr>
            <tr>
                <td>Email:</td>
                <td>{{ $quotationData['signed_sale_email'] }}</td>
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
                        <div>Agreed & Accepted By:</div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <img src="{{ 'data:image/png;base64,' . $customer['customer_signature'] }}"
                            style="height:100px;" class="twp-image">
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="ft-b">
                        Authorised Signature / Company 's Stamp
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="ft-b">
                        {{ $customer['customer']['name_prefix'] . ' ' . $customer['customer']['first_name'] . ' ' . $customer['customer']['last_name'] }}
                    </td>
                </tr>
                <tr>
                    <td>NRIC: </td>
                    <td style="border-bottom: 1px solid black;">{{ $customer['customer']['customers']['nric'] }}</td>
                </tr>
                <tr>
                    <td>Mobile No. </td>
                    <td>{{ $customer['customer']['contact_no'] }}</td>
                </tr>
                <tr>
                    <td>Email: </td>
                    <td>{{ $customer['customer']['email'] }}</td>
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
                        <div>Agreed & Accepted By:</div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div style="height:100px;width:200px;" class="border-b">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="ft-b">
                        Authorised Signature / Company 's Stamp
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="ft-b">
                        {{ $customer['name_prefix'] . ' ' . $customer['first_name'] . ' ' . $customer['last_name'] }}
                    </td>
                </tr>
                <tr>
                    <td>NRIC: </td>
                    <td style="border-bottom: 1px solid black;">{{ $customer['customers']['nric'] }}</td>
                </tr>
                <tr>
                    <td>Mobile No. </td>
                    <td>{{ $customer['contact_no'] }}</td>
                </tr>
                <tr>
                    <td>Email: </td>
                    <td>{{ $customer['email'] }}</td>
                </tr>
            </tbody>
        </table>
        @endforeach
    @endif
    </div>
</div>
