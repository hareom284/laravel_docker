<table class="avoid-break" style="width:100%">

    <tbody class="ft-12">
        <tr>
            <td colspan="4" align="right" class="ft-b">Sub Total :</td>
            <th style="width: 90px;">
                FOC</th>
        </tr>
        @if ($total_prices['discount_percentage'] != 0)
            <tr>
                <td colspan="4" align="right" class="ft-b">Goodwill Disc :</td>
                <th style="width: 90px;">
                    FOC
                </th>
            </tr>
        @endif

        @if ($total_prices['gst_percentage'] != 0)
            <tr>
                <td colspan="4" align="right" class="ft-b">{{ $total_prices['gst_percentage'] }}% GST  :</td>
                <th style="width: 90px;">
                   FOC</th>
            </tr>
        @endif

        <tr>
            <td colspan="4" align="right" class="ft-b">Total Amount :</td>
            <th style="width: 90px;border-top: 1px solid black; border-bottom: 2px solid black;" >
                FOC</th>
        </tr>
    </tbody>
</table>
