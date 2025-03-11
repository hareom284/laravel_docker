{{-- <table > --}}
<tr key="{{ $item['id'] }}">
    <td style="width: 50px"></td>
    <td style="width: 600px;" class="ft-12">
        <!-- Indent sub-items for clarity -->
        <span class="aow-item">
            <span style="margin-left: {{ 20 * $level }}px;float: left;">- &nbsp;</span> {!! formatText($item['name']) !!}
        </span>
    </td>
    <td style="width: 100px;" align="center" class="ft-12">
        {{ calculateMeasurement($item) }}
    </td>
    <td style="width: 100px;" align="right" class="ft-12">
        {{ calculateTotalPrice($item) }}
    </td>
</tr>
{{-- </table> --}}
@if (!empty($item['items']))
    @foreach ($item['items'] as $subItem)
        @include('pdf.QUOTATION.Twp.quotation_subitems', ['item' => $subItem, 'level' => $level + 1])
    @endforeach
@endif
