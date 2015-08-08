<?php

function slack($text, $attachments = false, $message = null, $title = null, $color = "orange", $room = "general")
{
    global $slack;
    switch ($color) {
        case 'blue':
            $color = "#3c8dbc";
            break;
        case 'green':
            $color = "#00a65a";
            break;
        case 'orange':
            $color = "#cb7730";
            break;
        case 'red':
            $color = "#f56954";
            break;

    }
    if ($attachments === true)
        $data = "payload=" . json_encode(
                array(
                    "attachments" => array(
                        array(
                            "fallback" => $message,
                            "pretext" => $message,
                            "color" => $color,
                            "fields" => array(
                                array(
                                    "title" => $title,
                                    "value" => $text,
                                    "short" => false
                                )
                            )
                        )
                    )
                )
            );
    else
        $data = "payload=" . json_encode(
                array(
                    "text" => $text
                )
            );


    $opts = array('http' =>
        array(
            'method' => 'POST',
            'header' => 'Content-type: application/x-www-form-urlencoded',
            'content' => $data
        )
    );
    $context = stream_context_create($opts);
    file_get_contents($slack[$room], false, $context);
}

?>