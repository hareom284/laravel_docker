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

            // var page = vars['page'];
            // var toPage = vars['topage'];

            // var footer = document.querySelector('.footer-style');
            // if(page==toPage){
            //     footer.classList.add('hide_header_and_footer');
            // }

        }
    </script>

    <style>
        * {
            font-family: sans-serif;
        }

        .footer-style {
            /* margin: 0 auto; */
            height: 200px;
            font-size: 12px;
            width: 100%;
            padding-bottom: 20px;

        }
        .footer-style td {
            vertical-align:bottom;
            width: 100%;

        }
        .footer-style pre {
            white-space: pre-wrap;
            /* margin: 0; */
        }

        .hide_header_and_footer{
          display: none;
        }
    </style>
</head>

<body onload="subst()">
    <table class="footer-style">
        <tr>
            <td align="center">
                <pre>{{ $footer }}</pre>
            </td>
        </tr>
    </table>
</body>

</html>
