<div class="summary-section content">
    <table border="1" style="border-collapse: collapse; border-color: transparent; width: 100%;">
        <thead>
            <tr class="head-row">
                <th style="width: 5%">S/N</th>
                <th style="width: 15%">Location</th>
                <th align="left" style="width: 50%;">Description</th>
                <th style="width: 5%">QTY</th>
                <th style="width: 5%">UOM</th>
                <th style="width: 10%">Unit Price($)</th>
                <th style="width: 10%">Total($)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th colspan="7" style="height: 10px"></th>
            </tr>
            <tr>
                <th colspan="7" align="center">Summary</th>
            </tr>
            @foreach ($sortQuotation as $index => $item)
                @if (isset($item['emptyAOWData']) || count($item['hasAOWData']) != 0)
                    <tr class="bg-gray">
                        <td align="center">
                            <span class="ft-b">{{ $index + 1 }}</span>
                        </td>
                        <td colspan="5">
                            <span>{{ $item['section_name'] }}</span>
                        </td>
                        <td align="center">
                            {{ calculateTotalAmountForEachSections($item['section_id'], $quotationList) }}
                        </td>
                    </tr>
                @endif
            @endforeach
            @php
                $dynamicRows = 4;
                if ($total_prices['discount_percentage'] == 0) {
                    $dynamicRows = $dynamicRows - 2;
                } elseif ($total_prices['gst_percentage'] == 0) {
                    $dynamicRows = $dynamicRows - 2;
                }
            @endphp
            <tr>
                <td rowspan="{{ $dynamicRows }}"></td>
                <td rowspan="{{ $dynamicRows }}" colspan="3"></td>
                <td colspan="2" align="right">Contract Sum:</td>
                <td align="center">
                    {{ number_format($total_prices['total_all_amount'], 2, '.', ',') }}</td>
            </tr>
            @if ($total_prices['discount_percentage'] != 0)
                <tr>
                    <td colspan="2" align="right">Less {{ $total_prices['discount_percentage'] }}%
                        discount:</td>
                    <td align="center">
                       - {{ number_format($total_prices['total_all_amount'] - $total_prices['total_special_discount'], 2, '.', ',') }}
                    </td>
                </tr>
                <tr>
                    <td colspan="2" align="right">After {{ $total_prices['discount_percentage'] }}%
                        discount:</td>
                    <td align="center">
                        {{ number_format($total_prices['total_special_discount'], 2, '.', ',') }}
                    </td>
                </tr>
            @endif
            @if ($total_prices['gst_percentage'] != 0)
                <tr>
                    <td colspan="2" align="right">GST ( {{ $total_prices['gst_percentage'] }}% ):</td>
                    <td align="center">{{ number_format($total_prices['total_gst'], 2, '.', ',') }}</td>
                </tr>
            @endif
            <tr class="grand-total">
                <td colspan="6" class="ft-b" align="right">Grand Total:</td>
                <td class="ft-b" align="center">
                    {{ number_format($total_prices['total_inclusive'], 2, '.', ',') }}</td>
            </tr>
        </tbody>
    </table>
</div>
