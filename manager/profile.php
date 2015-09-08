<?php
include('../includes/autoload.php');
include('header.php');
include('navbar.php');
include('sidebar.php');
?>

    <div class="content-wrapper" onmouseover="changeTitle('Let\'s Dev ! - Mon profil')">
        <div class="row">
            <div class="col-md-4">
                <div class="box">
                    <div class="box-header">
                        <h3>Gestion du mot de passe</h3>
                    </div>
                    <div class="box-body">
                        <form id="change_password">
                            <input type="hidden" name="action" value="edit_password" />
                            <div class="form-group">
                                <label for="old">Ancien mot de passe</label>
                                <input class="form-control" type="password" name="current" id="old"
                                       placeholder="Saisissez votre ancien mot de passe..."/>
                            </div>
                            <div class="form-group">
                                <label for="new">Nouveau mot de passe</label>
                                <input class="form-control" type="password" name="new" id="new"
                                       placeholder="Saisissez votre nouveau mot de passe..."/>
                            </div>
                            <div class="form-group">
                                <input class="form-control" type="password" name="confirm" id="confirmation"
                                       placeholder="Saisissez à nouveau votre nouveau mot de passe..."/>
                            </div>
                            <button class="btn btn-ld btn-flat btn-block">Confirmer</button>
                        </form>
                        <script>
                            $("#change_password").submit(function () {
                                $('.btn').attr('disabled', 'disabled');
                                $.ajax({
                                    type: "POST",
                                    url: "../includes/queries/account.php",
                                    data: $("#change_password").serialize(),
                                    success: function (data) {
                                            var i;
                                            for (i = 0; i < data.messages.length; i++)
                                                toastr[data.status](data.messages[i])
                                        $('.btn').removeAttr('disabled');
                                    }
                                });
                                return false;
                            });
                        </script>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="box">
                    <div class="box-header">
                        <h3>Gestion des informations de contact</h3>
                    </div>
                    <div class="box-body">
                        <form id="change_contact">
                            <input type="hidden" name="action" value="edit_contact" />
                            <div class="form-group">
                                <label for="email">Adresse E-Mail</label>
                                <input class="form-control" type="email" name="email" id="email"
                                       placeholder="Saisissez votre adresse e-mail..." value="<?php echo getInformation('email') ?>" />
                            </div>
                            <div class="form-group">
                                <label for="phone">Téléphone</label>
                                <input class="form-control" type="text" name="phone" id="phone"
                                       placeholder="Saisissez votre numéro de téléphone..." value="<?php echo getInformation('phone', getInformation()) ?>"/>
                            </div>
                            <button class="btn btn-ld btn-flat btn-block">Confirmer</button>
                        </form>
                        <script>
                            $("#change_contact").submit(function () {
                                $('.btn').attr('disabled', 'disabled');
                                $.ajax({
                                    type: "POST",
                                    url: "../includes/queries/account.php",
                                    dataType: "json",
                                    data: $("#change_contact").serialize(),
                                    success: function(data) {
                                        console.log(data);
                                        var i;
                                        for (i = 0; i < data.messages.length; i++)
                                            toastr[data.status](data.messages[i])
                                        $('.btn').removeAttr('disabled')
                                    }
                                });
                                return false;
                            });
                        </script>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="box">
                    <div class="box-header">
                        <h3>Gestion de l'avatar</h3>
                    </div>
                    <div class="box-body">
                        <p>
                            Les photos de profil sont gérées grâce à Gravatar. Pour changer de photo de profil,
                            nous vous invitons à vous connecter sur ce site et à lier une image à l'adresse e-mail suivante:
                            <center>
                            <b>
                                <?php echo getInformation('email'); ?>
                            </b>
                            </center>
                        </p>
                        <div class="text-center">
                            <a href="//gravatar.com" target="_blank" class="btn btn-flat btn-gravatar">
                                <i class="fa fa-wordpress"></i> Gravatar
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
<?php
include('footer.php');
