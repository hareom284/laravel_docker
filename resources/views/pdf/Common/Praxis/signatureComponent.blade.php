@php
$isIncludesDirector = false;
@endphp
<div class="signature-section" style="{{ $isIncludesDirector ? 'padding-bottom: 400px;' : 'padding-bottom: 170px;' }}">
    <table class="avoid-break" style="float: left;">
        <tbody>
            <tr>
                <td colspan="2">
                    <div>Thank You.</div>
                    <div class="ft-b">Praxis Space Pte Ltd</div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <img src="{{ 'data:image/png;base64,' . $quotationData['saleperson_signature'] }}"
                        style="height:100px" class="twp-image">
                    <p>_____________________________</p>
                </td>
            </tr>
            <tr>
                <td>{{ $quotationData['signed_saleperson'] }}</td>
            </tr>
            <tr>
                <td>{{ $quotationData['signed_sale_ph'] }} </td>
            </tr>
            <tr>
                <td style="height: 20px;"></td>
            </tr>
        </tbody>
@if($isIncludesDirector)
<tbody>
    <tr>
        <td colspan="2">
            <div class="ft-b">Verified By:</div>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <img src="{{ 'data:image/png;base64,' . $quotationData['saleperson_signature'] }}"
                style="height:100px" class="twp-image">
            <p>_____________________________</p>
        </td>
    </tr>
    <tr>
        <td>{{ $quotationData['signed_saleperson'] }}</td>
    </tr>
    <tr>
        <td>Project Director of Praxis Space Pte Ltd</td>
    </tr>
    <tr>
        <td>{{ $quotationData['signed_date'] }} </td>
    </tr>
</tbody>
@endif
    </table>
    <div style="float: right;" class="avoid-break">
        @if (!empty($quotationData['customer_signature']))
            @foreach ($quotationData['customer_signature'] as $customer)
                <table class="{{ $current_folder_name == 'Twp' ? 'twp-float-left' : '' }}">
                    <tbody>
                        <tr>
                            <td colspan="2">
                                <div class="ft-b">ACCEPTANCE</div>
                                <div>I/We hereby accept the above quotation</div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <img src="{{ 'data:image/png;base64,' . $customer['customer_signature'] }}"
                                    style="height:100px;" class="twp-image">
                                <p>_____________________________</p>
                            </td>
                        </tr>

                        <tr>
                            <td>Full Name </td>
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
                            <td>Last 4 Digit NRIC </td>
                            <td>: {{ $customer['customer']['customers']['nric'] }}</td>
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
                                <div class="ft-b">ACCEPTANCE</div>
                                <div>I/We hereby accept the above quotation</div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <div style="height:100px;width:200px;" class="border-b">
                                </div>
                                <p>_____________________________</p>
                            </td>
                        </tr>

                        <tr>
                            <td>Full Name </td>
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
                            <td>Last 4 Digit NRIC </td>
                            <td>: {{ $customer['customers']['nric'] }}</td>
                        </tr>
                    </tbody>
                </table>
            @endforeach
        @endif
    </div>
</div>
<div style="clear: both; margin-top: 100px;">
    <table border="1" style="border-collapse: collapse; border-color: transparent; width: 100%;clear: both;">
        <tr style="background: #000;color: #fff">
            <td colspan="6" align="center" style="vertical-align: top;width: 120px;height: 15px;" class="ft-b">
            </td>
        </tr>
    </table>
</div>
