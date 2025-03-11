<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=\, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link type="text/css" media="all" rel="stylesheet" href="{{ public_path('css/metis.css') }}">
    <style>
        .bg-gray {
            background: #5e5e5e;

        }

        .border-b {
            border-bottom: 1px solid black !important;
            /* padding: 5px 0; */
        }

        .border-t {
            border-top: 1px solid black !important;
            /* padding: 5px 0; */
        }

        .line-sp {
            line-height: 1.5 !important;
        }

        .ft-b-12 {
            font-size: 12px !important;
        }

        .ft-12 {
            font-size: 12px !important;
        }

        .ft-14 {
            font-size: 14px;
        }

        .ft-b-14 {
            font-size: 16px !important;
        }

        .custom-list::before {
            content: '\2022';
            left: 10px;
            color: black;
            margin-right: 5px;
        }

        .list-style-none::before {
            content: '';
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

        function removingPTab($data)
        {
            $data = str_replace('<p>', '<span>', $data);
            $data = str_replace('</p>', '</span>', $data);

            return $data;
        }
    @endphp
    @if (count($sortQuotation) != 0)
        <div class="pdf-content">
            <div class="avoid-break">
                @foreach ($sortQuotation as $index => $item)
                    <div key="{{ $item['section_id'] }}" class="avoid-break" style="margin-bottom: 10px;">
                        <table border="1"
                            style="border-collapse: collapse; border-color: transparent; width: 100%;">
                            @if (isset($item['emptyAOWData']) || count($item['hasAOWData']) != 0)
                                {{-- <thead> --}}
                                <tr class="bg-gray">
                                    <td class="section-name" style="width: 850px; padding: 5px; font-weight:bold;">
                                        <span class="ft-b-14" style="color: #fff;">[{{ chr(65 + $index) }}] {{ $item['section_name'] }}</span>
                                        @if (getDescription($item['section_id'], $quotationList))
                                            <span class="ft-b-12" style="color: #fff; display: block; font-weight:bold;">
                                                {{ getDescription($item['section_id'], $quotationList) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td style="width: 100px; color: #fff; font-weight:bold;">SGD (S$)</td>
                                </tr>
                                {{-- </thead> --}}
                            @endif
                        </table>


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


                                    <tr key="{{ $emptyAOW['id'] }}" style="margin-bottom: 15px;">
                                        <td style="width: 600px;" class="ft-12"> <!-- Adjust the width as needed -->
                                            <span class="custom-list line-sp">{!! removingPTab($emptyAOW['name']) !!}</span>
                                        </td>
                                        <td style="width: 100px;" align="center" class="ft-12">
                                        {{ calculateMeasurement($emptyAOW) }}
                                        </td>
                                        <!-- Adjust width -->

                                        <td style="width: 100px;" align="right" class="ft-12">
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
                                    <tbody>
                                        <tr>
                                            <td colspan="4" style="height: 5px;"></td>
                                        </tr>
                                        <tr class="aow-name">
                                            <td class="ft-b-12">
                                                <div>
                                                    <span style="font-weight: bold;">{{ $value['area_of_work_name'] }}</span>
                                                </div>
                                            </td>
                                            <td colspan="3"></td>
                                        </tr>
                                    </tbody>
                                    @foreach ($value['area_of_work_items'] as $hasAOW)
                                        @php
                                            if (isset($originalIndex[$item['section_id']])) {
                                                $originalIndex[$item['section_id']]++;
                                            } else {
                                                $originalIndex[$item['section_id']] = 1;
                                            }
                                            $countIndex = $originalIndex[$item['section_id']];
                                        @endphp

                                        <tr key="{{ $hasAOW['id'] }}" style="margin-bottom: 155px;">
                                            <td style="width: 600px;" class="ft-12">
                                                <!-- Adjust the width as needed -->
                                                <span class="custom-list line-sp">{!! removingPTab($hasAOW['name']) !!}</span>
                                            </td>
                                            <td style="width: 100px;" align="center" class="ft-12">
                                            {{ calculateMeasurement($hasAOW) }}
                                            </td>
                                            <!-- Adjust width -->

                                            <td style="width: 100px;" align="right" class="ft-12">
                                                {{ calculateTotalPrice($hasAOW) }}
                                            </td>
                                            <!-- Adjust width -->
                                            @if (!empty($hasAOW['items']))
                                                @foreach ($hasAOW['items'] as $subItem)
                                                    @include('pdf.CANCELLATION.Optimum.cancellation_subitems', [
                                                        'item' => $subItem,
                                                        'level' => 1,
                                                    ])
                                                @endforeach
                                            @endif
                                        </tr>
                                    @endforeach
                                </table>
                            @endforeach
                            <table border="1"
                                style="border-collapse: collapse; border-color: transparent; width: 100%;">

                                @if (calculateTotalAmountForEachSections($item['section_id'], $quotationList))
                                    <tr>
                                        <td colspan="4" style="height: 15px;"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="1"></td>
                                        <td style="width: 150px; font-weight: bold;" align="center" class="ft-12">
                                        [{{ chr(65 + $index) }}] Sub-Total:</td>
                                        <!-- Adjust width -->

                                        <td style="width: 100px; font-weight: bold;" align="right" class="ft-12">
                                            {{ calculateTotalAmountForEachSections($item['section_id'], $quotationList) }}
                                        </td>

                                    </tr>
                                    <tr>
                                        <td colspan="5" style="height: 5px;"></td>
                                    </tr>
                                @endif
                            </table>
                        @endif



                    </div>
                @endforeach
            </div>
            @include('pdf.CANCELLATION.Optimum.cancellationSummaryComponent')
        </div>
    @endif
</body>

</html>
