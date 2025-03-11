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
            /* Use the custom font, fallback to sans-serif if unavailable */
        }

        .footer-style {
            width: 100%;
        }

        .text-center red-text {
            text-align: center;
        }

        .red-text {
            color: red;
        }

        .ft-bold {
            font-weight: bold;
        }

        th,
        td {
            border: 1px solid transparent;
            padding: 4px;
        }

        table {
            border-collapse: collapse;
            font-size: 14px;
        }

        .note-box {
            padding: 5px;
            background: silver;
            text-align: center;
            color: gray;
            font-size: 16px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="footer-style">

        <table style="width: 100%;">
            <tr>
                <td colspan="3" align="center">
                    <div>
                        <p class="note-box">Waterproffing | Painting | Building Repair | Renovation | Reinstatement |
                            Thermography</p>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <img src="{{ public_path() . '/images/bizsafe.png' }}" style="width: auto;height:100px">
                </td>
                <td align="center">
                    <div class="text-center red-text">www.buildspec.com.sg</div>
                    <div class="text-center red-text">www.facebook.com/buildspecconstruction</div>
                    <br />
                    <div class="text-center red-text">


                        9 Tagore Lane #02-06 9@TAGORE Singapore 787472
                    </div>
                    <div class="text-center red-text">
                        Tel: 64660276 | Fax: 64529825 | Email: buildspec@yahoo.com.sg
                    </div>
                    <div class="text-center red-text">
                        Company Registration No: 201435642N | GST Registration No:
                        201435642N
                    </div>
                </td>
                <td align="right">
                    <img src="{{ public_path() . '/images/bca.png' }}" style="width: auto;height:100px">
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
