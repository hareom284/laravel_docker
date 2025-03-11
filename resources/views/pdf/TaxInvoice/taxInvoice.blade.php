<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=\, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Statement of Account</title>
    <link type="text/css" media="all" rel="stylesheet" href="{{ public_path('css/pdf.css') }}">
</head>

<style>
    * {
        font-family: sans-serif;
        /* Use the custom font, fallback to sans-serif if unavailable */
    }

    .pdf-content {
        padding: 20px 0;
    }

    .first_text {
        font-size: 14px;
        font-weight: bold;
        text-decoration: underline;
    }

    .text-2 {
        color: #1C1D21;
        font-weight: 900;
        font-size: 12px
    }

    .text-4 {
        color: #1C1D21;
        font-weight: 900;
        font-size: 14px
    }

    .term-list li {
        padding: 5px 0;
    }

    .term-list li::before {
        counter-increment: item;
        /* Increment the counter */
        content: ' ' !important;
        /* Display the counter and close with a parenthesis */
        position: absolute;
        /* Position the pseudo-element */
        left: 0;
        /* Align to the left */
    }

    .page {
        /* overflow: hidden; */
        page-break-before: always;
    }

    .right-section {
        float: right;
        width: 40%;
    }

    .left-section {
        float: left;
    }

    .text-center {
        text-align: center;
    }

    .doc-type {
        margin: 0 auto;
        width: 100%;
        text-align: center;
    }

    .bottom-header {
        clear: both;
    }

    .bottom-header-content {
        border: 1px solid black;
        height: 110px !important;
    }

    .detail th,
    .detail td {
        border: 1px solid transparent;
    }

    .detail table {
        border: 1px solid transparent !important;
        font-size: 12px;
        width: 100%;

    }
</style>

