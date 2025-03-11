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
            var pageElements = document.getElementsByClassName('page');
            var totalPageElements = document.getElementsByClassName('topage');

            // Set the current page and total page number
            for (var j = 0; j < pageElements.length; ++j) {
                pageElements[j].textContent = vars['page']; // Current page
            }

            for (var j = 0; j < totalPageElements.length; ++j) {
                totalPageElements[j].textContent = vars['topage']; // Total pages
            }
        }
    </script>
</head>

<body onload="subst()">
    <!-- Footer -->
    <table style="width: 100%;">
        <tr>
            <td style="width: 35%;">{{ $quotationData['document_agreement_no'] }}</td>
            <td style="width: 30%; text-align: center">
                Page <span class="page"></span> of <span class="topage"></span>
            </td>
            <td style="width: 35%; text-align: right;">__________________Signature</td>
        </tr>
    </table>
</body>

</html>
