<style>
.payment-table, .payment-table th, .payment-table td {
    border: 1px solid black;
    border-collapse: collapse;
    }
.praxis-payment li {
    padding-bottom: 15px;
}
</style>
@php
$payment_terms=null;
if($quotationList->payment_terms){
$payment_terms = $quotationList->payment_terms ? json_decode($quotationList->payment_terms) : null;
}
@endphp
<div class="avoid-break">
    <table border="1" style="border-collapse: collapse; border-color: transparent; width: 100%;">
        <tr style="background: #000;color: #fff">
            <td colspan="6" align="center" style="vertical-align: top;width: 120px;" class="ft-b">
                PAYMENT TERMS:
            </td>
        </tr>
    </table>
    <div class="content" style="padding: 10px 30px;">
    <table class="tearms hide_header_and_footer" style="width:100%;font-size:12px;">
            <tbody>
                <tr>
                    <td colspan="6" align="left" class="terms-text">
                        <ol class="praxis-payment" style="padding-left: 15px;margin-left: 15px;">
                            <li>All amount quoted are in Singapore Currency only and Payments to be made via PAYNOW to " ". Payment shall be transacted within
                                from Date of Invoice.
                            </li>
                            <li>
                                Payments shall be based on signed Quotation, other payment terms may apply for any additional or reimbursements
                            </li>
                            @if($payment_terms)
                            <li style="position: relative;min-height: 100px;"> <table class="payment-table" style="width: 60%;position:absolute;left:0;top:0;">
                                    <tr>
                                        <td class="ft-b">Payment Schedule</td>
                                        <td class="ft-b" style="width: 90px" align="center">Percentile</td>
                                    </tr>
                                    @foreach($payment_terms->payment_terms as $paymentTerm)
                                    <tr>
                                        <td>{{$paymentTerm->payment_term}}</td>
                                        <td align="center">{{$paymentTerm->payment_percentage}}%</td>

                                    </tr>
                                    @endforeach
                                </table>
                            </li>
                            @endif
                            <li>
                                <span class="ft-b">For Progressive Claims</span><br/>
                                NA
                            </li>
                            <li>
                                <span class="ft-b">Late Payment</span><br/>
                                In the event of late payment, an interest of ten percent (10%) per annum and any legal fee will be charged on the outstanding amount
                            </li>
                            <li>
                                <span class="ft-b">Disputes</span><br/>
                                Any disputes related to the contract to be resolved by amicable discussions between the parties.<br/>
                                If both parties are not agreeable with each other within 30 days, the dispute will be resolved by seeking laywer's advices.<br/>
                                Any legal fees will be borne by the offending party.
                            </li>
                        </ol>
                    </td>
                </tr>
            </tbody>
    </table>
    </div>
</div>
