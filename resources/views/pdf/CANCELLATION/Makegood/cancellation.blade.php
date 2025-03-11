<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link type="text/css" media="all" rel="stylesheet" href="{{ public_path('css/font_style.css') }}">
    <link type="text/css" media="all" rel="stylesheet" href="{{ public_path('css/common.css') }}">
    <style>
        .main-table {
            width: 100%;
            border-collapse: collapse;
            border: 5px solid black;
        }

        .main-table td,
        .main-table th {
            border: 1px solid rgba(0, 0, 0, 0.081) !important;
            padding: 2px;
        }

        .content-pdf th {
            padding: 10px !important;
            background-color: #CFE2F3;
        }

        .bg-gray {
            background-color: #D9D9D9;
        }

        .bg-yellow {
            background-color: #FFFF00;
        }

        .bg-lightblue {
            background-color: #D3F7FF;
        }

        .bg-total {
            background-color: #FFF0E0;
        }

        .bg-green {
            background-color: #D3FFD6;
        }

        .total td {
            padding: 10px;
        }
    </style>
</head>

<body>
    @php
        function convertDate($dateString)
        {
            $originalFormat = 'd/m/Y';
            $date = DateTime::createFromFormat($originalFormat, $dateString);
            if ($date) {
                $formattedDate = $date->format('d M Y');
                return $formattedDate;
            } else {
                return '';
            }
        }

        function calculateByPercent($totalAmount, $percent)
        {
            $amount = $totalAmount * ($percent / 100);
            return number_format($amount, 2, '.', ',');
        }

        $originalIndex = [];

        function calculateMeasurement($item)
        {
            if (isset($item['calculation_type'])) {
                $final_result = '';

                if ($item['quantity'] != 0) {
                    $final_result = $item['is_fixed_measurement']
                        ? $item['measurement']
                        : $item['quantity'] . ' ' . $item['measurement'];
                } else {
                    $final_result = '';
                }

                return $final_result;
            } else {
                return '';
            }
        }
        function calculateTotalAmountForEachSections($section_id, $quotationList)
        {
            foreach ($quotationList->section_total_amount as $item) {
                if ($item->section_id == $section_id) {
                    $total_price = (float) $item->total_price;

                    if ($total_price != 0) {
                        return '-$ ' . number_format($total_price, 2);
                    } else {
                        return 0;
                    }
                }
            }
            return 0; // Return 0 if section_id not found
        }

        function getDescription($section_id, $quotationList)
        {
            foreach ($quotationList->section_total_amount as $item) {
                if ($item->section_id == $section_id) {
                    return $item->section_description;
                }
            }
        }
        function calculateTotalPrice($items)
        {
            if ($items['calculation_type'] == 'NORMAL') {
                $final_result = '';
                $totalAmount = floatval($items['quantity']) * floatval($items['price']);

                $totalAmountFormatted = number_format($totalAmount, 2);

                $final_result = $totalAmount == 0 ? '' : '-$' . ' ' . $totalAmountFormatted;

                return $final_result;
            } else {
                return '';
            }
        }
    @endphp
    <div class="content">
        <table class="main-table">
            @include('pdf.Common.Makegood.makegoodHeader', [
                'type' => 'Cancellation',
            ])
            <tbody class="content-pdf">
                <tr>
                    <th colspan="3">Project / Item Deliverables</th>
                    <th>Sub Total</th>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="3" style="height: 20px;"></td>
                </tr>
            </tbody>
            @if (count($sortQuotation) != 0)
                <tbody class="items-section">
                    @foreach ($sortQuotation as $index => $item)
                        @if (isset($item['emptyAOWData']) || count($item['hasAOWData']) != 0)
                            <tr class="bg-gray">
                                <td align="center">
                                    <span class="ft-b-14">{{ $index + 1 }}</span>
                                </td>
                                <td class="section-name" colspan="2" align="center">
                                    <span class="ft-b-14">{{ $item['section_name'] }}</span>
                                </td>
                                <td></td>

                            </tr>
                        @endif
                        @if (isset($item['emptyAOWData']))
                            @foreach ($item['emptyAOWData'] as $emptyAOW)
                                @php
                                    if (!isset($originalIndex[$item['section_id']])) {
                                        $originalIndex[$item['section_id']] = $index + 1;
                                        $secondaryIndex[$item['section_id']] = 1;
                                    } else {
                                        $secondaryIndex[$item['section_id']]++;
                                    }
                                    $countIndex =
                                        $originalIndex[$item['section_id']] .
                                        '.' .
                                        $secondaryIndex[$item['section_id']];
                                @endphp

                                <tr key="{{ $emptyAOW['id'] }}">
                                    <td align="center" style="vertical-align: top;width: 50px;padding: 4px;"
                                        class="ft-12">
                                        <span> {{ $countIndex }}</span>
                                    </td>
                                    <td class="ft-12" colspan="2">
                                        <span class="line-sp aow-item">{!! formatText($emptyAOW['name']) !!}</span>
                                    </td>

                                    <td align="center" class="ft-12 bg-lightblue">
                                        {{ calculateTotalPrice($emptyAOW) }}</td>
                                    </td>
                                </tr>
                            @endforeach
                        @endif

                        @if (count($item['hasAOWData']) != 0)
                            @foreach ($item['hasAOWData'] as $value)
                                <tr class="aow-name">
                                    <td></td>
                                    <td class="ft-12 bg-gray" colspan="2" align="center">
                                        <div>
                                            <span class="ft-b">{{ $value['area_of_work_name'] }} </span>
                                        </div>
                                    </td>
                                    <td class="bg-gray"></td>
                                </tr>

                                @foreach ($value['area_of_work_items'] as $hasAOW)
                                    @php
                                        if (!isset($originalIndex[$item['section_id']])) {
                                            $originalIndex[$item['section_id']] = $index + 1;
                                            $secondaryIndex[$item['section_id']] = 1;
                                        } else {
                                            $secondaryIndex[$item['section_id']]++;
                                        }
                                        $countIndex =
                                            $originalIndex[$item['section_id']] .
                                            '.' .
                                            $secondaryIndex[$item['section_id']];
                                    @endphp
                                    <tr key="{{ $hasAOW['id'] }}">
                                        <td align="center" style="vertical-align: top;width: 50px;padding: 4px;"
                                            class="ft-12">
                                            <span> {{ $countIndex }}</span>
                                        </td>
                                        <td class="ft-12" colspan="2">
                                            <span class="line-sp aow-item">{!! formatText($hasAOW['name']) !!}</span>
                                        </td>

                                        <td align="center" class="ft-12 bg-lightblue">
                                            {{ calculateTotalPrice($hasAOW) }}</td>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                            @if (calculateTotalAmountForEachSections($item['section_id'], $quotationList))
                                {{-- @if ($item['hasAOWData'][0]['area_of_work_items'][0]['calculation_type'] != 'NORMAL') --}}
                                    <tr>
                                        <td colspan="3" align="center" class="ft-b-12 bg-yellow">
                                            Subtotal
                                        </td>
                                        <td align="center" class="ft-b-12 bg-yellow">
                                            {{ calculateTotalAmountForEachSections($item['section_id'], $quotationList) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td colspan="3" style="height:20px;"></td>
                                    </tr>
                                {{-- @endif --}}
                            @endif
                        @endif
                        @if (getDescription($item['section_id'], $quotationList))
                            <tr>

                                <td></td>
                                <td class="ft-i-11" colspan="3">
                                    {{ getDescription($item['section_id'], $quotationList) }}
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
                @include('pdf.CANCELLATION.Makegood.cancellationSummaryComponent')
                @include('pdf.Common.Makegood.paymentBreakdownComponent')
                @include('pdf.Common.Makegood.signatureComponent')
                @include('pdf.Common.Makegood.termsComponent')
                @include('pdf.Common.Makegood.warrantyComponent')
                @include('pdf.Common.Makegood.timelineComponent')
                @include('pdf.Common.Makegood.paymentMethodComponent')
            @endif
        </table>
    </div>
</body>

</html>
