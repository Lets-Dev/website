<?php
include('includes/autoload.php');
if (checkSession())
    header('Location: manager/index?alert=already_loggedin');
include('includes/version.php');
include('includes/layouts/sign_header.php');
include('includes/libraries/Facebook/autoload.php');
?>

<body class="login-page" id="wallpaper" onload="getWallpaper();changeTitle('Let\'s Dev ! - Inscription')">
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

<?php

$fb = new Facebook\Facebook([
    'app_id' => $facebook['APP_ID'],
    'app_secret' => $facebook['APP_SECRET'],
    'default_graph_version' => 'v2.4',
]);
$helper = $fb->getRedirectLoginHelper();

$permissions = ['email']; // Optional permissions

// TODO: change callback URL
$fb_loginUrl = $helper->getLoginUrl($url.'signup?register=facebook', $permissions);

if (isset($_GET['register']))
    $register = $_GET['register'];
else $register = 'classic';
switch ($register) {
    case 'facebook':
        $helper = $fb->getRedirectLoginHelper();

        try {
            $accessToken = $helper->getAccessToken();
        } catch (Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

        if (!isset($accessToken)) {
            if ($helper->getError()) {
                header('HTTP/1.0 401 Unauthorized');
                echo "Error: " . $helper->getError() . "\n";
                echo "Error Code: " . $helper->getErrorCode() . "\n";
                echo "Error Reason: " . $helper->getErrorReason() . "\n";
                echo "Error Description: " . $helper->getErrorDescription() . "\n";
            } else {
                header('HTTP/1.0 400 Bad Request');
                echo 'Bad request';
            }
            exit;
        }

        // Logged in
        $token = $accessToken->getValue();

        // The OAuth 2.0 client handler helps us manage access tokens
        $oAuth2Client = $fb->getOAuth2Client();

        // Get the access token metadata from /debug_token
        $tokenMetadata = $oAuth2Client->debugToken($accessToken);

        // Validation (these will throw FacebookSDKException's when they fail)
        $tokenMetadata->validateAppId($facebook['APP_ID']);
        // If you know the user ID this access token belongs to, you can validate it here
        //$tokenMetadata->validateUserId('123');
        $tokenMetadata->validateExpiration();

        if (!$accessToken->isLongLived()) {
            // Exchanges a short-lived access token for a long-lived one
            try {
                $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
            } catch (Facebook\Exceptions\FacebookSDKException $e) {
                echo "<p>Error getting long-lived access token: " . $helper->getMessage() . "</p>\n\n";
                exit;
            }
        }

        $_SESSION['fb_access_token'] = (string)$accessToken;

        $fb = new Facebook\Facebook([
            'app_id' => $facebook['APP_ID'],
            'app_secret' => $facebook['APP_SECRET'],
            'default_graph_version' => 'v2.4',
        ]);

        try {
            // Returns a `Facebook\FacebookResponse` object
            $response = $fb->get('/me?fields=first_name,last_name,email,public_key', $accessToken->getValue());
        } catch (Facebook\Exceptions\FacebookResponseException $e) {
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }
        $user = $response->getGraphUser();
        $query = $db->prepare("SELECT * FROM users WHERE user_facebook_token = :token");
        $query->bindValue(':token', $user['id'], PDO::PARAM_INT);
        $query->execute();

        if ($query->rowCount() == 0) {
            $query->closeCursor();

?>
        <form method="post" id="signup">
            <h3>Inscription</h3>
            <input type="hidden" name="method" value="facebook"/>
            <input type="hidden" name="facebook_id" value="<?php echo $user['id']; ?>" />

            <div class="form-group has-feedback">
                <input type="text" class="form-control" placeholder="Prénom" name="firstname" value="<?php echo $user['first_name']; ?>" required readonly="readonly"/>
                <span class="fa fa-user form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <input type="text" class="form-control" placeholder="Nom" name="lastname" value="<?php echo $user['last_name']; ?>" required readonly="readonly"/>
                <span class="fa fa-user form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <input type="email" class="form-control" placeholder="Adresse E-Mail" name="email" value="<?php echo $user['email']; ?>" required readonly="readonly"/>
                <span class="fa fa-envelope form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <input type="text" class="form-control" placeholder="Téléphone (facultatif)" name="phone"/>
                <span class="fa fa-phone form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <input type="password" class="form-control" placeholder="Mot de Passe" name="password" required/>
                <span class="fa fa-lock form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <input type="password" class="form-control" placeholder="Confirmation" name="confirm" required/>
                <span class="fa fa-lock form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <select class="form-control" name="promotion">
                    <option disabled selected value="0">Promotion</option>
                    <?php
                    for ($i = 1993; $i < date('Y') + 5; $i++) {
                        echo '<option value="' . $i . '">' . $i . '</option>';
                    }
                    ?>
                </select>
                <span class="fa fa-calendar form-control-feedback"></span>
            </div>

            <div class="row">
                <div class="col-xs-12">
                    <button type="submit" class="btn btn-ld btn-block btn-flat">S'inscrire</button>
                </div>
            </div>
        </form>

    </div>
</div>
<?php
        } else {
            header('Location:signin.php?alert=already_registered');
        }
        break;

    default:
        ?>
        <form method="post" id="signup">
            <h3>Inscription</h3>
            <input type="hidden" name="method" value="lets-dev"/>

            <div class="form-group has-feedback">
                <input type="text" class="form-control" placeholder="Prénom" name="firstname" required/>
                <span class="fa fa-user form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <input type="text" class="form-control" placeholder="Nom" name="lastname" required/>
                <span class="fa fa-user form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <input type="email" class="form-control" placeholder="Adresse E-Mail" name="email" required/>
                <span class="fa fa-envelope form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <input type="text" class="form-control" placeholder="Téléphone (facultatif)" name="phone"/>
                <span class="fa fa-phone form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <input type="password" class="form-control" placeholder="Mot de Passe" name="password" required/>
                <span class="fa fa-lock form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <input type="password" class="form-control" placeholder="Confirmation" name="confirm" required/>
                <span class="fa fa-lock form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <select class="form-control" name="promotion">
                    <option disabled selected value="0">Promotion</option>
                    <?php
                    for ($i = 1993; $i < date('Y') + 5; $i++) {
                        echo '<option value="' . $i . '">' . $i . '</option>';
                    }
                    ?>
                </select>
                <span class="fa fa-calendar form-control-feedback"></span>
            </div>

            <div class="row">
                <div class="col-xs-12">
                    <button type="submit" class="btn btn-ld btn-block btn-flat">S'inscrire</button>
                </div>
            </div>
        </form>
        <div class="login-separator text-center">
            <p>- OU -</p>
            <a href="signin">Déjà inscrit ?</a>
        </div>

        <div class="social-auth-links text-center">
            <p>- OU -</p>
            <a href="<?php echo $fb_loginUrl ?>" class="btn btn-block btn-social btn-facebook btn-flat"><i
                    class="fa fa-facebook"></i> S'inscrire avec Facebook</a>
        </div>
        <?php
        break;
}

?>



<script src="assets/js/jquery.min.js" type="text/javascript"></script>
<script src="assets/js/bootstrap.min.js" type="text/javascript"></script>
<script src="assets/js/toastr/toastr.min.js" type="text/javascript"></script>
<script src="assets/js/bootstrap-markdown/js/bootstrap-markdown.min.js"></script>
<script src="assets/js/lets-dev.min.js" type="text/javascript"></script>
<script>
    $("#signup").submit(function () {
        $('.btn').attr('disabled','disabled');
        $.ajax({
            type: "POST",
            url: "includes/queries/signup.php",
            data: $("#signup").serialize(),
            success: function (data) {
                console.log(data);
                if (data.status === "success") {
                    window.location = "./signin";
                }
                else {
                    var i;
                    for (i = 0; i < data.messages.length; i++)
                        toastr["error"](data.messages[i])
                }
                $('.btn').removeAttr('disabled');
            }
        });
        return false;
    });
</script>
</body>
</html>