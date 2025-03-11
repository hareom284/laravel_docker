<table class="avoid-break" style="width:100%;margin-top:10px;">
    <tbody class="ft-12">
        <tr align="right">
            <td colspan="4" align="right" class="ft-b">Sub Total :</td>
            <th style="width: 90px;">
                ${{ number_format($total_prices['total_all_amount'], 2, '.', ',') }}</th>
        </tr>
        @if ($total_prices['discount_percentage'] != 0)
            <tr align="right">
                <td colspan="4" align="right" class="ft-b">Goodwill Disc :</td>
                <th style="width: 90px;">
                    ${{ number_format($total_prices['total_all_amount'] - $total_prices['total_special_discount'], 2, '.', ',') }}
                </th>
            </tr>
        @endif

        @if ($total_prices['gst_percentage'] != 0)
            <tr align="right">
                <td colspan="4" align="right" class="ft-b">{{ $total_prices['gst_percentage'] }}% GST  :</td>
                <th style="width: 90px;">
                    ${{ number_format($total_prices['total_gst'], 2, '.', ',') }}</th>
            </tr>
        @endif

        <tr align="right">
            <td colspan="4" align="right" class="ft-b">Total Amount :</td>
            <th style="width: 90px;border-top: 1px solid black; border-bottom: 2px solid black;" >
                ${{ number_format($total_prices['total_inclusive'], 2, '.', ',') }}</th>
        </tr>
    </tbody>
</table>
