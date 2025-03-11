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
        * {
            font-family: 'HelveticaNeue-Thin' !important;
        }
    </style>
</head>

<body onload="subst()">
    <div class="footer-style">
        <table style="width: 100%;font-size:12px;">
            <thead>
                <tr>
                    <td style="width: 300px;">

                        @if (isset($properties))
                            {{ $properties['block_num'] . ' ' . $properties['street_name'] . ' ' . '#' . $properties['unit_num'] }}
                        @endif
                    </td>
                    <td align="center">{{ $companies['email'] }}</td>
                    <td align="right"><span class="page"></span> of <span class="topage"></span></td>
                </tr>
            </thead>
        </table>

    </div>
</body>

</html>
