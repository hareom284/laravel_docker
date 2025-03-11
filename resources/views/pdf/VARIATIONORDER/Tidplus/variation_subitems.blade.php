{{-- <table > --}}
    <tr key="{{ $item['id'] }}">
        <td style="vertical-align: top;width: 15px; padding-right: 5px;" align="right" class="ft-12"></td>
        <td style="width: 550px;" class="ft-12">
            <!-- Indent sub-items for clarity -->
            <span style="margin-left: {{ 10 * $level }}px;">{!! formatText($item['name']) !!}</span>
        </td>
        <td style="width: 150px;" align="center" class="ft-12">
            {{ calculateMeasurement($item) }}
        </td>
        <td style="width: 80px;" align="right" class="ft-12">
            {{ getDollarSign($item) }}
        </td>
        <td style="width: 80px;" align="right" class="ft-12">
            {{ calculateTotalPrice($item) }}
        </td>
    </tr>
    {{-- </table> --}}
    @if (!empty($item['items']))
        @php
            $subSubIndex = 1; // Initialize sub-sub-item index
        @endphp
        @foreach ($item['items'] as $subSubItem)
            @php
                // Sub-sub-item index as parentIndex.subIndex.subSubIndex (e.g., 1.1.1, 1.1.2)
                $subSubCountIndex = $countIndex . '.' . $subSubIndex;
            @endphp
            @include('pdf.VARIATIONORDER.Tidplus.variation_subitems', [
                'item' => $subSubItem,
                'countIndex' => $subSubCountIndex, // Pass sub-sub-item numbering
                'level' => $level + 1,
            ])
            @php
                $subSubIndex++; // Increment sub-sub-item index
            @endphp
        @endforeach
    @endif
    