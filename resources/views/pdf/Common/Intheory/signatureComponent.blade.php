<div class="signature-section ft-12">
    <table class="avoid-break" style="float: left;">
        <tbody>
            <tr>
                <td style="font-weight: bold;">Yours sincerely,</td>
            </tr>
            <tr>
                <td>
                    <img src="{{ 'data:image/png;base64,' . $quotationData['saleperson_signature'] }}"
                        style="height:70px" class="twp-image">
                </td>
            </tr>
            <tr>
                <td>{{ $quotationData['signed_saleperson'] }}</td>
            </tr>
            <tr>
                <td>Lead Designer</td>
            </tr>
            <tr>
                <td>{{ $quotationData['companies']['name'] }}</td>
            </tr>
        </tbody>
    </table>
    @if (!empty($quotationData['customer_signature']))
        @foreach ($quotationData['customer_signature'] as $customer)
            <table class="{{ $current_folder_name == 'Twp' ? 'twp-float-left' : '' }}" style="float: right; margin-right: 100px;">
                <tbody>
                    <tr>
                        <td style="font-weight: bold;">Confirmed and accepted by,</td>
                    </tr>
                    <tr>
                        <td>
                            <img src="{{ 'data:image/png;base64,' . $customer['customer_signature'] }}"
                                style="height:70px;" class="twp-image">
                        </td>
                    </tr>
                    <tr>
                        <td>Name: 
                        @if ($settings['enable_show_last_name_first'] == 'true')
                            
                                {{ $customer['customer']['name_prefix'] . ' ' . $customer['customer']['last_name'] . ' ' . $customer['customer']['first_name'] }}
                            
                        @else
                            
                                {{ $customer['customer']['name_prefix'] . ' ' . $customer['customer']['first_name'] . ' ' . $customer['customer']['last_name'] }}
                            
                        @endif
                    </td>
                    </tr>
                    <tr>
                        <td>NRIC: {{ $customer['customer']['customers']['nric'] }}</td>
                    </tr>
                    <tr>
                        <td>Date: {{$quotationData['signed_date']}}</td>
                    </tr>
                </tbody>
            </table>
        @endforeach
    @elseif(!empty($customers_array))
        @foreach ($customers_array as $customer)
            <table class="{{ $current_folder_name == 'Twp' ? 'twp-float-left' : '' }}" style="float: right; margin-right: 100px;">
                <tbody>
                    <tr>
                        <td style="font-weight: bold; height: 17px;"></td>
                    </tr>
                    <tr>
                        <td>
                            <div style="height:70px;" class="twp-image">
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>Name: 
                        @if ($settings['enable_show_last_name_first'] == 'true')
                            
                                {{ $customer['name_prefix'] . ' ' . $customer['last_name'] . ' ' . $customer['first_name'] }}
                            
                        @else
                            
                                {{ $customer['name_prefix'] . ' ' . $customer['first_name'] . ' ' . $customer['last_name'] }}
                            
                        @endif
                    </td>
                    </tr>
                    <tr>
                        <td>NRIC: {{ $customer['customers']['nric'] }}</td>
                    </tr>
                    <tr>
                        <td>Signed Date: {{ $quotationData['signed_date'] }}</td>
                    </tr>
                </tbody>
            </table>
        @endforeach
    @endif
</div>
