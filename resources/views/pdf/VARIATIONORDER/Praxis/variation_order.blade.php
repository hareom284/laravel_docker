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
            if (array_key_exists($item['variation_template_item_id'], $original_quantities)) {
                return $item['current_quantity'] > $original_quantities[$item['variation_template_item_id']];
            } else {
                return true;
            }

            // Return false if the item does not exist or the quantity is not greater
            return false;
        }
    @endphp
    <div style="clear: both;">
        @include('pdf.Common.Praxis.topHeader')
        @if ($settings['enable_cn_in_vo'])
            @include('pdf.VARIATIONORDER.Praxis.cancellation_variation_order')
        @else
            @include('pdf.VARIATIONORDER.Praxis.normal_variation_order')
        @endif
        @include('pdf.Common.Praxis.termsAndConditionComponent', ['terms' => $quotationList->terms])
        @include('pdf.Common.Praxis.paymentTermsComponent')
        @include('pdf.Common.Praxis.signatureComponent')
    </div>
</body>

</html>
