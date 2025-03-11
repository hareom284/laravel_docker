<div class="summary-section">
    <table style="border-collapse: collapse; border-color: transparent; width: 100%;font-size:12px;">
        <thead>
            <tr class="bg-gray" style="padding: 10px;">
                <td colspan="3" style="color: #fff; font-weight: bold;">Price Summary</td>
                <td></td>
            </tr>
            <tr>
                <td colspan="4" style="height: 5px;"></td>
            </tr>
        </thead>
        <tbody>
            @foreach ($sortQuotation as $index => $item)
                @if (isset($item['emptyAOWData']) || count($item['hasAOWData']) != 0)
                    <tr>
                        <td colspan="3" class="section-name">
                            <span class="ft-14">[{{ chr(65 + $index) }}]</span><span class="ft-14" style="margin-left: 5px;">{{ $item['section_name'] }}</span>
                        </td>
                        <td align="right">
                            {{ calculateTotalAmountForEachSections($item['section_id'], $quotationList) }}
                        </td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
    <table width="100%" style="margin-top: 140px;" cellspacing="0" border="0">
        <tr>
            <td width="70%" valign="top">
                <table cellpadding="8" cellspacing="0" width="100%">
                    <tr bgcolor="#4D4D4D">
                        <td><font color="white">Special notes and instructions</font></td>
                    </tr>
                    <tr>
                        <td bgcolor="#e0e0e0" height="150">&nbsp;</td>
                    </tr>
                </table>
            </td>
            <td width="30%" valign="top">
                <table width="100%" border="0" cellpadding="3" cellspacing="0">
                    <tr>
                        <td align="right"><strong>SUBTOTAL DISCOUNT</strong></td>
                        <td align="right">${{ number_format($total_prices['only_discount_amount'], 2, '.', ',') }}</td>
                    </tr>
                    <tr>
                        <td height="110"></td>
                    </tr>
                    <tr>
                        <td align="right"><strong>TOTAL</strong></td>
                        <td align="right"><strong>${{ number_format($total_prices['total_special_discount'], 2, '.', ',') }}</strong></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <p style="font-size: 12px; font-weight: bold;">All cheque payments are to be made payable to "Optimum Interior Pte Ltd"</p>
    <p style="font-size: 12px; font-weight: bold;">Bank Transfer to UOB 657-304-051-9/ PAYNOW to UEN 202341627W</p>
    <p style="font-size: 12px;">*In order to ensure protection against scams, please be aware that the Project Manager, Interior Designer</p>
    <p style="font-size: 12px;">and any third part Contractors will <strong>NOT ACCEPT CASH</strong> payments directly from homeowners.</p>
</div>