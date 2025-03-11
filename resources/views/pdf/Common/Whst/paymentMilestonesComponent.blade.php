<style>
    .payment-table {
        width: 100%;
        border-collapse: collapse;
        border-color: transparent;
    }
    .payment-table,
    .payment-table th,
    .payment-table td {
        border: 0.1px solid #ccc !important;
        padding: 5px;
    }

    .no-border,
    .no-border th,
    .no-border td {
        border: 0 !important;
    }
</style>
@php
    $payment_terms = null;
    if ($quotationList->payment_terms) {
        $payment_terms = $quotationList->payment_terms ? json_decode($quotationList->payment_terms) : null;
    }
@endphp
@if (isset($payment_terms) && $settings['enable_payment_terms'] == 'true')
    <div class="payment-percentage ft-12 avoid-break" style="margin-bottom: 15px;">
        <table class="payment-table">
            <tr>
                <td style="width: 50px" align="center">Sr</td>
                <td>Description</td>
                <td>Due Date</td>
                <td align="right" >Invoice Portion</td>
                <td align="right">Payment Amount</td>
            </tr>
            @if ($payment_terms)
                @foreach ($payment_terms->payment_terms as $index => $paymentTerm)
                    <tr>
                        <td align="center">{{ $index + 1 }}</td>
                        <td style="width: 50%;">{{ $paymentTerm->payment_term }}</td>
                        <td>{{ $paymentTerm->estimated_date }}</td>
                        <td align="right">{{ $paymentTerm->payment_percentage }}%</td>
                        <td class="percentage-total" align="right">
                            <table class="no-border">
                                <tr>
                                    <td align="right" style="width: 100px;">
                                        $ {{ calculateByPercent($total_prices['total_inclusive'], $paymentTerm->payment_percentage) }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                @endforeach
            @endif
        </table>
    </div>
@endif
