<tr key="{{ $item['id'] }}">
    <td style="text-align: center;"> - </td>
    <td class="aow-item">{!! formatText($item['name']) !!}</td>
    <td style="text-align: center;">{{ calculateMeasurement($item) }}</td>
    <td style="text-align: right;">
        @if ($item['price'] != '0')
            $ {{ $item['price'] }}
        @endif
    </td>
    <td style="text-align: right;">
        {{ calculateTotalPrice($item) }}
    </td>
</tr>

@if (!empty($item['items']))
    @foreach ($item['items'] as $subItem)
        @include('pdf.Common.Dorlich.subitems', ['item' => $subItem, 'level' => $level + 1])
    @endforeach
@endif
