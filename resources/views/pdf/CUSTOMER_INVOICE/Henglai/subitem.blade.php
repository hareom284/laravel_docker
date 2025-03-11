<tr key="{{ $item['id'] }}">
    <td align="center" style="vertical-align: top;width: 50px;padding: 2px;" class="ft-12">
        <span> - </span>
    </td>
    <td style="padding: 2px;" class="ft-12">
        <!-- Adjust the width as needed -->
        <span class="line-sp aow-item">{!! formatText($item['name']) !!}</span>
    </td>

    <td style="padding: 2px 5px;" class="ft-12" align="right">
        <div class="left-section">$</div>
        <div>{{ calculateItemTotalPrice($item) }}</div>
    </td>
</tr>

@if (!empty($item['items']) && is_array($item['items']))
    @php
        $subSubIndex = 1; // Initialize sub-sub-item index
    @endphp
    @foreach ($item['items'] as $subSubItem)
        @php
            $subSubCountIndex = $countIndex . '.' . $subSubIndex;
        @endphp
        @include('pdf.CUSTOMER_INVOICE.Henglai.subitem', [
            'item' => $subSubItem,
            'countIndex' => $subSubCountIndex,
            'level' => $level + 1,
        ])
        @php
            $subSubIndex++;
        @endphp
    @endforeach
@endif
