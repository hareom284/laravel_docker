<table border="0" style="width: 100%;" class="percentage-section">
    <tr>
        <td></td>
        <td align="right" >
            <span class="underline-blue text-blue ft-b-14">Percentage</span>
        </td>
        <td colspan="2" style="padding-left: 10px;">
            <span class="underline-blue text-blue ft-b-14">Amount</span>
        </td>
    </tr>
    <tr>
        <td>* 60% Down payment upon confirmation</td>
        <td align="right" style="width: 370px;" class="ft-b-14 text-blue">UPON CONFIRMATION 60% : </td>
        <td style="width: 20px;" align="right" class="ft-b-14 text-blue">$</td>
        <td align="right" class="ft-b-14 text-blue"> {{ calculateByPercent($total_prices['total_inclusive'],60) }}</td>
    </tr>
    <tr>
        <td>* Quote valid for 30days from quote date </td>
        <td align="right" class="ft-b-14 text-blue">UPON COMMENCEMENT 30% : </td>
        <td align="right" class="ft-b-14 text-blue">$</td>
        <td align="right" class="ft-b-14 text-blue"> {{ calculateByPercent($total_prices['total_inclusive'],30) }}</td>
    </tr>
    <tr>
        <td>All cheque should be made payble to:</td>
        <td align="right" class="ft-b-14 text-blue">UPON COMPLETION 10%  : </td>
        <td align="right" class="ft-b-14 text-blue">$</td>
        <td align="right" class="ft-b-14 text-blue"> {{ calculateByPercent($total_prices['total_inclusive'],10) }}</td>
    </tr>
    <tr>
        <td class="ft-b-14" style="width: 500px;">{{ $quotationData['companies']['name'] }}</td>
        <td>
            <span style="padding-left: 120px;">Authorized Signatory</span>
        </td>
        <td></td>
        <td></td>
    </tr>
</table>
