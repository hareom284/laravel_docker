<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link type="text/css" media="all" rel="stylesheet" href="{{ public_path('css/pdf.css') }}">

    <style>
        * {
            font-family: sans-serif;
            /* Use the custom font, fallback to sans-serif if unavailable */
        }

        .vertical-text-wrapper {
            display: table;
            vertical-align: bottom;
            height: 100%;

        }

        .vertical-text {
            transform: rotate(-90deg);
            -webkit-transform: rotate(-90deg);
            width: 20px;
        }

        .border-table table,
        .border-table th,
        .border-table td {
            border: 1px solid black;
            border-collapse: collapse;
            padding: 5px;
        }

        .align-bottom {
            vertical-align: bottom;


        }

        .avoid-break {
            page-break-inside: avoid;
        }

        .clear-border-table {
            border: 1px solid transparent !important;
            padding: 5px;
        }

        .header {
            color: #8181A5;
            text-decoration: underline;
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
            height: 70px !important;
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
</head>

<body>
    @if ($is_artdecor)
        @include('pdf.Common.artdecorEvoHeader', [
            'quotationData' => $quotationData,
        ])
    @endif
    <div class="border-table">
        <div style="width: 100%;text-align:center;">
            <h3 class="header">
                VARIATION ORDER FOR THE FOLLOWING ELECTRICAL WORKS (HDB)
            </h3>
        </div>
        <br />
        <table style="width: 100%;font-size: 12px;">
            <thead style="background:rgb(229, 231, 235);">
                <tr style="height: 150px" class="align-bottom">
                    <th>
                        NO
                    </th>
                    <th>
                        Description
                    </th>
                    @foreach ($roomLists as $room)
                        <th style="white-space: nowrap;">
                            <div class="vertical-text-wrapper">
                                <div class="vertical-text">{{ $room->room_name }}</div>
                            </div>
                        </th>
                    @endforeach
                    <th style="text-wrap: nowrap">
                        Qty
                    </th>
                    <th>
                        Unit Rate
                    </th>
                    <th>
                        Total ($)
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($itemLists as $index => $item)
                    <tr>
                        <td align="center">
                            {{ $index + 1 }}
                        </td>
                        <td>
                            {{ $item->item_name }}
                        </td>
                        @foreach ($item->rooms as $room)
                            <td align="center">
                                {{ $room->room_qty }}
                            </td>
                        @endforeach
                        <td align="center">
                            {{ $item->total_qty }}
                        </td>
                        <td align="center">
                            ${{ $item->unit_rate }}
                        </td>
                        <td align="center">
                            ${{ $item->total_amount }}
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="{{ count($roomLists) + 3 }}" align="right"
                        style="border: 1px solid transparent;border-right: 1px solid black;">
                        The Amount Of The Variation Works:
                    </td>
                    <td colspan="2" align="center">
                        ${{ number_format($totalAllAmount, 2) }}
                    </td>
                </tr>
                <tr>
                    <td colspan="{{ count($roomLists) + 3 }}" align="right"
                        style="border: 1px solid transparent;border-right: 1px solid black;">
                        GST {{ $gst_percentage }}%:
                    </td>
                    <td colspan="2" align="center">
                        ${{ number_format($gstAmount, 2) }}
                    </td>
                </tr>
                <tr>
                    <td colspan="{{ count($roomLists) + 3 }}" align="right"
                        style="border: 1px solid transparent;border-right: 1px solid black;">
                        GRAND TOTAL:
                    </td>
                    <td colspan="2" align="center">
                        ${{ number_format($grandTotal, 2) }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div style="margin: 10px 0;width:80%;font-size: 12px;" class="avoid-break">
        <p style="text-align: justify;">
            {{ $disclaimer }}
        </p>
    </div>
    <div class="signature-section avoid-break">
        <table class="avoid-break" style="float: left;font-size: 12px;">
            <tbody>
                <tr>
                    <td colspan="2">
                        <div>Yours faithfully</div>
                        <div>Sales Rep.Signature</div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <img src="{{ 'data:image/png;base64,' . $salepersonSignatureImage }}" style="height:100px">
                    </td>
                </tr>
                <tr>
                    <td colspan="2">{{ $company_name }}</td>
                </tr>
                <tr>
                    <td>NAME </td>
                    <td>: {{ $saleperson->name }}</td>
                </tr>
                <tr>
                    <td>DESIGNATION </td>
                    <td>: {{ $saleperson->rank }}</td>
                </tr>

            </tbody>
        </table>
        <div style="float: right;" class="avoid-break">
            @if (count($customerSignatureImage) > 0)
                @foreach ($customerSignatureImage as $customer)
                    <table class="avoid-break" style="font-size: 12px;">
                        <tbody>
                            <tr>
                                <td colspan="2">
                                    <div> I/We Confirm Our Acceptance</div>
                                    <div>Client Signature</div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <img src="{{ 'data:image/png;base64,' . $customer['customer_signature'] }}"
                                        style="height:100px">
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="height:30px;"></td>
                            </tr>
                            <tr>
                                <td>NAME </td>
                                <td>:
                                    {{ $customer['customer']['name_prefix'] . ' ' . $customer['customer']['first_name'] . ' ' . $customer['customer']['last_name'] }}
                                </td>
                            </tr>
                            <tr>
                                <td>NRIC </td>
                                <td>: {{ $customer['customer']['customers']['nric'] }}</td>
                            </tr>
                            <tr>
                                <td>DATE </td>
                                <td>: {{ $signed_date }}</td>
                            </tr>
                        </tbody>
                    </table>
                @endforeach
            @endif

        </div>
    </div>
</body>

</html>
