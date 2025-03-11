<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <title>
        {{ Src\Company\System\Infrastructure\EloquentModels\SiteSettingEloquentModel::find(1)->site_name ?? env('APP_NAME') }}
    </title>
    <link rel="icon" type="image/x-icon"
        href="{{ Src\Company\System\Infrastructure\EloquentModels\SiteSettingEloquentModel::find(1)->getFirstMedia('fav_icon')->original_url ?? ' ' }}">


    @routes
    @vite('resources/js/app.js')
    @inertiaHead
</head>
<style>
    *,
    html,
    body {
        margin: 0;
        padding: 0;
    }
</style>

<body>
    @inertia

    <script>
        localStorage.setItem("_token", document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    </script>
</body>

</html>
