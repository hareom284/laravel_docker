@php
$payment_terms=null;
if($quotationList->payment_terms){
$payment_terms = $quotationList->payment_terms ? json_decode($quotationList->payment_terms) : null;
}
@endphp

@if(isset($payment_terms) && $settings['enable_payment_terms'] == 'true')
<div class="payment-percentage">
    <table>
        <tr>
            <td style="width: 150px" align="center">Payment Percentage</td>
            <td>Payment Terms</td>
            <td>Amount Payable</td>
        </tr>
        @foreach($payment_terms->payment_terms as $paymentTerm)
        <tr>
            <td align="center">{{$paymentTerm->payment_percentage}}%</td>
            <td>{{$paymentTerm->payment_term}}</td>
            <td class="percentage-total">
                <table>
                    <tr>
                        <td>$</td>
                        <td align="right">
                            {{ calculateByPercent($total_prices['total_inclusive'], $paymentTerm->payment_percentage) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
        @endforeach
        <tr>
            <td colspan="2" align="right">Total Amount Payable</td>
            <td class="percentage-total">
                <table>
                    <tr>
                        <td>$</td>
                        <td align="right">
                            {{ number_format($total_prices['total_inclusive'], 2, '.', ',') }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>
@endif