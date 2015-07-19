<?php
session_start();
include('includes/credentials.php');
include('includes/functions/security.php');
if (checkSession())
    header('Location: manager?alert=already_loggedin');
include('includes/layouts/sign_header.html');
?>
<body class="login-page" id="wallpaper" onload="getWallpaper();changeTitle('Let\'s Dev ! - Connexion')">
<div class="login-box">
    <div class="login-logo">
        <img src="assets/img/public/banner.png" class="img-responsive" />
    </div>
    <div class="login-box-body">
        <div class="login-box-msg">
            <div class="callout text-left" id="alert-div" hidden>
                <i class="fa" id="alert-icon"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span id="alert-text"></span>
            </div>
        </div>
        <form method="post" id="signin">
            <h3>Connexion</h3>
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
                        <input type="checkbox" name="type" value="cookie" /> Se souvenir de moi
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
                    window.location = "./manager?alert=loggedin";
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
