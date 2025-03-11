<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=\, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link type="text/css" media="all" rel="stylesheet" href="{{ public_path('css/pdf.css') }}">
    <link type="text/css" media="all" rel="stylesheet" href="{{ public_path('css/font_style.css') }}">
    <link type="text/css" media="all" rel="stylesheet" href="{{ public_path('css/common.css') }}">
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var headers = document.querySelectorAll('.section-header');
        for (var i = 0; i < headers.length; i++) {
            var header = headers[i];
            var dynamicDiv = header.nextElementSibling; // Get the next sibling which is the div for dynamic height
            var headerHeight = header.offsetHeight; // Get the height of the header
            
            // Set the height of the dynamic div to the height of the header
            dynamicDiv.style.minHeight = headerHeight + 'px'; // Set the height directly
        }
        });
    </script>
</head>

<body style="padding-top: 20px;">

    @php
        $originalIndex = [];
        function calculateMeasurement($item)
        {
            if (isset($item['calculation_type'])) {
                $final_result = '';

                if ($item['quantity'] != 0) {

                    //before adding sqft_calculation feature
                    // $final_result = $item['is_fixed_measurement']
                    //     ? $item['measurement']
                    //     : $item['quantity'] . ' ' . $item['measurement'];

                    //after addin sqft_calculation feature
                    if($item['is_fixed_measurement'])
                    {
                        $final_result = $item['measurement'];
                    }else{
                        $unit = $item['measurement'];
                        $isSquareFoot = $unit === 'sqft';

                        if($isSquareFoot)
                        {
                            $hasDimension = isset($item['length']) && isset($item['breadth']);

                            if($hasDimension)
                            {
                                $isDefaultSize = ($item['length'] == 1 || $item['length'] == null) &&
                                                 ($item['breadth'] == 1 || $item['breadth'] == null);
                                                //  ($item['height'] == 1 || $item['height'] == null);

                                if ($isDefaultSize) {
                                    $final_result = $item['quantity'] . ' ' . $unit;
                                } else {
                                    $l = $item['length'];
                                    $b = $item['breadth'];
                                    // $h = $item['height'];
                                    $final_qty = $item['quantity'];
                                    $final_result = '(W ' . ($b === 0 || $b === 1 ? '' : $b . 'mm') .
                                                ' x H/D ' . ($l === 0 || $l === 1 ? '' : $l . 'mm') .
                                                // ' x H ' . ($h === 0 || $h === 1 ? '' : $h . 'mm') .
                                                ') ' . $final_qty . ' ' . $unit;

                                    // $final_result = "(L {$item['length']}mm x B {$item['breadth']}mm x H {$item['height']}mm) {$item['quantity']} $unit";
                                }
                            }else{
                                $final_result = $item['quantity'] . ' ' . $unit;
                            }
                        }else{
                            $final_result = $item['quantity'] . ' ' . $unit;
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
                        return '$ ' . number_format($total_price, 2);
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

                $final_result = $totalAmountFormatted;

                return $items['is_FOC'] ? 'FOC' : preg_replace('/\B(?=(\d{3})+(?!\d))/', ',', $final_result);
            } else {
                return $items['is_FOC'] ? 'FOC' : '';
            }
        }

        function getDollarSign($items)
        {
            $totalAmount = floatval($items['quantity']) * floatval($items['price']);

            return $totalAmount == 0 ? '' : '$';
        }
    @endphp
    @if (count($sortQuotation) != 0)
        <div class="pdf-content" style="padding: 30px;">
            @if($current_folder_name == 'FiveFoot10')
            <div class="page-after">
                @include('pdf.Common.summaryComponent')
                @include('pdf.QUOTATION.Twp.quotationSummaryComponent')
            </div>
            <br/>
            <br/>
            @endif
            @foreach ($sortQuotation as $index => $item)
                <div key="{{ $item['section_id'] }}"
                    class="{{ $current_folder_name == 'Twp' ? 'section-container' : '' }}">
                    <table border="1" style="border-collapse: collapse; border-color: transparent; width: 100%;">
                        @if (isset($item['emptyAOWData']) || count($item['hasAOWData']) != 0)
                            {{-- <thead> --}}
                            <tr>
                                <td style="width: 32px;">
                                    <span class="ft-b-14 ">{{ chr(65 + $index) }}</span>
                                </td>
                                <td class="section-name" style="width: 600px;">
                                    <span class="ft-b-14 underline">{{ $item['section_name'] }}</span>
                                </td>
                                <td colspan="2"></td>
                            </tr>

                            {{-- </thead> --}}
                        @endif
                        @if (getDescription($item['section_id'], $quotationList))
                            <tr>
                                <!-- Adjust width -->
                                <td style="width: 32px;">
                                    <span class="ft-b-14 "></span>
                                </td>
                                <td class="ft-i-11" style="width: 600px;">
                                    {{ '( ' . getDescription($item['section_id'], $quotationList) . ' )' }}
                                </td>
                                <td colspan="2"></td>
                            </tr>
                        @endif
                        <tr>
                            <td colspan="4" style="height: 10px"></td>
                        </tr>
                    </table>

                    {{-- Empty AOW items deprecated, all items should have a proper AOW --}}
                    @if (isset($item['emptyAOWData']))
                        <table border="1"
                            style="border-collapse: collapse; border-color: transparent; width: 100%;">

                            @foreach ($item['emptyAOWData'] as $emptyAOW)
                                @php
                                    // Increment or initialize the count for the section_id
                                    if (isset($originalIndex[$item['section_id']])) {
                                        $originalIndex[$item['section_id']]++;
                                    } else {
                                        $originalIndex[$item['section_id']] = 1;
                                    }
                                    $countIndex = $originalIndex[$item['section_id']];
                                @endphp


                                <tr key="{{ $emptyAOW['id'] }}">
                                    <td style="vertical-align: top;width: 60px;" class="ft-12">
                                        <span>{{ chr(65 + $index) }}.{{ $countIndex }}</span>
                                    </td>
                                    <td style="width: 600px;" class="ft-12"> <!-- Adjust the width as needed -->
                                        <span>{!! formatText($emptyAOW['name']) !!}</span>
                                    </td>
                                    <td style="width: 400px;" align="center" class="ft-12">
                                        {{ calculateMeasurement($emptyAOW) }}</td>
                                    <!-- Adjust width -->

                                    <td style="width: 90px;" align="right" class="ft-12">
                                        {{ calculateTotalPrice($emptyAOW) }}</td>
                                    <!-- Adjust width -->
                                </tr>
                            @endforeach
                        </table>
                    @endif

                    @if (count($item['hasAOWData']) != 0)
                        @foreach ($item['hasAOWData'] as $value)
                            <table border="1"
                                style="border-collapse: collapse; border-color: transparent; width: 100%;">
                                <thead>
                                    <tr class="aow-name">
                                        <td></td>
                                        <td class="ft-12" style="width: 600px;">
                                            <div>
                                                <span class="underline">{{ $value['area_of_work_name'] }} </span>
                                            </div>
                                        </td>
                                        <td colspan="2"></td>
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
                                            <td style="vertical-align: top;width: 60px;" class="ft-12">
                                                <span>{{ chr(65 + $index) }} . {{ $countIndex }}</span>
                                            </td>
                                            <td style="width: 600px;" class="ft-12">
                                                <!-- Adjust the width as needed -->

                                                <span class="aow-item">{!! formatText($hasAOW['name']) !!}</span>
                                            </td>
                                            <td style="width: 400px;" align="center" class="ft-12">
                                                {{ calculateMeasurement($hasAOW) }}</td>
                                            <!-- Adjust width -->

                                            <td style="width: 100px;" align="right" class="ft-12">
                                                {{ calculateTotalPrice($hasAOW) }}
                                            </td>
                                            <!-- Adjust width -->
                                            @if (!empty($hasAOW['items']))
                                                @foreach ($hasAOW['items'] as $subItem)
                                                    @include('pdf.VARIATIONORDER.AcCarpentry.variation_subitems', [
                                                        'item' => $subItem,
                                                        'level' => 1,
                                                    ])
                                                @endforeach
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endforeach
                        @include('pdf.Common.showSubTotalComponent', [
                            'name' =>
                                $current_folder_name == 'Twp' || $current_folder_name == 'Jream' || $current_folder_name == 'Henglai'
                                    ? 'TOTAL AMOUNT'
                                    : ' Sub-Total',
                            'is_bold' =>
                                $current_folder_name == 'Twp' || $current_folder_name == 'Jream' || $current_folder_name == 'Henglai' ? false : true,
                        ])
                    @endif

                </div>
                <br />
            @endforeach
            @include('pdf.Common.noteAndDisclaimerComponent')

            @if(!$hide_total && $current_folder_name != 'FiveFoot10')
                @include('pdf.VARIATIONORDER.AcCarpentry.variationSummaryComponent')
            @endif

            @if ($current_folder_name == 'Twp' || $current_folder_name == 'Jream')
                @include('pdf.Common.signatureComponent')
            @endif
            @if($current_folder_name == 'Optimum')
            <br/>
            <br/>
            <br/>
            <br/>
            @endif
            @if ($current_folder_name != 'Twp' && $current_folder_name != 'Jream' && $current_folder_name != 'Henglai')
                <div class="{{ isset($quotationList->terms) && $current_folder_name != 'Optimum' ? 'page' : '' }}">
                    @if (isset($quotationList->terms))
                        <div class="term-position">
                            @include('pdf.Common.termsAndConditionComponent', [
                                'terms' => $quotationList->terms,
                            ])
                        </div>
                    @endif
                </div>

                @include('pdf.Common.signatureComponent')
            @endif
        </div>
    @endif
</body>

</html>
