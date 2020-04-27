<div class="sidebar-content" style="scrollbar-width: none;">
    <div class="sidebar-brand">
        <a href="#">PigeOnLine</a>
        <a style="display: contents" href="index.php">
            <i style="padding-right: 15px" class="fa fa-sign-out-alt"></i>
        </a>
        <div id="close-sidebar">
            <i class="fas fa-times"></i>
        </div>
    </div>
    <!-- sidebar-header  -->
    <div class="sidebar-search">
        <div>
            <div class="input-group">
                <input id="searchBar" type="text" class="form-control search-menu" placeholder="Search...">
                <div class="input-group-append">
                    <span class="input-group-text">
                        <i class="fa fa-search" aria-hidden="true"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <!-- sidebar-search  -->
    <div class="sidebar-menu" style="padding: 0;">
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
                <a href="#">
                    <i class="fa fa-cloud fa-pull-left"></i>
                    <span class="fa-pull-left" style="margin-top: 3px">Messages Cloud (D)</span>
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
                if (!is_null($chats)) {
                    foreach ($chats as $chat) {
                        echo '<li>
                                <a href="index.php?action=viewChatPage&id=' . $chat->getChatId() . '" style="padding-top: 0">
                                    <img class="chat-img fa-pull-left" src="' . $chat->getPathToChatPhoto() . '" alt="Avatar">
                                    <span class="usernameChat" style="padding-left: 10px; font-size: normal; color: white">' . $chat->getTitle() . '</span>
                                </a>
                            </li>';
                    }
                } else {
                    echo '<li><a style="color: #b8bfce"><i>Apri la chat... ah no, non ce ne sono :)</i></a></li>';
                }
                ?>
                <!--
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
    </div>
    <!-- sidebar-menu  -->
</div>
<!-- sidebar-content  -->
<div class="sidebar-footer">
    <div style="padding: 0" class="sidebar-menu">
        <ul>
            <li class="header-menu">
                <a style="padding-bottom: 0; padding-top: 5" href="#">
                    <i class="fa fa-cog fa-pull-left"></i>
                    <span style="padding: 0; margin-top: 3px" class="fa-pull-left"><?= $user->getUsername() ?> - settings</span>
                </a>
            </li>
        </ul>
    </div>
</div>