<body>

    @php

        function renovationDocumentShort($type, $version)
        {
            switch ($type) {
                case 'QUOTATION':
                    return "Q$version";
                    break;

                case 'VARIATIONORDER':
                    return "VO$version";
                    break;

                case 'FOC':
                    return "FOC$version";
                    break;

                case 'CANCELLATION':
                    return "CN$version";
                    break;

                case 'ELECTRICALVARIATIONORDER':
                    return "EVO$version";
                    break;

                default:
                    return $version;
                    break;
            }
        }

        function calculateTotalSignedAmount($data)
        {
            $totalAmount = 0;
            
            foreach ($data as $value) {
                if ($value->type == 'CANCELLATION') {
                    $totalAmount = $totalAmount - $value->total_amount;
                } else {
                    $totalAmount = $totalAmount + $value->total_amount;
                }
            }
            return number_format($totalAmount, 2, '.', ',');
        }

    @endphp

    <div>
        @if ($is_artdecor)
            @include('pdf.Common.artdecorTaxHeader', [
                'quotationData' => $quotationData,
            ])
        @endif
        <div class="pdf-content">
            <p class="first_text">INVOICES</p>

            <p class="text-2">Agreement Amount:</p>

            <div>

                <table border="1" style="border-collapse: collapse; border-color: transparent; width: 100%;">
                    @foreach ($project->renovation_documents as $detail)
                        <tr>
                            <td style="vertical-align: top;width: 500px;" class="ft-12">
                                <span>Total amount signed as per AGR:
                                    {{ $detail->agreement_no ?? '' }}
                                    dated {{ $detail->signed_date }}</span>
                            </td>
                            <td style="width: 100px;" align="right" class="ft-12">
                                {{ $detail->type == 'CANCELLATION' ? '-' : '' }}${{ number_format($detail->total_amount, 2, '.', ',') }}
                            </td>
                        </tr>
                    @endforeach

                    <tr>
                        <td style="vertical-align: top;width: 500px;" class="ft-12">
                            <span style="float:right;">Total Amount Signed:</span>
                        </td>
                        <td style="width: 100px;" align="right" class="ft-12">
                            ${{ calculateTotalSignedAmount($project->renovation_documents) }}
                        </td>
                    </tr>
                </table>
                @if ($project->saleReport->customer_payments && count($project->saleReport->customer_payments) > 0)
                    <p class="text-2" style="margin-top: 30px;">Progressive Payment Mode:</p>

                    <table border="1" style="border-collapse: collapse; border-color: transparent; width: 100%;">
                        @foreach ($project->saleReport->customer_payments as $index => $payment)
                            <tr>
                                <td style="vertical-align: top;width: 500px;" class="ft-12">
                                    <span>{{ $index + 1 }}st payment received on {{ $payment->created_at }}</span>
                                </td>
                                <td style="width: 100px;" align="right" class="ft-12">
                                    ${{ number_format($payment->amount, 2, '.', ',') }}
                                </td>
                            </tr>
                        @endforeach

                        <tr>
                            <td style="vertical-align: top;width: 500px;" class="ft-12">
                                <span style="float:right;">Total Amount received:</span>
                            </td>
                            <td style="width: 100px;" align="right" class="ft-12">
                                ${{ number_format($project->saleReport->paid, 2, '.', ',') }}
                            </td>
                        </tr>

                        <tr>
                            <td style="vertical-align: top;width: 500px;" class="ft-12">
                                <span style="float:right;">Balance last Payment:</span>
                            </td>
                            <td style="width: 100px;" align="right" class="ft-12">
                                ${{ number_format($project->saleReport->remaining, 2, '.', ',') }}
                            </td>
                        </tr>

                    </table>
                @endif

                <div class="signature-section avoid-break">
                    <table style="float: left;" class="ft-12 avoid-break">
                        <tbody>
                            <tr>
                                <td colspan="2">
                                    <div>Yours faithfully</div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <img src="{{ 'data:image/png;base64,' . $saleperson_signature }}"
                                        style="height:100px">
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">{{ $project->company->name }}</td>
                            </tr>
                            <tr>
                                <td colspan="2">{{ $saleperson->staffs->rank->rank_name }}</td>
                            </tr>
                            <tr>
                                <td>Name </td>
                                <td>: {{ $saleperson->first_name . ' ' . $saleperson->last_name }}</td>
                            </tr>
                            <tr>
                                <td>MOBILE </td>
                                <td>: {{ $saleperson->contact_no }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <div style="float: right;" class="avoid-break">
                        <table class="ft-12 avoid-break">
                            <tbody>
                                <tr>
                                    <td colspan="2">
                                        <img src="{{ 'data:image/png;base64,' . $manager_signature }}"
                                            style="height:100px">
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">{{ $project->company->name }}</td>
                                </tr>
                                <tr>
                                    <td colspan="2">CREATIVE DIRECTOR</td>
                                </tr>
                                <tr>
                                    <td>Name </td>
                                    <td>: {{ $manager->first_name . ' ' . $manager->last_name }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
            @if ($is_magnum)
                <div class="term-list avoid-break text-4" style="clear: both;">
                    <table>
                        <tbody>
                            <tr>
                                <td style="vertical-align: top;padding-top:10px;">
                                    <div>
                                        <div>Note: Kindly remit your payment by T/T through our bank as follows:</div>

                                        <div>Beneficiary Bank Account Name : MAGNUM INTERIOR</div>
                                        <div>Bank Name : OCBC Bank</div>
                                        <div>Bank Code : 7339</div>
                                        <div>Bank Branch Code : 660</div>
                                        <div>Bank Swift Code : OCBCSGSG</div>
                                        <div>Bank Account No. : 660-863994-001</div>
                                        <div>PayNow : 53164956C</div>
                                        <div>Bank Branch Address : Holland Village</div>

                                    </div>
                                </td>
                                <td>
                                    <img src="{{ public_path() . '/images/magnumqr.png' }}" height="400" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</body>

</html>
