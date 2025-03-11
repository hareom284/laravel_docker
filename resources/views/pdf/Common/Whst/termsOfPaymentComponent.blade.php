<style>
    li::before {
        counter-increment: item;
        content: counter(item) '. ' !important;
        position: absolute;
        left: 0;
    }

    .payment-terms-text p {
        margin: 0 !important;
        padding: 0 !important;
    }
    .payment-terms-text ol {
        padding-bottom: 0 !important;
        margin-bottom: 0 !important;
    }

    .payment-terms-text {
        font-size: 14px;
    }

    .terms p{
        margin: 0 !important;
        padding: 0 !important;
    }
</style>
<div class="ft-12 avoid-break">
    <div class="payment-terms-text" style="white-space: pre-wrap;">{!! trim($quotationData['payment_terms_text']) !!}</div>
    <span>
        @if($quotationData['companies']['qr_code'])
        <img src="{{ $quotationData['companies']['qr_code'] }}" style="width: auto;height:150px;padding-left: 25px;">
        @endif
    </span>
    <div class="terms">{!! $terms !!}</div>
</div>
