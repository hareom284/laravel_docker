@foreach ($sectionWithCNData as $arrayName => $sections)
    @if (!empty($sections) && !empty($sections['data']))
        <div style="clear: both;padding-top:15px;">
            <h1 style="font-size: 20px;" class="ft-b-14"><span
                    class="underline">{{ $arrayName == 'sectionWithCNItems' ? 'Variation Order (Reduction and Omission)' : 'Variation Order (Addition)' }}</span>
            </h1>
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
        <div class="pdf-content">
            @foreach ($sections['data'] as $index => $item)
                <div key="{{ $item['section_id'] }}">
                    <table border="1" style="border-collapse: collapse; border-color: transparent; width: 100%;">
                        @if (count($item['hasAOWData']) != 0)
                            <tr style="background: #E0D9CC; color: #000">
                                <td colspan="4" align="center" style="width: 120px;">
                                    <span class="ft-b">{{ $index + 1 . '.' }} {{ $item['section_name'] }}</span>
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
                                            $subIndex =
                                                $originalIndex[$item['section_id']] < 10
                                                    ? sprintf('%02d', $originalIndex[$item['section_id']])
                                                    : $originalIndex[$item['section_id']];
                                            $countIndex = $index + 1 . '.' . $subIndex;
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
                                                    @include(
                                                        'pdf.VARIATIONORDER.Praxis.variation_subitems',
                                                        [
                                                            'item' => $subItem,
                                                            'level' => 1,
                                                        ]
                                                    )
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
        </div>
        <table border="1" style="border-collapse: collapse; border-color: transparent; width: 100%;">
            <tr class="">
                <td colspan="2"></td>
                <td style="width: 200px;" align="right" class="ft-12">
                    Total :
                </td>

                <td style="width: 100px;" align="right" class="ft-12">
                    {{ '$ ' . number_format($sections['total'], 2) }}
                </td>
            </tr>
            <tr>
                <td colspan="2"></td>
                <td style="width: 200px;" align="right" class="ft-12">
                    GST ( {{ $total_prices['gst_percentage'] }}% ) :
                </td>

                <td style="width: 100px;" align="right" class="ft-12">
                    {{ '$ ' . number_format($sections['total'] * ($total_prices['gst_percentage'] / 100), 2) }}
                </td>
            </tr>
            <tr>
                <td colspan="2"></td>
                <td style="width: 200px;" align="right" class="ft-12">
                    Grand Total :
                </td>

                <td style="width: 100px;" align="right" class="ft-12">
                    {{ '$ ' . number_format($sections['total'] + ($sections['total'] * ($total_prices['gst_percentage'] / 100)), 2) }}
                </td>
            </tr>
        </table>
    @endif
@endforeach
@include('pdf.VARIATIONORDER.Praxis.variationSummaryComponent')
