<tbody class="ft-12" class="payment-breakdown-section">
    <tr>
        <td colspan="3" align="center" class="ft-b-12 bg-lightblue">
            Payment Breakdown
        </td>
        <td align="center" class="ft-b-12 bg-lightblue">
            {{ number_format($total_prices['total_inclusive'], 2, '.', ',') }}
        </td>
    <tr>
        <td colspan="3" align="center">15% Payment - Upon Confirmation</td>
        <td align="center">$ {{ calculateByPercent($total_prices['total_inclusive'], 15) }}</td>
    </tr>
    <tr>
        <td colspan="3" align="center">50% Payment - Before commencement</td>
        <td align="center">$ {{ calculateByPercent($total_prices['total_inclusive'], 50) }}</td>
    </tr>
    <tr>
        <td colspan="3" align="center">30% Payment - Upon Carpentry Delivered
        </td>
        <td align="center">$ {{ calculateByPercent($total_prices['total_inclusive'], 30) }}</td>
    </tr>
    <tr>
        <td colspan="3" align="center">
            <div>5% Payment - Final Balance Upon Completion
                of Works
            </div>
            <div>
                (i.e living condition)
            </div>
        </td>
        <td align="center">$ {{ calculateByPercent($total_prices['total_inclusive'], 5) }}</td>
    </tr>
    </tr>
</tbody>
