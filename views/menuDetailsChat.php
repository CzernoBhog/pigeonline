<nav id="sidebar" class="sidebar-wrapper" style="right: 0px;left: auto; width: 500px">
    <div class="sidebar-brand">
        <a href="#"><?= is_null($chat->getTitle()) ? $otherUser['username'] : $chat->getTitle() ?></a>
        <?php
        echo '<i style="padding-right: 15px; color: white"';
        switch ($chat->getChatType()) {
            case 1:
                echo ' class="fas fa-user-friends">';
                break;

            case 2:
            case 3:
                echo ' class="fas fa-users">';
                break;

            case 4:
                echo ' class="fas fa-user-secret">';
                break;

            case 5:
                echo ' class="fas fa-cloud">';
                break;

            default:
                echo ' class="fas fa-comments">';
                break;
        }
        echo '</i>';
        ?>
        <!-- <a style="display: contents" href="index.php">
                <i style="padding-right: 15px" class="fa fa-sign-out-alt"></i>
            </a> -->
        <div id="close-sidebar-right">
            <i class="fas fa-times"></i>
        </div>
    </div>
    <!-- sidebar-header  -->
    <div class="sidebar-content" style="scrollbar-width: none; max-height: calc(100% - 95px);">

        <div id="menu-content" class="sidebar-menu" style="padding: 0;">
            <ul>
                <li class="header-menu">
                    <span>PICTURE:</span>
                </li>
                <li style="display: table; margin: auto; padding: 10px;">
                    <img style="height: 247px; width: 247px; border-radius: 50%;" src="<?= is_null($chat->getPathToChatPhoto()) ? $otherUser['pathProfilePicture'] : $chat->getPathToChatPhoto() ?>" alt="">
                </li>
            </ul>
            <ul>
                <li class="header-menu">
                    <span><?= !is_null($chat->getDescription()) ? 'DESCRIPTION:' : 'MOOD:' ?></span>
                </li>
                <li class="header-menu">
                    <span id="descriptionInput" style="padding: 0 20px 5px 20px; width: 100%"><?= !is_null($chat->getDescription()) ? ($chat->getDescription() == '' ? 'none' : $chat->getDescription()) : ($otherUser['mood'] == '' ? 'none' : $otherUser['mood']) ?></span>
                </li>
            </ul>
            <ul>
                <?php if (isset($users) && count($users) > 2) { ?>
                    <div class="sidebar-brand">
                        <a href="#" class="addUser">USERS:</a>
                        <a style="display: contents" href="#" class="addUser">
                            <i style="padding-right: 15px" class="fa fa-plus"></i>
                        </a>
                    </div>
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

                    <div class="pre-scrollable-right">
                        <?php
                        foreach ($users as $user) {
                            $src = $user['pathProfilePicture'];
                            echo '<li style="display: flex;">
                                <a style="padding-top: 0; width: 80%;">
                                    <img class="chat-img fa-pull-left" src="' . $src . '" alt="Avatar">
                                    <span class="usernameChat" style="padding-left: 10px; font-size: normal; color: white">' . $user['username'] . '</span>';
                            if ($user['privacyLevel'] != '3') {
                                $current_timestamp = strtotime(date("Y-m-d H:i:s") . '- 10 second');
                                $current_timestamp = date('Y-m-d H:i:s', $current_timestamp);
                                echo '<br><span style="padding-left: 10px; font-size: smaller">';
                                echo ($user['lastActivity'] > $current_timestamp) ? 'Online' : 'Offline';
                                echo '</span></a>';
                                echo '<a style="width: min-content; padding: 0;" href="#"><i style="color: green" class="fas fa-user-plus"></i></a>';
                                echo '<a style="width: min-content; padding: 0;" href="#"><i style="color: #db4949" class="fas fa-user-minus"></i></a>';
                            }
                            echo '</li>';
                        }
                        ?>
                    </div>
                <?php } ?>
            </ul>
        </div>
        <!-- sidebar-menu  -->

    </div>
    <!-- sidebar-content  -->
    <div class="sidebar-footer">
        <div style="padding: 0" class="sidebar-menu">
            <ul>
                <li class="header-menu">
                    <a style="padding-bottom: 0; padding-top: 5; color: #db4949" href="#" id="usrSettings">
                        <?php
                        if ($chat->getChatType() == '5')
                            echo '<i style="margin: 0 " class="fa fa-trash-alt fa-pull-left"></i>
                                        <span style="padding: 0; margin-top: 3px; color: #db4949" class="fa-pull-left">Elimina messaggi</span>';
                        else if ($chat->getChatType() == '1' || $chat->getChatType() == '4')
                            echo '<i style="margin: 0 " class="fa fa-user-lock fa-pull-left"></i>
                                        <span style="padding: 0; margin-top: 3px; color: #db4949" class="fa-pull-left">Blocca</span>';
                        else
                            echo '<i style="margin: 0 " class="fa fa-sign-out-alt fa-pull-left"></i>
                                        <span style="padding: 0; margin-top: 3px; color: #db4949" class="fa-pull-left">Abbandona</span>';
                        ?>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>