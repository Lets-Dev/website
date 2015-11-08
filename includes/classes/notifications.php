<?php

class Notifications
{
    private $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function __destruct()
    {
        return 1;
    }

    // Créer une notification
    public function create($text, $link)
    {
        global $db;
        $query = $db->prepare("INSERT INTO notifications (notification_user, notification_text, notification_link, notification_time, notification_status)
                        VALUES (:user, :text, :link, :time, 0)");
        $query->bindValue(':user', $this->user, PDO::PARAM_INT);
        $query->bindValue(':text', $text, PDO::PARAM_STR);
        $query->bindValue(':link', $link, PDO::PARAM_STR);
        $query->bindValue(':time', time(), PDO::PARAM_INT);
        $query->execute();
        $query->closeCursor();
    }

    // Nombre de notifications non-lues
    public function nb_unread()
    {
        global $db;
        $query = $db->prepare("SELECT * FROM notifications WHERE notification_user = :user AND notification_status = 0");
        $query->bindValue(':user', $this->user, PDO::PARAM_INT);
        $query->execute();
        return $query->rowCount();
    }

    // Nombre total de notifications
    public function nb_total()
    {
        global $db;
        $query = $db->prepare("SELECT * FROM notifications WHERE notification_user = :user");
        $query->bindValue(':user', $this->user, PDO::PARAM_INT);
        $query->execute();
        return $query->rowCount();
    }

    // Liste des notifications non-lues
    public function unread()
    {
        global $db;
        $return = array('count' => $this->nb_unread(),
            'notifications' => array());
        $query = $db->prepare('SELECT * FROM notifications WHERE notification_user = :user AND notification_status = 0 ORDER BY notification_time DESC');
        $query->bindValue(':user', $this->user, PDO::PARAM_INT);
        $query->execute();
        while ($data = $query->fetchObject())
            array_push($return['notifications'], array('id'=>$data->notification_id,'text' => $data->notification_text, 'link' =>$data->notification_link, 'time' => $data->notification_time));
        return $return;
    }

    // Liste des notifications non-lues
    public function all()
    {
        global $db;
        $return = array('count' => $this->nb_unread(),
            'notifications' => array());
        $query = $db->prepare('SELECT * FROM notifications WHERE notification_user = :user ORDER BY notification_time DESC');
        $query->bindValue(':user', $this->user, PDO::PARAM_INT);
        $query->execute();
        while ($data = $query->fetchObject())
            array_push($return['notifications'], array('id'=>$data->notification_id,'text' => $data->notification_text, 'link' =>$data->notification_link, 'time' => $data->notification_time));
        return $return;
    }

    // Marque les notifications comme lues
    public function mark_as_read()
    {
        global $db;
        $query = $db->prepare("UPDATE notifications SET notification_status = 1 WHERE notification_user=:user");
        $query->bindValue(':user', $this->user, PDO::PARAM_INT);
        $query->execute();
        $query->closeCursor();
    }
}