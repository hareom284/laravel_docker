<div style="width: 100%; position: relative;">
    <div style="position: absolute;top: -30px; left: 0;font-weight: bold;">
        @include('pdf.Common.noteAndDisclaimerComponent')
    </div>
<table class="avoid-break" style="width:100%">

    <tbody class="ft-b-12">
        <tr>
            <td colspan="4" align="right">Total Amount :</td>
            <td style="width: 90px;" align="right">
                -${{ number_format($total_prices['total_all_amount'], 2, '.', ',') }}</td>
        </tr>

        @if ($total_prices['discount_percentage'] != 0)
            <tr>
                <td colspan="4" align="right">Goodwill Discount :</td>
                <td style="width: 90px;" align="right">
                     -${{ number_format($total_prices['total_all_amount'] - $total_prices['total_special_discount'], 2, '.', ',') }}
                </td>
            </tr>
            <tr>
                <td colspan="5" style="height: 10px;"></td>
            </tr>
            <tr>
                <td colspan="4" align="right">Total Amount After Discount :</td>
                <td style="width: 90px;" align="right">
                    -${{ number_format($total_prices['total_special_discount'], 2, '.', ',') }}</td>
            </tr>
        @endif
        @if ($total_prices['gst_percentage'] != 0)
        <tr>
            <td colspan="4" align="right">( {{ $total_prices['gst_percentage'] }}% ) of GST :</td>
            <td style="width: 90px;" align="right">
                <span>-${{ number_format($total_prices['total_gst'], 2, '.', ',') }}</span>
            </td>
        </tr>
        @endif
        <tr>
            <td colspan="4" align="right">Grand Total Amount :</td>
            <td style="width: 90px;" align="right">
                <span style="border-bottom: 2px solid black;border-top: 2px solid black;">-${{ number_format($total_prices['total_inclusive'], 2, '.', ',') }}</span>
            </td>
        </tr>
    </tbody>
</table>
</div>
