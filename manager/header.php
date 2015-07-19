<?php
session_start();
include('../includes/credentials.php');
include('../includes/config.php');
include('../includes/functions/security.php');
    if (!checkSession())
        header('Location: ../signin.php');
?>
<!Doctype HTML>
<html>
<head>
    <meta charset="UTF-8" />
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" type="text/css" href="../assets/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="../assets/css/fontawesome.min.css" />
    <link rel="stylesheet" type="text/css" href="../assets/css/AdminLTE.min.css" />
    <link rel="stylesheet" type="text/css" href="../assets/css/skins/skin-black.min.css" />
    <link rel="stylesheet" type="text/css" href="../assets/css/lets-dev.min.css" />
    <link rel="icon" href="../assets/img/public/logo.png" />
    <title>Let's Dev !</title>
</head>
<body class="skin-black fixed" onload="getWallpaper('manager')">
<div class="wrapper" id="wallpaper">