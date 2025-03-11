<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link type="text/css" media="all" rel="stylesheet" href="{{ public_path('css/font.css') }}">
    <script>
        function subst() {
            var vars = {};
            var x = document.location.search.substring(1).split('&');
            for (var i in x) {
                var z = x[i].split('=', 2);
                vars[z[0]] = unescape(z[1]);
            }
            var x = ['frompage', 'topage', 'page', 'webpage', 'section', 'subsection', 'subsubsection'];
            for (var i in x) {
                var y = document.getElementsByClassName(x[i]);
                for (var j = 0; j < y.length; ++j) y[j].textContent = vars[x[i]];
            }
        }
    </script>
    <style>

        /* @import url('https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&display=swap'); */

        * {
            font-family: sans-serif;
            margin: 0 !important;
            /* Use the custom font, fallback to sans-serif if unavailable */
        }

        .header-section {
            padding-top: 20px;
            /* padding-bottom: 20px; */
            padding-right: 20px;
            padding-left: 20px;
            background-color: #6f6e69;
            color: white;
            /* margin-bottom: 40px !important; */
        }

        .right-header {
            float: right;
        }

        .left-header {
            float: left;
        }

        .logo-section {
            margin: 0 auto;
            /* text-align: left; */
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

        .designer-text{
            font-family: 'Dancing Script', cursive;
            font-weight: 700; /* Bold */
        }
        .segoe-script{
            font-family: 'Segoe Script' !important;
            font-size: 20px !important;
        }
        .segoe-script::before{
            content: "“";
        }
        .segoe-script::after{
            content: "”";
        }
    </style>

</head>

<body onload="subst()">
    <div class="header-section">
        <table style="width: 100%; table-layout: fixed;">
            <tr>
                <td style="text-align: left; vertical-align: top;width: 150px;">
                    <div class="logo-section" style="height: 150px;">
                        <img src="{{ 'data:image/png;base64,' . $company_logo }}" style="width: auto; height: 100%; max-height: 200px;">
                    </div>
                </td>
                <td style="text-align: right; vertical-align: bottom; height: 100px;">
                    <p class="segoe-script" style="margin: 0;padding-bottom: 10px;">design without limits. bringing interiors to life.</p>
                </td>
            </tr>
        </table>
    </div>
    <br/>
</body>

</html>
