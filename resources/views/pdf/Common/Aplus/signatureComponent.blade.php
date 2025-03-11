<div class="avoid-break">
    <div class="stamp" style="clear: both;">
        <img src="{{ 'data:image/png;base64,' . $quotationData['companies']['company_stamp'] }}" height="120" />
    </div>
    <hr style="clear: both;height: 2px;background-color: #000000;" />
    <div class="signatures ft-b-14">
        <table style="float: left;" class="avoid-break">
            <tbody>
                <tr>
                    <td colspan="2">
                        <div>{{ $quotationData['companies']['name'] }}</div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <img src="{{ 'data:image/png;base64,' . $quotationData['saleperson_signature'] }}"
                            style="height:100px" class="twp-image">
                    </td>
                </tr>
                <tr>
                    <td>NAME: </td>
                    <td>{{ $quotationData['signed_saleperson'] }}</td>
                </tr>
            </tbody>
        </table>
        <div style="float: right;" class="avoid-break">
            @if (!empty($quotationData['customer_signature']))
                @foreach ($quotationData['customer_signature'] as $customer)
                    <table>
                        <tbody>
                            <tr>
                                <td colspan="2">
                                    <div>CLIENT'S SIGNATURE</div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <img src="{{ 'data:image/png;base64,' . $customer['customer_signature'] }}"
                                        style="height:100px;" class="twp-image">
                                </td>
                            </tr>

                            <tr>
                                <td>NAME </td>
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
                        </tbody>
                    </table>
                @endforeach
            @elseif(!empty($customers_array))
                @foreach ($customers_array as $customer)
                    <table>
                        <tbody>
                            <tr>
                                <td colspan="2">
                                    <div>CLIENT'S SIGNATURE</div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <div style="height:100px;width:200px;" class="border-b">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>NAME </td>
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
                        </tbody>
                    </table>
                @endforeach
            @endif
        </div>
    </div>
</div>
