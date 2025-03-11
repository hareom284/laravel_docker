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

        .footer-style{
            width: 100%;
            font-size: 12px;
            padding-top: 50px;
        }

    </style>
</head>

<body onload="subst()">
    <table class="footer-style">
        <tr>
            <td style="width: 40%">Print : {{$created_at}} {{$document_agreement_no}}</td>
            <td style="10%" align="center">E.&O.E. </td>
            <td align="right" style="40%">Page <span class="page"></span> of <span class="topage"></span></td>
        </tr>
    </table>
</body>

</html>
