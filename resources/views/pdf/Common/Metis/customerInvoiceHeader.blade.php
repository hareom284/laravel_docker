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
            /* color: #344C52; */
        }

        body {
            margin: 0;
            padding: 0;
            /* background-color: #ECF0F1; */
            width: 100%;
            height: 100%;
        }

        .header-section {
            padding-top: 30px;
            padding-right: 30px;
            padding-left: 30px;
            color: black;
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
        <table  style="border-collapse: collapse; border-color: transparent; width: 100%;font-size: 16px;">
            <tr>
               <td style="width:50%">
                    <table  style="border-collapse: collapse; border-color: transparent; width: 100%;font-size: 16px;">
                        <tr><td colspan="2">{{ $company_name }}</td></tr>
                        <tr><td>U.E.N: T20LL1942A</td></tr>
                        <tr><td style="padding-top: 20px; width: 100px;">{{ $company_address }}</td><td></td></tr>
                        <tr><td style="padding-top:20px;">Tel: {{ $company_tel }}</td></tr>
                        <tr><td>Website: www.themetis.space</td></tr>
                        <tr><td>{{ $company_email }}</td></tr>
                    </table>
               </td>
               <td style="width:50%; vertical-align: middle;" align="right">
                <img src="{{ 'data:image/png;base64,' . $companies['company_logo'] }}" style="width: 160px; height: 100%; max-height: 150px;">
               </td>
            </tr>
        </table>
    </div>
    <br/>
</body>

</html>
