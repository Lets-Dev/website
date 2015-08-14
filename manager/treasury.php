<?php
include('../includes/autoload.php');
if (!checkPrivileges(getInformation(), 'desk_president') && !checkPrivileges(getInformation(), 'desk_treasurer'))
    header("Location: ./index");
include('header.php');
if (isset($_GET['action']) && $_GET['action'] == 'print') {
    $first = mktime(0, 0, 0, date('m', strtotime($_GET['month'] . ' ' . $_GET['year'])), 1, $_GET['year']);
    $last = mktime(0, 0, 0, date('m', strtotime($_GET['month'] . ' ' . $_GET['year'])) + 1, 1, $_GET['year']) - 1;
    ?>
    <body onload="window.print()" cz-shortcut-listen="true">
    <div class="wrapper">
      <!-- Main content -->
      <section class="invoice">
        <!-- title row -->
        <div class="row">
          <div class="col-xs-12">
                <div class="row">
                    <div class="col-xs-4">
                        <img src="../assets/img/public/banner.png" class="img-responsive">
                    </div>
                    <div class="col-xs-8">
                        <h2 class="pull-right">
                            <?php
                                echo date_fr('F Y', false, strtotime($_GET['month'] . ' ' . $_GET['year']));
                            ?>
                        </h2>
                    </div>
                </div>
          </div>
        </div>
        <div class="row">
          <div class="col-xs-12 table-responsive">
            <h3>
                Bilan des dépenses
            </h3>
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>Date</th>
                  <th>Désignation</th>
                  <th class="text-right">Montant</th>
                </tr>
              </thead>
              <tbody>
              <?php
                    $query = $db->prepare("SELECT * FROM treasury WHERE transaction_amount < 0 AND transaction_time BETWEEN :start AND :end ORDER BY transaction_time DESC");
                    $query->bindValue(":start", $first, PDO::PARAM_INT);
                    $query->bindValue(":end", $last, PDO::PARAM_INT);
                    $query->execute();
                    while ($data = $query->fetchObject()) {
                        echo "<tr>
                            <td>" . date_fr("d F Y", false, $data->transaction_time) . "</td>
                            <td>" . $data->transaction_designation . "</td>
                            <td class='text-right'>" . $data->transaction_amount . " €</td>
                        </tr>";
                    }
                    $query->closeCursor();
 ?>
              </tbody>
            </table>
            <h3>
                Bilan des recettes
            </h3>
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>Date</th>
                  <th>Désignation</th>
                  <th class="text-right">Montant</th>
                </tr>
              </thead>
              <tbody>
              <?php
                    $query = $db->prepare("SELECT * FROM treasury WHERE transaction_amount > 0 AND transaction_time BETWEEN :start AND :end ORDER BY transaction_time DESC");
                    $query->bindValue(":start", $first, PDO::PARAM_INT);
                    $query->bindValue(":end", $last, PDO::PARAM_INT);
                    $query->execute();
                    while ($data = $query->fetchObject()) {
                        echo "<tr>
                            <td>" . date_fr("d F Y", false, $data->transaction_time) . "</td>
                            <td>" . $data->transaction_designation . "</td>
                            <td class='text-right'>" . $data->transaction_amount . " €</td>
                        </tr>";
                    }
                    $query->closeCursor();
 ?>
              </tbody>
            </table>
            <?php
            $query = $db->prepare("SELECT sum(transaction_amount) AS total,
                                  sum(CASE WHEN transaction_amount < 0 THEN transaction_amount ELSE 0 END) AS depenses,
                                  sum(CASE WHEN transaction_amount >= 0 THEN transaction_amount ELSE 0 END) AS recettes
                                  FROM treasury WHERE transaction_time BETWEEN :start AND :end");
            $query->bindValue(":start", $first, PDO::PARAM_INT);
            $query->bindValue(":end", $last, PDO::PARAM_INT);
            $query->execute();
            $data = $query->fetchObject();
            $total = number_format($data->total,2, ",", " ");
            $depenses = number_format($data->depenses,2, ",", " ");
            $recettes = number_format($data->recettes,2, ",", " ");
            $query->closeCursor();
 ?>
            <h3>Bilan total</h3>
            <div class="row">
                <div class="col-xs-4">
                    <div class="box text-center" style="box-shadow: 1px 1px 1px rgba(0,0,0,0.1), -1px -1px 1px rgba(0,0,0,0.1);">
                        <div class="box-header" style="border-bottom: 1px solid rgba(0,0,0,0.05);">
                            <h4>Dépenses</h4>
                        </div>
                        <div class="box-body">
                            <?php echo $depenses; ?> €
                        </div>
                    </div>
                </div>
                <div class="col-xs-4">
                    <div class="box text-center" style="box-shadow: 1px 1px 1px rgba(0,0,0,0.1), -1px -1px 1px rgba(0,0,0,0.1);">
                        <div class="box-header" style="border-bottom: 1px solid rgba(0,0,0,0.05);">
                            <h4>Recettes</h4>
                        </div>
                        <div class="box-body">
                            <?php echo $recettes; ?> €
                        </div>
                    </div>
                </div>
                <div class="col-xs-4">
                    <div class="box text-center" style="box-shadow: 1px 1px 1px rgba(0,0,0,0.1), -1px -1px 1px rgba(0,0,0,0.1);">
                        <div class="box-header" style="border-bottom: 1px solid rgba(0,0,0,0.05);">
                            <h4>Total</h4>
                        </div>
                        <div class="box-body">
                            <?php echo $total; ?> €
                        </div>
                    </div>
                </div>
            </div>
          </div><!-- /.col -->
        </div><!-- /.row -->

      </section><!-- /.content -->
    </div><!-- ./wrapper -->

</body>
    <?php
    return;
}
include('navbar.php');
include('sidebar.php');

?>
    <div class="content-wrapper" onmouseover="changeTitle('Let\'s Dev ! - Trésorerie')">
        <?php

        if (isset($_GET['year']) && isset($_GET['month'])) {
            $first = mktime(0, 0, 0, date('m', strtotime($_GET['month'] . ' ' . $_GET['year'])), 1, $_GET['year']);
            $last = mktime(0, 0, 0, date('m', strtotime($_GET['month'] . ' ' . $_GET['year'])) + 1, 1, $_GET['year']) - 1;

            // On enregistre le bilan du mois
            $query = $db->prepare("SELECT sum(transaction_amount) AS total,
                                  sum(CASE WHEN transaction_amount < 0 THEN transaction_amount ELSE 0 END) AS depenses,
                                  sum(CASE WHEN transaction_amount >= 0 THEN transaction_amount ELSE 0 END) AS recettes
                                  FROM treasury WHERE transaction_time BETWEEN :start AND :end");
            $query->bindValue(":start", $first, PDO::PARAM_INT);
            $query->bindValue(":end", $last, PDO::PARAM_INT);
            $query->execute();
            $data = $query->fetchObject();
            $total = number_format($data->total,2, ",", " ");
            $depenses = number_format($data->depenses,2, ",", " ");
            $recettes = number_format($data->recettes,2, ",", " ");
            $query->closeCursor();

            ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="box">
                        <div class="box-header">
                            <div class="row">
                                <div class="col-md-4">
                                    <?php
                                    if ($_GET['month'] == 'january')
                                        $url = "./treasury/" . ($_GET['year'] - 1) . "/december";
                                    else
                                        $url = "./treasury/" . $_GET['year'] . "/" . strtolower(date("F", mktime(0, 0, 0, date('m', strtotime($_GET['month'] . ' ' . $_GET['year'])) - 1, 1, $_GET['year'])));
                                    ?>
                                    <p>
                                        <a href="<?php echo $url; ?>"><i class="fa fa-angle-left"></i> Mois
                                            précédent</a>
                                    </p>
                                </div>
                                <div class="col-md-4 text-center">
                                    <h3>
                                        <?php echo date_fr("F Y", false, $first); ?>
                                    </h3>
                                </div>
                                <div class="col-md-4">
                                    <?php
                                    if ($_GET['month'] == 'december')
                                        $url = "./treasury/" . ($_GET['year'] + 1) . "/january";
                                    else
                                        $url = "./treasury/" . $_GET['year'] . "/" . strtolower(date("F", mktime(0, 0, 0, date('m', strtotime($_GET['month'] . ' ' . $_GET['year'])) + 1, 1, $_GET['year'])));
                                    ?>
                                    <p class="text-right">
                                        <a href="<?php echo $url; ?>">Mois suivant <i class="fa fa-angle-right"></i></a>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-4 text-center">
                                    <h3>Dépenses</h3>

                                    <h1 class="text-red">
                                        <?php
                                        if ($depenses != null) echo $depenses;
                                        else echo "--" ?>
                                        €
                                    </h1>
                                </div>
                                <div class="col-md-4 text-center">
                                    <h3>Recettes</h3>

                                    <h1 class="text-green">
                                        <?php
                                        if ($recettes != null) echo $recettes;
                                        else echo "--" ?>
                                        €
                                    </h1>
                                </div>
                                <div class="col-md-4 text-center">
                                    <h3>Total</h3>
                                    <?php
                                    if ($total > 0)
                                        echo "<h1 class='text-green'>" . $total . " €</h1>";
                                    elseif ($total < 0)
                                        echo "<h1 class='text-red'>" . $total . " €</h1>";
                                    else
                                        echo "<h1 class='text-orange'>" . $total . " €</h1>";
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box">
                        <div class="box-header">
                            <h3>
                                Liste des déplacements d'argent
                                <a href="./treasury/<?php echo $_GET['year'].'/'.$_GET['month']; ?>/print" class="btn btn-ld btn-flat pull-right"><i class="fa fa-print"></i> Imprimer</a>
                            </h3>
                        </div>
                        <div class="box-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Désignation</th>
                                        <th class='text-right'>Montant</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $query = $db->prepare("SELECT * FROM treasury WHERE transaction_time BETWEEN :start AND :end ORDER BY transaction_time DESC");
                                    $query->bindValue(":start", $first, PDO::PARAM_INT);
                                    $query->bindValue(":end", $last, PDO::PARAM_INT);
                                    $query->execute();
                                    while ($data = $query->fetchObject()) {
                                        echo "<tr>
                                            <td>" . date_fr("d F Y", false, $data->transaction_time) . "</td>
                                            <td>" . $data->transaction_designation . "</td>
                                            <td class='text-right'>" . $data->transaction_amount . " €</td>
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

        }
        elseif (isset($_GET['year'])) {
        $first = mktime(0, 0, 0, 1, 1, $_GET['year']);
        $last = mktime(0, 0, 0, 1, 1, $_GET['year'] + 1) - 1;

        // On enregistre le bilan de l'année
        $query = $db->prepare("SELECT sum(transaction_amount) AS total,
                                  sum(CASE WHEN transaction_amount < 0 THEN transaction_amount ELSE 0 END) AS depenses,
                                  sum(CASE WHEN transaction_amount >= 0 THEN transaction_amount ELSE 0 END) AS recettes
                                  FROM treasury WHERE transaction_time BETWEEN :start AND :end");
        $query->bindValue(":start", $first, PDO::PARAM_INT);
        $query->bindValue(":end", $last, PDO::PARAM_INT);
        $query->execute();
        $data = $query->fetchObject();
        $total = number_format($data->total,2, ",", " ");
        $depenses = number_format($data->depenses,2, ",", " ");
        $recettes = number_format($data->recettes,2, ",", " ");
        $query->closeCursor();

        ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="box">
                        <div class="box-header">
                            <div class="row">
                                <div class="col-md-4">
                                    <?php
                                    $url = "./treasury/" . ($_GET['year'] - 1);
                                    ?>
                                    <p>
                                        <a href="<?php echo $url; ?>"><i class="fa fa-angle-left"></i> Année
                                            précédente</a>
                                    </p>
                                </div>
                                <div class="col-md-4 text-center">
                                    <h3>
                                        <?php echo "Année " . $_GET['year']; ?>
                                    </h3>
                                </div>
                                <div class="col-md-4">
                                    <?php
                                    $url = "./treasury/" . ($_GET['year'] + 1);
                                    ?>
                                    <p class="text-right">
                                        <a href="<?php echo $url; ?>">Année suivante <i
                                                class="fa fa-angle-right"></i></a>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-4 text-center">
                                    <h3>Dépenses</h3>

                                    <h1 class="text-red">
                                        <?php
                                        if ($depenses != null) echo $depenses;
                                        else echo "--" ?>
                                        €
                                    </h1>
                                </div>
                                <div class="col-md-4 text-center">
                                    <h3>Recettes</h3>

                                    <h1 class="text-green">
                                        <?php
                                        if ($recettes != null) echo $recettes;
                                        else echo "--" ?>
                                        €
                                    </h1>
                                </div>
                                <div class="col-md-4 text-center">
                                    <h3>Total</h3>
                                    <?php
                                    if ($total > 0)
                                        echo "<h1 class='text-green'>" . $total . " €</h1>";
                                    elseif ($total < 0)
                                        echo "<h1 class='text-red'>" . $total . " €</h1>";
                                    else
                                        echo "<h1 class='text-orange'>" . $total . " €</h1>";
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                    for ($month = 1; $month <= 12; $month++) {
                        $first = mktime(0, 0, 0, $month, 1, $_GET['year']);
                        $last = mktime(0, 0, 0, $month + 1, 1, $_GET['year']) - 1;

                        // On enregistre le bilan du mois
                        $query = $db->prepare("SELECT sum(transaction_amount) AS total,
                                  sum(CASE WHEN transaction_amount < 0 THEN transaction_amount ELSE 0 END) AS depenses,
                                  sum(CASE WHEN transaction_amount >= 0 THEN transaction_amount ELSE 0 END) AS recettes
                                  FROM treasury WHERE transaction_time BETWEEN :start AND :end");
                        $query->bindValue(":start", $first, PDO::PARAM_INT);
                        $query->bindValue(":end", $last, PDO::PARAM_INT);
                        $query->execute();
                        $data = $query->fetchObject();
                        $total = number_format($data->total,2, ",", " ");
                        $depenses = number_format($data->depenses,2, ",", " ");
                        $recettes = number_format($data->recettes,2, ",", " ");
                        $query->closeCursor();
                        if (($month - 1) % 3 == 0) {
                            echo '<div class="row">';
                        }
                        echo '<div class="col-md-4">
                                <div class="box">
                                    <div class="box-header text-center">
                                        <a href="treasury/'.$_GET['year'].'/'.url_slug(date("F", mktime(0,0,0,$month,1,$_GET['year']))).'">'.date_fr("F", false, mktime(0,0,0,$month,1,$_GET['year'])).'</a>
                                    </div>
                                    <div class="box-body">
                                        <div class="row text-center">
                                            <div class="col-md-4">
                                                <h6>Dépenses</h6>
                                                <h5 class="text-red">'.$depenses.' €</h5>
                                            </div>
                                            <div class="col-md-4">
                                                <h6>Recettes</h6>
                                                <h5 class="text-green">'.$recettes.' €</h5>
                                            </div>
                                            <div class="col-md-4">
                                                <h6>Total</h6>
                                                <h5>'.$total.' €</h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                              </div>';
                        if ($month % 3 == 0) {
                            echo '</div>';
                        }
                    }
                    ?>
                    </div>
                </div>
            </div>
        <?php
        }
        elseif (isset($_GET['month']) && $_GET['month'] == 'add') {
        ?>
            <div class="row">
                <div class="col-md-4 col-md-offset-4">
                    <div class="box" style="margin-top: 50px;">
                        <div class="box-header">
                            <h3>Ajouter un déplacement d'argent</h3>
                        </div>
                        <div class="box-body">
                            <form id="add_transaction">
                                <input type="hidden" name="action" value="add"/>

                                <div class="form-group">
                                    <label>
                                        <input type="checkbox" id="cotisation" name="cotisation" value="true"/> Ce
                                        déplacement est une cotisation
                                    </label>
                                </div>
                                <div class="form-group" id="user_field" hidden>
                                    <select name="user" id="user" class="form-control">
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
                                <div class="form-group" id="designation_field">
                                    <input type="text" class="form-control" placeholder="Désignation"
                                           name="designation">
                                </div>

                                <div class="form-group">
                                    <input type="date" class="form-control" placeholder="Date"
                                           name="time" required>
                                </div>
                                <div class="form-group">
                                    <input type="number" id="amount" step="0.01" class="form-control" placeholder="Montant"
                                           name="amount" required/>
                                </div>
                                <button type="submit" class="btn btn-flat btn-ld btn-block">Ajouter le déplacement
                                    d'argent
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <script src="../assets/js/select2/select2.min.js"></script>
            <script>
                $('#user').select2({
                    placeholder: "Veuillez choisir l'utilisateur cotisant...",
                    width: "100%"
                });
                $("#add_transaction").submit(function () {
                    $('.btn').attr('disabled', 'disabled');
                    var formData = new FormData($(this)[0]);
                    $.ajax({
                        type: "POST",
                        url: "../includes/queries/treasury.php",
                        data: formData,
                        success: function (data) {
                            console.log(data);
                            if (data.status === "success") {
                                window.location = "treasury/" + data.messages.year + "/" + data.messages.month;
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
                $('#cotisation').change(function () {
                    if ($(this).is(":checked")) {
                        $('#user_field').show();
                        $('#designation_field').hide();
                        $('#amount').val(<?php echo $config['association']['subscription_price']; ?>)
                        return;
                    }
                    $('#user_field').hide();
                    $('#designation_field').show();
                });
            </script>
            <?php
        }
        else redirect("./treasury/" . date("Y"));

        ?>
    </div>
<?php
include('footer.php');
?>