<?php
include('../autoload.php');
header('Content-Type: application/json');

$return = array('status' => 'success', 'messages' => array());

switch ($_POST['action']) {
    // Marquer tous les messages comme lus
    case "mark_as_read":
        $n = new Notifications(getInformation());
        $n->mark_as_read();
        array_push($return["messages"], "Les notifications ont été marquées comme lues.");
        break;
    case "view_all":
        $n = new Notifications(getInformation());
        $all = $n->all();
        $return['display'] = '';
        foreach ($all['notifications'] as $key => $notification) {
            $return['display'] .= '<li>
                    <a href="#">
                        ' . $notification['text'] . ' <small style="color: #AAA">' . date_fr("d M Y \à H:i", false, $notification['time']) . '</small>
                    </a>
                </li>';
        }
        break;
}
echo json_encode($return);