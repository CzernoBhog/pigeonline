<ul>
    <li class="header-menu">
        <span>GENERAL</span>
    </li>
    <li id="friends">
        <a href="index.php?controller=friendsController&action=viewFriendsPage">
            <i class="fa fa-user-friends fa-pull-left"></i>
            <span class="fa-pull-left" style="margin-top: 3px">Friends</span>
            <?php
            if ($friendPendingrequests !== null) {
            ?>
                <span class="badge badge-pill badge-warning notification"><?= count($friendPendingrequests) ?></span>
            <?php
            }
            ?>
        </a>
    </li>
    <li>
        <a href="index.php?controller=chatController&action=viewCloudChat">
            <i class="fa fa-cloud fa-pull-left"></i>
            <span class="fa-pull-left" style="margin-top: 3px">Messages Cloud</span>
        </a>
    </li>
</ul>
<ul>
    <div class="sidebar-brand">
        <a href="#" class="addChat">Chat</a>
        <a style="display: contents" href="#" class="addChat">
            <i style="padding-right: 15px" class="fa fa-plus"></i>
        </a>
    </div>
    <div class="pre-scrollable">
        <?php
        if (!empty($chats)) {
            foreach ($chats as $chat) {
                if ($chat['chatType'] == '5') {
                    if (count($chats) == 1)
                        echo '<li><a style="color: #b8bfce"><i>Apri la chat... ah no, non ce ne sono :)</i></a></li>';
                } else if ($chat['chatType'] == '1' || $chat['chatType'] == '4') {
                    $src = $chat['pathProfilePicture'];
                    echo '<li>
                                <a href="index.php?controller=chatController&action=viewChatPage&chatId=' . $chat['chatId'] . '" style="padding-top: 0">
                                    <img class="chat-img fa-pull-left" src="' . $src . '" alt="Avatar">
                                    <span class="usernameChat" style="padding-left: 10px; font-size: normal; color: white">' . $chat['username'] . '</span>';
                    if ($chat['privacyLevel'] != '3') {
                        $current_timestamp = strtotime(date("Y-m-d H:i:s") . '- 10 second');
                        $current_timestamp = date('Y-m-d H:i:s', $current_timestamp);
                        echo '<span style="padding-left: 10px; font-size: smaller">';
                        echo ($chat['lastActivity'] > $current_timestamp) ? 'Online</span>' : 'Offline</span>';
                    }
                    if ($chat['chatType'] == '4')
                        echo '<i style="background: none; font-size: 10px; width: 20px; height: 20px; line-height: 20px;" class="fas fa-lock"></i>';
                    if (isset($chat['newMessages'])) {
                        echo '<span style="margin-top: 0; float: none" class="badge badge-pill badge-success">New</span>';
                    }
                    echo '</a></li>';
                } else {
                    $src = $chat['pathToChatPhoto'];
                    echo '<li>
                                <a href="index.php?controller=chatController&action=viewChatPage&chatId=' . $chat['chatId'] . '" style="padding-top: 0">
                                    <img class="chat-img fa-pull-left" src="' . $src . '" alt="Avatar">
                                    <span class="usernameChat" style="padding-left: 10px; font-size: normal; color: white">' . $chat['title'] . '</span>';
                    echo '<span style="padding-left: 10px; font-size: smaller">';
                    echo $chat['chatType'] == 2 ? 'Group' : 'Channel';
                    if (isset($chat['newMessages'])) {
                        echo '<span style="margin-top: 0; float: none" class="badge badge-pill badge-success">New</span>';
                    }
                    echo '</a></li>';
                }
            }
        } else {
            echo '<li><a style="color: #b8bfce"><i>Apri la chat... ah no, non ce ne sono :)</i></a></li>';
        }
        ?>
        <!--
              <span class="badge badge-pill badge-success">New</span>
                <li>
                    <a href="#" style="padding-top: 0">
                        <img class="chat-img fa-pull-left" src="./utils/imgs/img_avatar.png" alt="Avatar">
                        <span class="usernameChat" style="padding-left: 10px; font-size: normal; color: white">Tonetto</span>
                        <br><span style="padding-left: 10px; font-size: smaller">Offline</span>
                    </a>
                </li>
                -->
    </div>
</ul>