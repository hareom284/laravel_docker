<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Order</title>
    <style>
        .purchase-table th,
        .purchase-table td {
            border: 1px solid #000;
            padding: 8px;
        }

        .purchase-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        .purchase-table thead th {
            text-align: center;
        }

        * {
            font-family: sans-serif;
            /* Use the custom font, fallback to sans-serif if unavailable */
        }

        .right-border-remove {
            border-right: 1px solid transparent !important;
        }

        .text-bold {
            font-weight: bold;
        }

        .img-center img {
            width: 200px;
            height: auto;
        }

        .header-section {
            padding-top: 10px;
            padding-bottom: 10px;
        }

        .logo-section {
            margin: 0 auto;
            text-align: center;
        }

        .right-section {
            float: right;
            width: 40%;
        }

        .left-section {
            float: left;
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="header-section">
        @if ($folder_name == 'Miracle')
            <div class="logo-section">
                <img src="{{ 'data:image/png;base64,' . $company_logo }}" style="width: auto;height:150px">
                <div>{{ $companies['name'] }}</div>
            </div>
        @elseif($folder_name == 'Artdecor')
            @include('pdf.Common.artdecorTopHeaderComponent', [
                'companies' => $companies,
            ])
        @else
            <div>
                <img src="{{ 'data:image/png;base64,' . $company_logo }}" style="width: auto;height:100px">
            </div>
        @endif
    </div>
    <table class="purchase-table">
        <thead>
            <tr>
                <th colspan="6">
                    <h1>Purchase Order</h1>
                </th>
            </tr>
            <tr>
                <td colspan="2">
                    <div style="float: left;"> Date :</div>
                    <div style="padding-left:140px;">
                        {{ $poData['date'] }}
                    </div>
                </td>
                <td colspan="2">
                    <div style="float: left;"> Time :</div>
                    <div style="padding-left:140px;">
                        {{ $poData['time'] }}
                    </div>
                </td>
                <td>
                    <div style="float: left;"> P/O NO :</div>
                    <div style="padding-left:140px;">
                        {{ $poData['purchase_order_number'] }}
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div style="float: left;"> VENDOR :</div>
                    <div style="padding-left:140px;">
                        {{ $poData['vendor_name'] }}
                    </div>
                </td>
                <td colspan="3">
                    <div style="float: left;"> FAX NO :</div>
                    <div style="padding-left:140px;">
                        {{ $poData['vendor_fax'] }}
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div style="float: left;"> ATTN :</div>
                    <div style="padding-left:140px;">
                        {{ $poData['vendor_person'] }}
                    </div>
                </td>
                <td colspan="3">
                    <div style="float: left;"> PO NO :</div>
                    <div style="padding-left:140px;">
                        {{ $poData['purchase_order_number'] }}
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div style="float: left;"> FROM :</div>
                    <div style="padding-left:140px;">
                        {{ $poData['company'] }} ({{ $poData['staff'] }})
                    </div>
                </td>
                <td colspan="3">
                    <div style="float: left;"> PAGE NO :</div>
                    <div style="padding-left:140px;">
                        {{ $poData['pages'] }}
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="5">
                    <div style="float: left;"> ADDRESS :</div>
                    <div style="padding-left:140px;">
                        {{ $poData['address'] }}
                    </div>
                </td>

            </tr>
            <tr>
                <td colspan="5">
                    <div style="float: left;"> DELIVERY DATE :</div>
                    <div style="padding-left:140px;">
                        {{ $poData['delivery_date'] }}
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="5" style="height: 30px;"></td>
            </tr>
        </thead>
        <tbody>

            <tr>
                <th>S/NO</th>
                <th>Description/LOCATION</th>
                <th>CODE</th>
                <th>QTY</th>
                <th>SIZE</th>
            </tr>
            @foreach ($poData['items'] as $key => $item)
                <tr>
                    <td align="center">{{ $key + 1 }}</td>
                    <td>{{ $item['description'] ?? '' }}</td>
                    <td align="center">{{ $item['code'] ?? '' }}</td>
                    <td align="center">{{ $item['quantity'] }}</td>
                    <td align="center">{{ $item['size'] }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="5" style="height:30px;"></td>
            </tr>
        </tbody>
    </table>
    <div style="padding: 20px 0;">
        <div style="float: left;font-size:12px;"> REMARK :</div>
        <div style="padding-left:140px;font-size:12px;">
            {{ $poData['remark'] }}
        </div>
    </div>
    <table style="width:100%;font-size:12px;" class="purchase-table">
        <tbody>
            <tr>
                <td colspan="1" align="center">
                    <span>THE COMPANY’S STAMP</span>
                </td>
                <td colspan="1" align="center">
                    <span>SALES REP. SIGNATURE</span>
                </td>
                <td colspan="1" align="center">
                    <span>MANAGER SIGNATURE</span>
                </td>
            </tr>
            <tr>
                <td colspan="1" align="center">
                    <div>
                        <div class="img-center">
                            <img src="{{ 'data:image/png;base64,' . $companyStamp }}" alt="">
                        </div>
                    </div>
                </td>
                <td colspan="1" align="center">
                    <div>
                        <div class="img-center">
                            <img src="{{ 'data:image/png;base64,' . $poData['sales_rep_signature'] }}" alt="...">
                        </div>
                    </div>
                    <div>
                        <span class="text-bold">HP :</span>
                        <span>{{ $poData['staff_no'] }} ( {{ $poData['staff'] }} )</span>
                    </div>
                </td>
                <td colspan="1" align="center">
                    @if ($poData['status'] === 'APPROVED')
                        <div class="text-sm">
                            <div>
                                <div class="img-center">
                                    <img src="{{ 'data:image/png;base64,' . $poData['manager_signature'] }}"
                                        alt="...">
                                </div>
                            </div>
                            <div>
                                <span class="text-bold">HP :</span>
                                <span>{{ $poData['manager_no'] }} ( {{ $poData['manager'] }}
                                    )</span>
                            </div>
                        </div>
                    @endif
                </td>
            </tr>
        </tbody>
    </table>
    <p style="font-size: 9px;">
        ORDER IS ONLY VALID WITH THE COMPANY'S STAMP.BOTH SALES REP. AND MANAGER SIGNATURE
        0% PREBATE FOR EVERY INVOICE. FOR OUR COMPANY ADVERTISING AND PROMOTION
    </p>

</body>

</html>
