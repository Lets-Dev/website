<?php
session_start();
include('includes/credentials.php');
include('includes/functions/security.php');
if (checkSession())
    header('Location: manager?alert=already_loggedin');
include('includes/layouts/sign_header.php');
include('includes/libraries/Facebook/autoload.php');
include('includes/libraries/user_agent.php');

$fb = new Facebook\Facebook([
    'app_id' => $facebook['APP_ID'],
    'app_secret' => $facebook['APP_SECRET'],
    'default_graph_version' => 'v2.4',
]);
$helper = $fb->getRedirectLoginHelper();

$permissions = ['email']; // Optional permissions

// TODO: change callback URL
$fb_loginUrl = $helper->getLoginUrl('http://localhost/lets-dev/signin.php?login=facebook', $permissions);

if (isset($_GET['login'])) {
    switch ($_GET['login']) {
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
                $response = $fb->get('/me?fields=first_name,last_name,email', $accessToken->getValue());
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

            if ($query->rowCount()>0) {
                $data = $query -> fetchObject();
                do {
                    $token = generateToken(50);
                    $key = generateToken(50);
                    $check = $db->prepare("SELECT * FROM user_logins WHERE login_token=:token AND login_key=:key");
                    $check->bindValue(':token', $token, PDO::PARAM_STR);
                    $check->bindValue(':key', $key, PDO::PARAM_STR);
                    $check->execute();
                } while ($check->rowCount() != 0);

                // On enregistre la connexion
                $insert = $db->prepare("INSERT INTO user_logins (login_token, login_key, login_user, login_time, login_platform, login_browser, login_ip)
                                      VALUES (:login_token, :login_key, :login_user, :login_time, :login_platform, :login_browser, :login_ip)");
                $insert->bindValue(':login_token', $token, PDO::PARAM_STR);
                $insert->bindValue(':login_key', $key, PDO::PARAM_STR);
                $insert->bindValue(':login_user', $data->user_id, PDO::PARAM_INT);
                $insert->bindValue(':login_time', time(), PDO::PARAM_INT);
                $insert->bindValue(':login_platform', parse_user_agent()['platform'], PDO::PARAM_STR);
                $insert->bindValue(':login_browser', parse_user_agent()['browser'] . " " . parse_user_agent()['version'], PDO::PARAM_STR);
                $insert->bindValue(':login_ip', $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR);
                $insert->execute();

                $_SESSION['connected'] = true;
                $_SESSION['informations'] = array("id" => $data->user_id,
                    "email" => $data->user_email,
                    "firstname" => $data->user_firstname,
                    "lastname" => $data->user_lastname);
                header("Location:manager?alert=loggedin");
            }
            else {
                header('Location:signup.php?alert=not_registered');
            }


            break;
    }
}

?>
<body class="login-page" id="wallpaper" onload="getWallpaper();changeTitle('Let\'s Dev ! - Connexion')">
<div class="login-box">
    <div class="login-logo">
        <img src="assets/img/public/banner.png" class="img-responsive"/>
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
                        <input type="checkbox" name="type" value="cookie"/> Se souvenir de moi
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

        <div class="social-auth-links text-center">
            <p>- OU -</p>
            <a href="<?php echo $fb_loginUrl ?>" class="btn btn-block btn-social btn-facebook btn-flat"><i
                    class="fa fa-facebook"></i> Se connecter avec Facebook</a>
        </div>

    </div>
</div>

<script src="assets/js/jquery.min.js" type="text/javascript"></script>
<script src="assets/js/bootstrap.min.js" type="text/javascript"></script>
<script src="assets/js/toastr/toastr.min.js" type="text/javascript"></script>
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
