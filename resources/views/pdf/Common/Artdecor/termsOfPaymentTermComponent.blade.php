@php
$payment_terms=null;
if($quotationList->payment_terms){
$payment_terms = $quotationList->payment_terms ? json_decode($quotationList->payment_terms) : null;
}
@endphp

@if(isset($payment_terms) && $settings['enable_payment_terms'] == 'true')
<table>
    <tr>
        <td style="width: 200px;">Terms of payment to be :</td>
        <td></td>
    </tr>

    @foreach($payment_terms->payment_terms as $paymentTerm)    
    <tr>
        <td></td>
        <td>{{$paymentTerm->payment_percentage}}% {{$paymentTerm->payment_term}}</td>
    </tr>
    @endforeach
</table>
@endif
