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

                                    $final_result = "(W {$item['breadth']}mm x H/D {$item['length']}mm) {$item['quantity']} $unit";
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

        function getUnitPrice($item)
        {
            $result = $item['price'] == 0 ? '' : '$' . ' ' . $item['price'];
            return preg_replace('/\B(?=(\d{3})+(?!\d))/', ',', $result);
        }
    @endphp
    @if (count($sortQuotation) != 0)
        <div class="pdf-content">
            <div>
                <table style="width: 100%; font-size: 12px; font-weight: bold;" class="small-text">
                    <tr>
                        <td style="width: 70%;">
                            <div>
                                <table style="width: 100%;">
                                    <tr>
                                        <td style="width: 22%;" class="ft-b">Name</td>
                                        <td style="width: 78%;"> :{{ $quotationData['customers']['name'] }} </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 22%;" class="ft-b">Contact Number.</td>
                                        <td style="width: 78%;"> :{{ $quotationData['customers']['contact_no'] }} </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 22%;" class="ft-b">Email</td>
                                        <td style="width: 78%;"> :{{ $quotationData['customers']['email'] }} </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 22%;" class="ft-b">Address</td>
                                        <td style="width: 78%;"> :{{ $quotationData['properties']['block_num'] . ' ' . $quotationData['properties']['street_name'] . ' ' . '#' . $quotationData['properties']['unit_num'] }} </td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                        <td style="width: 30%;" align="center">
                            <div style="text-align: center;">
                                <table style="width: 100%;">
                                    <tr>
                                        <td><span class="ft-b" style="text-align: right; letter-spacing: 3px;margin-right: 40px;">{{ $quotationData['doc_type'] }}</span></td>
                                    </tr>
                                    <tr>
                                        <td><span class="ft-b" style="text-align: right;">{{ $quotationData['document_agreement_no'] }}</span></td>
                                    </tr>
                                    <tr>
                                    </tr>
                                    <tr>
                                        <td><span class="ft-b" style="text-align: right;">{{ $quotationData['signed_date'] }}</span></td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                    </tr>
                </table>
                <div class="summary-section">
                    <table style="border-collapse: collapse; border-color: transparent; width: 100%;font-size:12px;">
                        <thead>
                            <tr style="background:rgb(152, 148, 148); font-weight:bold; color: #fff; padding: 2px; border-top: 2px solid #000; border-bottom: 2px solid #000">
                                <td style="width: 50px; font-weight: bold;">No.</td>
                                <td style="width: 600px; font-weight: bold;">PARTICULARS</td>
                                <td style="width: 100px; font-weight: bold;">QUANTITY</td>
                                <td style="width: 100px; font-weight: bold;"></td>
                                <td style="width: 100px; font-weight: bold;" align="right">(SGD)</td>
                            </tr>
                            <tr>
                                <td colspan="4" style="height: 5px;"></td>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sortQuotation as $index => $item)
                                @if (isset($item['emptyAOWData']) || count($item['hasAOWData']) != 0)
                                    <tr>
                                        <td width="5%">
                                            <span class="ft-b-14 ">{{ $index + 1 }}</span>
                                        </td>
                                        <td class="section-name" width="50%">
                                            <span class="ft-b-14">{{ $item['section_name'] }}</span>
                                        </td>
                                        <td width="20%">1.0</td>
                                        <td width="10%">$</td>
                                        <td align="right" width="15%">
                                            {{ calculateTotalAmountForEachSections($item['section_id'], $quotationList) }}
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>

                    </table>
                    <table class="avoid-break" style="width:100%">
                        <tbody class="ft-b-12">
                            <tr style="border-top: 2px solid #000; border-bottom: 2px solid #000">
                                <td colspan="3" align="right">Sub Total :</td>
                                <td width="10%"></td>
                                <td width="15%" align="right">
                                    {{ number_format($total_prices['total_all_amount'], 2, '.', ',') }}</td>
                            </tr>
                            <tr>
                                <td colspan="3" align="right">Discount :</td>
                                <td width="10%">$</td>
                                <td width="15%" align="right">
                                    {{ number_format($total_prices['total_discount'], 2, '.', ',') }}</td>
                            </tr>
                            <tr class="bg-gray" style="border-top: 2px solid #000; border-bottom: 2px solid #000;">
                                <td colspan="3" align="right">Total (SGD) :</td>
                                <td width="10%">$</td>
                                <td width="15%" align="right">
                                    {{ number_format($total_prices['total_amount'], 2, '.', ',') }}</td>
                            </tr>
                            <hr noshade size="1">
                        </tbody>
                    </table>
                </div>
                <div class="term-position">
                    @include('pdf.Common.Intheory.termsOfPaymentTermComponent', [
                        'terms' => $quotationList->terms,
                    ])
                    @include('pdf.Common.Intheory.signatureComponent')
                </div>
            </div>
            <div class="avoid-break page">
                <table style="border-collapse: collapse; border-color: black; border-right: none; border-left: none; width: 100%;"
                    class="ft-12">
                    <tr style="background:rgb(152, 148, 148); color: #fff; padding: 2px; border-top: 2px solid black !important; border-bottom: 2px solid black !important">
                        <td colspan="1" class="section-name" style="width: 50px; font-weight: bold;">
                            <span style="padding-left: 10px">No.</span>
                        </td>
                        <td style="width: 600px; font-weight: bold;">PARTICULARS</td>
                        <td style="width: 100px; font-weight: bold;" align="left">Quantity</td>
                        <td style="width: 100px; font-weight: bold;" align="left">Unit</td>
                        <td style="width: 100px; font-weight: bold;"></td>
                        <td style="width: 100px; font-weight: bold;"></td>
                        <td colspan="2" align="right" style="padding-right: 10px; width: 100px; font-weight: bold;">SGD</td>
                    </tr>
                </table>
                <br />
                @foreach ($sortQuotation as $index => $item)
                    <div key="{{ $item['section_id'] }}" class="avoid-break">
                        <table border="1"
                            style="border-collapse: collapse; border-color: transparent; width: 100%;">

                            @if (calculateTotalAmountForEachSections($item['section_id'], $quotationList))
                                <tr>
                                    <td colspan="4" style="height: 15px;"></td>
                                </tr>
                                @if (isset($item['emptyAOWData']) || count($item['hasAOWData']) != 0)
                                <tr class="bg-gray" style="border-top: 1.5px solid #000; border-bottom: 1.5px solid #000;">
                                    <td style="width: 50px;" class="ft-b-12">{{ ($index + 1) . '.00' }}</td>
                                    <td class="ft-b-12" style="width: 600px;">{{ $item['section_name'] }}</td>
                                    <td style="width: 100px;"></td>
                                    <td style="width: 100px;"></td>
                                    <td style="width: 100px;" align="center" class="ft-b-12">
                                        Sub Total:</td>
                                    <td style="width: 100px;" align="center" class="ft-b-12">
                                        $</td>
                                    <!-- Adjust width -->

                                    <td style="width: 100px;" align="right" class="ft-b-12">
                                        {{ calculateTotalAmountForEachSections($item['section_id'], $quotationList) }}
                                    </td>
                                </tr>
                                @endif
                                <tr>
                                    <td colspan="4" style="height: 5px;"></td>
                                </tr>
                            @endif
                        </table>
                        <table border="1"
                            style="border-collapse: collapse; border-color: transparent; width: 100%;">
                            @if (getDescription($item['section_id'], $quotationList))
                                <tr>
                                    <!-- Adjust width -->
                                    <td style="width: 50px;">
                                        <span class="ft-b-14 "></span>
                                    </td>
                                    <td colspan="3" class="ft-b-12">
                                        {{ '( ' . getDescription($item['section_id'], $quotationList) . ' )' }}
                                    </td>
                                </tr>
                            @endif
                        </table>

                        @if (isset($item['emptyAOWData']))
                            <table border="1"
                                style="border-collapse: collapse; border-color: transparent; width: 100%;">

                                @foreach ($item['emptyAOWData'] as $emptyAOW)
                                @php
                                    if (!isset($originalIndex[$item['section_id']])) {
                                        $originalIndex[$item['section_id']] = 1;
                                    } else {
                                        $originalIndex[$item['section_id']]++;
                                    }
                                    $countIndex = $originalIndex[$item['section_id']];

                                    $subItemCode = ($index + 1) . '.' . str_pad($countIndex, 2, '0', STR_PAD_LEFT);
                                @endphp


                                    <tr key="{{ $emptyAOW['id'] }}">
                                        <td style="vertical-align: top;width: 50px;" class="ft-12">
                                            <span>{{ $subItemCode }}</span>
                                        </td>
                                        <td style="width: 600px;" class="ft-12"> <!-- Adjust the width as needed -->
                                        @if ($settings['enable_sub_description_feature'] == 'true' && $emptyAOW['sub_description'] != null)
                                            <span class="line-sp aow-item">{!! formatText($emptyAOW['sub_description']) !!}</span>
                                        @else
                                            <span class="line-sp aow-item">{!! formatText($emptyAOW['name']) !!}</span>
                                        @endif
                                        </td>
                                        <td style="width: 100px;" align="center" class="ft-12">
                                            {{ $emptyAOW['quantity'] == 0 ? null : $emptyAOW['quantity'] }}</td>
                                        <td style="width: 100px;" align="center" class="ft-12">
                                            {{ calculateMeasurement($emptyAOW) }}</td>
                                        <td style="width: 100px;" align="center" class="ft-12">
                                            {{ getUnitPrice($emptyAOW) }}</td>
                                        <td style="width: 100px;" align="center" class="ft-12">
                                        {{ calculateTotalPrice($emptyAOW) ? '$' : null }}</td>
                                        <!-- Adjust width -->

                                        <td style="width: 100px;" align="right" class="ft-12">
                                            {{ calculateTotalPrice($emptyAOW) }}</td>
                                        <!-- Adjust width -->
                                        @if (!empty($emptyAOW['items']))
                                            @foreach ($emptyAOW['items'] as $subItem)
                                                @include('pdf.FOC.Intheory.foc_subitems', [
                                                    'item' => $subItem,
                                                    'level' => 1,
                                                ])
                                            @endforeach
                                        @endif
                                    </tr>
                                @endforeach
                            </table>
                        @endif

                        @if (count($item['hasAOWData']) != 0)
                            @foreach ($item['hasAOWData'] as $value)
                                <table border="1"
                                    style="border-collapse: collapse; border-color: transparent; width: 100%;">
                                    <tbody>
                                        <tr>
                                            <td colspan="4" style="height: 5px;"></td>
                                        </tr>
                                        <tr class="aow-name">
                                            <td></td>
                                            <td colspan="3" class="ft-b-12">
                                                <div>
                                                    <span class="underline">{{ $value['area_of_work_name'] }}</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                    @foreach ($value['area_of_work_items'] as $hasAOW)
                                    @php
                                        if (!isset($originalIndex[$item['section_id']])) {
                                            $originalIndex[$item['section_id']] = 1;
                                        } else {
                                            $originalIndex[$item['section_id']]++;
                                        }
                                        $countIndex = $originalIndex[$item['section_id']];
                                        $subItemCode = ($index + 1) . '.' . str_pad($countIndex, 2, '0', STR_PAD_LEFT);
                                    @endphp

                                        <tr key="{{ $hasAOW['id'] }}">
                                            <td style="vertical-align: top;width: 50px;" class="ft-12">
                                                <span>{{ $subItemCode }}</span>
                                            </td>
                                            <td style="width: 600px;" class="ft-12">
                                                <!-- Adjust the width as needed -->
                                                @if ($settings['enable_sub_description_feature'] == 'true' && $hasAOW['sub_description'] != null)
                                                    <span class="line-sp aow-item">{!! formatText($hasAOW['sub_description']) !!}</span>
                                                @else
                                                    <span class="line-sp aow-item">{!! formatText($hasAOW['name']) !!}</span>
                                                @endif
                                            </td>
                                            <td style="width: 100px;" align="center" class="ft-12">
                                                {{ $hasAOW['quantity'] == 0 ? '' : $hasAOW['quantity'] }}</td>
                                            <td style="width: 100px;" align="center" class="ft-12">
                                                {{ calculateMeasurement($hasAOW) }}</td>
                                            <!-- Adjust width -->
                                            <td style="width: 100px;" align="center" class="ft-12">
                                            {{ getUnitPrice($hasAOW) }}</td>
                                            <td style="width: 100px;" align="center" class="ft-12">
                                            {{ calculateTotalPrice($hasAOW) ? '$' : null }}</td>

                                            <td style="width: 100px;" align="right" class="ft-12">
                                                {{ calculateTotalPrice($hasAOW) }}
                                            </td>
                                            <!-- Adjust width -->
                                            @if (!empty($hasAOW['items']))
                                                @foreach ($hasAOW['items'] as $subItem)
                                                    @include(
                                                        'pdf.FOC.Intheory.foc_subitems',
                                                        [
                                                            'item' => $subItem,
                                                            'level' => 1,
                                                        ]
                                                    )
                                                @endforeach
                                            @endif
                                        </tr>
                                    @endforeach
                                </table>
                            @endforeach
                        @endif



                    </div>
                @endforeach
            </div>
            <table class="avoid-break" style="width:100%;font-size:12px;">
                <tbody>
                    <tr>
                        <td colspan="4" align="left" style="width: 60%">{{ $quotationList->disclaimer }} </td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
    @endif
</body>

</html>
