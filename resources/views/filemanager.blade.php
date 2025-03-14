<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'File Manager') }}</title>

    <!-- Styles -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <link href="/vendor/file-manager/css/file-manager.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">
</head>

<body>
    <div class="container">
        <h2 class="mt-5">Media Library File Manager</h2>
        <div class="row">
            <div class="col-12" id="fm-main-block">
                <div id="fm"></div>
            </div>
        </div>
    </div>

    <!-- File manager -->
    <script src="/vendor/file-manager/js/file-manager.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            var element = document.querySelector(".col-auto");
            var button = element.insertBefore(document.createElement("a"), element.firstChild);
            button.href = "/home";
            button.className = "btn btn-primary me-2";
            button.innerHTML = '<i class="bi bi-house-door-fill"></i>Back';
            document.getElementsByClassName('fm-body').setAttribute('style', 'height:' + window.innerHeight + 'px');

            fm.$store.commit('fm/setFileCallBack', function(fileUrl) {
                window.opener.fmSetLink(fileUrl);
                window.close();
            });
        });
    </script>
</body>

</html>
