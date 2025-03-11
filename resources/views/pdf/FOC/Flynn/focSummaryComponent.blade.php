<table class="avoid-break" style="width:100%">

    <tbody class="ft-12">
        <tr>
            <td colspan="4" align="right">Subtotal :</td>
            <th style="width: 90px;" align="right">
                ${{ number_format($total_prices['total_all_amount'], 2, '.', ',') }}</th>
        </tr>
        <tr>
            <td colspan="5" style="height: 10px;"></td>
        </tr>

        @if ($total_prices['discount_percentage'] != 0)
            <tr>
                <td colspan="4" align="right">Goodwill Disc :</td>
                <th style="width: 90px;border-bottom:1px solid black;" align="right">
                    ${{ number_format($total_prices['total_all_amount'] - $total_prices['total_special_discount'], 2, '.', ',') }}
                </th>
            </tr>
        @endif

        @if ($total_prices['gst_percentage'] != 0)
            <tr>
                <td colspan="4" align="right">GST ( {{ $total_prices['gst_percentage'] }}% ) :</td>
                <th style="width: 90px;" align="right">
                    ${{ number_format($total_prices['total_gst'], 2, '.', ',') }}</th>
            </tr>
        @endif

        <tr>
            <td colspan="4" align="right">Grand Total :</td>
            <th style="width: 90px;" align="right">
                ${{ number_format($total_prices['total_inclusive'], 2, '.', ',') }}</th>
        </tr>
    </tbody>
</table>
