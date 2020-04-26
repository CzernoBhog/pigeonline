<?php
function outputFriend($type = 'all', ?array $array, $message, $emptyMessage)
{
    echo '<ul id="results" class="list-group">';
    echo "<li class='list-group-item d-flex justify-content-center align-items-center'><h4><i>$message</i></h4></li>";
    if (!is_null($array)) {
        $cont = 0;
        foreach ($array as $friend) {
            $current_timestamp = strtotime(date("Y-m-d H:i:s") . '- 10 second');
            $current_timestamp = date('Y-m-d H:i:s', $current_timestamp);
            switch ($type) {

                    // All Friends
                case 'all':
                    $cont++;
                    echo '<li id=' . $friend->getUserId() . ' class="list-group-item d-flex justify-content-between align-items-center jumbotron">
                    <div>
                        <img class="chat-img" src="./utils/imgs/img_avatar.png" alt="" />
                        <b style="padding-left: 5px">' . $friend->getUsername() . '</b>
                    </div>';

                    switch ($friend->getPrivacyLevel()) {
                        case 'NORMAL':
                            echo ($friend->getLastActivity() > $current_timestamp) ? '<p style="color: green">Online</p>' : '<p style="color: red">Offline - ' . $friend->getLastActivity() . '</p>';
                            break;

                        case 'RESTRICTED':
                            echo ($friend->getLastActivity() > $current_timestamp) ? '<p style="color: green">Online</p>' : '<p style="color: red">Offline</p>';
                            break;

                        case 'HIDDEN':
                            break;
                    }

                    echo '</li>';
                    break;

                    // Online Friends
                case 'online':

                    if ($friend->getPrivacyLevel() !== 'HIDDEN' && $friend->getLastActivity() > $current_timestamp) {
                        echo '<li id=' . $friend->getUserId() . ' class="list-group-item d-flex justify-content-between align-items-center jumbotron">
                                <div>
                                    <img class="chat-img" src="./utils/imgs/img_avatar.png" alt="" />
                                    <b style="padding-left: 5px">' . $friend->getUsername() . '</b>
                                </div><p style="color: green">Online</p>
                              </li>';
                        $cont++;
                    }

                    break;

                    // Offline Friends
                case 'offline':
                    if ($friend->getPrivacyLevel() !== 'HIDDEN' && $friend->getLastActivity() < $current_timestamp) {
                        echo '<li id=' . $friend->getUserId() . ' class="list-group-item d-flex justify-content-between align-items-center jumbotron">
                                <div>
                                    <img class="chat-img" src="./utils/imgs/img_avatar.png" alt="" />
                                    <b style="padding-left: 5px">' . $friend->getUsername() . '</b>
                                </div><p style="color: red">Offline';

                        switch ($friend->getPrivacyLevel()) {
                            case 'NORMAL':
                                echo ' - ' . $friend->getLastActivity();
                                break;

                            case 'RESTRICTED':
                                break;
                        }

                        echo '</p></li>';
                        $cont++;
                    }

                    break;

                    // Sent Friend Requests
                case 'sentRequests':
                    $cont++;
                    echo '<li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <img class="chat-img" src="./utils/imgs/img_avatar.png" alt="" />
                                <b style="padding-left: 5px">' . $friend->getUsername() . '</b>
                            </div>
                            <div id="cancelRequest">
                                <a style="text-decoration: none;" class="cancelRequest" id=' . $friend->getUserId() . ' href="#">
                                    <span style="font-family: none; font-size: unset" class="badge badge-warning badge-pill">Cancel Request</span>
                                </a>
                            </div>
                        </li>';

                    break;

                    // Received Friend Requests
                case 'receivedRequests':
                    $cont++;
                    echo '<li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <img class="chat-img" src="./utils/imgs/img_avatar.png" alt="" />
                                    <b style="padding-left: 5px">' . $friend->getUsername() . '</b>
                                </div>
                                <div style="display: flex">
                                    <div id="blockUser" style="padding-right: 5px">
                                        <a style="text-decoration: none" class="blockUser" id=' . $friend->getUserId() . ' href="#">
                                            <span style="font-family: none; font-size: unset" class="badge badge-danger badge-pill">Block</span>
                                        </a>
                                    </div>
                                    <div id="acceptDeclineRequest">
                                        <a style="text-decoration: none;" class="acceptRequest" id=' . $friend->getUserId() . ' href="#">
                                            <span style="font-family: none; font-size: unset" class="badge badge-success badge-pill">Accept</span>
                                        </a>
                                        <a style="text-decoration: none;" class="declineRequest" id=' . $friend->getUserId() . ' href="#">
                                            <span style="font-family: none; font-size: unset" class="badge badge-warning badge-pill">Decline</span>
                                        </a>
                                    </div>
                                </div>
                            </li>';
                    break;

                    // Blocked Users
                case 'blocked':
                    $cont++;
                    echo '<li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <img class="chat-img" src="./utils/imgs/img_avatar.png" alt="" />
                                    <b style="padding-left: 5px">' . $friend->getUsername() . '</b>
                                </div>
                                <div id="unblockUser">
                                    <a style="text-decoration: none;" class="unblock" id=' . $friend->getUserId() . ' href="#">
                                        <span style="font-family: none; font-size: unset" class="badge badge-warning badge-pill">Forgive</span>
                                    </a>
                                </div>
                            </li>';
                    break;
            }
        }
        if ($cont == 0) {
            echo "<li class='list-group-item d-flex justify-content-between align-items-center'><i>$emptyMessage</i></li>";
        }
    } else {
        echo "<li class='list-group-item d-flex justify-content-between align-items-center'><i>$emptyMessage</i></li>";
    }
    echo '</ul>';
}
?>



<div class="tab-pane fade" id="nav-all-friends" role="tabpanel" aria-labelledby="nav-all-friends-tab">
    <?php
    // View All Friends
    outputFriend('all', $detailsFriends, 'Your friends', 'Non hai ancora amici? Cercali nella barra di ricerca per iniziare a chattare!');
    ?>
</div>
<div class="tab-pane fade" id="nav-online" role="tabpanel" aria-labelledby="nav-online-tab">
    <?php
    // View Online Friends
    outputFriend('online', $detailsFriends, 'Your online friends', 'Nessun amico online :(');
    ?>
</div>
<div class="tab-pane fade" id="nav-offline" role="tabpanel" aria-labelledby="nav-offline-tab">
    <?php
    // View Offline Friends
    outputFriend('offline', $detailsFriends, 'Your offline friends', 'Nessun amico offline :(');
    ?>
</div>
<div class="tab-pane fade" id="nav-friend-requests" role="tabpanel" aria-labelledby="nav-friend-requests-tab">
    <?php
    // View User's sent friend requests
    outputFriend('sentRequests', $userPendingRequests, 'Your sent requests', 'Nessuna richiesta inviata :(');

    echo "<br><br>";

    // View User's received friend requests
    outputFriend('receivedRequests', $friendPendingrequests, 'Your received requests', 'Nessuna richiesta ricevuta :(');
    ?>
</div>
<div class="tab-pane fade" id="nav-blocked" role="tabpanel" aria-labelledby="nav-blocked-tab">
    <?php
    // View Blocked Users
    outputFriend('blocked', $blockedUsers, 'Your blocked users', 'Nessun utente bloccato :)');
    ?>
</div>