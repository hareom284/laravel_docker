<style>
    body {
        font-family: Arial, sans-serif;
        font-size: 13px;
        line-height: 1;
    }

    .section-title {
        font-weight: bold;
        margin-bottom: -5px;
    }

    ul {
        list-style: none !important;
        padding-left: 20px;
    }

    li {
        margin-bottom: 3px;
        padding-left: 15px;
    }

    li::before {
        content: '\2022';
        left: 0;
        color: black;
    }

    .list-style-none::before {
        content: '';
    }

    .qr-code {
        margin: 20px 0;
    }

    .signature {
        margin-top: 30px;
    }

    .payment-table,
    .payment-table th,
    .payment-table td {
        border: 1px solid black;
        border-collapse: collapse;
    }

    .no-border,
    .no-border th,
    .no-border td {
        border: 0 !important;
    }

    .clear-both {
        clear: both;
    }

    .tid-term p {
        page-break-inside: avoid;

    }
</style>
@php
    $payment_terms = null;
    if ($quotationList->payment_terms) {
        $payment_terms = $quotationList->payment_terms ? json_decode($quotationList->payment_terms) : null;
    }
@endphp
<div class="container page clear-both tid-term"
    style="padding-left: 50px !important; padding-right: 50px !important; padding-top: 20px;position: relative;">
    <h3>Terms & Conditions:</h3>
    <p>
        @foreach ($customers_array as $customer)
            <span>{{ $customer['name_prefix'] . ' ' . $customer['last_name'] . ' ' . $customer['first_name'] }}</span>
        @endforeach
        (the “Client”), Tid Plus Design Pte Ltd shall be referred to as “Tid Plus”.
    </p>
    <p>This quotation is referred to in these terms & conditions as the “Agreement”.</p>

    <div class="section-title">1. Price</div>
    <p>All prices quoted are subject to 9% GST. All prices quoted are also subject to change upon Tid Plus inspecting
        and/or measuring the work site. Tid Plus reserves the right not to begin construction work in the event the
        Client does not agree in writing to any such price changes. Tid Plus shall not be liable for any loss or damages
        suffered by the Client in the event of any such variation. Tid Plus, upon exercising such a right, may charge
        the Client a proportionate amount for the work done up to the date it exercises such a right.</p>

    <div class="section-title">2. Payment</div>
    <p>All payments are due as follows:</p>
    <ul>
        <li>10% upon agreeing on terms of agreement</li>
        <li>30% prior to work commencement</li>
        <li>50% before commencement of carpentry works</li>
        <li>5% during the handover or when the client takes possession of the house after renovation, whichever is
            earlier</li>
    </ul>
    @if (isset($payment_terms) && $settings['enable_payment_terms'] == 'true')
        <div class="payment-percentage" style="margin-bottom: 15px;">
            <table class="payment-table">
                <tr>
                    <td style="width: 150px" align="center">Payment Percentage</td>
                    <td>Payment Terms</td>
                    <td>Amount Payable</td>
                </tr>
                @if ($payment_terms)
                    @foreach ($payment_terms->payment_terms as $paymentTerm)
                        <tr>
                            <td align="center">{{ $paymentTerm->payment_percentage }}%</td>
                            <td style="width: 50%;">{{ $paymentTerm->payment_term }}</td>
                            <td class="percentage-total">
                                <table class="no-border">
                                    <tr>
                                        <td>$</td>
                                        <td align="right">
                                            {{ calculateByPercent($total_prices['total_inclusive'], $paymentTerm->payment_percentage) }}
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    @endforeach
                @endif
                <tr>
                    <td colspan="2" align="right">Total Amount Payable</td>
                    <td class="percentage-total">
                        <table class="no-border">
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

    <div class="avoid-break">
        <div class="section-title">3. Mode of Payment</div>
        <p>All cheques must be crossed & made payable to: <strong>TID PLUS DESIGN PTE LTD</strong>, bank transfer to:
            <strong>OCBC 551-720972-001</strong> or PayNow to UEN No: <strong>200408517Z</strong> or scan through QR
            code.</p>
    </div>

    <div class="qr-code" align="left">
        <img src="{{ public_path() . '/images/tidplusqr.png' }}" height="150" />
    </div>

    <div class="avoid-break">
        <div class="section-title">4. Authorized Payment Procedures</div>
        <p>All payments must be made only to the Tidplus Design official bank account, as specified in Point 3 (Mode of
            Payment). Payments made to any other accounts will not be recognized and will be considered unpaid.</p>
    </div>

    <div class="signature" align="right">
        <p>Confirmed by: ______________</p>
    </div>

    <div class="avoid-break">
        <div class="section-title">5. Scope/Manner of Work</div>
        <p>This Agreement does not include the clearing of any rubbish. Tid Plus is not liable for any rubbish cleared
            during the process of hacking conducted by Tid Plus. The Client assumes the risk of such damage. All works
            carried out in a manner which Tid Plus, in its absolute discretion, deems fit unless the Client makes a
            special request in writing to Tid Plus with reasonable notice which such notice is given before the
            commencement of work. All furniture is complete with internal white ply unless otherwise stated.</p>
    </div>

    <div class="avoid-break">
        <div class="section-title">6. Wet Works</div>
        <p>All projects involving wet works will need at least 6 working days (excluding Saturdays, Sundays, Public
            Holidays, Public Holiday eves, and during a pandemic/epidemic) to complete unless otherwise stated in the
            contract. Wet works generally refer to works that generate or protect the internal space when tiles or
            bricks are being generated, examples of which include but are not limited to cementing, hacking, or tiling
            works. The schedule provided along with this Agreement will generally indicate when wet works are to be
            completed.</p>
    </div>

    <div class="avoid-break">
        <div class="section-title">7. Variations</div>
        <p>Should the Client wish to carry out additional work or variation in which some works or designs may be
            different from that stated in this Agreement, Tid Plus will issue a revised quotation or signed separate
            contract for such additional work or variations. Tid Plus has the right not to begin work without a signed
            or variation work until parties have agreed and signed such revised or separate quotation, with all payments
            due signed and issued for the variation. The Client shall also be liable for any additional cost for the
            variation works or rental of equipment such as scaffolding or hacking works, signing the contract, or
            changing materials, colors, or dimensions.</p>
    </div>

    <div class="avoid-break">
        <div class="section-title">8. Additional Fees/Charges</div>
        <p>An estimated amount of at least $500 is chargeable per request should there be any request from the Client
            for a set of as-built drawings. An estimated amount of at least $200 per day is chargeable for every day Tid
            Plus needs to coordinate with any external contractor engaged by the Client for works not envisioned by this
            Agreement. All disbursements which Tid Plus incurs in the course of executing the works under this Agreement
            and which may be borne by the Client, such disbursements as well as the Client for any outgoings reasonably
            incurred by Tid Plus must be agreed by Tid Plus prior to the start of works and include but are not limited
            to stamp fees, approvals by Government or other authorities, third-party endorsements, condominium
            management, and regulatory checks. Such disbursements are immediately payable to Tid Plus upon Tid Plus
            presenting the Client with relevant documentary proof of having incurred or needing to incur such
            disbursements.</p>
    </div>

    <div class="avoid-break">
        <div class="section-title">9. Low or Non-formaldehyde Materials</div>
        <p>For materials low or non-formaldehyde content, additional costs will apply.</p>
    </div>

    <div class="avoid-break">
        <div class="section-title">10. Ownership of Intellectual Property</div>
        <p>All 3D images, drawings, and graphics or images (whether in electronic, print, internal, or physical form)
            shown by Tid Plus to the Client are for illustration purposes only and is/are owned by Tid Plus at all
            times. Tid Plus may at any time change or use them in any way it deems fit, including posting them on any of
            its social media platforms.</p>
    </div>

    <div class="avoid-break">
        <div class="section-title">11. Indemnities</div>
        <p>The Client is aware that when the Client engages any external contractor to carry out work outside of this
            Agreement, Tid Plus shall not be responsible for any damage or negligence suffered by the Client. Any damage
            caused by such external third parties must be borne by the Client. The Client is also responsible for any
            damage caused by Tid Plus during the same time and place as any external third-party contractors. Tid Plus
            shall, at all times, be held harmless against any third-party claim or injury arising out of such works not
            executed by Tid Plus.</p>
    </div>

    <div class="signature" align="right">
        <p>Confirmed by: ______________</p>
    </div>

    <div class="avoid-break">
        <div class="section-title">12. Warranty</div>
        <p>Provided that all payments due to Tid Plus are made on time by the Client, Tid Plus provides a 1-year
            warranty on workmanship from the date of handover. This warranty excludes damages on all surfaces (i.e.
            countertops, carpentry laminates, tiling, paints, floors, etc.) due to user negligence, improper use,
            non-observance of operating and/or maintenance of materials, and any damage or problems arising from misuse,
            abuse, neglect, or impact by the users. For the avoidance of doubt, there shall be no 1-year warranty as
            long as any payment due to Tid Plus from the Client is made late.</p>
    </div>

    <div class="avoid-break">
        <div class="section-title">13. Delays</div>
        <p>The Client agrees that it shall not hold Tid Plus liable for any delays in the completion of any part of the
            works carried out pursuant to this Agreement which were caused by reasons not within the direct control of
            Tid Plus or any of its agents or subcontractors.</p>
        <p>All defects are not considered part of uncompleted works, will not be part of the schedule, and are not a
            reason for non or delayed payment.</p>
    </div>

    <div class="signature" align="right">
        <p>Confirmed by: ______________</p>
    </div>

    <div class="avoid-break">
        <div class="section-title">14. Premature Termination/Partial Cancellation of Agreed Scope of Works</div>
        <p>In the event the Client terminates this Agreement for any reason not due to any act of default of Tid Plus,
            Tid
            Plus reserves the right charge the Client 10% of the total contract sum, or an amount equivalent to the
            additional profits it would have made had it been allowed to complete its works in
            the Agreement assessed from the time of such premature termination whichever is the higher. This right is in
            addition to charging the Client a proportionate amount for the work done up
            to the date of such premature termination, In the event the Client cancels any part of the agreed scope of
            works after entering into this Agreement, the Client shall immediately pay 15% of
            the quoted fees for that particular item that was cancelled by the Client as fair compensation to Tid Plus.
            In the event the Client modifies any part of the agreed scope of works after
            entering into this Agreement such that Tid Plus need only perform less work than that originally envisioned,
            the Client expressly agrees that the fees payable to Tid Plus remains unchanged</p>
    </div>

    <div class="avoid-break">
        <div class="section-title">15. Termination by Tid Plus</div>
        <p>In the event any payment due to Tid Plus is not made on time by
            the Client, or if any such payment is not made in full, or in the event the client or its agents interfere
            with TidPlus’ execution of the scope of works in this Agreement where such
            interference is deemed by Tid Plus to be unnecessary ,Tid Plus has the right to stop work immediately and
            terminate this Agreement. Tid Plus shall not be liable for any loss or damages
            suffered by the Client in such an event. Parties hereby agree that timely payment is of the essence and/or a
            condition of this Agreement, the breach of which by the Client enables Tid Plus
            to terminate this Agreement without prejudice to any of its accrued rights. The Client shall indemnify and
            hold harmless Tid Plus from all damages and losses stemming from such
            termination as well as all consequential damages, losses, claims,suits, proceedings or any other liability,
            whether from a third party or otherwise, arising from or in connection with Tid Plus
            exercising its rights of termination under this clause. In addition to the conditions under which Tid Plus
            is entitled to terminate the Agreement with the Client specified in clause 13, , in the
            event Tid Plus’ staff, workers, officers or agents experience any abuse, harassment, threats or insults,
            whether physically or verbally, while carrying out works at the Client’s premises,
            accordance with clause 13. Tid Plus shall similarly have the right to terminate the Agreement in accordance
            with clause 13 .
        </p>
    </div>

    <div class="avoid-break">
        <div class="section-title">16. Private and Confidential</div>
        <p>l All documents issued by Tid Plus, this Terms & Conditions , have the status of being private and
            confidential. The Client shall not disclose any such documents to
            any third party, nor shall the Client permit any access to such documents by any means by any third party
            (including publication online and access to the same by third parties), without the
            consent of Tid Plus. In the event of breach of this clause, the Client agrees that Tid Plus is entitled to
            apply for and obtain an injunction compelling his compliance with this clause.
        </p>
    </div>

    <div class="avoid-break">
        <div class="section-title">17. No Waiver</div>
        <p>No forbearance or delay in exercising any right under this Agreement by either party shall be construed as a
            waiver of such rights.</p>
    </div>

    <div class="avoid-break">
        <div class="section-title">18. Third Party Rights</div>
        <p>A person who is not a party to this Agreement has no right to enforce any of its terms under the Contracts
            (Rights of Third Parties) Act.</p>
    </div>

    <div class="avoid-break">
        <div class="section-title">19. Applicable Law</div>
        <p>This Agreement shall be governed by and construed in accordance with the laws of Singapore.</p>
    </div>

    <div class="avoid-break">
        <div class="section-title">20. Legal Costs</div>
        <p>In the event Tid Plus has to commence legal proceedings to recover any unpaid amounts, the Client is liable
            to pay Tid Plus' legal fees.</p>
    </div>

    <div class="avoid-break">
        <div class="section-title">21. Validity of Quotation & Work Commencement</div>
        <p>This quotation is open for acceptance for 1 month from the date of issue. Renovation works must commence
            within 12 months from the date the contract is signed.</p>
        <p>In instances where the renovation starts after the 12-month period, the prices indicated in the contract are
            subjected to prevailing rates and taxes.</p>
        <p>Please note that any additional items or variation orders beyond the agreed scope of work will require full
            payment before implementation</p>
    </div>
    <!-- <div class="footer">
    </div> -->
        @include('pdf.Common.Tidplus.signatureComponent')

</div>
