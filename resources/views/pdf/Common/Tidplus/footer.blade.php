<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
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
        * {
            font-family: sans-serif;
            margin: 0 !important;
            /* Use the custom font, fallback to sans-serif if unavailable */
        }

        .footer-style {
            margin: 0 auto;
            /* text-align: center; */
            font-size: 15px;
            background-color: #6f6e69;
            padding-top: 20px;
            padding-bottom: 20px;
            padding-right: 20px;
            padding-left: 20px;
            color: white;
        }
    </style>

</head>

<body onload="subst()" style="padding-top: 40px;">
    <div style="margin: 0 auto; text-align: center;font-size: 12px;">
        Page <span class="page"></span> of <span class="topage"></span>
    </div>
    <br/>
    <div class="footer-style">
        <table style="width: 100%;" class="text-white">
            <tr>
                <td align="center">62861233</td>
                <td align="center" style="width: 2px; font-size: 24px;">•</td>
                <td align="center">enquiries@tidplusdesign.com.sg</td>
                <td align="center" style="width: 2px; font-size: 24px;">•</td>
                <td align="center">www.tidplusdesign.com.sg</td>
                <td align="center" style="width: 2px; font-size: 24px;">•</td>
                <td align="center">IG: tidplus_design</td>
                <td align="center" style="width: 2px; font-size: 24px;">•</td>
                <td align="center">CO/GST Reg no: 200405817Z</td>
            </tr>
        </table>

    </div>
</body>

</html>
