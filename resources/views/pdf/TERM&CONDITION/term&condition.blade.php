<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        body {
            padding: 0 50px;
            line-height: 1.5;
        }

        table tr td div {
            line-height: 1.5;
        }

        table tr td {
            line-height: 1.5;
        }

        .underline {
            text-decoration: underline;
        }

        .page-1 .company-title-section {
            width: 100%;
            text-align: center;
            display: flex;
            justify-content: center;
        }

        .page-1 .company-title-section .company-logo {
            margin-right: 10px;
        }

        .page-1 .company-title-section .company-info {
            text-align: left;
        }

        .page-1 .document-title p {
            text-align: center;
            text-decoration: underline;
            font-weight: bold;
        }

        .page-1 .section-1 {
            display: flex;
        }

        .page-2 .section-5 {
            display: flex;
        }

        .page-3 .section-6 .first-item {
            display: flex;
        }

        .footer-box {
            padding: 0px 5px;
            border: 1px solid #000;
            font-weight: bold;
            margin-top: 10px;
        }

        .signature {
            display: flex;
        }

        .signature p {
            margin-right: 150px;
        }

        .signature .box {
            width: 300px;
            padding-bottom: 30px;
            border: none;
            border-bottom: 2px solid #000;
            outline: none;
        }

        ol.list-view {
            list-style: none;
            padding-left: 0;
            margin-left: 0;
        }

        ol.list-view li {
            counter-increment: list-item;
            margin-left: 0em;
            padding-left: 4em;
            text-indent: 0em;
            position: relative;
        }

        ol.list-view li::before {
            content: "(" counter(list-item, lower-roman) ") ";
            position: absolute;
            left: 0;
        }

        .number {
            margin-right: 60px;
            font-weight: bold;
        }

        .sub-number {
            margin-right: 50px;
        }

        .input-area {
            border: none;
            border-bottom: 1px solid #6d6692;
            outline: none;
        }

        .input-area-alt {
            border: none;
            border-bottom: 1px solid #000;
            outline: none;
        }

        .page {
            page-break-before: always;
        }
    </style>
</head>

