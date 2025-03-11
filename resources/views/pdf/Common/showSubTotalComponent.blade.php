<table border="1"
style="border-collapse: collapse; border-color: transparent; width: 100%;">

@if (calculateTotalAmountForEachSections($item['section_id'], $quotationList))
    <tr>
        <td colspan="2"></td>
        @if ($item['hasAOWData'][0]['area_of_work_items'][0]['calculation_type'] != 'NORMAL')
            <td style="width: 200px;" align="right" class="{{ isset($is_bold) && $is_bold ? 'ft-b-12' : 'ft-12' }}">
                {{ $name }}</td>
            <!-- Adjust width -->

            <td style="width: 100px;" align="right" class="{{ isset($is_bold) && $is_bold ? 'ft-b-12' : 'ft-12' }}">
                {{ calculateTotalAmountForEachSections($item['section_id'], $quotationList) }}
            </td>
        @endif
    </tr>
@endif
</table>