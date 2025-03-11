<tr key="{{ $item['id'] }}">
    <td style="padding-bottom: 8px; text-align: center;">
        {{-- {{ $sectionIndex + 1 . '.' . $itemCounter . '.' . $subItemCounter }} --}}
    </td>
    <td style="padding-bottom: 8px;">
        <span class="aow-item">
            <span style="margin-left: {{ 8 * $level }}px;float: left;">- &nbsp;</span> {!! formatText($item['name']) !!}
        </span>
    </td>
    <td style="text-align: center; padding-bottom: 8px;">{{ getQty($item) }}</td>
    <td style="text-align: center; padding-bottom: 8px;">FOC
    </td>
</tr>
@if (!empty($item['items']))
    @foreach ($item['items'] as $subItem)
        @include('pdf.FOC.SaltRoom.foc_subitems', ['item' => $subItem, 'level' => $level + 1])
    @endforeach
@endif
