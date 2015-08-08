<?php
if (!checkSession())
    header('Location: ' . $url . 'signin');
include('../includes/version.php');
?>
<!Doctype HTML>
<html>
<head>
    <base href="<?php echo $url; ?>manager/"/>
    <meta charset="UTF-8"/>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" type="text/css" href="../assets/css/bootstrap.min.css"/>
    <link rel="stylesheet" type="text/css" href="../assets/css/fontawesome.min.css"/>
    <link rel="stylesheet" type="text/css" href="../assets/css/AdminLTE.min.css"/>
    <link rel="stylesheet" type="text/css" href="../assets/css/skins/skin-black.min.css"/>
    <link rel="stylesheet" type="text/css" href="../assets/js/toastr/toastr.min.css"/>
    <link rel="stylesheet" type="text/css" href="../assets/css/lets-dev.min.css"/>
    <link rel="icon" href="../assets/img/public/logo.png"/>
    <title>Let's Dev !</title>
    <script src="../assets/js/jquery.min.js"></script>
</head><?php
$onload = "getWallpaper('manager');";
if (isset($_GET['alert'])) {
    switch ($_GET['alert']) {
        case 'loggedin':
            $onload .= "toastr['success']('Vous êtes bien connecté.')";
            break;
    }
}
?>
<body class="skin-black fixed" onload="<?php echo $onload; ?>">
<div class="wrapper" id="wallpaper">
    <div style="position: fixed;bottom: 10px; right: 10px; color: #FEFEFE; text-shadow: 1px 0 0 #000,-1px 0 0 #000,0 1px 0 #000,0 -1px 0 #000" class="text-right">
        <?php
        foreach ($version as $versioning => $title) {
            $html = '<b>' . $versioning . '</b><p>' . $title . '</p>';
        }
        echo $html;

        ?>
    </div>