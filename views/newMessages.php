<?php
if (!is_null($messages)) {
    foreach ($messages as $msg) {
        if (date("D M Y", strtotime("-1 day")) == date("D M Y", strtotime($msg['timeStamp']))) {
            $day = "Yesterday";
        } else if (date("D M Y", strtotime("now")) == date("D M Y", strtotime($msg['timeStamp']))) {
            $day = "Today";
        } else {
            $day = date("D M Y", strtotime($msg['timeStamp']));
        }

        if ($msg['sentBy'] == $_SESSION['id']) {
            echo '<div class="outgoing_msg" id="' . $msg["messageId"] . '">
                    <div class="sent_msg">';
        } else {
            echo '<div class="incoming_msg" id="' . $msg["messageId"] . '">
                    <div class="incoming_msg_img"> <img style="border-radius: 50%;" src="' . $msg["pathProfilePicture"] . '" alt="sunil"> </div>
                    <div class="received_msg">
                        <div class="received_withd_msg">';
        }

        echo '<p class="jumbotron">';
        if (!is_null($msg['filePath'])) {
            $fileType = pathinfo($msg['filePath'], PATHINFO_EXTENSION);
            $imageType = array('jpg', 'png', 'jpeg', 'gif');
            $audioType = array('mp3', 'WAV', 'WMA');
            $videoType = array('mp4', 'mkv');
            if (in_array($fileType, $audioType)) {
                echo '<audio controls>
                            <source class="file" src="' . $msg['filePath'] . '" type="audio/'. $fileType .'">
                        </audio>
                        <br><span>' . $msg["text"] . '</span>';
            } else if (in_array($fileType, $videoType)) {
                echo '<video width="100%" height="100%" controls>
                            <source class="file" src="' . $msg['filePath'] . '" type="video/'. $fileType .'">
                        </video>
                        <br><span>' . $msg["text"] . '</span>';
            } else if (in_array($fileType, $imageType)) {
                echo '<img class="file" style="height: 100%; width: 100%; padding: 5px 0 15px 0; max-width:400px" src="' . $msg['filePath'] . '" />
                        <br><span>' . $msg["text"] . '</span>';
            } else {
                $fileName = explode('/', $msg['filePath']);
                $fileName = end($fileName);
                echo '<input class="file" fileName="' . $fileName . '" filePath="' . $msg['filePath'] . '" title="Click to download" type="image" style="height: 15%; width: 15%;padding: 5px 0 0 0;min-width:20%" src="./utils/imgs/dwnFileIcon.png" /><span style="float: right;width: 80%;padding: inherit;">' . $fileName . '</span>';
                if ($msg["text"] !== '') {
                    echo '<br><span style="margin-top:15px;display: inline-block">' . $msg["text"] . '</span>';
                }
            }
        } else {
            echo $msg["text"];
        }

        echo '</p>';

        if ($msg['sentBy'] == $_SESSION['id']) {
            echo '<span style="float: right; text-align: right" class="time_date">' . date("H:i", strtotime($msg["timeStamp"])) . ' | ' . $day;
            echo (!is_null($msg['seen'])) ? '<i class="fa fa-check-double check-out"></i></span>' : '<i class="fa fa-check check-out"></i></span>';
            echo '</div></div>';
        } else {
            echo (!is_null($msg['seen'])) ? '<i class="fa fa-check-double check-in"></i>' : '<i class="fa fa-check check-in"></i>';
            echo '<span class="time_date">' . date("H:i", strtotime($msg["timeStamp"])) . ' | ' . $day . '</span>';
            echo '</div></div></div>';
        }
    }
}
