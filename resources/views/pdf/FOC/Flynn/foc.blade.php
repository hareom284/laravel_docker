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
        * {
            font-family: sans-serif;
            font-size: 12px;
            /* Use the custom font, fallback to sans-serif if unavailable */
        }
    </style>
</head>
<body>
    @php
    $originalIndex = [];
    function calculateMeasurement($item)
        {
            if (isset($item['calculation_type'])) {
                $final_result = '';

                if ($item['quantity'] != 0) {
                    $final_result = $item['is_fixed_measurement'] ? $item['measurement'] : $item['measurement'];
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
                    return '$ ' . number_format($total_price, 2);
                } else {
                    return 0;
                }
            }
        }
        return 0; // Return 0 if section_id not found
    }
    function calculateTotalPrice($items)
    {
        if ($items['calculation_type'] == 'NORMAL') {
            $final_result = '';
            $totalAmount = floatval($items['quantity']) * floatval($items['price']);

            $totalAmountFormatted = number_format($totalAmount, 2);

            $final_result = $totalAmount == 0 ? '' : '$' . ' ' . $totalAmountFormatted;

            return $items['is_FOC'] ? 'FOC' : preg_replace('/\B(?=(\d{3})+(?!\d))/', ',', $final_result);
        } else {
            return $items['is_FOC'] ? 'FOC' : '';
        }
    }

    function getDescription($section_id, $quotationList)
    {
        foreach ($quotationList->section_total_amount as $item) {
            if ($item->section_id == $section_id) {
                return $item->section_description;
            }
        }
    }
@endphp
<div style="clear: both;">
    @include('pdf.Common.Flynn.topHeader')
    <div style="clear: both;padding-top:60px;">
        <table border="1" style="border-collapse: collapse; border-color: transparent; width: 100%;">
            <tr>
                <td colspan="6" align="center">
                    <span class="ft-b underline">Renovation Works at
                        @if (isset($quotationData['properties']))
                            {{ $quotationData['properties']['block_num'] . ' ' . $quotationData['properties']['street_name'] . ' ' . '#' . $quotationData['properties']['unit_num'] }}
                        @endif
                    </span>
                </td>
            </tr>
            <tr>
                <td colspan="6" style="height: 20px;"></td>
            </tr>
            <tr style="border-bottom: 1px solid black;">
                <td style="vertical-align: top;width: 35px;" class="ft-b">

                </td>
                <td style="width: 500px;" class="ft-b">
                    Description
                </td>
                <td class="ft-b" style="min-width:50px;" align="center">

                </td>
                <td class="ft-b" align="center" style="width: 100px;">
                    Quantity
                </td>
                <td class="ft-b" style="min-width:100px;" align="center">
                    Unit Price
                </td>
                <!-- Adjust width -->

                <td align="center" class="ft-b" style="min-width:100px;">
                    Amount
                </td>
                <!-- Adjust width -->

            </tr>
        </table>
    </div>
    @if (count($sortQuotation) != 0)
        <div class="pdf-content">
            @foreach ($sortQuotation as $index => $item)
                <div key="{{ $item['section_id'] }}" class="{{ $current_folder_name == 'Twp' ? 'section-container' : '' }}">
                    <table border="1"
                        style="border-collapse: collapse; border-color: transparent; width: 100%;">
                        @if (isset($item['emptyAOWData']) || count($item['hasAOWData']) != 0)
                            {{-- <thead> --}}
                            <tr>
                                <td style="width: 32px;">
                                    <span class="ft-b underline">{{ chr(65 + $index) }}</span>
                                </td>
                                <td class="section-name" style="width: 600px;">
                                    <span class="ft-b underline">{{ $item['section_name'] }}</span>
                                </td>
                                <td colspan="2"></td>
                            </tr>

                            {{-- </thead> --}}
                        @endif
                        @if (getDescription($item['section_id'], $quotationList))
                            <tr>
                                <!-- Adjust width -->
                                <td style="width: 32px;">
                                    <span class="ft-b-12 "></span>
                                </td>
                                <td class="ft-i-11" style="width: 600px;">
                                    {{ '( ' . getDescription($item['section_id'], $quotationList) . ' )' }}
                                </td>
                                <td colspan="4"></td>
                            </tr>
                        @endif
                        <tr>
                            <td colspan="6" style="height: 5px"></td>
                        </tr>
                    </table>

                    @if (count($item['hasAOWData']) != 0)
                        @foreach ($item['hasAOWData'] as $value)
                            <table border="1"
                                style="border-collapse: collapse; border-color: transparent; width: 100%;">
                                <thead>
                                    <tr class="aow-name">
                                        <td></td>
                                        <td class="ft-b-12" style="width: 600px;">
                                            <div>
                                                <span class="underline">{{ $value['area_of_work_name'] }} </span>
                                            </div>
                                        </td>
                                        <td colspan="4"></td>
                                    </tr>
                                </thead>
                                <tbody>
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
                                            <td style="vertical-align: top;width: 40px;" class="ft-12">
                                                <span>{{ $countIndex }}</span>
                                            </td>
                                            <td style="width: 500px;" class="ft-12">
                                                <!-- Adjust the width as needed -->
                                                <span class="aow-item">{!! formatText($hasAOW['name']) !!}</span>
                                            </td>
                                            <td class="ft-12" style="min-width:50px;" align="center">
                                                @if ($hasAOW['quantity'] != 0)
                                                    {{ $hasAOW['quantity'] }}
                                                @endif
                                            </td>
                                            <td class="ft-12" align="center" style="width: 100px;">
                                                {{ calculateMeasurement($hasAOW) }}
                                            </td>
                                            <td class="ft-12" style="min-width:100px;" align="center">
                                                @if ($hasAOW['price'] != 0)
                                                    $ {{ $hasAOW['price'] }}
                                                @endif
                                            </td>
                                            <!-- Adjust width -->

                                            <td align="center" class="ft-12" style="min-width:100px;">
                                                {{ calculateTotalPrice($hasAOW) }}
                                            </td>
                                            <!-- Adjust width -->

                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endforeach
                    @endif

                </div>
                <br />
            @endforeach
            @include('pdf.Common.noteAndDisclaimerComponent')

            @include('pdf.FOC.Flynn.focSummaryComponent')
            <div class="{{ isset($quotationList->terms) ? 'page' : '' }}">
                @if (isset($quotationList->terms))
                    <div class="term-position">
                        @include('pdf.Common.termsAndConditionComponent', [
                            'terms' => $quotationList->terms,
                        ])
                    </div>
                @endif
            </div>

        </div>
    @endif
</div>
</body>
</html>
