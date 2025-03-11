<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=\, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link type="text/css" media="all" rel="stylesheet" href="{{ public_path('css/font_style.css') }}">
    <link type="text/css" media="all" rel="stylesheet" href="{{ public_path('css/common.css') }}">

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
            }else{

                return true;
            }

            // Return false if the item does not exist or the quantity is not greater
            return false;
        }
    @endphp
    @if (count($sortQuotation) != 0)
        <div class="pdf-content">
            @if($current_folder_name == 'FiveFoot10')
            <div class="page-after">
            @include('pdf.Common.summaryComponent')
            @include('pdf.VARIATIONORDER.Twp.variationSummaryComponent')
            </div>
            <br/>
            <br/>
            @endif
            @if($settings['enable_cn_in_vo'])
                @include('pdf.VARIATIONORDER.cn_variation_items')
            @else
                @include('pdf.VARIATIONORDER.normal_variation_items')
            @endif

            @include('pdf.Common.noteAndDisclaimerComponent')

            @if($current_folder_name != 'FiveFoot10')
            @include('pdf.VARIATIONORDER.Twp.variationSummaryComponent')
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
            @if($current_folder_name !='Twp' && $current_folder_name != 'Jream' && $current_folder_name != 'Henglai')
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
