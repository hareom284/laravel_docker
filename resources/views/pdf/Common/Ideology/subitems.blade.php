<table>
    <tr key="{{ $item['id'] }}">
        <td style="vertical-align: top;width: 40px;" class="ft-12">
            <span>-</span>
        </td>
        <td style="width: 500px;" class="ft-12">
            <!-- Adjust the width as needed -->
            <span class="aow-item">{!! formatText($item['name']) !!}</span>
        </td>
        <td class="ft-12" style="min-width:50px;" align="center">
            @if ($item['quantity'] != 0)
                {{ $item['quantity'] }}
            @endif
        </td>
        <td class="ft-12" align="center" style="width: 100px;">
            {{ calculateMeasurement($item) }}
        </td>
        <td class="ft-12" style="min-width:50px;" align="center">
            $
        </td>

        <td align="center" class="ft-12" style="min-width:100px;">
            {{ calculateTotalPrice($item) }}
        </td>
    </tr>
    {{-- </table> --}}
    @if (!empty($item['items']))
        @foreach ($item['items'] as $subItem)
            @include('pdf.Common.Ideology.subitems', [
                'item' => $subItem,
                'level' => $level + 1,
            ])
        @endforeach
    @endif
