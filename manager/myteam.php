<?php
include('../includes/autoload.php');
if (!hasTeam(getInformation()))
    header("Location: teams.php");
include('header.php');
include('navbar.php');
include('sidebar.php');
?>

    <div class="content-wrapper bg-white" id="myteam" onmouseover="changeTitle('Let\'s Dev ! - Mon équipe')">
        <div class="row">
            <div class="col-md-6 col-sm-12">
                <img src="../assets/img/public/logo.png" class="img-responsive team-logo">
                <h1 class="team-name">Let's Dev !</h1>
            </div>
            <div class="col-md-6 col-sm-12">
            </div>
        </div>
    </div>
<?php
include('footer.php');
?>