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
            /* Use the custom font, fallback to sans-serif if unavailable */
        }

        .footer-style {
            margin: 0 auto;
            text-align: center;
            font-size: 12px;
        }
    </style>

</head>

<body onload="subst()">
    <table style="width: 100%">
        <tr>
            <td align="right">
                <img src="{{ public_path() . '/images/bizsafedorlich.png' }}" height="100" />
            </td>
        </tr>
    </table>
    <div class="footer-style">
        <span class="page"></span>
    </div>
</body>

</html>
