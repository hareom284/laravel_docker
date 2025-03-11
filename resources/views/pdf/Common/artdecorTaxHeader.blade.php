<div class="header-section">
    @include('pdf.Common.artdecorTopHeaderComponent', [
        'companies' => $quotationData['companies'],
    ])
    <div class="bottom-header">
        <div class="doc-type">
            <span class="underline ft-b-16">Tax Invoice</span>
        </div>
        <br />
        <div class="detail">
            <div class="bottom-header-content">
                <div class="left-section">
                    <table>
                        <tr>
                            <td>Inv</td>
                            <td>: {{ $quotationData['project']['invoice_no'] }}</td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top;">M/S</td>
                            <td style="vertical-align: top;">:
                                @if (isset($quotationData['properties']))
                                    {{ $quotationData['properties']['block_num'] . ' ' . $quotationData['properties']['street_name'] . ' ' . '#' . $quotationData['properties']['unit_num'] }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top;">Attn</td>
                            <td style="vertical-align: top;">
                                @foreach ($quotationData['customers_array'] as $customer)
                                    <div>
                                        :
                                        {{ $customer['name_prefix'] . ' ' . $customer['first_name'] . ' ' . $customer['last_name'] }}
                                    </div>
                                @endforeach
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="right-section">
                    <table>
                        <tr>
                            <td style="padding-left:120px;">Date</td>
                            <td>:
                                @if (isset($quotationData['signed_date']))
                                    <span>{{ $quotationData['signed_date'] }}</span>
                                @else
                                    <span>{{ $quotationData['created_at'] }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td style="padding-left:120px;">HP</td>
                            <td>: {{ $quotationData['signed_sale_ph'] }}</td>
                        </tr>
                        <tr>
                            <td style="padding-left:120px;">Tel</td>
                            <td>: {{ $quotationData['companies']['tel'] }}</td>
                        </tr>
                        <tr>
                            <td style="padding-left:120px;">Fax</td>
                            <td>: {{ $quotationData['companies']['fax'] }}</td>
                        </tr>
                        <tr>
                            <td style="padding-left:120px;">Email</td>
                            <td>: {{ $quotationData['signed_sale_email'] }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            <br />
            <br />
            <div class="ft-b-12">RE:<span class="underline">Renovation Works at @
                    @if (isset($quotationData['properties']))
                        {{ $quotationData['properties']['block_num'] . ' ' . $quotationData['properties']['street_name'] . ' ' . '#' . $quotationData['properties']['unit_num'] }}
                    @endif
                </span>
            </div>
        </div>
    </div>

</div>
