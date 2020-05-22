<nav id="sidebar" class="sidebar-wrapper" style="right: 0px;left: auto; width: 500px">
    <div class="sidebar-brand">
        <a class="chatTitle" id="<?= (($chat->getChatType() == '3' || $chat->getChatType() == '2') && $mainUser['userType'] == '3') ? 'titleInput' : 'title' ?>" href="#">
            <?= is_null($chat->getTitle()) ? $otherUser['username'] : $chat->getTitle() ?>
        </a>
        <?php
        echo '<i style="padding-right: 15px; color: white; padding-left: 10px;"';
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
    <div id="chatMenuContent" class="sidebar-content" style="scrollbar-width: none; max-height: calc(100% - 95px);">
        <div id="menu-content" class="sidebar-menu" style="padding: 0;">
            <ul>
                <li class="header-menu">
                    <span>PICTURE:</span>
                    <?php
                    if (($chat->getChatType() == '3' || $chat->getChatType() == '2') && $mainUser['userType'] == '3') {
                        echo '<form class="wrapper" action="" method="POST" enctype="multipart/form-data" id="formGroupPhoto">
                                    <div style="position: absolute; right: 0; top: 0" class="fileUpload btn btn-primary">
                                        Change <i class="fas fa-pencil-alt"></i>
                                        <input name="picture" id="changePhoto" type="file" class="upload" />
                                    </div>
                                </form>';
                    }
                    ?>
                </li>
                <li style="display: table; margin: auto; padding: 10px;">
                    <img id="chatPhoto" style="height: 247px; width: 247px; border-radius: 50%;" src="<?= is_null($chat->getPathToChatPhoto()) ? $otherUser['pathProfilePicture'] : $chat->getPathToChatPhoto() ?>" alt="">
                </li>
            </ul>
            <ul>
                <li class="header-menu">
                    <span><?= ($chat->getChatType() == '3' || $chat->getChatType() == '2') ? 'DESCRIPTION:' : 'MOOD:' ?></span>
                </li>
                <li class="header-menu">
                    <span style="white-space: break-spaces;" class="chatDescription" id="<?= (($chat->getChatType() == '3' || $chat->getChatType() == '2') && $mainUser['userType'] == '3') ? 'descriptionInput' : 'description' ?>" style="padding: 0 20px 5px 20px; width: 100%"><?= !is_null($chat->getDescription()) ? ($chat->getDescription() == '' ? 'none' : $chat->getDescription()) : ($otherUser['mood'] == '' ? 'none' : $otherUser['mood']) ?></span>
                </li>
            </ul>
            <ul>
                <?php if (($chat->getChatType() != '1' || $chat->getChatType() != '4' || $chat->getChatType() != '5') && (($chat->getChatType() == '3' && $mainUser['userType'] == '3') || $chat->getChatType() == '2')) { ?>
                    <div class="sidebar-brand">
                        <a href="#">USERS:</a>
                        <?php
                        if (($chat->getChatType() == '3' || $chat->getChatType() == '2') && $mainUser['userType'] == '3') {
                            echo '<a id="addUser" style="display: contents" title="Add user" href="#">
                                        <i style="padding-right: 15px" class="fa fa-plus"></i>
                                    </a>';
                        }
                        ?>
                    </div>
                    <div class="sidebar-search">
                        <div>
                            <div class="input-group">
                                <input id="searchMembers" type="text" class="form-control search-menu" placeholder="Search...">
                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        <i class="fa fa-search" aria-hidden="true"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- sidebar-search  -->

                    <div id="members" class="pre-scrollable-right">
                        <?php
                        foreach ($users as $user) {
                            $src = $user['pathProfilePicture'];
                            echo '<li style="display: flex;">
                                <a style="padding-top: 0; width: 80%;">
                                    <img class="chat-img fa-pull-left" src="' . $src . '" alt="Avatar">
                                    <span class="usernameMember" style="padding-left: 10px; font-size: normal; color: white">' . $user['username'] . '</span>';
                            if ($user['privacyLevel'] !== '3') {
                                $current_timestamp = strtotime(date("Y-m-d H:i:s") . '- 10 second');
                                $current_timestamp = date('Y-m-d H:i:s', $current_timestamp);
                                echo '<br><span style="padding-left: 10px; font-size: smaller">';
                                echo ($user['lastActivity'] > $current_timestamp) ? 'Online' : 'Offline';
                                echo '</span></a>';
                            }

                            if ($mainUser['userType'] === '3') {
                                if ($mainUser['userId'] !== $user['userId']) {
                                    echo '<a class="removeUser" style="width: min-content; padding: 0;" title="Remove user" userId="' . $user['userId'] . '" id="removeUser' . $user['userId'] . '" href="#"><i style="color: #db4949" class="fas fa-user-minus"></i></a>';
                                }
                                if ($user['userType'] !== '3') {
                                    echo '<a class="addRemoveAdmin" style="width: min-content; padding: 0;" title="Make Admin" userId="' . $user['userId'] . '" id="addRemoveAdmin' . $user['userId'] . '" href="#"><i class="far fa-star"></i></a>';
                                } else {
                                    echo '<a class="addRemoveAdmin" style="width: min-content; padding: 0;" title="Remove Admin" userId="' . $user['userId'] . '" id="addRemoveAdmin' . $user['userId'] . '" href="#"><i class="fas fa-star"></i></a>';
                                }
                            }

                            if (!$user['cantBeRequested']) {
                                echo '<a class="friendRequest" style="width: min-content; padding: 0;" title="Add friend" userId="' . $user['userId'] . '" id="friendRequest' . $user['userId'] . '"><i style="color: green" class="fas fa-user-plus"></i></a>';
                            } else if ($user['cantBeRequested'] === 'pending') {
                                echo '<a style="width: min-content; padding: 0;" title="Request sent"><i style="color: yellow" class="fas fa-user-clock"></i></a>';
                            }

                            echo '</li>';
                        }
                        ?>
                    </div>
                <?php } else if ($chat->getChatType() != '5' && $chat->getChatType() != '3') { ?>
                    <div class="sidebar-brand">
                        <a href="#">SHARED GROUPS:</a>
                    </div>

                    <?php
                    if(!is_null($sharedGroups)) {
                        foreach ($sharedGroups as $group) {
                            echo '<li style="display: flex;">
                                    <a style="padding-top: 0; width: 80%;">
                                        <img class="chat-img fa-pull-left" src="' . $group['pathToChatPhoto'] . '" alt="Avatar">
                                        <span class="usernameMember" style="padding-left: 10px; font-size: normal; color: white">' . $group['title'] . '</span>
                                    </a>
                                </li>';
                        }
                    } else {
                        echo '<li><a style="color: #b8bfce"><i>Non avete nessun gruppo in comune</i></a></li>';
                    }
                }
                    ?>
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
                            echo '<i style="margin: 0" class="fa fa-sign-out-alt fa-pull-left"></i>
                                        <span id="abbandona" style="padding: 0; margin-top: 3px; color: #db4949" class="fa-pull-left">Abbandona</span>';
                        ?>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>