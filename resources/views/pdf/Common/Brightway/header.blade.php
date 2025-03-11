<style>
    .right-header {
        float: right;
    }

    .left-header {
        float: left;
    }

    .logo-text {
        font-size: 24px;
        letter-spacing: 0;
        color: #0000FF;
        font-weight: bolder;
    }

    .doc-type-text {
        font-size: 30px;
        color: gray;
        font-weight: bolder;
    }

    .doc-type-container {
        text-align: right;
    }

    .light-blue-cell {
        background-color: #DEEAF6;
    }

    .table-dotted {
        border-style: dotted !important;
    }

    .table-dotted td,
    .table-dotted th {
        border-style: dotted !important;
    }
</style>
<div style="clear: both;">
    <div class="left-header">
        <table class="ft-12">
            <tr>
                <td><span class="logo-text">BRIGHTWAY ENGINEERING TECH</span></td>
            </tr>
            <tr>
                <td style="height: 14px;"></td>
            </tr>
            <tr>
                <td>
                    {{ $quotationData['companies']['main_office'] }}
                </td>
            </tr>
            <tr>
                <td align="left">Office: (65) {{ $quotationData['companies']['tel'] }} /
                    {{ $quotationData['companies']['fax'] }}</td>
            </tr>
            <tr>
                <td align="left">Email: {{ $quotationData['companies']['email'] }}</td>
            </tr>
            <tr>
                <td align="left">GST. Reg. No.: @if (isset($quotationData['companies']['gst_reg_no']) && $quotationData['companies']['gst_reg_no'] != '')
                        {{ $quotationData['companies']['gst_reg_no'] }}
                    @else
                        {{ $quotationData['companies']['reg_no'] }}
                    @endif
                </td>
            </tr>
        </table>
        <br />
        <br />
        <br />
        <br />
        <table style="border-collapse: collapse; border-color: black; width: 100%;" class="ft-12">
            <tr class="light-blue-cell" align="left" style="border-style: dotted !important;">
                <th>BILL TO</th>
            </tr>
            <tr style="border: none;">
                <td>Sir/Madam</td>
            </tr>
        </table>
        <br />
        <br />
        <span class="ft-12">ID :
            @if (count($quotationData['customers_array']) > 1)
                <span>
                    {{ implode(
                        ' / ',
                        array_map(function ($customer) {
                            return $customer['name_prefix'] . ' ' . $customer['last_name'] . ' ' . $customer['first_name'];
                        }, $quotationData['customers_array']),
                    ) }}
                </span>
            @else
                @foreach ($quotationData['customers_array'] as $customer)
                    <span>{{ $customer['name_prefix'] . ' ' . $customer['last_name'] . ' ' . $customer['first_name'] }}</span>
                @endforeach
            @endif
        </span>
        <br />
        <br />
    </div>
    <div class="right-header" style="width: 400px;">
        <div class="doc-type-container">
            <span class="doc-type-text">{{ $doc_type }}</span>
        </div>
        <br />
        <table border="1" style="border-collapse: collapse; border-color: black; width: 100%;"
            class="ft-12 table-dotted">
            <tr class="light-blue-cell">
                <th align="center" style="width: 50%;">{{ $doc_type }}#</th>
                <th align="center" style="width: 50%">DATE</th>
            </tr>
            <tr>
                <td align="center">
                    {{ $quotationData['document_agreement_no'] }}
                </td>
                @if (isset($quotationData['signed_date']))
                    <td align="center">{{ convertDate($quotationData['signed_date']) }}</td>
                @else
                    <td align="center">{{ convertDate($quotationData['created_at']) }}</td>
                @endif
            </tr>
            <tr class="light-blue-cell">
                <th></th>
                <th style="border-left: hidden !important;" align="left">PIC</th>
            </tr>
            <tr>
                <td align="center">{{ $quotationData['signed_saleperson'] }}</td>
                <td align="center">513</td>
            </tr>
        </table>
        <br />
        <br />
        <br />
        <br />
        <table style="border-collapse: collapse; border-color: black; width: 100%;" class="ft-12">
            <tr class="light-blue-cell" style="border-style: dotted !important;">
                <th>JOB SITE</th>
            </tr>
            <tr style="border: none;">
                <td>
                    @if (isset($quotationData['properties']))
                        {{ $quotationData['properties']['block_num'] . ' ' . $quotationData['properties']['street_name'] . ' ' . '#' . $quotationData['properties']['unit_num'] }}
                    @endif
                </td>
            </tr>
        </table>

    </div>
</div>
