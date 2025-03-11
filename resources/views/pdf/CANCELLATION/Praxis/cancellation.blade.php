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
                    return '-$ ' . number_format($total_price, 2);
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

            $final_result = $totalAmount == 0 ? '' : '-$' . ' ' . $totalAmountFormatted;

            return $final_result;
        } else {
            return '';
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
        @include('pdf.Common.Praxis.topHeader')
        <div style="clear: both;padding-top:15px;">
            <table border="1" style="border-collapse: collapse; border-color: transparent; width: 100%;">
                <tr style="background: #000;color: #fff">
                    <td align="center" style="vertical-align: top;width: 120px;" class="ft-b">
                        S/N
                    </td>
                    <td style="width: 500px;" class="ft-b">
                        Description of Work
                    </td>
                    <td class="ft-b" style="min-width:50px;" align="center">
                        Quantity
                    </td>
                    <td class="ft-b" align="center" style="width: 100px;">
                        Unit
                    </td>
                    <td class="ft-b" style="min-width:100px;" align="center">
                        Rate
                    </td>
                    <td align="center" class="ft-b" style="min-width:100px;">
                        Amount
                    </td>
                </tr>
            </table>
        </div>
        @if (count($sortQuotation) != 0)
            <div class="pdf-content">
                @foreach ($sortQuotation as $index => $item)
                    <div key="{{ $item['section_id'] }}">
                        <table border="1"
                            style="border-collapse: collapse; border-color: transparent; width: 100%;">
                            @if (count($item['hasAOWData']) != 0)
                                <tr style="background: #E0D9CC; color: #000">
                                    <td colspan="4" align="center" style="width: 120px;">
                                        <span class="ft-b">{{ ($index + 1) . '.' }} {{ $item['section_name'] }}</span>
                                    </td>
                                </tr>
                            @endif
                            @if (getDescription($item['section_id'], $quotationList))
                                <tr>
                                    <!-- Adjust width -->
                                    <td align="center" style="width: 120px;">
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
                                                $subIndex = $originalIndex[$item['section_id']] < 10 
                                                ? sprintf('%02d', $originalIndex[$item['section_id']]) 
                                                : $originalIndex[$item['section_id']];
                                                $countIndex = ($index + 1) . '.' . $subIndex;
                                            @endphp

                                            <tr key="{{ $hasAOW['id'] }}">
                                                <td align="center" style="vertical-align: top;width: 160px;" class="ft-12">
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
                                                <td align="center" class="ft-12" style="min-width:100px;">
                                                    {{ calculateTotalPrice($hasAOW) }}
                                                </td>
                                                @if (!empty($hasAOW['items']))
                                                @foreach ($hasAOW['items'] as $subItem)
                                                    @include('pdf.CANCELLATION.Praxis.cancellation_subitems', [
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
                        @endif

                    </div>
                    <br />
                @endforeach
                @include('pdf.CANCELLATION.Praxis.cancellationSummaryComponent')
            </div>
        @endif
        @include('pdf.Common.Praxis.termsAndConditionComponent', ['terms' => $quotationList->terms,])
        @include('pdf.Common.Praxis.paymentTermsComponent')
        @include('pdf.Common.Praxis.signatureComponent')
    </div>
</body>

</html>
