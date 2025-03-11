<tbody class="summary">
    <tr>
        <td rowspan="4" style="border: 1px solid black !important;"></td>
        <td rowspan="4" style="border: 1px solid black !important;min-width: 450px;">
            {{ $quotationList->disclaimer }}
        </td>
        <td style="width: 180px;height: 15px;"></td>
        <td rowspan="4" colspan="3" style="border: 1px solid black !important;">
            <table border="0" class="total-section ft-b-14">
                <tr>
                    <td align="right" style="width: 130px;">SUB TOTAL :</td>
                    <td align="right">$</td>
                    <td align="right">{{ number_format($total_prices['total_special_discount'], 2, '.', ',') }}</td>
                </tr>
                @if ($total_prices['gst_percentage'] != 0)
                <tr>
                    <td align="right">GST {{ $total_prices['gst_percentage'] }}% :</td>
                    <td align="right" style="width: 20px;">$</td>
                    <td align="right">{{ number_format($total_prices['total_gst'], 2, '.', ',') }}</td>
                </tr>
                @endif
                @if ($total_prices['discount_percentage'] != 0)
                <tr>
                    <td align="right">DISCOUNT :</td>
                    <td align="right" style="width: 20px;">$</td>
                    <td align="right">{{ number_format($total_prices['total_all_amount'] - $total_prices['total_special_discount'], 2, '.', ',') }}</td>
                </tr>
                @endif
                <tr>
                    <td align="right">TOTAL :</td>
                    <td align="right">$</td>
                    <td align="right">{{ number_format($total_prices['total_inclusive'], 2, '.', ',') }}</td>
                </tr>
                <tr>
                    <td align="right" colspan="3"></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td style="height: 15px;"></td>
    </tr>
    <tr>
        <td style="height: 15px;"></td>
    </tr>
    <tr>
        <td style="height: 15px;border-bottom: 1px solid black !important;"></td>
    </tr>

</tbody>
