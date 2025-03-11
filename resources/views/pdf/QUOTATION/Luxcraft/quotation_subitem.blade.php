<tr key="{{ $item['id'] }}">
    <td style="vertical-align: top;width: 50px;" class="ft-12">
    </td>
    <td style="width: 600px;" class="ft-12">
        <span class="aow-item">
            <span style="margin-left: {{ 20 * $level }}px;float: left;">- &nbsp;</span> {!! formatText($item['name']) !!}
        </span>
    </td>
    <td style="width: 100px;" align="center" class="ft-12">
        {{ calculateMeasurement($item) }}</td>
    <!-- Adjust width -->

    <td style="width: 100px;" align="right" class="ft-12">
        {{ calculateTotalPrice($item) }}
    </td>
    <!-- Adjust width -->
</tr>
@if (!empty($item['items']))
    @foreach ($item['items'] as $subItem)
        @include('pdf.QUOTATION.Luxcraft.quotation_subitem', ['item' => $subItem, 'level' => $level + 1])
    @endforeach
@endif