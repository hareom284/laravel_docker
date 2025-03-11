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
            margin: 0 auto;
            text-align: center;
            font-size: 12px;
        }
    </style>

</head>

<body>
    <div class="footer-style">
        <p>{{ $footer_text }}</p>
    </div>
</body>

</html>
