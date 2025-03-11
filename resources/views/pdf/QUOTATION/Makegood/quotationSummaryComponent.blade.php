<tbody class="ft-12" class="total-section">
    {{-- <tr>
        <td colspan="3" align="center" class="ft-b-12 bg-yellow">
            Purchased Items Total
        </td>
        <td align="center" class="ft-b-12 bg-yellow"></td>
    </tr> --}}
    <tr>
        <td></td>
        <td colspan="3" style="height:20px;"></td>
    </tr>
    <tr>
        <td></td>
        <td colspan="3" style="height:20px;"></td>
    </tr>
    <tr class="total">
        <td colspan="3" align="center" class="bg-total">Total Build Cost </td>
        <td align="center" class="bg-green">$
            {{ number_format($total_prices['total_all_amount'], 2, '.', ',') }}
        </td>
    </tr>
    @if ($total_prices['discount_percentage'] != 0)
        <tr class="total">
            <td colspan="3" align="center" class="bg-total">Goodwill Discount </td>
            <td align="center" class="bg-green">$
                {{ number_format($total_prices['total_all_amount'] - $total_prices['total_special_discount'], 2, '.', ',') }}
            </td>
        </tr>
    @endif
    @if ($total_prices['gst_percentage'] != 0)
        <tr class="total">
            <td colspan="3" align="center" class="bg-total">GST ( {{ $total_prices['gst_percentage'] }}% ) </td>
            <td align="center" class="bg-green">$ {{ number_format($total_prices['total_gst'], 2, '.', ',') }}</td>
        </tr>
    @endif
    <tr class="total">
        <td colspan="3" align="center" class="bg-total">
            <span class="ft-b" style="font-size:16px;">Total Renovation Charges</span>
        </td>
        <td align="center" class="bg-green">$
            <span class="ft-b"
                style="font-size:16px;">{{ number_format($total_prices['total_inclusive'], 2, '.', ',') }}</span>
        </td>
    </tr>
    <tr>
        <td colspan="4" style="height: 50px;"></td>
    </tr>
</tbody>
