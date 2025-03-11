<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <title><?= $page->get('title') ?></title>
    <link rel="stylesheet" href="<?= phpb_theme_asset('css/style.css') ?>" />

    <!-- Favicon -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <!-- Icons. Uncomment required icon fonts -->
    <link rel="stylesheet" href="<?= phpb_theme_asset('materialui/assets/vendor/fonts/materialdesignicons.css')?>" />
    <link rel="stylesheet" href="<?= phpb_theme_asset('materialui/assets/vendor/fonts/fontawesome.css')?>" />
    <!-- Core CSS -->
    <link rel="stylesheet" href="<?= phpb_theme_asset('materialui/assets/vendor/css/rtl/core.css')?>" class="template-customizer-core-css" />
    <link rel="stylesheet" href="<?= phpb_theme_asset('materialui/assets/vendor/css/rtl/theme-default.css')?>" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="<?= phpb_theme_asset('materialui/assets/css/demo.css')?>" />
    <!-- Vendors CSS -->
    <link rel="stylesheet" href="<?= phpb_theme_asset('materialui/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css')?>" />
    <link rel="stylesheet" href="<?= phpb_theme_asset('materialui/assets/vendor/libs/node-waves/node-waves.css')?>" />
    <!-- <link rel="stylesheet" href="<?= phpb_theme_asset('materialui/assets/vendor/libs/select2/select2.css')?>" /> -->
    <!-- sweet alert  -->
    <link rel="stylesheet" href="<?= phpb_theme_asset('materialui/assets/vendor/libs/animate-css/animate.css')?>" />
    <link rel="stylesheet" href="<?= phpb_theme_asset('materialui/assets/vendor/libs/sweetalert2/sweetalert2.css')?>" />
</head>

<body>
     <div class="container-fluid">

         <?= $body ?>
     </div>






    <script src="<?= phpb_theme_asset('materialui/assets/vendor/libs/jquery/jquery.js')?>"></script>
    <script src="<?= phpb_theme_asset('materialui/assets/vendor/libs/popper/popper.js')?>"></script>
    <script src="<?= phpb_theme_asset('materialui/assets/vendor/js/bootstrap.js')?>"></script>
    <script src="<?= phpb_theme_asset('materialui/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js')?>"></script>
    <script src="<?= phpb_theme_asset('materialui/assets/vendor/libs/node-waves/node-waves.js')?>"></script>
    <script src="<?= phpb_theme_asset('materialui/assets/vendor/libs/hammer/hammer.js')?>"></script>
    <script src="<?= phpb_theme_asset('materialui/assets/js/main.js')?>"></script>
    <script src="<?= phpb_theme_asset('materialui/assets/vendor/js/menu.js')?>"></script>
    <script src="<?= phpb_theme_asset('materialui/assets/vendor/js/dropdown-hover.js')?>"></script>
    <script src="<?= phpb_theme_asset('materialui/assets/vendor/js/mega-dropdown.js')?>"></script>
    <script src="<?= phpb_theme_asset('materialui/assets/js/ui-navbar.js')?>"></script>
    <script src="<?= phpb_theme_asset('materialui/assets/js/tables-datatables-advanced.js')?>"></script>
    <!-- <script src="<?= phpb_theme_asset('materialui/js/ckeditor.js')?>"></script> -->

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous">

    </script>
</body>

</html>
