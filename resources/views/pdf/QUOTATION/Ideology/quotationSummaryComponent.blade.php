<style>
    .bg-yellow {
        background-color: yellow;
    }
    .summary-section td {
        padding: 3px 10px;
    }
</style>
<div class="summary-section">
    <p>Services descriptions by sections and detailed costings for the following:</p>
    <table style="border-collapse: collapse; border-color: transparent; width: 100%;font-size:12px;">
        <tbody>
            @foreach ($sortQuotation as $index => $item)
                @if (isset($item['emptyAOWData']) || count($item['hasAOWData']) != 0)
                    <tr>
                        <td style="width: 20px;">
                            <span class="ft-b-14 ">{{ chr(65 + $index) }}</span>
                        </td>
                        <td colspan="3" class="section-name">
                            <span class="ft-b-14">{{ $item['section_name'] }}</span>
                        </td>
                        <td>$</td>
                        <td align="right">
                            {{ calculateTotalAmountForEachSections($item['section_id'], $quotationList) }}
                        </td>
                    </tr>
                @endif
            @endforeach
        </tbody>
        <tbody class="ft-b-12">
            <tr>
                <td colspan="4" align="right">Total Sum :</td>
                <td class="bg-yellow">$</td>
                <td style="width: 100px;" align="right" class="border-b bg-yellow">
                    {{ number_format($total_prices['total_all_amount'], 2, '.', ',') }}</td>
            </tr>

            <tr>
                <td colspan="4" align="right">Lunp sum Discount :</td>
                <td>$</td>
                <td style="width: 100px;" align="right" class="border-b">
                    @if ($total_prices['discount_percentage'] != 0)
                    {{ number_format($total_prices['only_discount_amount'], 2, '.', ',') }}
                    @endif
                </td>
            </tr>
            @if ($total_prices['gst_percentage'] != 0)
            <tr>
                <td colspan="4" align="right">GST ( {{ $total_prices['gst_percentage'] }}% ) :</td>
                <td>$</td>
                <td style="width: 100px;" align="right" class="border-b">
                    {{ number_format($total_prices['total_gst'], 2, '.', ',') }}</td>
            </tr>
            @endif
            <tr>
                <td colspan="4" align="right">Total :</td>
                <td>$</td>
                <td style="width: 100px;" align="right" class="border-b">
                    {{ number_format($total_prices['total_inclusive'], 2, '.', ',') }}</td>
            </tr>
        </tbody>
    </table>
</div>
