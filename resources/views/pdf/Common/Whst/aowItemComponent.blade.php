<style>
    .line-sp ul{
        list-style-type: decimal !important;
    }
</style>
<tr key="{{ $hasAOW['id'] }}">
    <td style="border: 0.1px solid #ccc !important; padding: 5px; width: 5%;">
        <span> {{ $globalIndex }}</span>
    </td>
    <td style="border: 0.1px solid #ccc !important; padding: 5px; width: 40%;">
        <!-- Adjust the width as needed -->
        <span class="line-sp aow-item">
            {!! formatText($hasAOW['name']) !!}
        </span>
    </td>
    <td style="border: 0.1px solid #ccc !important; padding: 5px; width: 10%;">
        {{ $value['area_of_work_name'] }}
    </td>
    <td style="border: 0.1px solid #ccc !important; padding: 5px; width: 10%;">
        {{ $item['section_name'] }}
    </td>
    <td style="border: 0.1px solid #ccc !important; padding: 5px; width: 8%;" align="right">
        {{ calculateMeasurement($hasAOW) }}
    </td>
    @if ($settings['enable_show_selling_price'] == 'true')
    <td style="border: 0.1px solid #ccc !important; padding: 5px; width: 8%" align="right">
        @if($doc_type == 'CANCELLATION')
        - {{ $hasAOW['price'] }}
        @else
        {{ $hasAOW['price'] }}
        @endif
    </td>
    @endif
    <td style="border: 0.1px solid #ccc !important; padding: 5px; width: 8%" align="right">
        @if($doc_type == 'CANCELLATION')
        - {{ calculateTotalPrice($hasAOW) }}
        @else
        {{ calculateTotalPrice($hasAOW) }}
        @endif
    </td>
</tr>