<!DOCTYPE html>
<html>

<head>
    <title>PigeOnLine</title>
    <?php require_once('./utils/includeHead.php'); ?>

    <link href="utils/css/menu.css" rel="stylesheet">
    <link href="utils/css/chat.css" rel="stylesheet">

</head>

<body style="background-color: #7c7e83">
    <!-- #bfbfbf  -  #b3b3b3  -  #a6a6a6 -->

    <div class="page-wrapper chiller-theme toggled">
        <a id="show-sidebar" class="btn btn-sm btn-dark" href="#" style="z-index: 10">
            <i class="fas fa-bars"></i>
        </a>
        <?php
        require_once('./views/menu.php')
        ?>

        <main class="page-content">

            <div class="messaging" style="padding: 0;">

                <?php
                if ($chat->getChatType() == '1' || $chat->getChatType() == '4') {
                    $otherUser = ($chatMembers[0]['userId'] !== $_SESSION['id']) ? $chatMembers[0] : $chatMembers[1];
                    echo '<div class="outgoing_usr" style="height: 52px; background-color: #dddddd">
                                <div style="height: 50px; padding: 2px 2px 2px 2px;">
                                    <img style="width: 48px; height: 48px;" class="chat-img fa-pull-left" src="' . $otherUser['pathProfilePicture'] . '" alt="Avatar">
                                    <span style="padding-left: 10px; font-size: large; color: Black; height: 25px; display: inline-block;">' . $otherUser['username'] . '</span>';
                    if ($otherUser['privacyLevel'] != '3') {
                        $current_timestamp = strtotime(date("Y-m-d H:i:s") . '- 10 second');
                        $current_timestamp = date('Y-m-d H:i:s', $current_timestamp);
                        echo '<br><span style="padding-left: 10px; font-size: smaller">';
                        echo ($otherUser['lastActivity'] > $current_timestamp) ? 'Online</span>' : 'Offline</span>';
                    }
                    echo        '</div>
                                <button style="right: 15px;" class="msg_send_btn" type="button"><i class="fa fa-ellipsis-v" aria-hidden="true"></i></button>
                            </div>';
                } else {
                    $groupMembers = '';
                    for ($i = 0; $i < count($chatMembers); $i++) {
                        $groupMembers .= $chatMembers[$i]['username'];
                        if ($i < count($chatMembers) - 1)
                            $groupMembers .= ', ';
                    }
                    echo '<div class="outgoing_usr" style="height: 52px; background-color: #dddddd">
                                <div style="height: 50px; padding: 2px 2px 2px 2px;">
                                    <img style="width: 48px; height: 48px;" class="chat-img fa-pull-left" src="' . $chat->getPathToChatPhoto() . '" alt="Avatar">
                                    <span style="padding-left: 10px; font-size: large; color: Black; height: 25px; display: inline-block;">' . $chat->getTitle() . '</span>
                                    <br><span class="span-goup-members">' . $groupMembers . '</span>
                                </div>
                                <button style="right: 15px;" class="msg_send_btn" type="button"><i class="fa fa-ellipsis-v" aria-hidden="true"></i></button>
                            </div>';
                }
                ?>

                <div class="msg_history" id="messaggi">
                    <?php
                    if (!is_null($messages)) {
                        foreach ($messages as $msg) {
                            if (date("D M Y", strtotime("-1 day")) == date("D M Y", strtotime("04/30/2020"))) {
                                $day = "Yesterday";
                            } else if (date("D M Y", strtotime("now")) == date("D M Y", strtotime("04/30/2020"))) {
                                $day = "Today";
                            } else {
                                $day = date("D M Y", strtotime("04/29/2020"));
                            }

                            if ($msg['sentBy'] == $_SESSION['id']) {
                                echo '<div class="outgoing_msg">
                                            <div class="sent_msg">
                                                <p>' . $msg["text"] . '</p>
                                                <span style="float: right; text-align: right" class="time_date">' . date("H:i", strtotime($msg["timeStamp"])) . ' | ' . $day . '<i class="fa fa-check-double check-out"></i></span>
                                            </div>
                                        </div>';
                            } else {
                                echo '<div class="incoming_msg">
                                            <div class="incoming_msg_img"> <img style="border-radius: 50%;" src="' . $msg["pathProfilePicture"] . '" alt="sunil"> </div>
                                            <div class="received_msg">
                                                <div class="received_withd_msg">
                                                    <p>' . $msg["text"] . '</p>
                                                    <i class="fa fa-check-double check-in"></i>
                                                    <span class="time_date">' . date("H:i", strtotime($msg["timeStamp"])) . ' | ' . $day . '</span>
                                                </div>
                                            </div>
                                        </div>';
                            }
                        }
                    } else {
                        /*echo '<div style="width: 100%" class="received_withd_msg">
                                    <p style="margin: auto; color: white; background: #31353d none repeat scroll 0 0">Chat vuota :(</p>
                                </div>';*/
                    }

                    ?>
                       
                    <div id="newMessages"></div>
                    <!-- <div class="outgoing_msg">
                        <div class="sent_msg">
                            <p>We work directly with our designers and suppliers,
                                and sell direct to you, which means quality, exclusive
                                products, at a price anyone can afford.</p>
                            <span style="float: right; text-align: right" class="time_date"> 11:01 AM | Today <i class="fa fa-check-double check-out"></i></span>
                        </div>
                    </div>
                    <div class="incoming_msg">
                        <div class="incoming_msg_img"> <img src="https://ptetutorials.com/images/user-profile.png" alt="sunil"> </div>
                        <div class="received_msg">
                            <div class="received_withd_msg">
                                <p>We work directly with our designers and suppliers,
                                    and sell direct to you, which means quality, exclusive
                                    products, at a price anyone can afford.</p>
                                <i class="fa fa-check-double check-in"></i>
                                <span class="time_date"> 11:01 AM | Today</span>
                            </div>
                        </div>
                    </div> -->
                </div>
                <div class="type_msg">
                    <div class="input_msg_write" style="background: #ddd;">
                        <form action="" method="POST" enctype="multipart/form-data" id="formSendMessage">
                            <input autocomplete="off" style="padding-left: 15px;" type="text" class="write_msg" name="messageText" id="messageText" placeholder="Type a message" />
                            <button class="msg_send_btn" style="right: 55px;" type="button"><i class="fa fa-paperclip" aria-hidden="true"></i></button>
                            <button id="BTNSendMessage" type="submit" class="msg_send_btn" style="right: 20px;" type="button"><i class="fa fa-paper-plane" aria-hidden="true"></i></button>
                        </form>
                    </div>
                </div>

            </div>
        </main>

    </div>

    <div id="modal"></div>

    <!-- CONTENUTO DELLA PAGINA  -->

    <!-- JS -->

    <?php require_once('./utils/includeBody.php'); ?>
    <script src="./utils/js/menu.js"></script>
    <script src="./utils/js/chat.js"></script>

    <!-- JS -->
</body>

</html>