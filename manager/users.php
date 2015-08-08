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
                                        <td>' . $data->user_lastname . ' ' . $data->user_firstname . '</td>
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
                                        <td></td>
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
                "paging": false,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": false,
                "autoWidth": false
            });
            function deleteLanguage(id) {
                var button = $(event.target);
                $('.btn').attr('disabled', 'disabled');
                $.post('../includes/queries/languages.php', {
                        action: "delete",
                        id: id
                    },
                    function (data) {
                        var i;
                        if (data.status == "success") {
                            button.closest('tr').remove();
                            for (i = 0; i < data.messages.length; i++)
                                toastr["success"](data.messages[i])
                        }
                        else
                            for (i = 0; i < data.messages.length; i++)
                                toastr["error"](data.messages[i])
                        $('.btn').removeAttr('disabled');
                    })
            }
        </script>
        <?php
        break;
    case "desks":
        if (!checkPrivileges(getInformation(), 'desk_president') && !checkPrivileges(getInformation(), 'desk_secretary'))
            redirect("./users");
        switch ($_GET['step']) {
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
                                        bureau <?php echo (getCurrentYear() + 1) . " " . (getCurrentYear() + 2); ?></h3>
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
                <link rel="stylesheet" type="text/css" href="../assets/js/select2/select2.min.css" />
                <link rel="stylesheet" type="text/css" href="../assets/css/AdminLTE.min.css" />
                <script>
                    $('#president').select2({
                        placeholder: "Veuillez choisir un président..."
                    })
                    $("#add_language_set").submit(function () {
                        $('.btn').attr('disabled', 'disabled');
                        $.ajax({
                            type: "POST",
                            url: "../includes/queries/languages.php",
                            data: $("#add_language_set").serialize(),
                            success: function (data) {
                                console.log(data);
                                if (data.status === "success") {
                                    window.location = "./languages/sets";
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