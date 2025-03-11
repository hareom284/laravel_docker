<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        * {
            font-family: sans-serif;
            font-size: 12px;
            /* Use the custom font, fallback to sans-serif if unavailable */
        }

        .right-header {
            float: right;
        }

        .left-header {
            float: left;
        }

        .ft-b {
            font-weight: bold;
        }

        .ft-xs {
            font-size: 10px !important;
        }
    </style>
</head>

<body>
    <div style="padding-bottom:150px;">
        <div class="left-header">
            {{-- <img src="{{ public_path() . '/images/flynn_logo.png' }}" height="100"/> --}}
            <img src="{{ 'data:image/png;base64,' . $companies['company_logo'] }}" style="width: auto;height:100px">
        </div>
        <div class="right-header">
            <table>
                <tr>
                    <td align="right" class="ft-xs">{{ $companies['name'] }}</td>
                </tr>
                <tr>
                    <td align="right" class="ft-xs">1 TAMPINES NORTH DRIVE 1</td>
                </tr>
                <tr>
                    <td align="right" class="ft-xs">#06-08, T-SPACE</td>
                </tr>
                <tr>
                    <td align="right" class="ft-xs">SINGAPORE 528559</td>
                </tr>
                <tr>
                    <td class="ft-b ft-xs" align="right">UEN:
                        @if (isset($companies['gst_reg_no']) && $companies['gst_reg_no'] != '')
                            {{ $companies['gst_reg_no'] }}
                        @else
                            {{ $companies['reg_no'] }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td align="right" class="ft-xs">{{ $companies['email'] }}</td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>
