<style>
    .basic-chip {
        padding: 5px 10px;
        border-radius: 50px;
        display: inline-flex;
        margin: 5px;
    }

    .background-orange {
        background: #FFC8AA;
        color: #000;
    }

    .background-grey {
        background: #dddddd;
        color: #616161;
    }

    .small-chip {
        width: 30%;
    }

    .text-sm {
        font-size: 12px;
    }
</style>
<tr>
    <th style="width: 50px;" rowspan="11"></th>
    <th colspan="3" align="center" class="text-sm"><i>"Building Dreams, Building Trust: Your Reliable Partner in
            Singapore
            Construction"
        </i></th>
</tr>
<tr>
    <td rowspan="4">
        <span>
            <img src="{{ 'data:image/png;base64,' . $quotationData['companies']['company_logo'] }}"
                style="width: auto;height:110px">
        </span>
        <span>
            <img src="{{ public_path() . '/images/bcav2.png' }}" height="100" />
            <img src="{{ public_path() . '/images/bizsafev2.png' }}" height="100" />
        </span>
    </td>
    <td style="height: 20px" colspan="2"></td>
</tr>
<tr>
    <td colspan="2" align="right" class="text-sm">{{ $quotationData['companies']['email'] }} | +65
        {{ $quotationData['companies']['fax'] }}</td>
</tr>
<tr>
    <td align="right" colspan="2" class="text-sm">
        <div>{{ $quotationData['companies']['main_office'] }}</div>
        <div>UEN :
            @if (isset($quotationData['companies']['gst_reg_no']) && $quotationData['companies']['gst_reg_no'] != '')
                {{ $quotationData['companies']['gst_reg_no'] }}
            @else
                {{ $quotationData['companies']['reg_no'] }}
            @endif
        </div>
    </td>
</tr>
<tr>
    <td colspan="2" style="height: 20px"></td>
</tr>
<tr>
    <td colspan="3" style="height: 20px;"></td>
</tr>
<tr>
    <td rowspan="2">
        <div class="basic-chip background-orange">Budgetary Quote</div>
    </td>
    <td>
        <div class="basic-chip background-grey ft-b">{{ $type }}:</div>
    </td>
    <td align="center">{{ $quotationData['project']['agreement_no'] }}</td>
</tr>
<tr>
    <td align="right" class="ft-b">Date: </td>
    <td align="center">
        @if (isset($quotationData['signed_date']))
            <span>{{ convertDate($quotationData['signed_date']) }}</span>
        @else
            <span>{{ convertDate($quotationData['created_at']) }}</span>
        @endif
    </td>
</tr>
<tr>
    <td></td>
    <td align="right" class="ft-b">Terms: </td>
    <td align="center">14 Days</td>
</tr>
<tr>
    <td style="height: 20px;"></td>
    <td rowspan="2" colspan="2" style="height: 20px;"></td>
</tr>
<tr>
    <td style="height: 20px;"></td>
</tr>
<tr>
    <td colspan="4" style="height: 20px;"></td>
</tr>
