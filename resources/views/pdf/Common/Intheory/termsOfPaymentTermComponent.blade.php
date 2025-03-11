@php
    $payment_terms = null;
    if ($quotationList->payment_terms) {
        $payment_terms = $quotationList->payment_terms ? json_decode($quotationList->payment_terms) : null;
    }
@endphp
<div class="terms" style="margin-top: 20px;">
    <p style="font-size: 13px; font-weight: bold; font-style: underline;" class="underline">TERMS & CONDITIONS</p>
    <p>a. Quote validity: 7 days</p>
    <p>b. Warranty: 2 years</p>
    <p>c. Payment Terms:</p>
    {{-- <p>&nbsp;&nbsp;&nbsp;i) 10% deposit upon confirmation</p>
    <p>&nbsp;&nbsp;&nbsp;ii) 40% upon commencement of Works Agree</p>
    <p>&nbsp;&nbsp;&nbsp;iii) 45% Before Carpentry and fabrication commencement</p>
    <p>&nbsp;&nbsp;&nbsp;iv) 5% Upon handover</p> --}}
    @if (isset($payment_terms) && $settings['enable_payment_terms'] == 'true')
        @foreach ($payment_terms->payment_terms as $key => $paymentTerm)
            <p>&nbsp;&nbsp;&nbsp;{{ $key + 1 . ')' }} {{ $paymentTerm->payment_percentage }}%
                {{ $paymentTerm->payment_term }}</p>
        @endforeach
    @endif
    <p>
        &nbsp;&nbsp;&nbsp;Last payment to be made within 7 days to avoid 10% late payment charges
    </p>
    <p>d. Any scope of work not specified in Contract will be treated as a VO and reasonable time will be given to
        complete
    </p>
    <p>e. Payment method:</p>
    <p>&nbsp;&nbsp;&nbsp;Cheque - {{ $quotationData['companies']['name'] }}</p>
    <p>&nbsp;&nbsp;&nbsp;Bank Transfer - OCBC BANK - 595050899001</p>
    <p>&nbsp;&nbsp;&nbsp;Paynow - UEN - @if (isset($quotationData['companies']['gst_reg_no']) && $quotationData['companies']['gst_reg_no'] != '')
            {{ $quotationData['companies']['gst_reg_no'] }}
        @else
            {{ $quotationData['companies']['reg_no'] }}
        @endif
    </p>
    <p>&nbsp;&nbsp;&nbsp;@if ($quotationData['companies']['qr_code'])
            <img src="{{ $quotationData['companies']['qr_code'] }}" height="100" />
        @endif
    </p>
    <p>f. If hacking works require the services of a Professional Engineer (PE) or Licensed Electrical Worker (LEW)<br>
        / Licensed Plumber (LP), will be borne by the client</p>
</div>
