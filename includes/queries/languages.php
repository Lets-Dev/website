<?php
include('../autoload.php');
header('Content-Type: application/json');

$return = array('status' => 'success', 'messages' => array());

switch ($_POST['action']) {

    // Ajouter un langage
    case 'new':
        // On vérifie que le membre est bien un membre du bureau
        if (!checkPrivileges(getInformation())) {
            $return['status'] = 'error';
            array_push($return['messages'], 'Vous devez être membre du bureau pour ajouter un langage.');
        }

        // On vérifie que les champs ont bien été remplis
        if (empty($_POST['name']) || empty($_POST['documentation'])) {
            $return['status'] = 'error';
            array_push($return['messages'], 'Veuillez saisir tous les champs.');
        }

        // On vérifie que le nom d'équipe n'est pas déjà pris
        if (!checkEntryAvailability($_POST['name'], 'language_name', 'languages')) {
            $return['status'] = 'error';
            array_push($return['messages'], 'Le langage existe déjà');
        }

        if ($return['status'] == 'success') {

            if (is_uploaded_file($_FILES['logo']['tmp_name'])) {
                include('../libraries/SimpleImage.php');
                $img = new abeautifulsite\SimpleImage($_FILES['logo']['tmp_name']);
                $img->best_fit(100, 100)->save('../../assets/img/private/languages/' . url_slug($_POST['name']) . '.png');
            }

            // On ajoute le langage
            $query = $db->prepare('INSERT INTO languages (language_name, language_documentation)
                              VALUES (:name, :doc)');
            $query->bindValue(':name', $_POST['name'], PDO::PARAM_STR);
            $query->bindValue(':doc', $_POST['documentation'], PDO::PARAM_STR);
            $query->execute();
            array_push($return['messages'], 'Le langage a bien été ajouté.');
            slack("<" . $_POST['documentation'] . ">", true, getInformation("firstname") . " " . getInformation("lastname") . " vient d'ajouter un langage.", $_POST['name'], "green");
        }
        break;

    // Modifier un langage
    case 'edit':
        // On vérifie que le membre est bien un membre du bureau
        if (!checkPrivileges(getInformation())) {
            $return['status'] = 'error';
            array_push($return['messages'], 'Vous devez être membre du bureau pour modifier un langage.');
        }

        // On vérifie que les champs ont bien été remplis
        if (empty($_POST['name']) || empty($_POST['documentation'])) {
            $return['status'] = 'error';
            array_push($return['messages'], 'Veuillez saisir tous les champs.');
        }

        $query = $db->prepare("SELECT * FROM languages WHERE language_id=:id");
        $query->bindValue(':id', $_POST['id'], PDO::PARAM_INT);
        $query->execute();
        $data = $query->fetchObject();
        // On vérifie que le nom d'équipe n'est pas déjà pris
        if (!checkEntryAvailability($_POST['name'], 'language_name', 'languages') && $data->language_name != $data->language_name) {
            $return['status'] = 'error';
            array_push($return['messages'], 'Le langage existe déjà');
        }
        $query->closeCursor();

        if ($return['status'] == 'success') {
            if (is_uploaded_file($_FILES['logo']['tmp_name'])) {
                if (file_exists('../../assets/img/private/languages/' . url_slug($_POST['name']) . '.png'))
                    unlink('../../assets/img/private/languages/' . url_slug($_POST['name']) . '.png');
                include('../libraries/SimpleImage.php');
                $img = new abeautifulsite\SimpleImage($_FILES['logo']['tmp_name']);
                $img->best_fit(100, 100)->save('../../assets/img/private/languages/' . url_slug($_POST['name']) . '.png');
            }

            // On ajoute le langage
            $query = $db->prepare('UPDATE languages SET language_name=:name, language_documentation=:doc WHERE language_id=:id');
            $query->bindValue(':name', $_POST['name'], PDO::PARAM_STR);
            $query->bindValue(':doc', $_POST['documentation'], PDO::PARAM_STR);
            $query->bindValue(':id', $_POST['id'], PDO::PARAM_INT);
            $query->execute();
            array_push($return['messages'], 'Le langage a bien été modifié.');
        }
        break;
    case 'delete':
        // On vérifie que le membre est bien un membre du bureau
        if (!checkPrivileges(getInformation())) {
            $return['status'] = 'error';
            array_push($return['messages'], 'Vous devez être membre du bureau pour modifier un langage.');
        } else {
            // On sélectionne les infos du langage
            $query = $db -> prepare("select * from languages WHERE language_id=:id");
            $query->bindValue(':id', $_POST['id'], PDO::PARAM_INT);
            $query->execute();
            $data = $query->fetchObject();
            $documentation = $data->language_documentation;
            $name = $data->language_name;
            $query->closeCursor();

            $query = $db -> prepare("select count(*) as nb from language_set_association where association_language=:id");
            $query->bindValue(':id', $_POST['id'], PDO::PARAM_INT);
            $query->execute();
            $data = $query->fetchObject();
            if ($data->nb > 0) {
                $return['status'] = 'error';
                array_push($return['messages'], 'Le langage est inclu dans des sets de langages, veuillez les supprimer des sets avant de supprimer le langage.');
            }
            else {
                $query = $db->prepare("DELETE FROM languages WHERE language_id=:id");
                $query->bindValue(':id', $_POST['id'], PDO::PARAM_INT);
                $query->execute();
                array_push($return['messages'], 'Le langage a bien été supprimé.');
                slack("<" . $documentation . ">", true, getInformation("firstname") . " " . getInformation("lastname") . " vient de supprimer un langage.", $name, "red");
            }
        }
        break;
    case 'sets':
        switch ($_POST['step']) {
            case 'new':
                // On vérifie que le membre est bien un membre du bureau
                if (!checkPrivileges(getInformation())) {
                    $return['status'] = 'error';
                    array_push($return['messages'], 'Vous devez être membre du bureau pour ajouter un set de langages.');
                }

                // On vérifie que les champs ont bien été remplis
                if (empty($_POST['name']) || empty($_POST['language'])) {
                    $return['status'] = 'error';
                    array_push($return['messages'], 'Veuillez saisir tous les champs.');
                }

                // On vérifie que le nom n'est pas déjà pris
                if (!checkEntryAvailability($_POST['name'], 'set_name', 'language_sets')) {
                    $return['status'] = 'error';
                    array_push($return['messages'], 'Le nom de set de langages existe déjà');
                }

                if ($return['status'] == 'success') {
                    // On ajoute le langage
                    $query = $db->prepare('INSERT INTO language_sets (set_name)
                              VALUES (:name)');
                    $query->bindValue(':name', $_POST['name'], PDO::PARAM_STR);
                    $query->execute();
                    $set = $db->lastInsertId('set_id');
                    $query->closeCursor();

                    $languages = "";
                    foreach ($_POST['language'] as $name => $id) {
                        $query = $db->prepare('INSERT INTO language_set_association (association_set, association_language)
                                          VALUES (:set, :language)');
                        $query->bindValue(':set', $set, PDO::PARAM_INT);
                        $query->bindValue(':language', $id, PDO::PARAM_INT);
                        $query->execute();
                        $query->closeCursor();
                        $query = $db->prepare("SELECT * FROM languages WHERE language_id=:id");
                        $query->bindValue(":id", $id, PDO::PARAM_INT);
                        $query->execute();
                        $data = $query->fetchObject();
                        $languages .= $data->language_name . ", ";
                        $query->closeCursor();
                    }
                    $languages = substr($languages, 0, -2) . ".";
                    array_push($return['messages'], 'Le set de langages a bien été ajouté.');
                    slack("Le set est composé de: " . $languages, true, getInformation("firstname") . " " . getInformation("lastname") . " vient de créer un set de langages.", $_POST['name'], "green");
                }
                break;
            case 'edit':
                // On vérifie que le membre est bien un membre du bureau
                if (!checkPrivileges(getInformation())) {
                    $return['status'] = 'error';
                    array_push($return['messages'], 'Vous devez être membre du bureau pour ajouter un set de langages.');
                }

                // On vérifie que les champs ont bien été remplis
                if (empty($_POST['name']) || empty($_POST['language'])) {
                    $return['status'] = 'error';
                    array_push($return['messages'], 'Veuillez saisir tous les champs.');
                }

                if ($return['status'] == 'success') {
                    // On ajoute le langage
                    $query = $db->prepare('UPDATE language_sets SET set_name = :name WHERE set_id=:id');
                    $query->bindValue(':name', $_POST['name'], PDO::PARAM_STR);
                    $query->bindValue(':id', $_POST['id'], PDO::PARAM_INT);
                    $query->execute();
                    $query->closeCursor();

                    $query = $db->prepare("DELETE FROM language_set_association WHERE association_set=:set");
                    $query->bindValue(':set', $_POST['id'], PDO::PARAM_INT);
                    $query->execute();
                    $query->closeCursor();

                    foreach ($_POST['language'] as $name => $id) {
                        $query = $db->prepare('INSERT INTO language_set_association (association_set, association_language)
                                          VALUES (:set, :language)');
                        $query->bindValue(':set', $_POST['id'], PDO::PARAM_INT);
                        $query->bindValue(':language', $id, PDO::PARAM_INT);
                        $query->execute();
                        $query->closeCursor();
                    }
                    array_push($return['messages'], 'Le set de langages a bien été modifié.');
                }
                break;
            case 'delete':
                // On vérifie que le membre est bien un membre du bureau
                if (!checkPrivileges(getInformation())) {
                    $return['status'] = 'error';
                    array_push($return['messages'], 'Vous devez être membre du bureau pour modifier un langage.');
                } else {
                    // On récupère le nom du set de langages
                    $query = $db->prepare("SELECT * FROM language_sets WHERE set_id=:id");
                    $query->bindValue(':id', $_POST['id'], PDO::PARAM_INT);
                    $query->execute();
                    $data = $query->fetchObject();
                    $set = $data->set_name;
                    $query->closeCursor();

                    // On récupère les langages en faisant partie
                    $languages="";
                    $query = $db->prepare("select * from language_set_association
                                            left join languages on association_language=language_id
                                            where association_set=:id
                                            ORDER BY language_name");
                    $query->bindValue(':id', $_POST['id'], PDO::PARAM_INT);
                    $query->execute();
                    while ($data = $query->fetchObject()) {
                        $languages .= $data->language_name . ", ";
                    }

                    $query = $db->prepare("DELETE FROM language_sets WHERE set_id=:id");
                    $query->bindValue(':id', $_POST['id'], PDO::PARAM_INT);
                    $query->execute();
                    $query = $db->prepare("DELETE FROM language_set_association WHERE association_set=:id");
                    $query->bindValue(':id', $_POST['id'], PDO::PARAM_INT);
                    $query->execute();
                    array_push($return['messages'], 'Le set de langages a bien été supprimé.');
                    $languages = substr($languages, 0, -2) . ".";
                    slack("Le set était composé de: " . $languages, true, getInformation("firstname") . " " . getInformation("lastname") . " vient de supprimer un set de langages.", $set, "red");
                }
                break;
        }
        break;
}

echo json_encode(array_to_utf8($return));