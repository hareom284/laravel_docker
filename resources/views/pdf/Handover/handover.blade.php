<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=\, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Handover Certificate</title>
    {{-- <link type="text/css" media="all" rel="stylesheet" href="{{ public_path('css/pdf.css') }}"> --}}
</head>

<style>
    * {
        box-sizing: border-box;
        font-family: sans-serif;
        /* Use the custom font, fallback to sans-serif if unavailable */
    }

    .container-1 {
        border: 2px solid black;
        padding: 0 40px;
        padding-bottom: 40px;
    }

    .header {
        font-size: 44px;
        text-align: center;
        font-weight: 900;
    }

    .content {
        font-size: 19px;
        font-weight: bolder;
    }

    .text {
        font-size: 19px;
    }

    tr {
        margin-top: 90px;
    }

    .input {
        border: none;
        text-align: center;
        border-bottom: 2px solid black;
    }
</style>

<body>
    <div class="container-1">
        <p class="header">HANDOVER CERTIFICATE</p>

        <p class="content">
            This is to certify that <span>{{ $companyName }}</span> has completed all said items in the
            contract and returned whatsoever item or document belonging to the owner.
        </p>

        <table border="1" style="border-collapse: collapse; border-color: transparent; width: 80%;">
            <tr>
                <td class="content">Project Reference Number</td>
                <td class="content">: {{ $referenceNo }}</td>
            </tr>
            <tr>
                <td class="content" style="padding-top: 30px;">Name Of Client</td>
                <td class="content" style="padding-top: 30px;"> :
                    @foreach ($clientArr as $client)
                        {{ $client['first_name'] . ' ' . $client['last_name'] }},
                    @endforeach
                </td>
            </tr>
            <tr>
                <td class="content" style="padding-top: 30px;">Passport or NRIC No</td>
                <td class="content" style="padding-top: 30px;"> :
                    @foreach ($clientArr as $client)
                        *****{{ $client['customers']['nric'] }},
                    @endforeach
                </td>
            </tr>
            <tr>
                <td class="content" style="padding-top: 30px;">Project Address</td>
                <td class="content" style="padding-top: 30px;">: {{ $block_num }} {{ $street_name }}</td>
            </tr>
            <tr>
                <td></td>
                <td class="content">: {{ $unit_num }}</td>
            </tr>
            <tr>
                <td class="content" style="padding-top: 30px;">Date Of Handover</td>
                <td class="content" style="padding-top: 30px;">: {{ $handoverDate }}</td>
            </tr>
        </table>

        <p class="text">
            This is to certify that I, (NAME)
            <?php
            function custom_function($clientArr)
            {
                $input_value = '';
            
                foreach ($clientArr as $client) {
                    $input_value .= $client['first_name'] . ' ' . $client['last_name'] . ' ';
                }
            
                echo '<input type="text" value="' . htmlspecialchars($input_value) . '" class="input">';
            }
            
            custom_function($clientArr);
            ?>
            @if($company_folder_name == 'Intheory')
            agree to handover project in move in condition.
            @else
            had inspected all works pertaining to the contract and is satisfied with the works and
            no further defect.
            @endif
        </p>

        <table border="1" style="border-collapse: collapse; border-color: transparent; width: 100%;">
            @foreach ($handoverCustomers as $index => $client)
                <tr>
                    @if ($index == 0)
                        <td>
                            <table>
                                <tbody>
                                    <tr>
                                        <td colspan="2">
                                            <div class="content">{{ $companyName }}</div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <img src="{{ 'data:image/png;base64,' . $salepersonSignature }}"
                                                style="height:100px">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="content">NAME </td>
                                        <td class="content">: {{ $salepersonName }}</td>
                                    </tr>
                                    <tr>
                                        <td class="content">DESIGNATION </td>
                                        <td class="content">: {{ $salepersonRank }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    @else
                        <td></td>
                    @endif
                    <td>
                        <table style="float: right;">
                            <tbody>
                                <tr>
                                    <td colspan="2">
                                        <div class="content">CHECKED AND AGREED BY</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <img src="{{ 'data:image/png;base64,' . $client['customer_signature'] }}"
                                            style="height:100px">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="content">NAME </td>
                                    <td class="content">:
                                        {{ $client['customer']['first_name'] . ' ' . $client['customer']['last_name'] }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="content">NRIC </td>
                                    <td class="content">: *****{{ $client['customer']['customers']['nric'] }}</td>
                                </tr>
                            </tbody>
                        </table>

                    </td>
                </tr>
            @endforeach
        </table>

    </div>
</body>

</html>
