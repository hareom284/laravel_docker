<div class="signature-section">
    <table class="{{$current_folder_name != 'Optimum' ? 'avoid-break' : ''}}" style="float: left;">
        <tbody>
            <tr>
                <td colspan="2">
                    <div>Yours faithfully</div>
                    <div>Sales Rep.Signature</div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <img src="{{ 'data:image/png;base64,' . $quotationData['saleperson_signature'] }}"
                        style="height:100px" class="twp-image">
                </td>
            </tr>
            <tr>
                <td colspan="2">{{ $quotationData['companies']['name'] }}</td>
            </tr>
            <tr>
                <td>Name </td>
                <td>: {{ $quotationData['signed_saleperson'] }}</td>
            </tr>
            <tr>
                <td>DESIGNATION </td>
                <td>: {{ $quotationData['rank'] }}</td>
            </tr>
            <tr>
                <td>MOBILE </td>
                <td>: {{ $quotationData['signed_sale_ph'] }}</td>
            </tr>
        </tbody>
    </table>
    <div style="float: right;" class="{{$current_folder_name != 'Optimum' ? 'avoid-break' : ''}}">
        @if (!empty($quotationData['customer_signature']))
            @foreach ($quotationData['customer_signature'] as $customer)
                <table class="{{ $current_folder_name == 'Twp' ? 'twp-float-left' : '' }}">
                    <tbody>
                        <tr>
                            <td colspan="2">
                                <div> I/We Confirm Our Acceptance</div>
                                <div>Client Signature</div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <img src="{{ 'data:image/png;base64,' . $customer['customer_signature'] }}"
                                    style="height:100px;" class="twp-image">
                            </td>
                        </tr>

                        <tr>
                            <td>Name </td>
                            @if ($settings['enable_show_last_name_first'] == 'true')
                                <td>:
                                    {{ $customer['customer']['name_prefix'] . ' ' . $customer['customer']['last_name'] . ' ' . $customer['customer']['first_name'] }}
                                </td>
                            @else
                                <td>:
                                    {{ $customer['customer']['name_prefix'] . ' ' . $customer['customer']['first_name'] . ' ' . $customer['customer']['last_name'] }}
                                </td>
                            @endif
                        </tr>
                        <tr>
                            <td>NRIC </td>
                            <td>: {{ $customer['customer']['customers']['nric'] }}</td>
                        </tr>
                        <tr>
                            <td>DATE </td>
                            <td>: {{ $quotationData['signed_date'] }}</td>
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
                                <div> I/We Confirm Our Acceptance</div>
                                <div>Client Signature</div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <div style="height:100px;width:200px;" class="border-b">
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td>Name </td>
                            @if ($settings['enable_show_last_name_first'] == 'true')
                                <td>:
                                    {{ $customer['name_prefix'] . ' ' . $customer['last_name'] . ' ' . $customer['first_name'] }}
                                </td>
                            @else
                                <td>:
                                    {{ $customer['name_prefix'] . ' ' . $customer['first_name'] . ' ' . $customer['last_name'] }}
                                </td>
                            @endif
                        </tr>
                        <tr>
                            <td>NRIC </td>
                            <td>: {{ $customer['customers']['nric'] }}</td>
                        </tr>
                        <tr>
                            <td>DATE </td>
                            <td>: {{ $quotationData['created_at'] }}</td>
                        </tr>
                    </tbody>
                </table>
            @endforeach
        @endif
    </div>
</div>