<body>
    <div>

        <div class="page-1">
            <div style="width: 100%;">
                <table style="width: 100%;">
                    <tr>
                        <td style="width: 40%; text-align: right;"><img
                                src="{{ public_path() . '/images/tagstudio_logo.jpg' }}" height="150" /></td>
                        <td style="width: 60%;">
                            <b>TAG STUDIO PTE LTD</b> <br>
                            7 Mandai Link #09-31 Mandai Connection, Singapore 728653 <br>Co. Reg.No 201502086C
                        </td>
                    </tr>
                </table>
            </div>
            <div class="document-title">
                <p>STANDARD TERMS AND CONDITIONS FOR<br>INTERIOR DESIGN & RENOVATION CONTRACT AGREEMENT</p>
            </div>
            <div class="document-intro">
                <p>
                    These terms and conditions will apply to and govern all contracts under which the Contractor agrees
                    to render services to the client of the Contractor. There shall
                    be no variation of these terms and conditions whatsoever.
                </p>
            </div>
            <div class="section-1">
                <table>
                    <tr>
                        <td>
                            <div class="number">1.</div>
                        </td>
                        <td><b>DEFINITIONS</b></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <div>
                                <p>
                                    In these terms and conditions and in all contracts to which these terms and
                                    conditions apply:
                                </p>
                                <p><b>"Contractor"</b> shall mean <input class="input-area" value="Tag Studio Pte Ltd"
                                        style="width: 300px;" /> </p>
                                <p><b>"Client"</b> shall mean any individual, firm or company whose name and details
                                    appear in the
                                    Renovation Contract Agreement to which these terms and
                                    conditions are a schedule thereto.</p>
                                <p><b>"Contract Sum"</b> shall mean the fees payable by the Client to the Contractor, at
                                    the rates
                                    specified in the Renovation Contract Agreement.</p>
                                <p><b>"Works"</b> save where varied in writing and agreed between the Client and the
                                    Contractor,
                                    shall mean the work to be performed are specified in the
                                    Renovation Contract Agreement. All descriptions and illustrations contained in
                                    drawings and
                                    specifications as duly approved by the relevant authorities
                                    shall form part of the Works.</p>
                                <p><b>"Premise"</b> shall mean the property the Client is desirous of renovation &
                                    decorating.</p>
                                <span>The address of the Premise:</span><br><br>
                                <input class="input-area-alt" value="{{ $documentData['address'] }}"
                                    style="width: 600px;" />
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
            <br>
            <div class="section-2">
                <table>
                    <tr>
                        <td>
                            <div class="number">2.</div>
                        </td>
                        <td><b>CONTRACT DOCUMENTS</b></td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">
                            <div class="sub-number">(A)</div>
                        </td>
                        <td>
                            <div>
                                The contract documents for the Works are:-
                                <p>
                                <ol class="list-view">
                                    <li>the Interior Design & Renovation Contract Agreement – setting out the Works and
                                        contract
                                        sum;</li>
                                    <li>the Plans, Drawings and Specifications of the Works;</li>
                                    <li>the Standard Terms and Conditions; and</li>
                                    <li>Variation Addendum,</li>
                                </ol>
                                </p>
                                Collectively known as <b>“Contract Documents”</b>.
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">
                            <div class="sub-number">(B)</div>
                        </td>
                        <td>
                            <div>
                                The terms as set out in the Contract Documents supersede, override and exclude any other
                                terms
                                stipulated, incorporated or referred to by the Client,
                                whether in any negotiations or if any course of dealing established between the
                                Contractor and
                                the Client.
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">
                            <div class="sub-number">(C)</div>
                        </td>
                        <td>
                            <div>
                                Any omissions in the Contract Documents and any work requested in variance to the
                                Contract
                                Documents are considered extra to the Renovation Contract Agreement and are not included
                                in the
                                Contract Sum. Any additional works are not included in the Contract Sum and shall be
                                extra to
                                the Contract Sum.
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">
                            <div class="sub-number">(D)</div>
                        </td>
                        <td>
                            <div>
                                Any special request change of terms and condition of The Interior Design & Renovation
                                Contract
                                Agreement will only be valid with management approval
                                in signature. Alteration Agreement without management approval will not be recognized
                                and
                                entertained by the Contractor.
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">
                            <div class="sub-number">(E)</div>
                        </td>
                        <td>
                            <div>
                                Copyright in all documents, including but not limited to the Plans and Drawings prepared
                                by the
                                Contractor pursuant to the Renovation Contract
                                Agreement will remain the property of the Contractor.<br>
                                All our designs, which cannot be reproduce (whether part/whole) or modify without first
                                obtaining our written consent.
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">
                            <div class="sub-number">(F)</div>
                        </td>
                        <td>
                            <div>
                                The Interior Design & Renovation Contract Agreement must be in written with all price
                                (including
                                discount & promotion) & Works clearly stated. Client
                                will be provided with a copy. The Contract Agreement undertaken between the Client and
                                the
                                Contractor’s representative is subjected to final approval
                                and acceptance by the Contractor. The Contractor will not entertain and hold any
                                responsibility
                                for any verbal agreements which are not stated in the
                                Contract Agreement.
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">
                            <div class="sub-number">(G)</div>
                        </td>
                        <td>
                            <div>
                                All information provided by the Client pertaining to the Contract Agreement is solely
                                for the
                                purpose of completing sales transactions and is kept
                                confidential unless permission is sought from the customer to use for other purposes.
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="section-3">
                <table>
                    <tr>
                        <td>
                            <div class="number">3.</div>
                        </td>
                        <td><b>SCOPE OF WORKS</b></td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">
                            <div class="sub-number">(A)</div>
                        </td>
                        <td>
                            <div>
                                All Works as set out in the Contract Documents, as duly approved by the Client, shall
                                not be
                                valid unless mutually agreed by both parties and such
                                agreement must be by way of an express written agreement.
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">
                            <div class="sub-number">(B)</div>
                        </td>
                        <td>
                            <div>
                                The Client shall review all plans, drawings and specifications on or before the date of
                                commencement of Works. All dimensions/ quantities are to be verified and approved by the
                                Client
                                at the Client’s Premise. The Contractor reserves the right to make such adjustments
                                necessary to
                                the designs on-site as far as it does not occurs additional charges on the part of the
                                Client.
                                In the event that any changes to the plans, drawings and specifications as
                                requested by the Client, such changes shall be subject to the Contractor’s approval and
                                additional charges shall be incurred by the Client.
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">
                            <div class="sub-number">(C)</div>
                        </td>
                        <td>
                            <div>
                                Any alteration or variation to the Works shall be made by way of a Variation Addendum
                                and the
                                Variation Addendum shall form part of the Contract
                                Documents. Such alterations and variation to the Works as requested by the Client are
                                not
                                included in the Contract Sum and shall be extra to the Contract
                                Sum. Total omission values from the original contract shall not more than 20.
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">
                            <div class="sub-number">(D)</div>
                        </td>
                        <td>
                            <div>
                                Any job & items included different in dimensions or material price which is not
                                contracted in
                                the Contract Agreement shall be deemed as "Additional Job
                                Items" and stated in Variation Addendum and all costs shall be made chargeable to the
                                Client by
                                the Contractor. The Client shall be responsible to make
                                payment for all “Additional Job Items” which have already carried out.
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">
                            <div class="sub-number">(E)</div>
                        </td>
                        <td>
                            <div>
                                Any alteration or variation to the Works not evidenced by way of a Variation Addendum,
                                duly
                                signed by the Client and the Contractor shall be null and
                                void. Any verbal agreement between Client and Contractor’s representatives will not be
                                recognised by the Contractor. In such event, the Contractor
                                reserves the right not to carry out such alteration or variation works.
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">
                            <div class="sub-number">(F)</div>
                        </td>
                        <td>
                            <div>
                                Variation Addendum only valid with management approval in authorised signature.
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">
                            <div class="sub-number">(G)</div>
                        </td>
                        <td>
                            <div>
                                To ensure the accuracy in billings, all addition, omission, substitution of Works
                                requested in
                                the course of Works is to be recorded in the Variation
                                Addendum for reckoning upon works completion. Please sign on every item in the Variation
                                Addendum.
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="page-2">
            <div class="section-4">
                <table>
                    <tr>
                        <td>
                            <div class="number">4.</div>
                        </td>
                        <td><b>PERMITS & APPROVALS</b></td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">
                            <div class="sub-number">(A)</div>
                        </td>
                        <td>
                            <div>
                                The Contractor shall assist the Client in the application of all necessary renovation
                                permits as
                                required by the relevant authorities with the consent of the
                                Client prior to the commencement of Works. All expenses related to the application of
                                all
                                necessary approvals, licenses, permits in respect of the Works
                                shall be solely borne by the Clients.
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">
                            <div class="sub-number">(B)</div>
                        </td>
                        <td>
                            <div>
                                In the event that the appointment of any professional services is required, including
                                that of
                                the services of architects, engineers, surveyors or any
                                professional institutions, for the purposes of carrying out the Works, all such expenses
                                shall
                                be solely borne by the Client.
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">
                            <div class="sub-number">(C)</div>
                        </td>
                        <td>
                            <div>
                                All Works shall be strictly subjected to the compliance or covenants of HDB or relevant
                                authorities' approval. In the event of any Works are rejected or
                                restricted by the relevant authorities, the Contractor reserves the right to make such
                                amendments, alterations or cancellation of these Works and thereafter,
                                the Client shall be not be entitled to raise objections, dispute or cancellation to the
                                Renovation Contract Agreement.
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="section-5">
                <table>
                    <tr>
                        <td>
                            <div class="number">5.</div>
                        </td>
                        <td><b>PROCESS AND PROCEDURE</b></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <p>
                                The work flow process is as follows:-
                            </p>
                            <table>
                                <tr>
                                    <td style="vertical-align: top;">
                                        <div class="sub-number">(A)</div>
                                    </td>
                                    <td>
                                        Upon receipt of the Client’s confirmation and signing of the Renovation Contract
                                        Agreement,
                                        the Contractor will proceed to prepare the Plans
                                        and Drawings in accordance with the Specifications of the Works. The time taken
                                        for the
                                        preparation of such plans and drawings (including
                                        any alterations and revision) is about 10 to 12 working days. <br>
                                        *Subject to case by case basis or peak period, time taken may be longer for
                                        preparation.
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <table>
                                <tr>
                                    <td style="vertical-align: top;">
                                        <div class="sub-number">(B)</div>
                                    </td>
                                    <td>
                                        Upon receipt of the Client’s confirmation and approval of the Plans and
                                        Drawings, the
                                        Contractor will provide the Client with a projected work
                                        schedule of the Works. The Contractor shall not be liable for any changes made
                                        to the
                                        projected work schedule in the event of any of the
                                        following:-
                                        <ol class="list-view">
                                            <li>Additional variation orders instructed by the Client;</li>
                                            <li>Delay in obtaining approval from relevant authorities;</li>
                                            <li>Delay in obtaining conformation of the Plans and Drawings or materials
                                                by the
                                                Client;</li>
                                            <li>Delay in the receipt of the deposit or any payments by the Client; or
                                            </li>
                                            <li>Delay/shortage in supply of materials by vendors beyond the control of
                                                the
                                                Contractor.</li>
                                        </ol>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <table>
                                <tr>
                                    <td style="vertical-align: top;">
                                        <div class="sub-number">(C)</div>
                                    </td>
                                    <td>
                                        Upon the completion of the Works, the Contractor shall provide the Client with a
                                        Pre-Handover Checklist and also a Project Handover
                                        Certificate.
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <table>
                                <tr>
                                    <td style="vertical-align: top;">
                                        <div class="sub-number">(D)</div>
                                    </td>
                                    <td>
                                        The Client shall inspect the Works and if he/she considers that the Works or any
                                        part
                                        thereof is not in accordance with the Contract Documents,
                                        he/she shall within 7 calendar days after he/she took over the key/move into the
                                        premise,
                                        give the Contractor detailed written notice thereof.
                                        The absence such notice within the specified 7 calendar day period will
                                        invalidate any claim
                                        made subsequently. The absence of any such
                                        notice, the Work shall be conclusively presumed to be free from any defect which
                                        would be
                                        apparent on reasonable examination. The Client
                                        shall then make full payment for all remaining balance payments.
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <table>
                                <tr>
                                    <td style="vertical-align: top;">
                                        <div class="sub-number">(E)</div>
                                    </td>
                                    <td>
                                        All pre-handover touch up works will base on the individual defect touch up
                                        area. The Client
                                        shall not be entitled to a one-to-one exchange
                                        of whole item.
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <table>
                                <tr>
                                    <td style="vertical-align: top;">
                                        <div class="sub-number">(F)</div>
                                    </td>
                                    <td>
                                        The Contractor shall on being given notice by the Client, make good at their own
                                        costs any
                                        defects of work & delayed in work schedule but
                                        will not extend to any consequential of economic losses, liquidated damages or
                                        damages
                                        suffered by the Client.
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="page-3">
            <div class="section-6">
                <table>
                    <tr>
                        <td>
                            <div class="number">6.</div>
                        </td>
                        <td><b>PAYMENT OF CONTRACT SUM</b></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <p>
                                For the consideration for the performance of the Works, the Client agree to pay the
                                Contractor the total contract sum of money in SGD as follows: <br>
                                <input class="input-area" value="{{ $word_total_all_amount }}" style="width: 500px;" />
                                (SGD $<input class="input-area" value="{{ $total_all_amount }}"
                                    style="width: 100px;" />)
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">
                            <div class="sub-number">(A)</div>
                        </td>
                        <td>
                            <span><b class="underline">Schedule of Payment</b></span>
                            <table>
                                <tr>
                                    <td><span class="underline"><b>Payment Due</b></span></td>
                                    <td><span class="underline"><b>Due Date</b></span></td>
                                </tr>
                                <tr>
                                    <td>(i) 20% of the Contract Sum as a deposit</td>
                                    <td>Upon confirmation and signing of the Renovation
                                        Contract Agreement</td>
                                </tr>
                                <tr>
                                    <td>(ii) 40% of the Contract Sum</td>
                                    <td>Upon commencement of the Works</td>
                                </tr>
                                <tr>
                                    <td>(iii) 35% of the Contract Sum together full payment for all
                                        Variation works as set out in the Variation Addendum (if
                                        applicable)</td>
                                    <td>Upon measurement of carpentry works</td>
                                </tr>
                                <tr>
                                    <td>(iv) 5% of the Contract Sum </td>
                                    <td>Upon the handover of the Client’s property to the Client</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="sub-number">(B)</div>
                        </td>
                        <td><b class="underline">Payment Terms</b></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <table>
                                <tr>
                                    <td style="vertical-align: top;">
                                        <div class="sub-number">(i)</div>
                                    </td>
                                    <td>
                                        All payments of the Contract Sum must be made by way of cheque or cashiers
                                        order. All
                                        cheques should be crossed “A/C PAYEE ONLY”
                                        & made payable to: <br>
                                        "<input class="input-area" value="Tag Studio Pte Ltd" />".
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <table>
                                <tr>
                                    <td style="vertical-align: top;">
                                        <div class="sub-number">(ii)</div>
                                    </td>
                                    <td>
                                        For amounts not exceeding Singapore Dollars One Thousand Only (SGD $1,000-00),
                                        payment
                                        can be made by way of NETS OR VISA CARD
                                        OR MASTER CARD.
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <table>
                                <tr>
                                    <td style="vertical-align: top;">
                                        <div class="sub-number">(iii)</div>
                                    </td>
                                    <td>
                                        The Client acknowledges and confirms that he/she shall be solely responsible for
                                        any
                                        loss incurred from any payment of the Contract Sum
                                        by way of cash or cash cheques to the Contractor’s representatives.
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <table>
                                <tr>
                                    <td style="vertical-align: top;">
                                        <div class="sub-number">(iv)</div>
                                    </td>
                                    <td>
                                        Upon receipt of the payment, the Contractor shall provide the Client with a
                                        receipt by
                                        way of mailing/ email to the Client’s mailing/ email
                                        address.
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <table>
                                <tr>
                                    <td style="vertical-align: top;">
                                        <div class="sub-number">(v)</div>
                                    </td>
                                    <td>
                                        It is the Client responsibility to keep track of the payment due and make
                                        payment
                                        promptly without the Contractor reminder.
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <table>
                                <tr>
                                    <td style="vertical-align: top;">
                                        <div class="sub-number">(vi)</div>
                                    </td>
                                    <td>
                                        Any amendment of payment term must seek for management approval in written
                                        notice, if
                                        not the Contractor will not entertain such amendment
                                        and reserved the rights to collect the payment base on this payment term.
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <table>
                                <tr>
                                    <td style="vertical-align: top;">
                                        <div class="sub-number">(vii)</div>
                                    </td>
                                    <td>
                                        In the event that the Client chooses to cancel the Renovation Contract Agreement
                                        prior
                                        to the commencement of the Works, the deposit paid
                                        shall be forfeited to the Contractor. In addition, the Client shall be liable to
                                        the
                                        Contractor for an administrative fee equivalent to 20% of the
                                        Contract Sum.
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="sub-number">(C)</div>
                        </td>
                        <td><b class="underline">Default Payment</b></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <table>
                                <tr>
                                    <td style="vertical-align: top;">
                                        <div class="sub-number">(i)</div>
                                    </td>
                                    <td>
                                        In the event of any delay or default by the Client in making any of the payment
                                        due by
                                        the due date, the Contractor hereby reserves the right
                                        to cease all Works forthwith. In such an event, the Contractor shall not be
                                        liable for
                                        any loss or damage whether direct, indirect or consequential
                                        suffered by the Client as a result of failure or delay by the Client in
                                        performing the
                                        obligations referred to above.
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <table>
                                <tr>
                                    <td style="vertical-align: top;">
                                        <div class="sub-number">(ii)</div>
                                    </td>
                                    <td>
                                        In the event that the Client neglects or fails to make any payment with 7 days
                                        from the
                                        due date, the Contractor reserves the right to terminate
                                        the Renovation Contract Agreement, in such event, the Contractor shall be
                                        entitled to
                                        claim up to the value of the Works already carried out,
                                        including such amounts in respect of any materials supplied or purchased, work
                                        prepared
                                        (partially or in full) and any other losses the
                                        Contractor may suffer as a natural consequence of the termination.
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <table>
                                <tr>
                                    <td style="vertical-align: top;">
                                        <div class="sub-number">(iii)</div>
                                    </td>
                                    <td>
                                        For the avoidance of doubt, all materials provided by the Contractor remain the
                                        property
                                        of the Contractor until full and final settlement of
                                        the Contract Sum.
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="sub-number">(D)</div>
                        </td>
                        <td><b class="underline">Discounts and Refunds</b></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <table>
                                <tr>
                                    <td style="vertical-align: top;">
                                        <div class="sub-number">(i)</div>
                                    </td>
                                    <td>
                                        All free-of-charge (“FOC”) items, discounts or refunds are subject to the
                                        written
                                        approval of the Contractor.
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <table>
                                <tr>
                                    <td style="vertical-align: top;">
                                        <div class="sub-number">(ii)</div>
                                    </td>
                                    <td>
                                        FOC items, discounts or refund must seek management approval in written notice
                                        in
                                        signature and authorised stamp. Any verbal agreement
                                        will not be recognised and entertained by the Contractor.
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <table>
                                <tr>
                                    <td style="vertical-align: top;">
                                        <div class="sub-number">(iii)</div>
                                    </td>
                                    <td>
                                        In the event that the Client receives a discount or FOC items on any part of the
                                        Contract Sum, the Client shall not be entitled to reduce the
                                        scope of the Works. In the event that the Client wishes to reduce the scope of
                                        Works,
                                        the Contractor reserves the right to withdraw all such
                                        discounts provided and shall make the necessary revision to the Contract Sum.
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <table>
                                <tr>
                                    <td style="vertical-align: top;">
                                        <div class="sub-number">(iv)</div>
                                    </td>
                                    <td>
                                        In the event that the Client receives a discount or FOC items on any part of the
                                        Contract Sum, the Client shall be require to partake in our
                                        Brand Ambassador (BA) program.
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="page-4">
            <div class="section-7">
                <table>
                    <tr>
                        <td>
                            <div class="number">7.</div>
                        </td>
                        <td><b>CARPENTRY WORKS</b></td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">
                            <div class="sub-number">(A)</div>
                        </td>
                        <td>
                            Upon receipt of payment on measurement of carpentry works, the Contractor requires a minimum
                            period of 18 working days to fabricate the carpentry
                            works and an addition 3 to 5 working days for the installation of such carpentry works.
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">
                            <div class="sub-number">(B)</div>
                        </td>
                        <td>
                            In the event that of any delay or default in any payment of the Contract Sum, the Contractor
                            reserves the right to dismantle and remove such Works &
                            carpentry works from the Client’s Premises. The Client hereby will allow the Contractor to
                            enter
                            the Premise to do such removal & dismantle. In such
                            an event, the Contractor shall not be liable for any damages caused by these dismantle and
                            removal works.
                        </td>
                    </tr>
                </table>
            </div>
            <div class="section-8">
                <table>
                    <tr>
                        <td>
                            <div class="number">8.</div>
                        </td>
                        <td><b>THIRD PARTY SERVICE PROVIDERS</b></td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">
                            <div class="sub-number">(A)</div>
                        </td>
                        <td>
                            In the event of the Client appoints any employees, servants, agents or any third party
                            working
                            for or under the direction of the Client to carry out other
                            renovation work items apart from those as set out in the Contract Documents, the Contractor
                            shall not be liable for damages caused by such employees,
                            servants, agents or any third party working for or under the direction of the Client.
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">
                            <div class="sub-number">(B)</div>
                        </td>
                        <td>
                            The Client should not personally appoints any employees, servants, agents or sub-contractor
                            (representative of Company) working for or under the
                            direction of the Client to carry out other renovation work items apart from those as set out
                            in
                            the Contract Documents or engage into any other form of
                            private deals or arrangement. The Company reserves the rights to pursue legal actions.
                        </td>
                    </tr>
                </table>
            </div>
            <div class="section-9">
                <table>
                    <tr>
                        <td>
                            <div class="number">9.</div>
                        </td>
                        <td><b>MATERIALS</b></td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">
                            <div class="sub-number">(A)</div>
                        </td>
                        <td>
                            For avoidance of doubt, marble, granite and timber veneer are examples of the natural
                            products
                            and may not be exhaustive. The natural products may
                            have imperfections, which occur naturally and for the purpose of the Renovation Contract
                            Agreement, such natural occurrences are not deemed to be
                            defective goods or material supplied.
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">
                            <div class="sub-number">(B)</div>
                        </td>
                        <td>
                            The installation and delivery of these natural properties may give rise to imperfections due
                            to
                            its nature and these shall not also be deemed to poor
                            workmanship.
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">
                            <div class="sub-number">(C)</div>
                        </td>
                        <td>
                            The products supplied may vary in colour and shade as between themselves and as between the
                            catalogues or any print materials and such difference shall
                            not be the subject of dispute or objection by the Client.
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">
                            <div class="sub-number">(D)</div>
                        </td>
                        <td>
                            All standard household items (e.g. hinges, taps, roller tracks, locks, handles, etc.,)
                            provide
                            by the Contractor shall be of satisfactory quality and suitable
                            for their intended use. However, the catalogues and samples presented to the Client are only
                            indicative in nature.
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">
                            <div class="sub-number">(E)</div>
                        </td>
                        <td>
                            Where materials for finishes/ furnishes are provided by the Client, the Contractor will
                            adhere
                            to the instructions of the manufacturers or suppliers of
                            such materials. The Contractor gives no warranty as to the quality of such materials, their
                            suitability & for the intended use.
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">
                            <div class="sub-number">(F)</div>
                        </td>
                        <td>
                            The Contractor warrants that any materials supplied by the Contract will be of good quality,
                            suitable for their intended use and shall correspond with their
                            description and sample (if any). The Clients shall be allowed to retain one issue of any
                            sample
                            wherever possible.
                        </td>
                    </tr>
                </table>
            </div>
            <div class="section-10">
                <table>
                    <tr>
                        <td>
                            <div class="number">10.</div>
                        </td>
                        <td><b>ACCESS TO THE CLIENT’S PREMISES</b></td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">
                            <div class="sub-number">(A)</div>
                        </td>
                        <td>
                            The Client shall permit the Contractor, his servants, sub-contractors and agents free access
                            to
                            the Client’s Premises at all reasonable hours to carry out the
                            Works.
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">
                            <div class="sub-number">(B)</div>
                        </td>
                        <td>
                            The Client accepts that there may be inconveniences from time to time, and that during the
                            carrying out of the Works, the Client should not leave any
                            valuables unattended. The Contractor shall not be responsible for any loss of any unattended
                            valuables. All items left in the Client’s premises shall be
                            at the Client’s own risks.
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">
                            <div class="sub-number">(C)</div>
                        </td>
                        <td>
                            The Client must obtain any permission for the Contractor, his employees, servants or agents
                            to
                            proceed over Premise belonging to third parties if this is
                            necessary for the proper progression of the Works and shall obtain any permission necessary
                            to
                            carry out work on Premise belonging to third parties.
                            The Client shall indemnify the Contractor against all claims of whatsoever nature made by
                            third
                            parties arising out of the presence of the Contractor, his
                            employees, servants or agents on the Client’s Premise save where such claim results directly
                            from negligence on Contractor’s part. The Client shall be
                            liable to the Contractor for all loss or damage whether direct, indirect or consequential
                            suffered by the Contractor as a result of failure or delay by the
                            Client in performing the obligations referred to above.
                        </td>
                    </tr>
                </table>
            </div>
            <div class="section-11">
                <table>
                    <tr>
                        <td>
                            <div class="number">11.</div>
                        </td>
                        <td><b>STANDARDS OF WORK</b></td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">
                            <div class="sub-number">(A)</div>
                        </td>
                        <td>
                            The Contractor agrees to supply all labour, materials and supervision to complete the Works
                            in
                            accordance with the Contract Documents.
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">
                            <div class="sub-number">(B)</div>
                        </td>
                        <td>
                            The Contractor agrees to undertake the Works diligently in a good and workmanlike manner, in
                            accordance with good quality residential standards and
                            practices, and in compliance with the market standard.
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">
                            <div class="sub-number">(C)</div>
                        </td>
                        <td>
                            The Client accepts that there may be inconveniences from time to time, and the Contractor
                            agrees to keep such inconveniences to a reasonable minimum.
                            It is the responsibility of the Client to take reasonable steps to provide a work area free
                            of household obstructions, and to remove or protect household
                            items in areas where it may be reasonably anticipated by the Client that they may be subject
                            to dust, damage or vibrations.
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">
                            <div class="sub-number">(D)</div>
                        </td>
                        <td>
                            The design drawing is subjected to modification of its design and measurement from time to
                            time to suit construction purpose and site condition.
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">
                            <div class="sub-number">(E)</div>
                        </td>
                        <td>
                            The Contractor shall act upon our professionalism for construction details base on standard
                            market practice, unless specified.
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">
                            <div class="sub-number">(F)</div>
                        </td>
                        <td>
                            Any defects, shrinkage or other faults arising from materials supplied by the Contractor or
                            Workmanship not in accordance with the Renovation Contract
                            and scope of Works which may appear within the defects liability period stated in the
                            warranty card and which are notified by the Clients in writing to the
                            Contractor from time but not later than fourteen (14days) form the expiration of the said
                            defect liability period shall be made good by the Contractor at
                            his own expenses within a reasonable time after receipt of such notification.
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="page-5">
            <div class="section-12">
                <table>
                    <tr>
                        <td>
                            <div class="number">12.</div>
                        </td>
                        <td><b>WARRANTY</b></td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">
                            <div class="sub-number">(A)</div>
                        </td>
                        <td>
                            The Contractor provides to the Client a workmanship warranty for a period of 12 months
                            from
                            the completion date of the Works, such workmanship
                            warranty shall be given by way of the Contractor’s Official 12 months' Warranty
                            Certificate
                            bearing the Contractor’s official stamp and authorised
                            signature.
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">
                            <div class="sub-number">(B)</div>
                        </td>
                        <td>
                            This Warranty shall only apply to Works undertaken properly invoiced by the Contractor.
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">
                            <div class="sub-number">(C)</div>
                        </td>
                        <td>
                            This Warranty also covers water proofing for 12 months.
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">
                            <div class="sub-number">(D)</div>
                        </td>
                        <td>
                            In the event of invalid warranty coverage, there will be a transport charge of SGD$80
                            per
                            trip.
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">
                            <div class="sub-number">(E)</div>
                        </td>
                        <td>
                            This Warranty shall not be valid in any of the following events:-
                            <ol class="list-view">
                                <li>That the Client cannot provide evidence that the work was originally undertaken
                                    by
                                    the Contractor;</li>
                                <li>Where full payment of the Contract Sum has not been made by the Client;</li>
                                <li>Where the Client has notified the Contractor of any defects which requires
                                    rectification and the Client refuses for whatsoever reason to allow the
                                    Contractor to conduct any rectification works;</li>
                                <li>Defects resulting from misuse, willful act, or faulty workmanship by the Client,
                                    his
                                    employees, servants, agents or any third party working for or
                                    under the direction of the Client.</li>
                            </ol>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="section-13">
                <table>
                    <tr>
                        <td>
                            <div class="number">13.</div>
                        </td>
                        <td><b>TERMINATION</b></td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">
                            <div class="sub-number">(A)</div>
                        </td>
                        <td>
                            In the event that the Renovation Contract Agreement is terminated by the Client for
                            whatever
                            reason through no fault or negligence on the Contractor,
                            the Contractor shall be entitled to recover from the Client to claim up to the value of
                            the
                            Works already carried out, including such amounts in respect
                            of any materials supplied or purchased, work prepared (partially or in full) and any
                            other
                            losses the Contractor may suffer as a natural consequence
                            of the termination.
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">
                            <div class="sub-number">(B)</div>
                        </td>
                        <td>
                            In addition, the Client shall indemnify the Principal from and against any and all
                            actions,
                            proceedings, liabilities, claims, demands, losses, damages,
                            charges, costs (including legal costs on a full indemnity basis) and expenses of
                            whatever
                            nature which the Contractor may directly sustain, incur or
                            suffer by reason of, or arising out of or in connection with (except to the extent such
                            loss
                            is caused or contributed to by the Client.
                        </td>
                    </tr>
                </table>
            </div>
            <div class="section-14">
                <table>
                    <tr>
                        <td>
                            <div class="number">14.</div>
                        </td>
                        <td><b>NON-WAIVER</b></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            No failure to exercise and no delay in exercising on the part of the Contractor any
                            right,
                            power or privilege under the Contract Documents shall operate
                            as a waiver thereof nor shall any single or partial exercise of any right, power or
                            privilege preclude any other or further exercise thereof or any other
                            right, power or privilege.
                        </td>
                    </tr>
                </table>
            </div>
            <div class="section-15">
                <table>
                    <tr>
                        <td>
                            <div class="number">15.</div>
                        </td>
                        <td><b>GOVERNING LAW</b></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            The terms and conditions shall be governed by the laws of the Republic of Singapore.
                        </td>
                    </tr>
                </table>
            </div>
            <div class="section-16">
                <table>
                    <tr>
                        <td>
                            <div class="number">16.</div>
                        </td>
                        <td><b>Contact Methods</b></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            The Company shall send invoices, receipts, warranty certificates, feedback forms or
                            promotions, if applicable via the Client’s given email address or
                            handphone number. Representatives from the Company shall also contact the Client through
                            email or handphone for other matters regarding the contracted
                            services.
                        </td>
                    </tr>
                </table>
            </div>
            <div class="footer-box">
                THE CLIENT HAS READ AND RECEIVED A COPY OF THIS TERMS AND CONDITIONS. THE CLIENT AGREES TO ALL TERMS
                AND
                CONDITIONS AS STATED. THERE ARE NO VERBAL AGREEMENTS BETWEEN THE CLIENT AND THE CONTRACTOR
                MODIFYING,
                AMENDING THESE TERMS AND CONDITIONS
            </div>
            <div class="signature">
                <table>
                    <tr>
                        <td style="vertical-align: top;">
                            <p>SIGNED by</p>
                        </td>
                        <td>
                            <div class="box">
                                <img src="{{ 'data:image/png;base64,' . $contractLists['encode_owner_signature'] }}"
                                    style="height:100px;">
                            </div><br>
                            <div class="info">
                                <span>Name of Client:</span> {{ $contractLists['customer_name'] }} <br>
                                <span>NRIC No:</span> {{ $documentData['nric'] }} <br>
                                <span>Date:</span> {{ $documentData['full_date'] }} <br>
                                <span>Company Stamp (if any):</span>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
            <div style="width: 100%; text-align: right; margin-top: 80px;">
                READ, UNDERSTOOD & AGREED BY: <input class="input-area"
                    value="{{ $contractLists['customer_name'] }}" style="width: 250px;" />
            </div>
        </div>

    </div>
</body>

</html>
