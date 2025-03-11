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

                $final_result = $totalAmountFormatted == 0 ? '' : $totalAmountFormatted;

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
    <div class="header-section" style="margin-bottom: 20px; padding-right: 85px; padding-left: 85px;">
        <table>
            <tr>
                <td style="width: 70px;" class="ft-12">Date :</td>
                <td class="ft-12">
                    @if (isset($quotationData['signed_date']))
                        <span>{{ $quotationData['signed_date'] }}</span>
                    @else
                        <span>{{ $quotationData['created_at'] }}</span>
                    @endif
                </td>
            </tr>
            <tr>
                <td style="vertical-align: top; width: 70px;" class="ft-12">Name :</td>
                @if($settings['enable_show_last_name_first'] == 'true')
                <td class="ft-12">
                    @foreach ($customers_array as $customer)
                        <span>{{ $customer['name_prefix'] . ' ' . $customer['last_name'] . ' ' . $customer['first_name'] }}</span>
                    @endforeach
                </td>
                @else
                <td class="ft-12">
                    @foreach ($customers_array as $customer)
                        <span>{{ $customer['name_prefix'] . ' ' . $customer['first_name'] . ' ' . $customer['last_name'] }}</span>
                    @endforeach
                </td>
                @endif
            </tr>
            <tr>
                <td style="vertical-align: top; width: 70px;" class="ft-12">Address :</td>
                @if (isset($quotationData['properties']))
                    <td class="ft-12">
                        <span>
                            {{ $quotationData['properties']['block_num'] ? 'BLK ' . $quotationData['properties']['block_num'] : '' }} {{ ' ' . $quotationData['properties']['street_name'] . ' ' . '#' . $quotationData['properties']['unit_num'] }}
                            <br>
                            {{ $quotationData['properties']['postal_code'] ? 'S' . $quotationData['properties']['postal_code'] : '' }}
                        </span>
                    </td>
                @endif
            <tr>
                <td style="vertical-align: top; width: 70px;" class="ft-12">HP :</td>
                <td class="ft-12">
                    <span>{{ $quotationData['customers']['contact_no'] }}</span>
                </td>
            </tr>
        </table>
    </div>
    @if (count($sortQuotation) != 0)
        <div style="padding-right: 85px; padding-left: 85px;">
            @foreach ($sortQuotation as $index => $item)
            <p style="border-bottom: 0.5px solid black !important; width: 810px;"></p>
            <br/>
                <div key="{{ $item['section_id'] }}" class="{{ $item['is_page_break'] ? 'page' : '' }}">
                    <div class="section-header" style="
                            position: absolute;
                            left: 95px;    
                            padding: 5px 0 5px 0 !important;
                            width: 170px;
                            line-height: 1;">
                        @if (isset($item['emptyAOWData']) || count($item['hasAOWData']) != 0)
                            <p class="ft-12 ft-b" style="margin: 0; padding: 0;">{{ $item['section_name'] }}</p>
                            @if (getDescription($item['section_id'], $quotationList))
                                <p class="ft-12 italic" style="margin: 0; padding: 0; font-style: italic;padding-top: 10px;">
                                    {{ getDescription($item['section_id'], $quotationList) }}
                                </p>
                            @endif
                        @endif
                    </div>
                    <div class="dynamic-height-div">
                        @if (count($item['hasAOWData']) != 0)
                            @foreach ($item['hasAOWData'] as $value)
                                <table border="1"
                                    style="
                                        border-collapse: collapse;
                                        border-color: transparent;
                                        width: 100%;
                                        margin: 0 !important;"
                                >
                                    <thead>
                                        <tr class="aow-name">
                                            <td style="padding-left: 200px;"></td> <!-- Move only the first column -->
                                            <td class="ft-11 ft-b" style="width: 600px">
                                                <div>
                                                    <span class="underline">{{ $value['area_of_work_name'] }} </span>
                                                </div>
                                            </td>
                                            <td colspan="2"></td>
                                        </tr>
                                        <tr>
                                            <td style="height: 5px;"></td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $originalIndex[$item['section_id']] = 0;
                                        @endphp
                                        @foreach ($value['area_of_work_items'] as $hasAOW)
                                            @php
                                                $originalIndex[$item['section_id']]++;
                                                $countIndex = $originalIndex[$item['section_id']];
                                            @endphp
    
                                            <tr key="{{ $hasAOW['id'] }}">
                                                <td style="vertical-align: top;width: 15px; padding-left: 50px; padding-right: 5px; margin-right: 5px;" align="right" class="ft-12">
                                                    <span>{{ $countIndex }}</span>
                                                </td>
                                                <td style="width: 550px;" class="ft-12"> <!-- Move the description column -->
                                                    <span class="aow-item">{!! formatText($hasAOW['name']) !!}</span>
                                                </td>
                                                <td style="width: 100px;" align="center" class="ft-12">
                                                    {{ calculateMeasurement($hasAOW) }}
                                                </td>
                                                <td style="width: 80px;" align="right" class="ft-12">
                                                    {{ getDollarSign($hasAOW) }}
                                                </td>
                                                <td style="width: 80px;" align="right" class="ft-12"> <!-- No padding/margin on right-aligned columns -->
                                                    {{ calculateTotalPrice($hasAOW) }}
                                                </td>
                                                @if (!empty($hasAOW['items']))
                                                    @php
                                                        $index = 1; // Initialize sub-item index
                                                    @endphp
                                                    @foreach ($hasAOW['items'] as $subItem)
                                                        @php
                                                            // Sub-item index as parentIndex.subIndex (e.g., 1.1, 1.2)
                                                            $subCountIndex = $countIndex . '.' . $index;
                                                        @endphp
                                                        @include('pdf.VARIATIONORDER.Tidplus.variation_subitems', [
                                                            'item' => $subItem,
                                                            'countIndex' => $subCountIndex, // Pass sub-item numbering
                                                            'level' => 1,
                                                        ])
                                                        @php
                                                            $index++; // Increment sub-item index
                                                        @endphp
                                                    @endforeach
                                                @endif
                                            </tr>
                                            <tr>
                                                <td style="height: 5px;"></td>
                                            </tr>
                                        @endforeach
                                        <tr>
                                            <td style="height: 10px;"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            @endforeach
                            @include('pdf.Common.showSubTotalComponent', [
                                'name' => ' Sub-Total',
                                'is_bold' => true,
                            ])
                        @endif
                    </div>
                </div>
                <br/>
                @endforeach
                <p style="border-bottom: 0.5px solid black !important; width: 810px;"></p>

            @include('pdf.VARIATIONORDER.Tidplus.variationSummaryComponent')
            
            @include('pdf.Common.Tidplus.signatureComponent')
            {{-- @include('pdf.Common.Tidplus.electricalWorkComponent') --}}
            {{-- @include('pdf.Common.Tidplus.termConditionComponent') --}}
        </div>
    @endif
</body>

</html>
