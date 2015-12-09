<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css"/>
    <link rel="stylesheet" type="text/css" href="assets/css/fontawesome.min.css"/>
    <link rel="stylesheet" type="text/css" href="assets/css/AdminLTE.min.css"/>
    <link rel="stylesheet" type="text/css" href="assets/css/skins/skin-black.min.css"/>
    <link rel="stylesheet" type="text/css" href="assets/js/toastr/toastr.min.css"/>
    <link rel="stylesheet" type="text/css" href="assets/css/lets-dev.min.css"/>
    <link rel="icon" href="assets/img/public/logo.png" />
    <title>Let's Dev !</title>
    <script src="https://www.google.com/recaptcha/api.js"></script>
</head>
<?php
$onload = "getWallpaper();changeTitle('Let\'s Dev ! - Connexion');toastr.options = {'positionClass': 'toast-top-center'};";
if (isset($_GET['alert'])) {
    switch($_GET['alert']) {
        case 'already_registered':
            $onload .= "toastr['warning']('Vous êtes déjà inscrit.')";
            break;
        case 'not_registered':
            $onload .= "toastr['error']('Vous n\'êtes pas encore inscrit.')";
            break;
    }
}
?>
<body class="login-page" id="wallpaper" onload="<?php echo $onload; ?>">