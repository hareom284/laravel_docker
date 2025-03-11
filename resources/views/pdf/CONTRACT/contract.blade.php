<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        .w-full {
            width: 100%;
        }

        .content-center {
            margin: 0 auto;
            text-align: center;
        }

        .my-50 {
            margin: 50px 0;
        }

        .py-20 {
            padding: 20px 0;
        }

        .mt-20 {
            margin-top: 20px;
        }

        .mt-50 {
            margin-top: 50px;
        }

        .py-10 {
            padding: 10px 0;
        }

        .cover-section {
            height: 100vh;

        }

        .ft-bold {
            font-weight: bold;
        }

        * {
            font-family: sans-serif;
            /* Use the custom font, fallback to sans-serif if unavailable */
        }

        .ft-normal {
            font-weight: 400;
        }

        .text-3xl {
            font-size: 36px !important;
        }

        .text-2xl {
            font-size: 30px !important;
        }

        .text-xl {
            font-size: 22px !important;
        }

        .page {
            /* overflow: hidden; */
            page-break-before: always;
        }

        .blank-border {
            border-bottom: 2px solid #8181A5;
            width: 150px !important;
            text-align: center;
            display: inline-block;
            margin: 10px 0;

        }

        .blank-border-bold {
            border-bottom: 2px solid #8181A5;
            width: 150px !important;
            text-align: center;
            display: inline-block;
            font-weight: bold;
            margin: 10px 0;
        }

        .data-section {
            padding: 20px;
        }

        .signature-section {
            /* font-size: 12px !important; */
        }

        .page-1 {
            padding: 20px;
        }

        .text-underline {
            text-decoration: underline;
        }

        .appendix-b th,
        .appendix-b td {
            border: 1px solid #000;
            padding: 8px;
        }

        .appendix-b {
            width: 100%;
            border-collapse: collapse;

        }

        ol li {
            padding: 10px 0;
        }
    </style>
</head>

