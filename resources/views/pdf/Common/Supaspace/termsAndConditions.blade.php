<style>
    .top-align {
        vertical-align: top;
    }

    .border-tb {
        border-top: 2px solid black;
        border-bottom: 2px solid black;
        padding: 3px 0;
    }

    .text-italic {
        font-style: italic;
    }
</style>
<table class="page">
    {{-- <tbody class="note text-italic">
        <tr>
            <td class="top-align" align="center"></td>
            <td colspan="2">
                * This quotation <span class="ft-b">does not include electrical works</span> unless otherwise stated
            </td>
        </tr>
        <tr>
            <td class="top-align" align="center"></td>
            <td colspan="2">
                * Any renovation, security deposits and/or bonds required by various agencies for the course of
                renovation to be borne by Client.
            </td>
        </tr>
        <tr>
            <td class="top-align" align="center"></td>
            <td colspan="2">
                * Any items not stated in our Contract will be charged accordingly as Variation Order.
            </td>
        </tr>
        <tr>
            <td class="top-align" align="center"></td>
            <td colspan="2">
                * Any items not stated in our Contract will be charged accordingly as Variation Order
            </td>
        </tr>
        <tr>
            <td colspan="3" style="height: 5px;"></td>
        </tr>
    </tbody>

    <tbody class="term-of-pay">
        <tr class="bg-gray">
            <th colspan="3" align="left" style="padding: 5px 10px 5px 35px;">TERMS OF PAYMENT</th>
        </tr>
        <tr>
            <td style="width: 30px;" class="top-align" align="center"></td>
            <th align="left">
                10% - Upon Confirmation
            </th>
            <td align="right">
                ${{ calculateByPercent($total_prices['total_special_discount'], 10) }}
            </td>
        </tr>
        <tr>
            <td style="width: 30px;" class="top-align" align="center"></td>
            <th align="left">
                40% - Before Commencement of Works
            </th>
            <td align="right">
                ${{ calculateByPercent($total_prices['total_special_discount'], 40) }}
            </td>
        </tr>
        <tr>
            <td style="width: 30px;" class="top-align" align="center"></td>
            <th align="left">
                30% - Upon Completion of Wet Works
            </th>
            <td align="right">
                ${{ calculateByPercent($total_prices['total_special_discount'], 30) }}
            </td>
        </tr>
        <tr>
            <td style="width: 30px;" class="top-align" align="center"></td>
            <th align="left">
                15% - Before lnstallation Of Carpentry Works
            </th>
            <td align="right">
                ${{ calculateByPercent($total_prices['total_special_discount'], 15) }}
            </td>
        </tr>
        <tr>
            <td style="width: 30px;" class="top-align" align="center"></td>
            <th align="left">
                5% - Upon Completion Of Works
            </th>
            <td align="right">
                ${{ calculateByPercent($total_prices['total_special_discount'], 5) }}
            </td>
        </tr>
        <tr>
            <td colspan="3" align="right" class="ft-b" style="padding-top: 5px;">Grand Total: <span class="border-tb"
                    style="margin-left: 30px;">${{ number_format($total_prices['total_special_discount'], 2, '.', ',') }}</span>
            </td>
        </tr>
        <tr>
            <td colspan="3" style="height: 5px;"></td>
        </tr>
    </tbody> --}}

    <tbody class="term-&-conditions">

        <tr class="bg-gray">
            <th colspan="3" align="left" style="padding: 5px 10px 5px 35px;">TERMS & CONDITIONS</th>
        </tr>
        <tr>
            <td style="width: 30px;" class="top-align" align="center">1.</td>
            <td colspan="2">
                This contract is strictly based on the scope of work, bill of material, specification intent,
                and rating of products proposed
            </td>
        </tr>
        <tr>
            <td class="top-align" align="center">2.</td>
            <td colspan="2">
                All prices quoted are based on Singapore dollars (S$) unless otherwise stated.
            </td>
        </tr>
        <tr>
            <td class="top-align" align="center">3.</td>
            <td colspan="2">
                Any additional work other than those stated in this contract shall be deemed as a variation order and
                will
                be chargeable.
            </td>
        </tr>
        <tr>
            <td class="top-align" align="center">4.</td>
            <td colspan="2">
                A separate invoice will be issued for any additional or variation items that were authorized or verbally
                agreed by you/your company
            </td>
        </tr>
        <tr>
            <td class="top-align" align="center">5.</td>
            <td colspan="2">
                All goods remain the property of SUPASPACE PTE. LTD. until full payment is received.
            </td>
        </tr>
        <tr>
            <td class="top-align" align="center">6.</td>
            <td colspan="2">
                All construction drawings and/or revisions have to be approved and endorsed by you/your company before
                work
                can commence.
            </td>
        </tr>
        <tr>
            <td class="top-align" align="center">7.</td>
            <td colspan="2">
                Any cancellation of items from this contract during the contract period must be agreed upon by both
                parties
                before the commencement of work or order
            </td>
        </tr>
        <tr>
            <td class="top-align" align="center">8.</td>
            <td colspan="2">
                Should the contract be aborted, charges will be made according to the works that have already been
                completed
                or materials already purchased.
            </td>
        </tr>
        <tr>
            <td class="top-align" align="center">9.</td>
            <td colspan="2">
                All cheque payments are to be crossed and made payable to SUPASPACE PTE. LTD.
            </td>
        </tr>
        <tr>
            <td class="top-align" align="center">10.</td>
            <td colspan="2" class="tog-align">
                <div class="ft-b">By Internet Banking/ Bank Transfer:</div>
                <div>Bank Name: Oversea-Chinese Banking Corporation Limited</div>
                <div>Account no: 596855296001</div>
            </td>
        </tr>
        <tr>
            <td align="center">11.</td>
            <td>
                <div class="ft-b">By PAYNOW:</div>
                <div>
                    Scan the QR code provided using your Banking App or key in SUPASPACE's UEN Number:
                </div>
                <div>
                    202407921E
                </div>
            </td>
            <td style="padding-right: 90px;">
                <img src="{{ public_path() . '/images/supaspace_paynow_qr.jpg' }}" height="100" />
            </td>
        </tr>
        <tr>
            <td class="top-align" align="center">12.</td>
            <td colspan="2">
                Under the condition that the progressive payment is not being made, SUPASPACE PTE. LTD. reserves the
                rights
                to stop work at any point in
                time and only resume once payment is received. Consequently, SUPASPACE PTE. LTD. cannot be penalized or
                held
                liable for any delays in
                project completion as a result of such a situation.
            </td>
        </tr>
        <tr>
            <td class="top-align" align="center">13.</td>
            <td colspan="2">
                This contract is binding only if approved by the authorized signatory of SUPASPACE PTE. LTD
            </td>
        </tr>
    </tbody>
</table>
