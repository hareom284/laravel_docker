<div class="signature-section ft-12">
    <div style="clear: both">
        <span>I/We agree and confirm acceptance of the price, terms & conditions stated above:
        </span>
    </div><br/>
    <table class="avoid-break" style="float: left;">
        <tbody>

            <tr>
                <td>
                    @if(isset($quotationData['saleperson_signature']) && !empty($quotationData['saleperson_signature']))
                    <img src="{{ 'data:image/png;base64,' . $quotationData['saleperson_signature'] }}"
                        style="height:100px" class="twp-image">
                    @else
                    <div style="height: 100px;"></div>
                    @endif
                    <p>_____________________________</p>
                </td>
            </tr>
            <tr>
                <td>
                    <div>Agreed & Accepted by </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div>{{ $quotationData['companies']['name'] }}</div>
                </td>
            </tr>
            <tr>
                <td>Designer In Charge: {{ $quotationData['signed_saleperson'] }}</td>
            </tr>
            <tr>
                <td>Conact No: {{ $quotationData['signed_sale_ph'] }}</td>
            </tr>
            <tr>
                <td>Signed Date: {{ $quotationData['created_at'] }}</td>
            </tr>
        </tbody>
    </table>
    <div style="float: right;" class="avoid-break">
        @if (!empty($quotationData['customer_signature']))
            @foreach ($quotationData['customer_signature'] as $customer)
                <table class="{{ $current_folder_name == 'Twp' ? 'twp-float-left' : '' }}">
                    <tbody>

                        <tr>
                            <td>
                                @if(isset($customer['customer_signature']) && !empty($customer['customer_signature']))
                                <img src="{{ 'data:image/png;base64,' . $customer['customer_signature'] }}"
                                    style="height:100px;" class="twp-image">
                                @else
                                <div style="height: 100px;"></div>
                                @endif
                                <p>_____________________________</p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div>Agreed & Accepted by </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div>Customer Name & Signature</div>
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
                            <td>Signed Date: {{$quotationData['signed_date']}}</td>
                        </tr>
                    </tbody>
                </table>
            @endforeach
        @elseif(!empty($customers_array))
            @foreach ($customers_array as $customer)
                <table class="{{ $current_folder_name == 'Twp' ? 'twp-float-left' : '' }}">
                    <tbody>
                        <tr>
                            <td>
                                <div style="height:100px;width:200px;" class="border-b">
                                </div>
                                <p>_____________________________</p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div>Agreed & Accepted by </div>
                            </td>
                        </tr>
                        <td>
                            <div>Customer Name & Signature</div>
                        </td>
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
</div>
