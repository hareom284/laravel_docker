<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link type="text/css" media="all" rel="stylesheet" href="{{ public_path('css/font.css') }}">

    <style>
        * {
            font-family: sans-serif;
            /* Use the custom font, fallback to sans-serif if unavailable */
        }

        /*
        .header-section {
            padding-top: 10px;
            padding-bottom: 140px;
        } */

        .right-header {
            float: right;
        }

        .left-header {
            float: left;
        }

        .logo-section {
            margin: 0 auto;
            text-align: center;
        }

        .miracle-padding {
            padding-bottom: 30px;
        }

        .twp-padding {
            padding-bottom: 15px;
        }

        .hide_header_and_footer {
            display: none;
        }

        .header-name {
            clear: both;
        }

        .header-name {
            clear: both;
        }

        .ft-b {
            font-weight: bold;
        }

        .bottom-line {
            height: 10px;
            width: 100%;
            background: 1px solid black;
        }

        .watermark {
            position: fixed;
            top: 450%;
            left: 25%;
            -webkit-transform: rotate(-45deg);
            -ms-transform: rotate(-45deg);
            font-size: 200px;
            color: rgba(0, 0, 0, 0.1);
            white-space: nowrap;
            pointer-events: none;
            z-index: -1;
            font-family: Arial, sans-serif;
            font-weight: bold;
        }

        .ibm-plex-bold {
            font-family: 'IBM-Plex-Condensed-Bold' !important;
            font-size: 38px;
            white-space: nowrap;
            letter-spacing: -0.2px;
            font-weight: bolder;
        }

        .ibm-plex-regular-company-name {
            font-family: 'IBM-Plex-Regular' !important;
            font-size: 18px;
            letter-spacing: -0.2px;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .ibm-plex-regular-address {
            font-family: 'IBM-Plex-Regular' !important;
            font-size: 15px;
            letter-spacing: -0.2px;
            width: 290px;
        }

        .ibm-plex-regular {
            font-family: 'IBM-Plex-Regular' !important;
            font-size: 15px;
            letter-spacing: -0.2px;
        }

        .address p {
            padding: 0;
            margin: 0;
        }
    </style>
</head>

<body>
    @if ($status == 'pending')
        <div class="watermark">DRAFT</div>
    @endif
    <div class="header-section">
        <table>
            <tr>
                <td>
                    <div class="logo-section">
                        <img src="{{ 'data:image/png;base64,' . $companies['company_logo'] }}"
                            style="width: auto;height:150px">
                    </div>
                </td>
                <td>
                    <div class="logo-section address">
                        {{-- <img src="{{ public_path() . '/images/whst_logo_2.png' }}" style="width: auto;height:180px"> --}}
                        <p class="ibm-plex-bold">WHST DESIGN</p>
                        <p class="ibm-plex-regular-company-name">{{ $companies['name'] }}</p>
                        <p class="ibm-plex-regular-address">{{ $companies['main_office'] }}</p>
                        <p class="ibm-plex-regular-address">Tel: {{ $companies['tel'] }}</p>
                        <p class="ibm-plex-regular-address">Co. Reg No:
                            @if (isset($companies['gst_reg_no']) && $companies['gst_reg_no'] != '')
                                {{ $companies['gst_reg_no'] }}
                            @else
                                {{ $companies['reg_no'] }}
                            @endif
                        </p>
                    </div>
                </td>
                <td>
                    {{-- <div style="width: 80px;"></div> --}}
                </td>
                <td style="vertical-align: bottom;">
                    <div class="logo-section">
                        <img src="{{ public_path() . '/images/whst_logo_3.png' }}" style="width: auto;height:80px">
                    </div>
                </td>
                <td style="vertical-align: bottom;">
                    <div class="logo-section">
                        <img src="{{ public_path() . '/images/whst_logo_5.png' }}" style="width: auto;height:80px">
                    </div>
                </td>
                <td style="vertical-align: bottom;">
                    <div class="logo-section">
                        <img src="{{ public_path() . '/images/whst_logo_4.png' }}" style="width: auto;height:70px">
                    </div>
                </td>
            </tr>
        </table>
        <hr style="width: 100%;">
        <br />
    </div>
</body>

</html>
