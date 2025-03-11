{{-- <table > --}}
<tr key="{{ $item['id'] }}">
    <td style="width: 600px;" class="ft-12">
        <!-- Indent sub-items for clarity -->
        <span style="margin-left: {{ 10 * $level }}px;">> {!! formatText($item['name']) !!}</span>
    </td>
    <td style="width: 100px;" align="center" class="ft-12">
    
    </td>
    <td style="width: 100px;" align="right" class="ft-12">
        {{ calculateTotalPrice($item) }}
    </td>
</tr>
{{-- </table> --}}
@if (!empty($item['items']))
    @foreach ($item['items'] as $subItem)
        @include('pdf.QUOTATION.Optimum.quotation_subitems', ['item' => $subItem, 'level' => $level + 1])
    @endforeach
@endif
