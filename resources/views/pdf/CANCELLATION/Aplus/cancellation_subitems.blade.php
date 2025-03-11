<tr key="{{ $hasAOW['id'] }}">
    <td style="padding: 10px;width: 75%" class="ft-12" colspan="3">
        <span class="line-sp aow-item">{!! formatText($item['name']) !!}</span>
    </td>
    <td style="padding: 10px;width: 25%;" class="ft-12">
        {{ calculateMeasurement($item) }}
    </td>
</tr>

@if (!empty($item['items']))
    @foreach ($item['items'] as $subItem)
        @include('pdf.CANCELLATION.Aplus.cancellation_subitems', ['item' => $subItem, 'level' => $level + 1])
    @endforeach
@endif
