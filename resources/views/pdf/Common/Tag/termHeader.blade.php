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

        .full-table {
            width: 100%;
        }

        .border-bottom {
            /* border-bottom: 3px solid black; */
        }

        .uppercase{
            text-transform: uppercase;
        }
    </style>
</head>

<body>

    <div>
        <div style="position: relative;padding-top: 50px;">
            <table class="full-table border-bottom">
                <tr>
                    <td style="width: 100px;"></td>
                    <td align="right" style="vertical-align: middle;">
                        <img src="{{ 'data:image/png;base64,' . $company_logo }}" height="100" />
                    </td>
                    <td>
                        <p style="font-weight: bold; font-size: 16px;">{{ $companies['name'] }}</p>
                        <p>{{ $companies['main_office'] }}</p>
                        <p>CO. Reg. No. {{ $companies['reg_no'] }}</p>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <br/>
    <br/>
</body>

</html>
