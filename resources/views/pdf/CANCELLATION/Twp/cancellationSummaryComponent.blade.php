<table class="avoid-break" style="width:100%">

    <tbody class="ft-b-12">
        @if ($current_folder_name == 'Henglai')
            @if ($total_prices['discount_percentage'] != 0)
                <tr>
                    <td colspan="4" align="right">Total For The Above Mentioned Renovation Works :</td>
                    <td style="width: 90px;" align="right">
                        - $ {{ number_format($total_prices['total_all_amount'], 2, '.', ',') }}</td>
                </tr>
            @endif
        @else
            <tr>
                <td colspan="4" align="right">Total For The Above Mentioned Renovation Works :</td>
                <td style="width: 90px;" align="right">
                    - $ {{ number_format($total_prices['total_all_amount'], 2, '.', ',') }}</td>
            </tr>
        @endif
        @if ($total_prices['discount_percentage'] != 0)
            @if ($settings['enable_only_show_discount_amount'] == 'true')
                <tr>
                    <td colspan="4" align="right">Discount Amount :</td>
                    <td style="width: 90px;" align="right">
                        -
                        $ {{ number_format($total_prices['total_all_amount'] - $total_prices['total_special_discount'], 2, '.', ',') }}
                    </td>
                </tr>
            @else
                <tr>
                    <td colspan="4" align="right">Total For The Above Mentioned Renovation Works After
                        Special
                        Discount :</td>
                    <td style="width: 90px;" align="right">
                        - $ {{ number_format($total_prices['total_special_discount'], 2, '.', ',') }}</td>
                </tr>
            @endif
        @endif


        @if ($current_folder_name == 'Henglai')
            @if ($total_prices['discount_percentage'] != 0 && $total_prices['gst_percentage'] != 0)
                <tr>
                    <td colspan="4" align="right">GST ( {{ $total_prices['gst_percentage'] }}% ) :</td>
                    <td style="width: 90px;" align="right">
                        - $ {{ number_format($total_prices['total_gst'], 2, '.', ',') }}</td>
                </tr>
            @endif
        @else
            @if ($total_prices['gst_percentage'] != 0)
                <tr>
                    <td colspan="4" align="right">GST ( {{ $total_prices['gst_percentage'] }}% ) :</td>
                    <td style="width: 90px;" align="right">
                        - $ {{ number_format($total_prices['total_gst'], 2, '.', ',') }}</td>
                </tr>
            @endif
        @endif

        <tr>
            <td colspan="4" align="right">Grand Total For The Above Mentioned Renovation Works
                {{ $total_prices['gst_percentage'] != 0 ? 'Inclusive of GST' : '' }}
                :</td>
            <td style="width: 90px;" align="right">
                - $ {{ number_format($total_prices['total_inclusive'], 2, '.', ',') }}</td>
        </tr>
    </tbody>
</table>
