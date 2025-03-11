<div class="signature-section page" style="position: relative;padding-top: 500px;">
    <table class="ft-12 avoid-break" style="float: left;">
        <tbody>
            <tr>
                <td>
                    <span class="ft-b-14">A/C NO.: OCBC BANK : 628 253 056 001</span>
                </td>
            </tr>
            <tr>
                <td>
                    Kindly quote our invoice number at the back of the cheque(s)
                </td>
            </tr>
            <tr>
                <td>
                    <span class="ft-b-14">PAYNOW TO UEN : @if (isset($quotationData['companies']['gst_reg_no']) && $quotationData['companies']['gst_reg_no'] != '')
                            {{ $quotationData['companies']['gst_reg_no'] }}
                        @else
                            {{ $quotationData['companies']['reg_no'] }}
                        @endif
                    </span>
                </td>
            </tr>
        </tbody>
    </table>
    <div style="float: right;" class="avoid-break">
        @if ($quotationData['already_sign'])
            @foreach ($quotationData['customer_signature'] as $customer)
                <table class="ft-12" style="float: right;">
                    <tbody>
                        <tr>
                            <td>
                                <span>For Brightway Engineering Technology Pte Ltd</span>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <img src="{{ 'data:image/png;base64,' . $customer['customer_signature'] }}"
                                    style="height:100px;border-bottom: 1px solid black;">
                            </td>
                        </tr>
                    </tbody>
                </table>
            @endforeach
        @else
            <div style="height: 200px; width:200px;"></div>
        @endif
    </div>
</div>
<br />
<br />
<div style="width: 100%;text-align:center;clear:both;">
    <span class="text-xl text-blue">E.&.O.E</span>
</div>
