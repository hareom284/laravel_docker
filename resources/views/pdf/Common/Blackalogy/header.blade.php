<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=\, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link type="text/css" media="all" rel="stylesheet" href="{{ public_path('css/font_style.css') }}">
    <style>
        .type {
            display: inline-block;
            border: 2px solid #000;
            padding: 8px 70px;
            font-weight: bold;
            margin-bottom: 3px;
        }

        .company {
            padding: 0px 50px;
        }

        .custom-hr {
            border: none;
            /* Remove the default border */
            height: 2px;
            /* Set the height (thickness) of the line */
            background-color: black;
            /* Set the color of the line */
            margin: 10px 0;
            /* Optional: add some vertical spacing */
        }

        .small-text {
            font-size: 15px;
        }

        .style-text{
            text-decoration: underline;
            font-style: italic;
        }

    </style>

</head>

<body>
    <!-- Header -->
    <table style="width: 100%;" class="small-text">
        <tr>
            <td style="width: 25%";><img src="{{ 'data:image/png;base64,' . $quotationData['companies']['company_logo'] }}" style="width: auto;height:150px"></td>
            <td style="width: 40%";>
                <div class="company">
                    <span>Company Reg No. </span>
                    <span> {{ $quotationData['companies']['reg_no'] }} </span>
                </div> <br>
                <div class="company">
                    <span>Company Address. </span>
                    <span> {{ $quotationData['companies']['main_office'] }} </span>
                </div>
            </td>
            <td style="width: 30%";>
                <div class="type"> <span> {{ $quotationData['doc_type'] }} </span>
                </div>
                <div>
                    <span><span class="ft-b">Contract No: </span>{{ $quotationData['document_agreement_no'] }}</span>
                </div>
                <div>
                    <span>Date: {{ $quotationData['created_at'] }}</span>
                </div>
                <div>
                    <span class="style-text">(a subsidiary of Genuine Interior Concept & Design)</span>
                </div>
            </td>
        </tr>
    </table>
    <hr class="custom-hr">
    <table style="width: 100%;" class="small-text">
        <tr>
            <td style="width: 60%;">
                <p class="ft-b">Customer Details</p>
                <div>
                    <table style="width: 100%;">
                        <tr>
                            <td style="width: 20%;" class="ft-b">Name</td>
                            <td style="width: 80;"> {{ $quotationData['customers']['name'] }} </td>
                        </tr>
                        <tr>
                            <td style="width: 20%;" class="ft-b">Contact No.</td>
                            <td style="width: 80;"> {{ $quotationData['customers']['contact_no'] }} </td>
                        </tr>
                        <tr>
                            <td style="width: 20%;" class="ft-b">Address</td>
                            <td style="width: 80;"> {{ $quotationData['properties']['block_num'] . ' ' . $quotationData['properties']['street_name'] . ' ' . '#' . $quotationData['properties']['unit_num'] }} </td>
                        </tr>
                        <tr>
                            <td style="width: 20%;" class="ft-b">Singapore</td>
                            <td style="width: 80;"> ({{ $quotationData['properties']['postal_code'] }}) </td>
                        </tr>
                    </table>
                </div>
            </td>
            <td style="width: 40%;">
                <p class="ft-b">Payment Details</p>
                <div>
                    <table style="width: 100%;">
                        <tr>
                            <td><span class="ft-b">10%</span> - Upon Confirmation</td>
                        </tr>
                        <tr>
                            <td><span class="ft-b">50%</span> - Commencement of Work</td>
                        </tr>
                        <tr>
                            <td><span class="ft-b">35%</span> - Upon Measurement of Carpentry</td>
                        </tr>
                        <tr>
                            <td><span class="ft-b" style="padding-left: 8px;">5%</span> - Upon Handover of Project</td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table><br>
</body>

</html>
