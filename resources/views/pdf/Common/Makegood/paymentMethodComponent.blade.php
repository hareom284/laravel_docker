<tbody class="ft-12" class="payment-method">
    <tr>
        <th rowspan="8"></th>
        <th align="left" colspan="3">Payment Method:</th>
    </tr>
    <tr>
        <td colspan="3">
            · 15% upon confirmation of job, 50% before commencement of work, 30% upon Carpentry/Metal
            works being delivered to site and
            remaining 5% upon completion of work
        </td>
    </tr>
    <tr>
        <td colspan="3">
            · Confirmation of order is subject to receive of signed documents & deposit payment.
        </td>
    </tr>
    <tr>
        <td colspan="3">
            · All cheques can be made payable to <span class="ft-b">{{ $quotationData['companies']['name'] }}</span>
        </td>
    </tr>
    <tr>
        <td colspan="3">
            · Payment via bank transfer can be made to <span class="ft-b">OCBC Current Account
                595-348-962-001</span>
        </td>
    </tr>
    <tr>
        <td colspan="3">
            · Payment via PayNow can be made to <span class="ft-b">UEN:
                @if (isset($quotationData['companies']['gst_reg_no']) && $quotationData['companies']['gst_reg_no'] != '')
                    {{ $quotationData['companies']['gst_reg_no'] }}
                @else
                    {{ $quotationData['companies']['reg_no'] }}
                @endif
            </span>
        </td>
    </tr>
    <tr>
        <td colspan="3">
            · Payment via QR Code:
        </td>
    </tr>
    <tr>
        <td colspan="3" align="center">
            <img src="{{ public_path() . '/images/makegoodqr.png' }}" height="180" />
        </td>
    </tr>
</tbody>
