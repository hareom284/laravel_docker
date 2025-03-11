<tr key="{{ $item['id'] }}">
    <td style="width: 50px"></td>
    <td style="width: 600px;" class="ft-12">
        <span class="aow-item">
            <span style="margin-left: {{ 20 * $level }}px;float: left;">- &nbsp;</span>
            @if ($settings['enable_sub_description_feature'] == 'true' && $item['sub_description'] != null)
                {!! formatText($item['sub_description']) !!}
            @else
                {!! formatText($item['name']) !!}
            @endif
        </span>
    </td>
    <td style="width: 100px;" align="center" class="ft-12">
        {{ $item['quantity'] == 0 ? '' : $item['quantity'] }}</td>
    <td style="width: 100px;" align="center" class="ft-12">
        {{ calculateMeasurement($item) }}</td>
    <!-- Adjust width -->
    <td style="width: 100px;" align="center" class="ft-12">
        {{ getUnitPrice($item) }}</td>
    <td style="width: 100px;" align="center" class="ft-12">
        {{ calculateTotalPrice($item) ? '$' : null }}</td>

    <td style="width: 100px;" align="right" class="ft-12">
        {{ calculateTotalPrice($item) }}
    </td>
</tr>
@if (!empty($item['items']))
    @foreach ($item['items'] as $subItem)
        @include('pdf.VARIATIONORDER.Intheory.variation_subitems', [
            'item' => $subItem,
            'level' => $level + 1,
        ])
    @endforeach
@endif
