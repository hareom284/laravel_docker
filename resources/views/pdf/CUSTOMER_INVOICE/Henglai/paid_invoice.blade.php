<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=\, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link type="text/css" media="all" rel="stylesheet" href="{{ public_path('css/pdf.css') }}">
    <link type="text/css" media="all" rel="stylesheet" href="{{ public_path('css/font_style.css') }}">

    <style>
        .qr-code {
            margin: 20px 0;
            padding: 20px 0;
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

        .detail th,
        .detail td {
            border: 1px solid transparent;
        }

        .detail table {
            border: 1px solid black !important;
            font-size: 12px;
            width: 100%;

        }

        .footer-section {
            padding: 100px;
        }

        .payment-percentage table {
            border: 1px solid black !important;
            border-collapse: collapse;
            font-size: 12px;
            width: 100%;
        }

        .payment-percentage th,
        .payment-percentage td {
            border: 1px solid black !important;
        }

        .percentage-total {
            width: 100px;
        }

        .percentage-total table {
            border-collapse: collapse;
            font-size: 12px;
        }

        .percentage-total th,
        .percentage-total td {
            border: 1px solid transparent !important;
        }

        .page {
            /* overflow: hidden; */
            page-break-before: always;
        }

        .content table {
            border-collapse: collapse;
            font-size: 12px;
        }

        .content th,
        .content td {
            border: 1px solid black !important;
        }

        .bg-gray {
            background: #d9d9d9;

        }

        .clear-border td {
            border-left: 1px solid transparent !important;
            border-bottom: 1px solid transparent !important;
        }

        .total-pri .border-b {
            border-bottom: 1px solid black !important;
            padding-bottom: 5px;
            /* padding: 5px 0; */
        }

        .ft-12 {
            font-size: 12px !important;
        }

        .ft-b-12 {
            font-size: 12px !important;
        }

        .aow-item p {
            margin: 0 !important;
            padding: 0 !important;
        }

        .overlay-text {
            position: absolute;
            top: 25%;
            left: 20%;
            transform: translate(-50%, -50%);
            font-size: 72px;
            color: red;
            opacity: 0.5;
            font-weight: bold;
            z-index: 1;
            pointer-events: none;
        }
    </style>
</head>

@php
    $originalIndex = [];
@endphp

<body style="padding-top: 20px;">
    <div class="overlay-text">PAID</div>
    <div class="content-section">
        <div class="content">
            <table border="1" style="border-collapse: collapse; border-color: transparent; width: 100%;"
                class="ft-12">
                <tr class="bg-gray ft-b">
                    <td class="section-name" style="width: 50px;" align="center">
                        <span>Item</span>
                    </td>
                    <td style="padding: 2px;">Description</td>
                    <td align="center" style="padding: 2px;width: 200px;">Amount</td>
                </tr>
                @foreach ($sortQuotation as $index => $item)
                    @if (count($item['hasAOWData']) != 0)
                        <tr class="bg-gray">
                            <td style="padding: 2px; vertical-align:top;" align="center">{{ chr(65 + $index) }}</td>
                            <td class="section-name" style="padding:  2px;">
                                <span class="ft-12 ft-b">{{ $item['section_name'] }}</span>
                            </td>
                            <td></td>
                        </tr>
                    @endif

                    @if (count($item['hasAOWData']) != 0)
                        @foreach ($item['hasAOWData'] as $value)
                            <tr class="aow-name">
                                <td></td>
                                <td class="ft-12" style="padding: 2px;">
                                    <div>
                                        <span class="ft-bold">{{ $value['area_of_work_name'] }} </span>
                                    </div>
                                </td>
                                <td></td>
                            </tr>

                            @foreach ($value['area_of_work_items'] as $hasAOW)
                                @php
                                    if (isset($originalIndex[$item['section_id']])) {
                                        $originalIndex[$item['section_id']]++;
                                    } else {
                                        $originalIndex[$item['section_id']] = 1;
                                    }
                                    $countIndex = $originalIndex[$item['section_id']];
                                @endphp
                                <tr key="{{ $hasAOW['id'] }}">
                                    <td align="center" style="vertical-align: top;width: 50px;padding: 2px;"
                                        class="ft-12">
                                        <span> {{ chr(65 + $index) }} . {{ $countIndex }} </span>
                                    </td>
                                    <td style="padding: 2px;" class="ft-12">
                                        <!-- Adjust the width as needed -->
                                        <span class="line-sp aow-item">{!! formatText($hasAOW['name']) !!}</span>
                                    </td>

                                    <td style="padding: 2px 5px;" class="ft-12" align="right">
                                        <div class="left-section">$</div>
                                        <div>{{ calculateItemTotalPrice($hasAOW) }}</div>
                                    </td>
                                    @if (!empty($hasAOW['items']))
                                        @foreach ($hasAOW['items'] as $subItem)
                                            @php
                                                $subCountIndex = $countIndex . chr(97 + $index);
                                            @endphp
                                            @include('pdf.CUSTOMER_INVOICE.Henglai.subitem', [
                                                'item' => $subItem,
                                                'countIndex' => $subCountIndex,
                                                'level' => 1,
                                            ])
                                        @endforeach
                                    @endif
                                </tr>
                            @endforeach
                        @endforeach
                    @endif
                @endforeach
                @if (count($sortQuotation) > 0 && $qoDiscountAmount != 0)
                    <tr>
                        <td></td>
                        <td style="padding: 2px;" class="ft-12">Discount Amount</td>
                        <td style="padding: 2px 5px;" class="ft-12" align="right">
                            <div class="left-section">$</div>
                            <div>- {{ number_format($qoDiscountAmount, 2, '.', ',')  }}</div>
                        </td>
                    </tr>
                @endif
                @if (count($sortVariation) > 0)
                    <tr>
                        <td colspan="3" style="height: 15px;"></td>
                    </tr>
                    <tr>
                        <td colspan="3" align="center">Variation Order</td>
                    </tr>
                @endif
                @foreach ($sortVariation as $index => $item)
                    @if (count($item['hasAOWData']) != 0)
                        <tr class="bg-gray">
                            <td style="padding: 2px; vertical-align:top;" align="center">{{ chr(65 + $index) }}</td>
                            <td class="section-name" style="padding:  2px;">
                                <span class="ft-12 ft-b">{{ $item['section_name'] }}</span>
                            </td>
                            <td></td>
                        </tr>
                    @endif

                    @if (count($item['hasAOWData']) != 0)
                        @foreach ($item['hasAOWData'] as $value)
                            <tr class="aow-name">
                                <td></td>
                                <td class="ft-12" style="padding: 2px;">
                                    <div>
                                        <span class="ft-bold">{{ $value['area_of_work_name'] }} </span>
                                    </div>
                                </td>
                                <td></td>
                            </tr>

                            @foreach ($value['area_of_work_items'] as $hasAOW)
                                @php
                                    if (isset($originalVOIndex[$item['section_id']])) {
                                        $originalVOIndex[$item['section_id']]++;
                                    } else {
                                        $originalVOIndex[$item['section_id']] = 1;
                                    }
                                    $countIndex = $originalVOIndex[$item['section_id']];
                                @endphp
                                <tr key="{{ $hasAOW['id'] }}">
                                    <td align="center" style="vertical-align: top;width: 50px;padding: 2px;"
                                        class="ft-12">
                                        <span> {{ chr(65 + $index) }} . {{ $countIndex }} </span>
                                    </td>
                                    <td style="padding: 2px;" class="ft-12">
                                        <!-- Adjust the width as needed -->
                                        <span class="line-sp aow-item">{!! formatText($hasAOW['name']) !!}</span>
                                    </td>

                                    <td style="padding: 2px 5px;" class="ft-12" align="right">
                                        <div class="left-section">$</div>
                                        <div>{{ calculateItemTotalPrice($hasAOW) }}</div>
                                    </td>
                                    @if (!empty($hasAOW['items']))
                                        @foreach ($hasAOW['items'] as $subItem)
                                            @php
                                                $subCountIndex = $countIndex . chr(97 + $index);
                                            @endphp
                                            @include('pdf.CUSTOMER_INVOICE.Henglai.subitem', [
                                                'item' => $subItem,
                                                'countIndex' => $subCountIndex,
                                                'level' => 1,
                                            ])
                                        @endforeach
                                    @endif
                                </tr>
                            @endforeach
                        @endforeach
                    @endif
                @endforeach
                @if (count($sortVariation) > 0 && $voDiscountAmount != 0)
                    <tr>
                        <td></td>
                        <td style="padding: 2px;" class="ft-12">Discount Amount</td>
                        <td style="padding: 2px 5px;" class="ft-12" align="right">
                            <div class="left-section">$</div>
                            <div>- {{ number_format($voDiscountAmount, 2, '.', ',')  }}</div>
                        </td>
                    </tr>
                @endif
                <tr class="clear-border">
                    <td colspan="2" align="right" style="padding-right: 5px;">Total </td>
                    <td style="width: 100px;border: 1px solid black !important;padding: 8px 5px;" align="right">
                        <div class="left-section">$</div>
                        <div>{{ number_format($total_sales_amount, 2, '.', ',') }}</div>
                    </td>
                </tr>

                <tr class="clear-border">
                    <td colspan="2" align="right" style="padding-right: 5px;">Amount Paid </td>
                    <td style="width: 100px;border: 1px solid black !important;padding: 8px 5px;" align="right">
                        <div class="left-section">$</div>
                        <div>{{ number_format($paid_amount, 2, '.', ',') }}</div>
                    </td>
                </tr>

                <tr class="clear-border">
                    <td colspan="2" align="right" style="padding-right: 5px;">Balance Due </td>
                    <td style="width: 100px; border: 1px solid black !important;padding: 8px 5px;" align="right">
                        <div class="left-section">$</div>
                        <div>{{ number_format($remaining_amount, 2, '.', ',') }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="page">
            <br />
            <div class="total-section content" style="clear: both;">
                <table border="1" style="border-collapse: collapse; border-color: transparent; width: 100%;"
                    class="ft-12">
                    <tr class="bg-gray ft-b">
                        <td class="section-name" style="width: 50px;" align="center">
                            <span>Item</span>
                        </td>
                        <td style="padding: 2px;">Description</td>
                        <td align="center" style="padding: 2px;width: 200px;">Amount</td>
                    </tr>
                    <tr class="clear-border">
                        <td></td>
                        <td style="font-weight: bold;">{{ $remark }}</td>
                        <td style="border-right: 0 !important;"></td>
                    </tr>
                    <tr class="clear-border">
                        <td></td>
                        <td style="border: 1px solid black !important;">
                            <p style="margin: 0 !important;padding: 0 !important;">
                                All cheque should be crossed and made payable to<br />
                                HENG LAI FURNISHING CONTRACTOR.<br />
                                Details for Bank Transfer (FAST Transfer): Bank: OCBC<br />
                                Account No: 554-004192-001<br />
                                Paynow to UEN: 52841279E
                            </p>
                        </td>
                        <td style="border-bottom: 0 !important;border-right: 0 !important;"></td>
                    </tr>
                    <tr class="clear-border">
                        <td colspan="2" style="height: 10px;border-right: 0 !important;"></td>
                        <td
                            style="border-bottom: 1px solid black !important;border-right: 0 !important;border-top: 0 !important;">
                        </td>
                    </tr>
                    <tr class="clear-border">
                        <td colspan="2" align="right" style="padding-right: 5px;">Total </td>
                        <td style="width: 100px;border: 1px solid black !important;padding: 8px 5px;" align="right">
                            <div class="left-section">$</div>
                            <div>{{ number_format($amount, 2, '.', ',') }}</div>
                        </td>
                    </tr>
                </table>
                <br />
            </div>
            <br />
            <div class="signature-section" style="padding: 0 70px; clear: both;">
                <table class="avoid-break" style="float: left;">
                    <tbody>
                        <tr>
                            <td colspan="2" align="center">
                                <div style="font-weight: bold;">Issued By</div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" align="center">
                                <div style="height:50px;width:200px;" class="border-b">
                                </div>
                                <br />
                                <span>_____________________________</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" align="center">Heng Lai Furnishing Contractor</td>
                        </tr>
                    </tbody>
                </table>
                <div style="float: right;" class="avoid-break">
                    <table>
                        <tbody>
                            <tr>
                                <td colspan="2" align="center">
                                    <div style="font-weight: bold;">Received By</div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" align="center">
                                    <div style="height:50px;width:200px;" class="border-b">
                                    </div>
                                    <br />
                                    <span>_____________________________</span>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="height: 5px;"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
