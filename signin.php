<?php
session_start();
include('includes/functions/security.php');
if (checkSession())
    header('Location: manager?alert=already_loggedin');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css"/>
    <link rel="stylesheet" type="text/css" href="assets/css/fontawesome.min.css"/>
    <link rel="stylesheet" type="text/css" href="assets/css/AdminLTE.min.css"/>
    <link rel="stylesheet" type="text/css" href="assets/css/skins/skin-blue.min.css"/>
    <link rel="stylesheet" type="text/css" href="assets/css/lets-dev.min.css"/>
    <title>Let's Dev ! - Connexion</title>
</head>
<body class="login-page" id="wallpaper" onload="getWallpaper()">
<div class="login-box">
    <div class="login-logo">
        <a href="./"><b>Let's</b> Dev !</a>
    </div>
    <div class="login-box-body">
        <div class="login-box-msg">
            <div class="callout text-left" id="alert-div" hidden>
                <i class="fa" id="alert-icon"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span id="alert-text"></span>
            </div>
        </div>
        <form method="post" id="signin">
            <input type="hidden" name="method" value="lets-dev"/>

            <div class="form-group has-feedback">
                <input type="email" class="form-control" placeholder="Adresse E-Mail" name="email" required/>
                <span class="fa fa-envelope form-control-feedback" style="line-height: 34px;"></span>
            </div>
            <div class="form-group has-feedback">
                <input type="password" class="form-control" placeholder="Mot de Passe" name="password" required/>
                <span class="fa fa-lock form-control-feedback" style="line-height: 34px;"></span>
            </div>
            <div class="row">
                <div class="col-xs-8">
                    <label>
                        <input type="checkbox"> Se souvenir de moi
                    </label>
                </div>
                <div class="col-xs-4">
                    <button type="submit" class="btn btn-ld btn-block btn-flat">Connexion</button>
                </div>
            </div>
        </form>
        <div class="login-separator text-center">
            <p>- OU -</p>
            <a href="signup.php">Pas encore inscrit ?</a>
        </div>

        <!--        <div class="social-auth-links text-center">-->
        <!--            <p>- OU -</p>-->
        <!--            <a href="#" class="btn btn-block btn-social btn-facebook btn-flat"><i class="fa fa-facebook"></i> Sign in using Facebook</a>-->
        <!--            <a href="#" class="btn btn-block btn-social btn-google-plus btn-flat"><i class="fa fa-google-plus"></i> Sign in using Google+</a>-->
        <!--        </div><!-- /.social-auth-links -->

    </div>
</div>

<script src="assets/js/jquery.min.js" type="text/javascript"></script>
<script src="assets/js/bootstrap.min.js" type="text/javascript"></script>
<script src="assets/js/lets-dev.min.js" type="text/javascript"></script>
<script>
    $("#signin").submit(function () {
        $('.btn').attr('disabled', 'disabled');
        $.ajax({
            type: "POST",
            url: "includes/queries/signin.php",
            data: $("#signin").serialize(),
            success: function (data) {
                console.log(data);
                if (data.status === "success") {
                    window.location = "./manager";
                }
                else {
                    $('#alert-div').fadeIn().addClass('callout-danger');
                    $('#alert-icon').addClass('fa-warning');
                    var text, i;
                    for (i = 0; i < data.messages.length; i++)
                        text = data.messages[i] + '<br/>';
                    $('#alert-text').html(text);
                }
                $('.btn').removeAttr('disabled');
            }
        });
        return false;
    });
</script>
</body>
</html>
