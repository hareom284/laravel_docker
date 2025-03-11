<tbody class="ft-12" class="signature-section">
    <tr>
        <td colspan="4" style="height: 20px;"></td>
    </tr>
    <tr>
        <td rowspan="6" style="height: 20px;"></td>
        <td colspan="2">Prepared By:</td>
        <td>Acceptance By Client:</td>
    </tr>
    <tr>
        <td colspan="2">
            {{ $quotationData['signed_saleperson'] }}
        </td>
        <td rowspan="3">
            @if (!empty($quotationData['customer_signature']))
                @foreach ($quotationData['customer_signature'] as $customer)
                    <img src="{{ 'data:image/png;base64,' . $customer['customer_signature'] }}"
                        style="border-bottom:1px solid black;width: 150px;" class="border-b">
                @endforeach
            @endif
        </td>
    </tr>
    <tr>
        <td style="height: 20px" colspan="2">{{ $quotationData['rank'] }}, {{ $quotationData['companies']['name'] }}
        </td>
    </tr>
    <tr>
        <td style="height: 20px" colspan="2">This is an electronically generated quotation, hence
            does not require
            signature</td>
    </tr>
    <tr>
        {{-- <td style="height: 20px;"></td> --}}
        <td rowspan="2" colspan="2"></td>
        <td>NAME / SIGNATURE & DATE</td>
    </tr>
    <tr>
        <td style="height: 20px;"></td>
    </tr>
</tbody>
