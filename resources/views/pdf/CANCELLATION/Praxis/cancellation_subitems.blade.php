<tr key="{{ $item['id'] }}">
    <td align="center" style="vertical-align: top;width: 160px;" class="ft-12">
    </td>
    <td style="width: 500px;" class="ft-12">
        <span class="aow-item">
            <span style="margin-left: {{ 20 * $level }}px;float: left;">- &nbsp;</span> {!! formatText($item['name']) !!}
        </span>
    </td>
    <td class="ft-12" style="min-width:50px;" align="center">
        @if ($item['quantity'] != 0)
            {{ $item['quantity'] }}
        @endif
    </td>
    <td class="ft-12" align="center" style="width: 100px;">
        {{ calculateMeasurement($item) }}
    </td>
    <td class="ft-12" style="min-width:100px;" align="center">
        @if ($item['price'] != 0)
            $ {{ $item['price'] }}
        @endif
    </td>
    <td align="center" class="ft-12" style="min-width:100px;">
        {{ calculateTotalPrice($item) }}
    </td>
</tr>
@if (!empty($item['items']))
    @foreach ($item['items'] as $subItem)
        @include('pdf.CANCELLATION.Praxis.cancellation_subitems', ['item' => $subItem, 'level' => $level + 1])
    @endforeach
@endif
    