@foreach ($sortQuotation as $index => $item)
    <div key="{{ $item['section_id'] }}" class="{{ $current_folder_name == 'Twp' ? 'section-container' : '' }} {{ $item['is_page_break'] ? 'page' : '' }}">
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
                            <span class="aow-item">{!! formatText($emptyAOW['name']) !!}</span>
                        </td>
                        @if($emptyAOW['is_excluded'] == 0)
                        <td style="width: 400px;" align="center" class="ft-12">
                            {{ calculateMeasurement($emptyAOW) }}</td>
                        <!-- Adjust width -->

                        <td style="width: 90px;" align="right" class="ft-12">
                            {{ calculateTotalPrice($emptyAOW) }}</td>
                        @else
                        <td colspan="2" style="width: 90px;"></td>
                        @endif
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
                                <span>{{ chr(65 + $index) }}.{{ $countIndex }}</span>
                            </td>
                            <td style="width: 600px;" class="ft-12">
                                <!-- Adjust the width as needed -->
                                <span class="aow-item">{!! formatText($hasAOW['name']) !!}</span>
                            </td>
                            @if($hasAOW['is_excluded']==0)
                            <td style="width: 400px;" align="center" class="ft-12">
                                {{ calculateMeasurement($hasAOW) }}</td>
                            <!-- Adjust width -->

                            <td style="width: 90px;" align="right" class="ft-12">
                                {{ calculateTotalPrice($hasAOW) }}</td>
                            @else
                            <td colspan="2" style="width: 90px;"></td>
                            @endif
                            <!-- Adjust width -->
                            @if (!empty($hasAOW['items']))
                            @foreach ($hasAOW['items'] as $subItem)
                                @include('pdf.VARIATIONORDER.Twp.variation_subitems', [
                                    'item' => $subItem,
                                    'level' => 1,
                                ])
                            @endforeach
                        @endif
                        </tr>
                    @endforeach
                </table>
            @endforeach
            @include('pdf.Common.showSubTotalComponent', [
                'name' => ($current_folder_name == 'Twp' || $current_folder_name == 'Jream' || $current_folder_name == 'Henglai') ? 'TOTAL AMOUNT' : ' Sub-Total',
                'is_bold' => ($current_folder_name == 'Twp' || $current_folder_name == 'Jream') || $current_folder_name == 'Henglai' ? false : true
            ])
        @endif

    </div>
    <br />
@endforeach
