<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <div>
        <h1>{{ $title }}</h1>
        <p>{{ $content }}</p>
        <p>{{ $sender->first_name . ' ' . $sender->last_name }}</p>
        <p>{{ $receiver->first_name . ' ' . $receiver->last_name }}</p>
        <p>{{ $created_at }}</p>
    </div>
</body>
</html>
