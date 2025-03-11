<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        .text-underline {
            text-decoration: underline;
        }

        .ft-14 {
            font-size: 14px;
        }

        .ft-semi-bold {
            font-weight: 700;
        }

        .ft-bold {
            font-weight: bold;
        }

        .header-title {
            margin: 0 auto;
            text-align: center;
            clear: both;
            padding-top: 50px;
        }

        .header-title h3 {
            text-decoration: underline;
        }

        * {
            font-family: sans-serif;
            /* Use the custom font, fallback to sans-serif if unavailable */
        }

        .pdpa-section {
            padding: 55px;
        }

        .signature-section {
            padding-top: 200px;
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
    <div class="pdpa-section">
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

        <div class="header-title">
            <h3>Collection of Personal Information</h3>
        </div>
        <br />

        <div class="ft-14 ft-semi-bold">
            <span>Dear Customer,</span>
        </div>
        <br />
        <div class="ft-14 ft-semi-bold">
            We are aware of the revised Advisory Guidelines on the PDPA for NRIC and other National Identification
            Numbers that the Personal Data Protection Commission (PDPC) issued on 31 August 2018, and are in the midst
            of reviewing and policies to adhere to these guidelines.
        </div>
        <br />
        <br />
        <br />
        <table class="ft-14 ft-semi-bold">
            <tr>
                <td style="width: 20px;"></td>
                <td style="vertical-align: top;">1.</td>
                <td style="width: 10px;"></td>
                <td>
                    <div> I/We (The Wooden Platform) entered the consent of collection and use of NRIC Numbers/Foreign
                        Identification Numbers ("FIN")/Birth Certificate Numbers/Work Permit Numbers, while we are
                        authorized on behalf of you to submit 'Renovation Form' to <span class="px-2 pb-2">(HDB / BCD /
                            Others: <span
                                class="text-underline">{{ $documentData['pdpa_authorization'] }}</span>)</span>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="4" style="height: 40px;"></td>
            </tr>
            <tr>
                <td style="width: 20px;"></td>
                <td style="vertical-align: top;">2.</td>
                <td style="width: 10px;"></td>
                <td>
                    <div>
                        I/We (The Wooden Platform), the Form should make sure that TWP has received the necessary
                        consents for the collection, use and disclosure of their personal data. We assure you that we
                        will undertake proper safeguarding measures to protect your personal data (including NRIC
                        Numbers/FIN/Birth Certificate Numbers/Work Permit Numbers) under our care.
                    </div>
                </td>
            </tr>
        </table>
        <br />
        <br />
        <br />
        <div class="ft-14 ft-semi-bold">
            Contact us at <span class="text-underline">67343133</span> or DPO@thewoodenplatform.com should you have any
            queries or concerns. Thank you for your understanding and we look forward to your continuous support!
        </div>
        <br />
        <div class="signature-section ">
            <table class="ft-14 ft-semi-bold" style="float: left;">
                <tbody>
                    <tr>
                        <td colspan="2">
                            <img src="{{ 'data:image/png;base64,' . $contractLists['encode_owner_signature'] }}"
                                style="height:100px;border-bottom:1px solid black;">

                            <p>EMPLOYER/OWNER</p>

                        </td>
                    </tr>
                    <tr>
                        <td>NAME </td>
                        <td>: <span class="text-underline">{{ $contractLists['customer_name'] }}</span></td>
                    </tr>
                    <tr>
                        <td>DATE </td>
                        <td>: </td>
                    </tr>
                </tbody>
            </table>
            {{-- <div style="float: right;">
                <table class="ft-14 ft-semi-bold">
                    <tbody>
                        <tr>
                            <td colspan="2">
                                <img src="{{ 'data:image/png;base64,' . $contractLists['encode_contractor_signature'] }}"
                                    style="height:100px;border-bottom:1px solid black;">

                                <p>CONTRACTOR</p>
                            </td>
                        </tr>

                        <tr>
                            <td>NAME </td>
                            <td>: <span class="text-underline">{{ $contractLists['company_name'] }}</span></td>
                        </tr>
                        <tr>
                            <td>DATE </td>
                            <td>: </td>
                        </tr>
                    </tbody>
                </table>
            </div> --}}
        </div>
    </div>
</body>

</html>
