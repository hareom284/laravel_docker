<table class="avoid-break" style="width:100%">

    <tbody class="ft-12">
        <tr>
            <td colspan="6" align="right">Net Total :</td>
            <th style="width: 120px;" align="right">FOC</th>
        </tr>
        <tr>
            <td style="height: 10px;">
                <div></div>
            </td>
        </tr>
        @if ($total_prices['discount_percentage'] != 0)
            <tr>
                <td colspan="6" align="right">Discount Amount :</td>
                <th style="width: 120px;border-bottom:1px solid black;" align="right">
                    FOC
                </th>
            </tr>
            <tr>
                <td style="height: 10px;">
                    <div></div>
                </td>
            </tr>
        @endif

        @if ($total_prices['gst_percentage'] != 0)
            <tr>
                <td colspan="6" align="right">GST ( {{ $total_prices['gst_percentage'] }}% ) :</td>
                <th style="width: 120px;" align="right">FOC</th>
            </tr>
            <tr>
                <td style="height: 10px;">
                    <div></div>
                </td>
            </tr>
        @endif

        <tr>
            <td colspan="6" align="right">Grand Total :</td>
            <th style="width: 120px;" align="right">FOC</th>
        </tr>
        <tr>
            <td style="height: 10px;">
                <div></div>
            </td>
        </tr>
    </tbody>
</table>
