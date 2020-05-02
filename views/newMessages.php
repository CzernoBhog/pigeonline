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
            echo '<div class="outgoing_msg">
                        <div class="sent_msg">
                            <p>' . $msg["text"] . '</p>';
                        echo '<span style="float: right; text-align: right" class="time_date">' . date("H:i", strtotime($msg["timeStamp"])) . ' | ' . $day;
            echo (!is_null($msg['seen'])) ? '<i class="fa fa-check-double check-out"></i></span></div></div>' : '<i class="fa fa-check check-out"></i></span></div></div>';
        } else {
            echo '<div class="incoming_msg">
                        <div class="incoming_msg_img"> <img style="border-radius: 50%;" src="' . $msg["pathProfilePicture"] . '" alt="sunil"> </div>
                        <div class="received_msg">
                            <div class="received_withd_msg">
                                <p>' . $msg["text"] . '</p>';
                            echo (!is_null($msg['seen'])) ? '<i class="fa fa-check-double check-in"></i>' : '<i class="fa fa-check check-in"></i>';
                            echo '<span class="time_date">' . date("H:i", strtotime($msg["timeStamp"])) . ' | ' . $day . '</span>
                            </div>
                        </div>
                    </div>';
        }
    }
}
