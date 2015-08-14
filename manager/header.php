<?php
if (!checkSession())
    header('Location: ' . $url . 'signin');
?>
<!Doctype HTML>
<html>
<head>
    <base href="<?php echo $url; ?>manager/"/>
    <meta charset="UTF-8"/>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" type="text/css" href="../assets/css/bootstrap.min.css"/>
    <link rel="stylesheet" type="text/css" href="../assets/css/fontawesome.min.css"/>
    <link rel="stylesheet" type="text/css" href="../assets/js/select2/select2.min.css" />
    <link rel="stylesheet" type="text/css" href="../assets/css/AdminLTE.min.css"/>
    <link rel="stylesheet" type="text/css" href="../assets/css/skins/skin-black.min.css"/>
    <link rel="stylesheet" type="text/css" href="../assets/js/toastr/toastr.min.css"/>
    <link rel="stylesheet" type="text/css" href="../assets/css/lets-dev.min.css"/>
    <link rel="icon" href="../assets/img/public/logo.png"/>
    <title>Let's Dev !</title>
    <script src="../assets/js/jquery.min.js"></script>
</head>