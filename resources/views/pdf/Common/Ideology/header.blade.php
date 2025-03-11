<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quotation Header</title>
    <style>
        * {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            font-size: 12px;
        }

        .header-container {
            /* padding: 20px; */
        }

        .logo {
            width: auto; 
            height: 120px;
            position: absolute;
            top: -23px;
            left: 0;
        }

        .border-bottom {
            border-bottom: 1px solid gray;
        }

        .header-text-1 {
            padding-top: 5px;
            padding-bottom: 0 !important;
            margin-bottom: 0 !important;
        }

        .header-container table,
        .header-container th,
        .header-container td {
            border-collapse: collapse;
        }

        .gray-text {
            color: gray;
        }

        .orange-text {
            color: orange;
        }
    </style>
</head>

<body>
    <div class="header-container">
        <table style="width: 100%;position: relative;">
            <tr>
                <td style="width: 200px; height: 80px;">
                    <img src="{{ 'data:image/png;base64,' . $companies['company_logo'] }}" class="logo">
                </td>
                <td style="width: 220px;" class="border-bottom"></td>
                <td class="border-bottom">
                    <div>
                        <span><b style="letter-spacing: 0.4px;text-transform: uppercase;">{{ $companies['name'] }}</b></span><br/>
                        <p class="header-text-1 gray-text">{{ $companies['main_office'] }}</p><br/>
                    </div>
                </td>
                <td class="border-bottom">
                    <div style="margin-left: 60px;">
                        <span><b>T.</b> <span class="gray-text">(65) {{ $companies['tel'] }}</span></span><br/>
                        <p class="header-text-1"><b>E. </b> <span class="gray-text">{{ $companies['email'] }}</span></p><br/>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="4" align="right">
                    <p class="orange-text" style="margin-top: 10px;"><b>www.ideology.sg</b></p>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
