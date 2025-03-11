<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=\, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link type="text/css" media="all" rel="stylesheet" href="{{ public_path('css/font_style.css') }}">
    <link type="text/css" media="all" rel="stylesheet" href="{{ public_path('css/common.css') }}">
    <style>
        .container {
            padding-top: 50px;
        }
    </style>
</head>

<body>

    @php
        $originalIndex = [];

        function checkAdditionOrDeduction($status, $value)
        {
            if (($status && strpos($value, '-') !== false) || (!$status && strpos($value, '-') === false)) {
                return $value;
            }

            return '';
        }

        function calculateMeasurement($item)
        {
            if (isset($item['calculation_type'])) {
                $final_result = '';

                if ($item['quantity'] != 0) {
                    //before adding sqft_calculation feature
                    // $final_result = $item['is_fixed_measurement']
                    //     ? $item['measurement']
                    //     :  $item['measurement'];

                    //after addin sqft_calculation feature
                    if ($item['is_fixed_measurement']) {
                        $final_result = $item['measurement'];
                    } else {
                        $unit = $item['measurement'];
                        $isSquareFoot = $unit === 'sqft';

                        if ($isSquareFoot) {
                            $hasDimension = isset($item['length']) && isset($item['breadth']);

                            if ($hasDimension) {
                                $isDefaultSize =
                                    ($item['length'] == 1 || $item['length'] == null) &&
                                    ($item['breadth'] == 1 || $item['breadth'] == null);
                                //  ($item['height'] == 1 || $item['height'] == null);

                                if ($isDefaultSize) {
                                    $final_result =  $unit;
                                } else {
                                    $l = $item['length'];
                                    $b = $item['breadth'];
                                    // $h = $item['height'];
                                    $final_qty = $item['quantity'];
                                    $final_result =
                                        '(W ' .
                                        ($b === 0 || $b === 1 ? '' : $b . 'mm') .
                                        ' x H/D ' .
                                        ($l === 0 || $l === 1 ? '' : $l . 'mm') .
                                        // ' x H ' . ($h === 0 || $h === 1 ? '' : $h . 'mm') .
                                        ') ' .
                                        $final_qty .
                                        ' ' .
                                        $unit;

                                    // $final_result = "(L {$item['length']}mm x B {$item['breadth']}mm x H {$item['height']}mm) {$item['quantity']} $unit";
                                }
                            } else {
                                $final_result =  $unit;
                            }
                        } else {
                            $final_result =  $unit;
                        }
                    }
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
                        return number_format($total_price, 2);
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

                $final_result = $totalAmount == 0 ? '' : '$' . ' ' . $totalAmountFormatted;

                return $items['is_FOC'] ? 'FOC' : preg_replace('/\B(?=(\d{3})+(?!\d))/', ',', $final_result);
            } else {
                return $items['is_FOC'] ? 'FOC' : '';
            }
        }

        function isQuantityGreaterThan($item, $original_quantities)
        {
            // Check if the item exists in the array
            if (array_key_exists($item['quotation_template_item_id'], $original_quantities)) {
                return $item['current_quantity'] > $original_quantities[$item['quotation_template_item_id']];
            } else {
                return true;
            }

            // Return false if the item does not exist or the quantity is not greater
            return false;
        }
    @endphp
    <div class="container">
        <div class="avoid-break">
            @include('pdf.Common.Ideology.topHeader')
            <br />
            @include('pdf.VARIATIONORDER.Ideology.variationSummaryComponent')
            <div class="payment-terms-text" style="white-space: pre-wrap;">{!! trim($quotationData['payment_terms_text']) !!}</div>
            @include('pdf.Common.Ideology.signatureComponent')
        </div>

        <div style="clear: both;padding-top:60px;" class="page">
            <table border="1" style="border-collapse: collapse; border-color: transparent; width: 100%;">
                <tr>
                    <td colspan="6" style="height: 20px;"></td>
                </tr>
                <tr>
                    <td style="vertical-align: top;width: 35px;" class="ft-b"></td>
                    <td style="width: 500px;" class="ft-b"></td>
                    <td class="ft-b" style="min-width:50px;" align="center">Qty</td>
                    <td class="ft-b" style="width: 50px;" align="center">
                        Unit
                    </td>
                    <td class="ft-b" style="min-width:50px;" align="center"></td>
                    <td align="center" class="ft-b" style="min-width:100px;">
                        Total
                    </td>
                </tr>
            </table>
        </div>
        @if (count($sortQuotation) != 0)
            <div class="pdf-content">
                @foreach ($sortQuotation as $index => $item)
                    <div key="{{ $item['section_id'] }}"
                        class="{{ $current_folder_name == 'Twp' ? 'section-container' : '' }}">
                        <table border="1"
                            style="border-collapse: collapse; border-color: transparent; width: 100%;">
                            @if (isset($item['emptyAOWData']) || count($item['hasAOWData']) != 0)
                                {{-- <thead> --}}
                                <tr>
                                    <td style="width: 32px;">
                                        <span class="ft-b">{{ chr(65 + $index) }} -</span>
                                    </td>
                                    <td class="section-name" style="width: 600px;">
                                        <span class="ft-b">{{ $item['section_name'] }}</span>
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
                        @php
                            // Initialize an item counter for each section
                            $itemCounter = 1;
                        @endphp
                        @if (count($item['hasAOWData']) != 0)
                            @foreach ($item['hasAOWData'] as $aowIndex => $value)
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
                                            <tr key="{{ $hasAOW['id'] }}">
                                                <td style="vertical-align: top;width: 40px;" class="ft-12">
                                                    <span>{{ $aowIndex + 1 . '.' . $itemCounter }}</span>
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
                                                <td class="ft-12" style="width: 100px;" align="center">
                                                    {{ calculateMeasurement($hasAOW) }}
                                                </td>
                                                <td class="ft-12" style="min-width:50px;">
                                                    &nbsp;$
                                                </td>

                                                <td align="center" class="ft-12" style="min-width:100px;">
                                                    {{ calculateTotalPrice($hasAOW) }}
                                                </td>
                                                @if (!empty($item['items']))
                                                    @foreach ($item['items'] as $subItem)
                                                        @include('pdf.Common.Ideology.subitems', [
                                                            'item' => $subItem,
                                                            'level' => 1,
                                                        ])
                                                    @endforeach
                                                @endif
                                            </tr>
                                            @php
                                                $itemCounter++;
                                            @endphp
                                        @endforeach
                                    </tbody>
                                </table>
                            @endforeach
                        @endif
                        <table border="1"
                            style="border-collapse: collapse; border-color: transparent; width: 100%;">
                            <tr class="ft-12">
                                <td style="width: 40px;"></td>
                                <td style="width: 500px;"></td>
                                <td style="min-width:50px;"></td>
                                <td style="width: 100px;" class="ft-b" align="center">Total:</td>
                                <td style="min-width:50px;" class="bg-yellow">&nbsp;$</td>
                                <td align="center" class="ft-b bg-yellow" style="min-width:100px;">
                                    {{ calculateTotalAmountForEachSections($item['section_id'], $quotationList) }}
                                </td>
                            </tr>
                        </table>
                    </div>
                    <br />
                @endforeach
                @include('pdf.Common.noteAndDisclaimerComponent')
            </div>
        @endif
        <br />
        <div>
            @include('pdf.Common.noteAndDisclaimerComponent')
            @include('pdf.Common.Ideology.signatureComponent')
        </div>
    </div>
</body>

</html>
