<div class="summary">
    <table border="1" style="border-collapse: collapse; border-color: transparent; width: 100%;">
        @foreach ($sortQuotation as $index => $item)
            @if (isset($item['emptyAOWData']) || count($item['hasAOWData']) != 0)
                <tr class="ft-12">
                    <td style="width: 32px;vertical-align:top;">
                        <span class="ft-b-14 ">{{ chr(65 + $index) }}</span>
                    </td>
                    <td colspan="2" style="width:400px;vertical-align: top;">
                        <span class="ft-12 ft-b">{{ $item['section_name'] }}</span>
                    </td>
                    <td colspan="3" align="center" class="ft-12 ft-b"
                        style="width: 250px;vertical-align: top;">
                        1
                    </td>
                    <td align="right" style="vertical-align: top;" class="ft-b">
                        {{ calculateTotalAmountForEachSections($item['section_id'], $quotationList) }}
                    </td>
                </tr>
            @endif
        @endforeach
    </table>
</div>
<br/>
<br/>
<br/>
<br/>