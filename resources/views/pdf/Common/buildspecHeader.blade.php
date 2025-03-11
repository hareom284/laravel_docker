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

        .logo-section {
            margin: 0 auto;
            text-align: center;
        }

        .red-text {
            font-weight: bold;
            color: red;
            padding: 0;
            margin: 0;
        }
    </style>
</head>

<body>
    <div class="logo-section">
        <img src="{{ public_path() . '/images/build_spec.jpg' }}" style="width: auto;height:100px">
        <h2 class="red-text">BUILDSPEC CONSTRUCTION PTE LTD</h2>
        <h4 class="red-text">Established Since 1991</h4>
    </div>
</body>

</html>
