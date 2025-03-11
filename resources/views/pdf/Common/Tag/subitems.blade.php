<tr key="{{ $item['id'] }}">
    <td align="center">
        <span> {{ $countIndex }}</span>
    </td>
    <td align="center">
        {{ $value['area_of_work_name'] }}
    </td>
    <td>
        <!-- Adjust the width as needed -->
        <span class="line-sp aow-item">{!! formatText($item['name']) !!}</span>
    </td>

    <td align="center">
        {{ $item['quantity'] }}
    </td>
    <td align="center">
        {{ $item['measurement'] }}
    </td>
    <td align="center">
        {{ $item['price'] }}
    </td>
    <td align="center">
       @if(isset($cancellation)) - @endif {{ calculateTotalPrice($item) }}</td>
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
        @include('pdf.Common.Tag.subitems', [
            'item' => $subSubItem,
            'countIndex' => $subSubCountIndex,
            'level' => $level + 1,
        ])
        @php
            $subSubIndex++;
        @endphp
    @endforeach
@endif
