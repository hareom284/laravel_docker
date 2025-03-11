<div style="width: 100%; position: relative;">
    <div style="position: absolute;top: -30px; left: 0;font-weight: bold;">
        @include('pdf.Common.noteAndDisclaimerComponent')
    </div>
<table class="avoid-break" style="width:100%">

    <tbody class="ft-b-12">
        <tr>
            <td colspan="4" align="right">Total Amount :</td>
            <td style="width: 90px;" align="right">
                FOC</td>
        </tr>

        @if ($total_prices['discount_percentage'] != 0)
            <tr>
                <td colspan="4" align="right">Goodwill Discount :</td>
                <td style="width: 90px;" align="right">
                     FOC
                </td>
            </tr>
            <tr>
                <td colspan="5" style="height: 10px;"></td>
            </tr>
            <tr>
                <td colspan="4" align="right">Total Amount After Discount :</td>
                <td style="width: 90px;" align="right">
                    FOC
            </tr>
        @endif
        @if ($total_prices['gst_percentage'] != 0)
        <tr>
            <td colspan="4" align="right">( {{ $total_prices['gst_percentage'] }}% ) of GST :</td>
            <td style="width: 90px;" align="right">
                <span>FOC</span>
            </td>
        </tr>
        @endif
        <tr>
            <td colspan="4" align="right">Grand Total Amount :</td>
            <td style="width: 90px;" align="right">
                <span style="border-bottom: 2px solid black;border-top: 2px solid black;">FOC</span>
            </td>
        </tr>
    </tbody>
</table>
</div>