<body>
    <div>
        <div class="cover-section">

            <div class="w-full mt-50 py-20 content-center" style="padding-top: 200px;">
                <img src="{{ public_path() . '/images/contract_logo.png' }}" height="200" />
            </div>
            <div class="w-full mt-50 py-20 content-center">
                <h1 class="ft-normal text-3xl">CONSUMERS ASSOCIATION OF SINGPORE</h1>
            </div>
            <div class="w-full mt-20  content-center py-10">
                <h1 class="ft-bold text-2xl">CASETRUST</h1>
            </div>
            <div class="w-full  content-center py-5">
                <h1 class="ft-bold text-2xl">STANDARD RENOVATION</h1>
            </div>
            <div class="w-full mt-20 content-center py-10">
                <h1 class="ft-bold text-2xl">CONTRACT</h1>
            </div>

        </div>

        <div class="data-section page">
            <div class="py-10" style="padding-top:50px;">
                <h1 class="content-center ft-bold text-xl">CASETRUST STANDARD
                    RESIDENTIAL RENOVATION CONTRACT</h1>
                <hr style="border-bottom: 1px dashed black;">
                <div style="padding-bottom: 20px;">
                    <p>THIS AGREEMENT is made on the
                        <span class="blank-border">
                            {{ $documentData['ordinal_day'] }}
                        </span> day of
                        <span class="blank-border">
                            {{ $documentData['date_by_month'] }}
                        </span> between:
                    </p>
                    <p>(1)
                        <span class="blank-border-bold" style="width:350px !important;">
                            {{ $documentData['customer_name'] }}
                        </span> (NRIC No: <span class="blank-border-bold" style="width:250px !important;">

                            {{ $documentData['nric'] }}
                        </span>
                        ) (hereinafter referred to as the <span class="ft-bold">“Employer/Owner”</span>) of the one
                        party;
                    </p>
                    <p>(2) <span class="blank-border-bold" style="width:400px !important;">

                            {{ $documentData['company'] }}
                        </span>
                        (a company with limited liability incorporated under the laws of Singapore) of <br> <span
                            class="blank-border-bold" style="width:350px !important;">10
                            WOODLANDS SECTOR 2 S(737 727)</span>
                        (hereinafter referred to as the “<span class="ft-bold">Contractor</span>”) of the other party;
                    </p>
                    <p>WHEREAS;</p>
                    <p>
                        (1) The Employer/Owner requires renovating and/or decorating of the premises located at<br>
                        <span class="blank-border-bold" style="width:700px !important;">

                            {{ $documentData['address'] }}
                        </span><br>
                        (hereinafter called the "Premises") and is engaging the services of the Contractor for this
                        purpose.
                    </p>
                    <p>
                        (2) The Contractor accepts such appointment and is willing and able to carry out the work
                        described in the Scope of Works attached and in accordance with the plans, drawings and
                        specifications specified in Appendix A (hereafter called the "<span
                            class="ft-bold">Works</span>").
                    </p>
                    <p>NOW IT IS HEREBY AGREED as follows:</p>
                    <p class="ft-bold">SCOPE OF WORKS</p>
                    <p>
                    <div>
                        <table>
                            <tr>
                                <td style="vertical-align: top;">1.</td>
                                <td style="width:15px;"></td>
                                <td>The Contractor agrees and undertakes to carry out and
                                    complete the Works to the satisfaction of the Employer/Owner and in accordance with
                                    the
                                    terms and conditions of this Agreement.</td>
                            </tr>
                        </table>
                    </div>
                    </p>
                    <p>
                    <div>
                        <table>
                            <tr>
                                <td style="vertical-align: top;">2.</td>
                                <td style="width:15px;"></td>
                                <td>Subject to the Employer/Owner obtaining the relevant authorities approval (if any),
                                    the
                                    Contractor shall carry out and complete the Works as approved by the Employer/Owner
                                    diligently and in accordance with the said plans, drawings, variation addendum and
                                    specifications ("<span class="ft-bold">Contract Documents</span>") and specified in
                                    <span class="ft-bold">Appendix B</span> on or before the Date for completion of the
                                    Works.
                                    To this end, the Contractor shall supervise the work of its employees and agents
                                    accordingly
                                    and that all finishes, furnishing and furniture are of specified or satisfactory
                                    quality and
                                    finish.
                                </td>
                            </tr>
                        </table>
                    </div>
                    </p><br><br>
                    <p class="ft-bold">PAYMENT AND PROCEDURE</p>
                    <p>
                    <div>
                        <table>
                            <tr>
                                <td style="vertical-align: top;">3.</td>
                                <td style="width:15px;"></td>
                                <td>
                                    <span>
                                        For the performance of the Works, the Employer/Owner agrees to pay to the
                                        Contractor the sum
                                        of Singapore <br>Dollars
                                        <span class="blank-border-bold" style="width: 600px !important;">
                                            {{ $word_total_all_amount }}

                                        </span>
                                        (S$
                                        <span class="blank-border-bold" style="width: 100px !important;">

                                            {{ $total_all_amount }}
                                        </span>
                                        ) (hereinafter referred to as the "<span class="ft-bold">Contract Sum</span>")
                                        in the
                                        manner specified in <span class="ft-bold">Appendix C</span>
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    </p>
                    <div>
                        <table>
                            <tr>
                                <td style="vertical-align: top;">4.</td>
                                <td style="width:15px;"></td>
                                <td>

                                    <div style="padding-bottom: 20px;">
                                        (a) Subject to the satisfactory performance of the Works, in the event of
                                        any delay or
                                        default by the Employer/Owner in making payment as stipulated in Appendix B,
                                        the
                                        Contractor may cease all work forthwith.
                                    </div>
                                    <div>
                                        (b) If without reasonable cause, the Contractor shall fail or neglect to
                                        commence or
                                        complete the Works on the dates referred to in the Contract Documents, he
                                        agrees to pay
                                        the Employer/Owner (by way of damages, and not by way of penalty) the sum of
                                        Singapore
                                        <br>Dollars
                                        <span class="blank-border-bold" style="width: 600px !important;">
                                            {{ $word_total_all_amount }}
                                        </span>
                                        (S$
                                        <span class="blank-border-bold" style="width: 100px !important;">
                                            {{ $total_all_amount }}
                                        </span>
                                        ) for every week or part thereof during which the commencement or completion
                                        of the
                                        Works is delayed. (OPTIONAL)
                                    </div>

                                </td>
                            </tr>
                        </table>
                    </div>
                    <br />
                    <br />
                    <p class="ft-bold">REPRESENTATIONS AND WARRANTIES</p>
                    <p>
                    <div>
                        <table>
                            <tr>
                                <td style="vertical-align: top;">5.</td>
                                <td style="width:15px;"></td>
                                <td> The Contractor warrants that any materials supplied by the Contractor will be of
                                    specified
                                    or satisfactory quality, suitable for their intended use and shall correspond with
                                    their
                                    description and sample (if any).</td>
                            </tr>
                        </table>
                    </div>
                    </p>
                    <p>
                    <div>
                        <table>
                            <tr>
                                <td style="vertical-align: top;">6.</td>
                                <td style="width:15px;"></td>
                                <td> Where laying of floor finishes forms a part of the Works to be carried out by the
                                    Contractor
                                    under this Agreement, the Contractor shall ensure that such floor finishes are laid
                                    in
                                    accordance with the instructions and advice of the suppliers.</td>
                            </tr>
                        </table>
                    </div>
                    </p>
                    <p>
                    <div>

                        <table>
                            <tr>
                                <td style="vertical-align: top;">7.</td>
                                <td style="width:15px;"></td>
                                <td> Where the materials for floor finishes are furnished by the Employer/Owner, the
                                    Contractor
                                    shall adhere to the instructions of the manufacturers of such materials.</td>
                            </tr>
                        </table>
                    </div>
                    </p>
                    <br />
                    <br />
                    <p class="ft-bold">ACCESS TO EMPLOYER/OWNER'S PREMISES</p>
                    <p>
                    <div>
                        <table>
                            <tr>
                                <td style="vertical-align: top;">8.</td>
                                <td style="width:15px;"></td>
                                <td> The Employer/Owner shall permit the Contractor, his employees, servants and agents
                                    free
                                    access to the Premises (at all reasonable hours) to carry out the Works and if
                                    required,
                                    obtain the necessary permission for the Contractor, his employees, servants and
                                    agents to
                                    carry out the Works required.</td>
                            </tr>
                        </table>
                    </div>
                    </p>
                    <p>
                    <div>
                        <table>
                            <tr>
                                <td style="vertical-align: top;">9.</td>
                                <td style="width:15px;"></td>
                                <td> The Employer/Owner shall obtain the necessary permission for the Contractor, his
                                    employees,
                                    servants or agent to carry out the necessary work.</td>
                            </tr>
                        </table>
                    </div>
                    </p>
                    <br />
                    <br />
                    <p class="ft-bold">STANDARD OF WORKS</p>
                    <p>
                    <div>
                        <table>
                            <tr>
                                <td style="vertical-align: top;">10.</td>
                                <td style="width:15px;"></td>
                                <td> The Contractor shall, at his own expense, remove all tools and surplus materials
                                    from the
                                    premises and leave it in a clean and tidy condition, upon completion of the Works or
                                    the
                                    termination of the Agreement whichever the earlier.</td>
                            </tr>
                        </table>
                    </div>
                    </p>
                    <div class="">
                        <table>
                            <tr>
                                <td style="vertical-align: top;">11.</td>
                                <td style="width:15px;"></td>
                                <td>
                                    <div style="padding-bottom: 20px;">
                                        (a) Any defects, shrinkage or other faults arising from materials supplied by
                                        the
                                        Contractor or workmanship not in accordance with the Agreement which may appear
                                        within
                                        the defects liability period stated in
                                        <span class="blank-border-bold">Appendix
                                            A</span> and which are notified by the Employer/Owner in writing to the
                                        Contractor
                                        from time to time but no later than
                                        <span class="blank-border-bold">
                                            {{ $contractLists['contractor_days'] }}

                                        </span>
                                        days from the expiration of the said defects liability period or such time may
                                        be agreed
                                        by the parties, shall be made good by the Contractor at his own expense within
                                        reasonable time frame according to the number of defects after receipt of such
                                        notification.
                                    </div>
                                    <div style="padding-bottom: 20px;">
                                        (b) Should the Contractor not perform the Rectification Works. subject to
                                        sub-clause
                                        (c), the Contractor agrees to compensate the Employer/Owner for the cost of
                                        engaging a
                                        third party to perform the Rectification Works.
                                    </div>
                                    <div style="padding-bottom: 20px;">
                                        (c) The Employer/Owner shall inform the Contractor in writing of the cost of
                                        engaging a
                                        third party to perform the Rectification Works and allow the Contractor, at his
                                        own
                                        expense to perform the Rectification Works.
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <br />
                    <br />
                    <p class="ft-bold">WARRANTY</p>
                    <p>
                    <div>
                        <table>
                            <tr>
                                <td style="vertical-align: top;">12.</td>
                                <td style="width:15px;"></td>
                                <td> <span>
                                        The Contractor shall provide to the client a workmanship warranty ("<span
                                            class="ft-bold">Warranty</span>") for a period of 12 months ("<span
                                            class="ft-bold">Warranty Period</span>") from the completion date of the
                                        Works.

                                    </span></td>
                            </tr>
                        </table>
                    </div>
                    </p>
                    <p>
                    <div>
                        <table>
                            <tr>
                                <td style="vertical-align: top;">13.</td>
                                <td style="width:15px;"></td>
                                <td>
                                    <span>
                                        In the event of any defects arising from the Works during the Warranty Period,
                                        the
                                        Contractor shall at its own cost, conduct the necessary rectifications works.
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    </p>
                    <div class="">
                        <table>
                            <tr>
                                <td style="vertical-align: top;">14.</td>
                                <td style="width:15px;"></td>
                                <td>
                                    <div style="padding-bottom: 20px;">
                                        (i) The Works have been completed to the satisfaction of the Employer/Owner but
                                        the
                                        Employer/Owner has not made full payment of the Contract Sum;
                                    </div>
                                    <div style="padding-bottom: 20px;">
                                        (ii) The Employer/Owner refuses for whatsoever reason to allow the Contractor to
                                        conduct
                                        any rectification works;
                                    </div>
                                    <div>
                                        (iii) The Contractor is able to show that the defects are as a result of misuse,
                                        wilful
                                        act or faulty workmanship by the Employer/Owner, his employees, servant, agents
                                        or third
                                        party working for or under the directions of the Employer/Owner.
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div><br>
                    <p class="ft-bold">PERMITS AND APPROVALS</p>
                    <p>
                    <div>
                        <table>
                            <tr>
                                <td style="vertical-align: top;">15.</td>
                                <td style="width:15px;"></td>
                                <td>
                                    <span>
                                        The Contractor shall assist the Employer/Owner to obtain the necessary
                                        renovations permits
                                        as required by the relevant authorities. Subject to the Contractor providing the
                                        Employer/Owner an estimate of the reasonable expenses incurred, all expenses
                                        related to the
                                        application of the renovations permits shall be borne by the Employer/Owner.
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    </p>
                    <br />
                    <p class="ft-bold">TERMINATION</p>
                    <p>
                    <div>
                        <table>
                            <tr>
                                <td style="vertical-align: top;">16.</td>
                                <td style="width:15px;"></td>
                                <td>
                                    <span>
                                        In the event that the Works are not of an acceptable standard, or if the
                                        Contractor ceases
                                        work on the Premises without reasonable explanation for more than
                                        <span class="blank-border-bold">

                                            {{ $contractLists['termination_days'] }}
                                        </span>
                                        consecutive days, the Employer/Owner may terminate the Agreement by paying the
                                        Contractor
                                        only the value of the Works already performed, less compensation for
                                        inconvenience or
                                        additional expense caused as a result thereof or the Employer/Owner may exercise
                                        and enforce
                                        their strict legal rights for such stoppage.
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    </p>
                    <p>
                    <div>
                        <table>
                            <tr>
                                <td style="vertical-align: top;">17.</td>
                                <td style="width:15px;"></td>
                                <td>
                                    <span>
                                        In the event that this Agreement is terminated by the Employer/Owner for
                                        whatever reason
                                        through no fault or negligence on the part of the Contractor, the Contractor
                                        shall be
                                        entitled to recover from the Client to claim up to the value of the Works
                                        already carried
                                        out, including such amounts in respect of any materials supplied or purchased,
                                        work prepared
                                        (partially or fully).
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    </p>
                    <p>
                    <div>
                        <table>
                            <tr>
                                <td style="vertical-align: top;">18.</td>
                                <td style="width:15px;"></td>
                                <td>
                                    <span>
                                        In the event that this Agreement is terminated by the Contractor for whatever
                                        reason through
                                        no fault or negligence on the part of the Contractor, the Employer/Owner shall
                                        be entitled
                                        to recover from the Contractor the difference in the cost required to complete
                                        the Works.
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    </p><br><br><br>
                    <p class="ft-bold">NO VARIATION</p>
                    <p>
                    <div>
                        <table>
                            <tr>
                                <td style="vertical-align: top;">19.</td>
                                <td style="width:15px;"></td>
                                <td>
                                    <span>
                                        No variation of the Works described shall invalidate the Agreement, but any such
                                        variation,
                                        whether by addition, omission or substitution, together with the cost and effect
                                        on the Date
                                        for commencement and completion of the Works, shall be agreed in writing between
                                        the
                                        Employer/Owner and the Contractor before the variation is carried out, and the
                                        contracts sum
                                        stated in Clause (3) and the Date for commencement and / or completion of the
                                        Works stated
                                        in
                                        <span
                                            class="bg-transparent ft-bold border-b-2 border-[#8181A5] pb-2 w-64 text-center">Appendix
                                            A</span>
                                        shall be altered accordingly.
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    </p>
                    <p class="ft-bold">NON-ASSIGNMENT</p>
                    <p>
                    <div>
                        <table>
                            <tr>
                                <td style="vertical-align: top;">20.</td>
                                <td style="width:15px;"></td>
                                <td>
                                    <span>
                                        The Contractor shall not assign, transfer or in any other manner make over to
                                        any third
                                        party the benefit and/or burden of this Agreement without prior written consent
                                        of the
                                        Employer/Owner.
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    </p><br><br>
                    <p class="ft-bold">NOTICE</p>
                    <p>
                    <div>
                        <table>
                            <tr>
                                <td style="vertical-align: top;">21.</td>
                                <td style="width:15px;"></td>
                                <td>
                                    <span>
                                        Any notice or demand under this Agreement may be sent by certificate of posting
                                        to the
                                        Employer/Owner or Contractor (as the case may be at his address as stated herein
                                        or in any
                                        other modes as agreed by the Parties).
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    </p>
                    <p class="ft-bold">NON-WAIVER</p>
                    <p>
                    <div>
                        <table>
                            <tr>
                                <td style="vertical-align: top;">22.</td>
                                <td style="width:15px;"></td>
                                <td>
                                    <span>
                                        No failure to exercise and no delay in exercising on the part of the
                                        Employer/Owner or
                                        Contractor (as the case may be) shall operate as a waiver thereof nor shall any
                                        single or
                                        partial exercise of any right, power or privilege preclude any other or further
                                        exercise
                                        thereof or any other right, power or privilege.
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    </p>
                    <p class="ft-bold">GOVERNING LAW AND JURISDICTION</p>
                    <p>
                    <div>
                        <table>
                            <tr>
                                <td style="vertical-align: top;">23.</td>
                                <td style="width:15px;"></td>
                                <td>
                                    <span>
                                        Without prejudice to any rights of the parties, the Contractor agrees that any
                                        disputes
                                        arising out of or in connection with this Agreement shall be first referred to
                                        the Consumers
                                        Association of Singapore (CASE) Mediation Centre for resolution by mediation.
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    </p>
                    <p>
                    <div>
                        <table>
                            <tr>
                                <td style="vertical-align: top;">24.</td>
                                <td style="width:15px;"></td>
                                <td>
                                    <span>
                                        This Contract shall be governed by the laws of the Republic of Singapore.
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    </p>
                    <p>
                    <div>
                        <table>
                            <tr>
                                <td style="vertical-align: top;">25.</td>
                                <td style="width:15px;"></td>
                                <td>
                                    <span>
                                        This Contract shall be applicable to residential renovation works only.
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    </p>
                </div>
                <div class="flex pt-8 px-5">
                    <span class="text-sm text-[#1C1D21]">AS WITNESS the hands of the parties here to the day and year
                        first above written.</span>
                </div>

            </div>
            <div class="signature-section">
                <table class="ft-14 ft-semi-bold" style="float: left;">
                    <tbody>
                        <tr>
                            <td colspan="2">
                                <img src="{{ 'data:image/png;base64,' . $contractLists['encode_owner_signature'] }}"
                                    style="height:100px;border-bottom:1px solid black;">

                                <p>EMPLOYER/OWNER</p>

                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">In The presence of {{ $contractLists['employer_witness_name'] }} </td>

                        </tr>

                    </tbody>
                </table>
                <div style="float: right;">
                    <table class="ft-14 ft-semi-bold">
                        <tbody>
                            <tr>
                                <td colspan="2">
                                    <img src="{{ 'data:image/png;base64,' . $contractLists['encode_contractor_signature'] }}"
                                        style="height:100px;border-bottom:1px solid black;">

                                    <p>CONTRACTOR</p>
                                </td>
                            </tr>


                            <tr>
                                <td colspan="2">In The presence of {{ $contractLists['contractor_witness_name'] }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="page-1 page">
            <div class="content-center" style="padding-top: 40px;padding-bottom:30px;">
                <p class="text-underline">APPENDIX A</p>
            </div>
            <div class="content-center" style="padding-bottom:30px;">
                <p class="ft-bold">WORKS</p>
            </div>
            <table>
                <tr>
                    <td style="vertical-align: top;">1.</td>
                    <td style="width:20px;"></td>
                    <td>
                        Drawings (attached)
                    </td>
                </tr>
                <tr>
                    <td style="height: 50px;"></td>
                </tr>
                <tr>
                    <td style="vertical-align: top;">2.</td>
                    <td style="width:20px;"></td>
                    <td>
                        Stages of Renovation (to identify) including dates of completion
                    </td>
                </tr>
                <tr>
                    <td style="height: 50px;"></td>
                </tr>
                <tr>
                    <td style="vertical-align: top;">3.</td>
                    <td style="width:20px;"></td>
                    <td>
                        Defects / liability period (to specify)
                    </td>
                </tr>
            </table>
        </div>

        <div class="page-1 page">
            <div class="content-center" style="padding-top: 40px;padding-bottom:30px;">
                <p class="text-underline">APPENDIX B</p>
            </div>
            <div class="content-center" style="padding-bottom:50px;">
                <p class="ft-bold">CONTRACT DOCUMENTS</p>
            </div>
            <div class="content-center" style="padding-bottom:30px;">
                <p class="text-underline">OPTIONAL</p>
            </div>
            <div class="content-center" style="padding-bottom:10px;">
                <p class="ft-bold">CONTRACTOR'S WORKS PROGRAMME</p>
                <p>(EXAMPLE)</p>
            </div>
            <table class="appendix-b">
                <tr>
                    <td></td>
                    <td>% of overall works</td>
                    <td>No. of weeks from commencement date</td>
                </tr>
                <tr>
                    <td style="width: 500px;">
                        <ol>
                            <li>Date of commencement</li>
                            <li>Completion of fabrication at factory</li>
                            <li>Delivery of suppliers</li>
                            <li>Installation</li>
                            <li>Delivery of suppliers for wall/floor</li>
                            <li>Complete furnishing for wall/floor</li>
                            <li>Delivery of woodwork</li>
                            <li>Completion installation of woodwork</li>
                            <li>Delivery of furniture/soft furnishings</li>
                            <li>Completion installation of furniture/soft furnishings</li>
                        </ol>
                    </td>
                    <td>

                    </td>
                    <td>

                    </td>
                </tr>
            </table>
        </div>

        <div class="page-1 page">
            <div class="content-center" style="padding-top: 40px;padding-bottom:30px;">
                <p class="text-underline">APPENDIX C</p>
            </div>

            <div class="content-center" style="padding-bottom:10px;">
                <p class="ft-bold">PAYMENT SCHEDULE</p>
                <p>(EXAMPLE)</p>
            </div>
            <table class="appendix-b">
                <tr>
                    <td align="center" colspan="3" class="ft-bold" style="height: 50px;">
                        GENERAL RENOVATION WORK (BUILDER' WORK; MECHANICAL, ELECTRICAL, PLUMBING, ETC)
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>% playable at various stages</td>
                    <td>Amount to be paid at various stage ($)</td>
                </tr>
                <tr>
                    <td style="width: 500px;">
                        <ol>
                            <li>Immediately on signing this agreement</li>
                            <li>On practical completion of each stage of works
                                including all finishes etc. If the Works are of the specified
                                standard, the Employer/Owner will make payment on receiving the Contractor's account.
                            </li>
                            <li>____ days after satisfactory completion of all Works.</li>

                        </ol>
                    </td>
                    <td>

                    </td>
                    <td>

                    </td>
                </tr>
                <tr>
                    <td>Total</td>
                    <td>100%</td>
                    <td></td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>
