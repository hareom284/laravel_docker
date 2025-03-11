<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Payment</title>
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
            text-align: left;
        }
    </style>
</head>

<body>
    <div class="header-section">
        <div class="logo-section">
            <img src="{{ 'data:image/png;base64,' . $company_logo }}" style="width: auto;height:150px">
        </div>
    </div>
    <table class="purchase-table">
        <thead>
            <tr>
                <td colspan="4">
                    <div style="float: left;"> Bank Trans ID :</div>
                    <div style="padding-left:140px;">
                        {{ $documentData['bank_transaction_id'] }}
                    </div>
                </td>
                <td colspan="4">
                    <div style="float: left;"> Payment Method :</div>
                    <div style="padding-left:140px;">
                        {{ $paymentMethod }}
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="4">
                    <div style="float: left;"> Payment Type :</div>
                    <div style="padding-left:140px;">
                        {{ $paymentType }}
                    </div>
                </td>
                <td colspan="4">
                    <div style="float: left;"> Payment Date :</div>
                    <div style="padding-left:140px;">
                        {{ date('Y-m-d', strtotime($documentData['payment_date'])) }}
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="4">
                    <div style="float: left;"> Amount :</div>
                    <div style="padding-left:140px;">
                        {{ $documentData['amount'] }}
                    </div>
                </td>
                <td colspan="4">
                    <div style="float: left;"> Payment Made By :</div>
                    <div style="padding-left:140px;">
                        {{ $accountant }}
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="4">
                    <div style="float: left;"> STATUS :</div>
                    <div style="padding-left:140px;">
                        {{ $documentData['status'] === 0 ? 'Preparing' : 'Sent'  }}
                    </div>
                </td>
                <td colspan="4">
                    <div style="float: left;"> Manager :</div>
                    <div style="padding-left:140px;">
                        {{ $managerName }}
                    </div>
                </td>
            </tr>
        </thead>
    </table>
    <div style="padding: 20px 0;">
        <div style="float: left;font-size:12px;text-decoration: underline;"> VENDOR INVOICES</div>
    </div>
    <table class="purchase-table">
        <tbody>
            <tr>
                <th>S/NO</th>
                <th>PROJECT</th>
                <th>INVOICE</th>
                <th>VENDOR NAME</th>
                <th>VENDOR INVOICE</th>
                <th>TOTAL AMOUNT</th>
                <th>Payment Date</th>
                <th>STATUS</th>
            </tr>
            @foreach ($documentData['supplierCostings'] as $key => $item)
                <tr>
                    <td align="center">{{ $key + 1 }}</td>
                    <td align="center">
                        {{ $item->project->property->block_num .' '.$item->project->property->street_name.' #'.$item->project->property->unit_num.' '.$item->project->property->postal_code }}
                    </td>
                    <td align="center">{{ $item->project->invoice_no }}</td>
                    <td align="center">{{ $item->vendor->vendor_name }}</td>
                    <td align="center">{{ $item->invoice_no }}</td>
                    <td align="center">{{ $item->payment_amt }}</td>
                    <td align="center">{{ date('Y-m-d', strtotime($item->created_at)) }}</td>
                    <td align="center">
                        @php
                        $statusLists = [
                            0 => "Verifying",
                            1 => "Pending Approval",
                            2 => "Approved",
                            3 => "Paid"
                        ];

                        $statusString = isset($statusLists[$item->status]) ? $statusLists[$item->status] : " - " ; 

                        @endphp
                        {{$statusString}}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div style="padding: 20px 0;">
        <div style="float: left;font-size:12px;"> REMARK :</div>
        <div style="padding-left:140px;font-size:12px;">
            {{ $documentData['remark'] }}
        </div>
    </div>
    <table style="width:100%;font-size:12px;">
        <tbody>
            <tr>
                <td colspan="1">
                    <span>MANAGER SIGNATURE</span>
                </td>
            </tr>
            <tr>
                <td colspan="1">
                    <div class="text-sm">
                        <div>
                            <div class="img-center">
                                <img src="{{ 'data:image/png;base64,' . $managerSign }}" alt="...">
                            </div>
                        </div>
                        <div>
                            <span class="text-bold">Name :</span>
                            <span>{{ $managerName }}</span>
                        </div>
                        <div>
                            <span class="text-bold">Phone :</span>
                            <span>{{ $managerNo }}</span>
                        </div>
                    </div>
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
