<?php
include('../includes/autoload.php');
include('header.php');
include('navbar.php');
include('sidebar.php');

switch ($_GET['action']) {
    case 'manage':
        if (!checkPrivileges(getInformation()))
            header('Location: index');
        ?>
        <div class="content-wrapper" onmouseover="changeTitle('Let\'s Dev ! - Gestion des utilisateurs')">
            <div class="row">
                <div class="col-md-12">
                    <div class="box">
                        <div class="box-header">
                            <h3>Gestion des utilisateurs
                            </h3>
                        </div>
                        <div class="box-body">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>E-Mail</th>
                                    <th>Dernière cotisation</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $query = $db->prepare("SELECT * FROM users ORDER BY user_lastname, user_firstname");
                                $query->execute();
                                while ($data = $query->fetchObject()) {
                                    echo '<tr>
                                            <td class="name">
                                                '.$data->user_lastname . ' ' . $data->user_firstname.'
                                            </td>
                                        <td><a href="mailto:' . $data->user_email . '" target="_blank">' . $data->user_email . '</a></td>
                                        <td>';
                                    $query2 = $db->prepare("SELECT * FROM user_subscriptions
                                                            WHERE subscription_user=:user
                                                            ORDER BY subscription_school_year DESC LIMIT 1");
                                    $query2->bindValue(':user', $data->user_id, PDO::PARAM_INT);
                                    $query2->execute();
                                    if ($data2 = $query2->fetchObject())
                                        if ($data2->subscription_school_year == getCurrentYear())
                                            echo '<span class="label label-primary">' . $data2->subscription_school_year . '</span>';
                                        else
                                            echo '<span class="label label-danger">' . $data2->subscription_school_year . '</span>';
                                    else
                                        echo "Jamais";
                                    echo '</td>
                                        <td class="text-right">';
                                    if ($data->user_ban == 0 && $data->user_honor == 0)
                                        echo '<button class="btn btn-flat btn-xs btn-danger" onclick="banMember(' . $data->user_id . ')">Bannir</button>
                                        <button class="btn btn-flat btn-xs btn-success" onclick="honorMember(' . $data->user_id . ')">Honorer</button>';
                                    elseif($data->user_honor == 1)
                                        echo '<i class="fa fa-star text-yellow"></i>';
                                    else
                                        echo '<i class="fa fa-times text-red"></i>';

                                    echo '</td>
                                    </tr>';
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <link rel="stylesheet" href="../assets/js/datatables/dataTables.bootstrap.css">
        <script src="../assets/js/datatables/jquery.dataTables.min.js"></script>
        <script src="../assets/js/datatables/dataTables.bootstrap.min.js"></script>

        <script>
            $('.table').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": false,
                "autoWidth": false
            });
            function honorMember(id) {
                var button = $(event.target);
                $('.btn').attr('disabled', 'disabled');
                $.post('../includes/queries/account.php', {
                        action: "honor",
                        user_id: id
                    },
                    function (data) {
                        console.log(data);
                        data = JSON.parse(data);
                        var i;
                        if (data.status == "success") {
                            for (i = 0; i < data.messages.length; i++)
                                toastr["success"](data.messages[i])
                        }
                        else
                            for (i = 0; i < data.messages.length; i++)
                                toastr["error"](data.messages[i])
                        $('.btn').removeAttr('disabled');
                        button.remove();
                    })
            }
            function banMember(id) {
                var button = $(event.target);
                $('.btn').attr('disabled', 'disabled');
                $.post('../includes/queries/account.php', {
                        action: "ban",
                        user_id: id
                    },
                    function (data) {
                        console.log(data);
                        data = JSON.parse(data);
                        var i;
                        if (data.status == "success") {
                            for (i = 0; i < data.messages.length; i++)
                                toastr["success"](data.messages[i])
                        }
                        else
                            for (i = 0; i < data.messages.length; i++)
                                toastr["error"](data.messages[i])
                        $('.btn').removeAttr('disabled');
                        button.remove();
                    })
            }
        </script>
        <?php
        break;
    case "desks":
        if (!checkPrivileges(getInformation(), 'desk_president') && !checkPrivileges(getInformation(), 'desk_secretary'))
            redirect("./users");
        switch ($_GET['step']) {
            case "manage":
                ?>
                <div class="content-wrapper" onmouseover="changeTitle('Let\'s Dev ! - Gestion des bureaux')">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="box">
                                <div class="box-header">
                                    <h3>
                                        Gestion des bureaux
                                        <a href="users/desks/add" class="btn btn-ld btn-flat pull-right">Ajouter un
                                            bureau</a>
                                    </h3>
                                </div>
                                <div class="box-body">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th>Année scolaire</th>
                                            <th>Président</th>
                                            <th>Secrétaire</th>
                                            <th>Trésorier</th>
                                            <th>Responsable communication</th>
                                            <th>Responsable jurys</th>
                                            <th>Responsable challenges</th>
                                            <th></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $query = $db->prepare("SELECT * FROM desks
                                                                 ORDER BY desk_year DESC");
                                        $query->execute();
                                        while ($data = $query->fetchObject()) {
                                            echo "<tr>
                                                <td>" . $data->desk_year . " - " . ($data->desk_year + 1) . "</td>
                                                <td>" . getInformation("firstname", $data->desk_president) . " " . getInformation("lastname", $data->desk_president) . "</td>
                                                <td>" . getInformation("firstname", $data->desk_secretary) . " " . getInformation("lastname", $data->desk_secretary) . "</td>
                                                <td>" . getInformation("firstname", $data->desk_treasurer) . " " . getInformation("lastname", $data->desk_treasurer) . "</td>
                                                <td>" . getInformation("firstname", $data->desk_communication) . " " . getInformation("lastname", $data->desk_communication) . "</td>
                                                <td>" . getInformation("firstname", $data->desk_jurys) . " " . getInformation("lastname", $data->desk_jurys) . "</td>
                                                <td>" . getInformation("firstname", $data->desk_challenges) . " " . getInformation("lastname", $data->desk_challenges) . "</td>
                                                <th>";
                                            if ($data->desk_year >= getCurrentYear())
                                                echo "<a href='./users/desks/edit/" . $data->desk_year . "' class='btn btn-xs btn-flat btn-warning'>Modifier</a>";
                                            echo "</th>
                                            </tr>";
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                break;
            case "add":
                $query = $db->prepare("SELECT count(*) AS nb FROM desks WHERE desk_year = :year");
                $query->bindValue(":year", getCurrentYear() + 1, PDO::PARAM_INT);
                $query->execute();
                $data = $query->fetchObject();
                if ($data->nb > 0)
                    redirect("./users/desks/manage");
                ?>
                <div class="content-wrapper" onmouseover="changeTitle('Let\'s Dev ! - Ajouter un bureau')">
                    <div class="row">
                        <div class="col-md-4 col-md-offset-4">
                            <div class="box" style="margin-top: 50px;">
                                <div class="box-header">
                                    <h3>Ajouter le
                                        bureau <?php echo (getCurrentYear() + 1) . " - " . (getCurrentYear() + 2); ?></h3>
                                </div>
                                <div class="box-body">
                                    <form id="add_desk">
                                        <input type="hidden" name="action" value="add_desk"/>

                                        <div class="form-group">
                                            <select name="president" id="president" class="form-control">
                                                <option></option>
                                                <?php
                                                $query = $db->prepare('SELECT * FROM users ORDER BY user_lastname, user_firstname');
                                                $query->execute();
                                                while ($data = $query->fetchObject()) {
                                                    echo '<option value="' . $data->user_id . '">' . $data->user_lastname . ' ' . $data->user_firstname . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <select name="secretary" id="secretary" class="form-control">
                                                <option></option>
                                                <?php
                                                $query = $db->prepare('SELECT * FROM users ORDER BY user_lastname, user_firstname');
                                                $query->execute();
                                                while ($data = $query->fetchObject()) {
                                                    echo '<option value="' . $data->user_id . '">' . $data->user_lastname . ' ' . $data->user_firstname . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <select name="treasurer" id="treasurer" class="form-control">
                                                <option></option>
                                                <?php
                                                $query = $db->prepare('SELECT * FROM users ORDER BY user_lastname, user_firstname');
                                                $query->execute();
                                                while ($data = $query->fetchObject()) {
                                                    echo '<option value="' . $data->user_id . '">' . $data->user_lastname . ' ' . $data->user_firstname . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <select name="communication" id="communication" class="form-control">
                                                <option></option>
                                                <?php
                                                $query = $db->prepare('SELECT * FROM users ORDER BY user_lastname, user_firstname');
                                                $query->execute();
                                                while ($data = $query->fetchObject()) {
                                                    echo '<option value="' . $data->user_id . '">' . $data->user_lastname . ' ' . $data->user_firstname . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <select name="jurys" id="jurys" class="form-control">
                                                <option></option>
                                                <?php
                                                $query = $db->prepare('SELECT * FROM users ORDER BY user_lastname, user_firstname');
                                                $query->execute();
                                                while ($data = $query->fetchObject()) {
                                                    echo '<option value="' . $data->user_id . '">' . $data->user_lastname . ' ' . $data->user_firstname . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <select name="challenges" id="challenges" class="form-control">
                                                <option></option>
                                                <?php
                                                $query = $db->prepare('SELECT * FROM users ORDER BY user_lastname, user_firstname');
                                                $query->execute();
                                                while ($data = $query->fetchObject()) {
                                                    echo '<option value="' . $data->user_id . '">' . $data->user_lastname . ' ' . $data->user_firstname . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-flat btn-ld btn-block">Ajouter le bureau
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <script src="../assets/js/select2/select2.min.js"></script>
                <script>
                    $('#president').select2({
                        placeholder: "Veuillez choisir un président..."
                    });
                    $('#secretary').select2({
                        placeholder: "Veuillez choisir un secrétaire..."
                    });
                    $('#treasurer').select2({
                        placeholder: "Veuillez choisir un trésorier..."
                    });
                    $('#communication').select2({
                        placeholder: "Veuillez choisir un responsable communication..."
                    });
                    $('#jurys').select2({
                        placeholder: "Veuillez choisir un responsable jurys..."
                    });
                    $('#challenges').select2({
                        placeholder: "Veuillez choisir un responsable challenges..."
                    });
                    $("#add_desk").submit(function () {
                        $('.btn').attr('disabled', 'disabled');
                        $.ajax({
                            type: "POST",
                            url: "../includes/queries/users.php",
                            data: $("#add_desk").serialize(),
                            success: function (data) {
                                console.log(data);
                                if (data.status === "success") {
                                    window.location = "./users/desks/manage";
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
                <?php
                break;
            case "edit":
                $query0 = $db->prepare("SELECT * FROM desks WHERE desk_year = :year");
                $query0->bindValue(":year", $_GET['id'], PDO::PARAM_INT);
                $query0->execute();
                $data0 = $query0->fetchObject();
                if ($data0->desk_year < getCurrentYear())
                    redirect("./users/desks/manage");
                ?>
                <div class="content-wrapper" onmouseover="changeTitle('Let\'s Dev ! - Modifier un bureau')">
                    <div class="row">
                        <div class="col-md-4 col-md-offset-4">
                            <div class="box" style="margin-top: 50px;">
                                <div class="box-header">
                                    <h3>Modifier le
                                        bureau <?php echo $data0->desk_year . " - " . ($data0->desk_year + 1); ?></h3>
                                </div>
                                <div class="box-body">
                                    <form id="edit_desk">
                                        <input type="hidden" name="action" value="edit_desk"/>
                                        <input type="hidden" name="desk" value="<?php echo $_GET['id'] ?>"/>

                                        <div class="form-group">
                                            <select name="president" id="president" class="form-control">
                                                <option></option>
                                                <?php
                                                $query = $db->prepare('SELECT * FROM users ORDER BY user_lastname, user_firstname');
                                                $query->execute();
                                                while ($data = $query->fetchObject()) {
                                                    if ($data0->desk_president == $data->user_id)
                                                        echo '<option value="' . $data->user_id . '" selected="selected">' . $data->user_lastname . ' ' . $data->user_firstname . '</option>';
                                                    else
                                                        echo '<option value="' . $data->user_id . '">' . $data->user_lastname . ' ' . $data->user_firstname . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <select name="secretary" id="secretary" class="form-control">
                                                <option></option>
                                                <?php
                                                $query = $db->prepare('SELECT * FROM users ORDER BY user_lastname, user_firstname');
                                                $query->execute();
                                                while ($data = $query->fetchObject()) {
                                                    if ($data0->desk_secretary == $data->user_id)
                                                        echo '<option value="' . $data->user_id . '" selected="selected">' . $data->user_lastname . ' ' . $data->user_firstname . '</option>';
                                                    else
                                                        echo '<option value="' . $data->user_id . '">' . $data->user_lastname . ' ' . $data->user_firstname . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <select name="treasurer" id="treasurer" class="form-control">
                                                <option></option>
                                                <?php
                                                $query = $db->prepare('SELECT * FROM users ORDER BY user_lastname, user_firstname');
                                                $query->execute();
                                                while ($data = $query->fetchObject()) {
                                                    if ($data0->desk_treasurer == $data->user_id)
                                                        echo '<option value="' . $data->user_id . '" selected="selected">' . $data->user_lastname . ' ' . $data->user_firstname . '</option>';
                                                    else
                                                        echo '<option value="' . $data->user_id . '">' . $data->user_lastname . ' ' . $data->user_firstname . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <select name="communication" id="communication" class="form-control">
                                                <option></option>
                                                <?php
                                                $query = $db->prepare('SELECT * FROM users ORDER BY user_lastname, user_firstname');
                                                $query->execute();
                                                while ($data = $query->fetchObject()) {
                                                    if ($data0->desk_communication == $data->user_id)
                                                        echo '<option value="' . $data->user_id . '" selected="selected">' . $data->user_lastname . ' ' . $data->user_firstname . '</option>';
                                                    else
                                                        echo '<option value="' . $data->user_id . '">' . $data->user_lastname . ' ' . $data->user_firstname . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <select name="jurys" id="jurys" class="form-control">
                                                <option></option>
                                                <?php
                                                $query = $db->prepare('SELECT * FROM users ORDER BY user_lastname, user_firstname');
                                                $query->execute();
                                                while ($data = $query->fetchObject()) {
                                                    if ($data0->desk_jurys == $data->user_id)
                                                        echo '<option value="' . $data->user_id . '" selected="selected">' . $data->user_lastname . ' ' . $data->user_firstname . '</option>';
                                                    else
                                                        echo '<option value="' . $data->user_id . '">' . $data->user_lastname . ' ' . $data->user_firstname . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <select name="challenges" id="challenges" class="form-control">
                                                <option></option>
                                                <?php
                                                $query = $db->prepare('SELECT * FROM users ORDER BY user_lastname, user_firstname');
                                                $query->execute();
                                                while ($data = $query->fetchObject()) {
                                                    if ($data0->desk_challenges == $data->user_id)
                                                        echo '<option value="' . $data->user_id . '" selected="selected">' . $data->user_lastname . ' ' . $data->user_firstname . '</option>';
                                                    else
                                                        echo '<option value="' . $data->user_id . '">' . $data->user_lastname . ' ' . $data->user_firstname . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-flat btn-ld btn-block">Modifier le bureau
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <script src="../assets/js/select2/select2.min.js"></script>
                <script>
                    $('#president').select2({
                        placeholder: "Veuillez choisir un président..."
                    });
                    $('#secretary').select2({
                        placeholder: "Veuillez choisir un secrétaire..."
                    });
                    $('#treasurer').select2({
                        placeholder: "Veuillez choisir un trésorier..."
                    });
                    $('#communication').select2({
                        placeholder: "Veuillez choisir un responsable communication..."
                    });
                    $('#jurys').select2({
                        placeholder: "Veuillez choisir un responsable jurys..."
                    });
                    $('#challenges').select2({
                        placeholder: "Veuillez choisir un responsable challenges..."
                    });
                    $("#edit_desk").submit(function () {
                        $('.btn').attr('disabled', 'disabled');
                        $.ajax({
                            type: "POST",
                            url: "../includes/queries/users.php",
                            data: $("#edit_desk").serialize(),
                            success: function (data) {
                                console.log(data);
                                if (data.status === "success") {
                                    window.location = "./users/desks/manage";
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
                <?php
                break;
        }
        break;
}
include('footer.php');
?>