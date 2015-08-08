<?php
include('../includes/autoload.php');
include('header.php');
include('navbar.php');
include('sidebar.php');

switch ($_GET['action']) {
    case 'manage':
        ?>
        <div class="content-wrapper" onmouseover="changeTitle('Let\'s Dev ! - Gestion des langages')">
            <div class="row">
                <div class="col-md-12">
                    <div class="box">
                        <div class="box-header">
                            <h3>Gestion des langages
                                <a class="btn btn-ld btn-flat pull-right" href="languages/new">Ajouter un langage</a>
                            </h3>
                        </div>
                        <div class="box-body">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Langage</th>
                                    <th>Documentation</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $query = $db->prepare("SELECT * FROM languages ORDER BY language_name");
                                $query->execute();
                                while ($data = $query->fetchObject()) {
                                    echo '<tr>
                                        <td>';
                                    if (file_exists('../assets/img/private/languages/' . url_slug($data->language_name) . '.png'))
                                        echo '<img src="../assets/img/private/languages/' . url_slug($data->language_name) . '.png" height="20px"/>&nbsp;&nbsp;';
                                    else
                                        echo '<b style="margin-left: 20px;">&nbsp;&nbsp;</b>';
                                    echo $data->language_name . '</td>
                                        <td><a href="' . $data->language_documentation . '" target="_blank">' . $data->language_documentation . '</a></td>
                                        <td class="text-right">
                                            <a href="languages/edit/' . $data->language_id . '" class="btn btn-flat btn-xs btn-warning">Modifier</a>
                                            <button class="btn btn-flat btn-xs btn-danger" onclick="deleteLanguage(' . $data->language_id . ')">Supprimer</button>
                                        </td>
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
                        console.log(data);
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
    case 'sets':
        if (isset($_GET['step'])) {
            switch ($_GET['step']) {
                case 'new':
                    ?>
                    <div class="content-wrapper" onmouseover="changeTitle('Let\'s Dev ! - Ajouter un set de langages')">
                        <div class="row">
                            <div class="col-md-4 col-md-offset-4">
                                <div class="box" style="margin-top: 50px;">
                                    <div class="box-header">
                                        <h3>Ajouter un set de langages</h3>
                                    </div>
                                    <div class="box-body">
                                        <form id="add_language_set">
                                            <input type="hidden" name="action" value="sets"/>
                                            <input type="hidden" name="step" value="new"/>

                                            <div class="form-group">
                                                <input type="text" class="form-control"
                                                       placeholder="Nom du set de langages"
                                                       name="name">
                                            </div>
                                            <div class="form-group">
                                                <select name="language[]" class="form-control">
                                                    <?php
                                                    $query = $db->prepare('SELECT * FROM languages ORDER BY language_name');
                                                    $query->execute();
                                                    while ($data = $query->fetchObject()) {
                                                        echo '<option value="' . $data->language_id . '">' . $data->language_name . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="text-center">
                                                <p>
                                                    <button class="btn-link" type="button" onclick="addSelect()">Ajouter
                                                        une autre langage
                                                    </button>
                                                </p>
                                            </div>
                                            <button type="submit" class="btn btn-flat btn-ld btn-block">Ajouter le
                                                set de langages
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <script>
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
                        function addSelect() {
                            $('.form-group').last().clone().insertAfter($('.form-group').last())
                        }
                    </script>
                    <?php
                    break;
                case 'edit':
                    ?>
                    <div class="content-wrapper"
                         onmouseover="changeTitle('Let\'s Dev ! - Modifier un set de langages')">
                        <div class="row">
                            <div class="col-md-4 col-md-offset-4">
                                <div class="box" style="margin-top: 50px;">
                                    <div class="box-header">
                                        <h3>Modifier un set de langages</h3>
                                    </div>
                                    <div class="box-body">
                                        <form id="add_language_set">
                                            <input type="hidden" name="action" value="sets"/>
                                            <input type="hidden" name="step" value="edit"/>
                                            <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>"/>
                                            <?php
                                            $main = $db->prepare("SELECT * FROM language_sets WHERE set_id = :id");
                                            $main->bindValue(':id', $_GET['id'], PDO::PARAM_INT);
                                            $main->execute();
                                            if ($main->rowCount() != 1)
                                            echo '<script>$(document).ready(function () {window.location = "languages/sets"})</script>';
                                            else {
                                            $datam = $main->fetchObject();
                                            ?>
                                            <div class="form-group">
                                                <input type="text" class="form-control"
                                                       placeholder="Nom du set de langages"
                                                       name="name" value="<?php echo $datam->set_name; ?>">
                                            </div>
                                            <div id="existing_languages">
                                                <?php
                                                $query = $db->prepare("SELECT * FROM language_set_association
                                                                LEFT JOIN languages ON language_id=association_language
                                                                WHERE association_set=:set");
                                                $query->bindValue(':set', $_GET['id'], PDO::PARAM_INT);
                                                $query->execute();
                                                while ($data = $query->fetchObject()) {
                                                    echo '<div style="min-height: 30px;" id="' . $data->language_id . '">
                                                        ' . $data->language_name . '
                                                        <button type="button" class="btn-link pull-right text-danger" onclick="removeLanguage(' . $data->language_id . ')">
                                                            <i class="fa fa-times"></i>
                                                        </button>
                                                        <input type="hidden" name="language[]" value="' . $data->language_id . '" />
                                                    </div>';
                                                }
                                                ?>
                                            </div>
                                            <div class="text-center">
                                                <p>
                                                    <button class="btn-link" type="button" onclick="addSelect()">Ajouter
                                                        une autre langage
                                                    </button>
                                                </p>
                                            </div>
                                            <button type="submit" class="btn btn-flat btn-ld btn-block">Modifier le
                                                set de langages
                                            </button>
                                        </form>
                                        <div class="form-group" hidden>
                                            <select name="language[]" class="form-control">
                                                <?php
                                                $query = $db->prepare('SELECT * FROM languages ORDER BY language_name');
                                                $query->execute();
                                                while ($data = $query->fetchObject()) {
                                                    echo '<option value="' . $data->language_id . '">' . $data->language_name . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <script>
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
                        function addSelect() {
                            $('.form-group').last().clone().insertBefore($('#existing_languages').first()).removeAttr('hidden')
                        }
                        function removeLanguage(id) {
                            $('#' + id).remove();
                        }
                    </script>
                    <?php
                }
                    break;
            }
        } else {
            ?>
            <div class="content-wrapper" onmouseover="changeTitle('Let\'s Dev ! - Gestion des sets de langages')">
                <div class="row">
                    <div class="col-md-12">
                        <div class="box">
                            <div class="box-header">
                                <h3>Gestion des sets de langages
                                    <a class="btn btn-ld btn-flat pull-right" href="languages/sets/new">Ajouter un set
                                        de langages</a>
                                </h3>
                            </div>
                            <div class="box-body">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th>Nom du set</th>
                                        <th>Langages</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php

                                    $query = $db->prepare("SELECT * FROM language_sets
                                                           ORDER BY set_name");
                                    $query->execute();
                                    while ($data = $query->fetchObject()) {
                                        echo '<tr>
                                            <td>' . $data->set_name . '</td>
                                            <td>';
                                        $languages = array();
                                        $query2 = $db->prepare('SELECT * FROM language_set_association
                                                                LEFT JOIN languages ON language_id=association_language
                                                                WHERE  association_set = :set
                                                                ORDER BY language_name');
                                        $query2->bindValue(':set', $data->set_id);
                                        $query2->execute();
                                        $i = false;
                                        while ($data2 = $query2->fetchObject()) {
                                            echo '<a href="' . $data2->language_documentation . '" target="_blank">';
                                            if (file_exists('../assets/img/private/languages/' . url_slug($data2->language_name) . '.png'))
                                                echo '<img src="../assets/img/private/languages/' . url_slug($data2->language_name) . '.png" height="20px" data-toggle="tooltip" title="' . $data2->language_name . '"/>';
                                            else {
                                                echo $data2->language_name;
                                            }
                                            $i = true;
                                            echo '&nbsp;&nbsp;</a>';
                                        }
                                        echo '</td>
                                            <td class="text-right">
                                                <a href="languages/sets/edit/' . $data->set_id . '" class="btn btn-flat btn-xs btn-warning">Modifier</a>
                                                <button class="btn btn-flat btn-xs btn-danger" onclick="deleteSet(' . $data->set_id . ')">Supprimer</button>
                                            </td>
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
                    "searching": false,
                    "ordering": true,
                    "info": false,
                    "autoWidth": false
                });
                function deleteSet(id) {
                    var button = $(event.target);
                    $('.btn').attr('disabled', 'disabled');
                    $.post('../includes/queries/languages.php', {
                            action: "sets",
                            step: "delete",
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
        }
        break;
    case 'new':
        ?>
        <div class="content-wrapper" onmouseover="changeTitle('Let\'s Dev ! - Ajouter un langage')">
            <div class="row">
                <div class="col-md-4 col-md-offset-4">
                    <div class="box" style="margin-top: 50px;">
                        <div class="box-header">
                            <h3>Ajouter un langage</h3>
                        </div>
                        <div class="box-body">
                            <form id="add_language">
                                <input type="hidden" name="action" value="new"/>

                                <div class="form-group">
                                    <input type="text" class="form-control" placeholder="Nom du langage"
                                           name="name">
                                </div>
                                <div class="form-group">
                                    <input type="url" class="form-control" placeholder="Lien de la documentation"
                                           name="documentation"/>
                                </div>
                                <div class="form-group">
                                    <input type="file" name="logo" placeholder="logo"/>
                                </div>
                                <button type="submit" class="btn btn-flat btn-ld btn-block">Ajouter le langage</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            $("#add_language").submit(function () {
                $('.btn').attr('disabled', 'disabled');
                var formData = new FormData($(this)[0]);
                $.ajax({
                    type: "POST",
                    url: "../includes/queries/languages.php",
                    data: formData,
                    success: function (data) {
                        console.log(data);
                        if (data.status === "success") {
                            window.location = "./languages/manage";
                        }
                        else {
                            var i;
                            for (i = 0; i < data.messages.length; i++)
                                toastr["error"](data.messages[i])
                        }
                        $('.btn').removeAttr('disabled');
                    },
                    cache: false,
                    contentType: false,
                    processData: false
                });
                return false;
            });
        </script>
        <?php
        break;
    case 'edit':
        $query = $db->prepare('SELECT * FROM languages WHERE language_id = :id');
        $query->bindValue(':id', $_GET['id'], PDO::PARAM_INT);
        $query->execute();
        if ($data = $query->fetchObject()) {
            ?>
            <div class="content-wrapper" onmouseover="changeTitle('Let\'s Dev ! - Ajouter un langage')">
                <div class="row">
                    <div class="col-md-4 col-md-offset-4">
                        <div class="box" style="margin-top: 50px;">
                            <div class="box-header">
                                <?php

                                if (file_exists('../assets/img/private/languages/' . url_slug($data->language_name) . '.png'))
                                    echo "<img src='../assets/img/private/languages/" . url_slug($data->language_name) . ".png' class='img-responsive' style='margin:auto;margin-top:-50px;'/>";
                                else
                                    echo "<h3>Modifier un langage</h3>"
                                ?>
                            </div>
                            <div class="box-body">
                                <form id="add_language">
                                    <input type="hidden" name="action" value="edit"/>
                                    <input type="hidden" name="id" value="<?php echo $data->language_id; ?>"/>

                                    <div class="form-group">
                                        <input type="text" class="form-control" placeholder="Nom du langage"
                                               name="name" value="<?php echo $data->language_name; ?>">
                                    </div>
                                    <div class="form-group">
                                        <input type="url" class="form-control" placeholder="Lien de la documentation"
                                               name="documentation"
                                               value="<?php echo $data->language_documentation; ?>"/>
                                    </div>
                                    <div class="form-group">
                                        <input type="file" name="logo" placeholder="logo"/>
                                    </div>
                                    <button type="submit" class="btn btn-flat btn-ld btn-block">Modifier le langage
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                $("#add_language").submit(function () {
                    $('.btn').attr('disabled', 'disabled');
                    var formData = new FormData($(this)[0]);
                    $.ajax({
                        type: "POST",
                        url: "../includes/queries/languages.php",
                        data: formData,
                        success: function (data) {
                            console.log(data);
                            if (data.status === "success") {
                                window.location = "./languages/manage";
                            }
                            else {
                                var i;
                                for (i = 0; i < data.messages.length; i++)
                                    toastr["error"](data.messages[i])
                            }
                            $('.btn').removeAttr('disabled');
                        },
                        cache: false,
                        contentType: false,
                        processData: false
                    });
                    return false;
                });
            </script>
            <?php
        }
        break;
}
include('footer.php');
?>