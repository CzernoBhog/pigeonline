<div class="tab-pane fade" id="nav-all-friends" role="tabpanel" aria-labelledby="nav-all-friends-tab">
    <?php
    // View All Friends
    echo '<ul id="results" class="list-group">';
    echo "<li class='list-group-item d-flex justify-content-center align-items-center'><h4><i>Your friends</i></h4></li>";
    if (!is_null($detailsFriends)) {
        foreach ($detailsFriends as $friend) {
            echo '<li class="list-group-item d-flex justify-content-between align-items-center">
                                <b>' . $friend['username'] . '</b>
                                </li>';
        }
    } else {
        echo '<li class="list-group-item d-flex justify-content-between align-items-center"><i>Non hai ancora amici? Cercali nella barra di ricerca per iniziare a chattare!</i></li>';
    }
    echo '</ul>';
    ?>
</div>
<div class="tab-pane fade" id="nav-online" role="tabpanel" aria-labelledby="nav-online-tab">
    <?php
    // View Online Friends
    echo '<ul id="results" class="list-group">';
    echo "<li class='list-group-item d-flex justify-content-center align-items-center'><h4><i>Your friends online</i></h4></li>";
    if (!is_null($detailsFriends)) {
        $cont = 0;
        foreach ($detailsFriends as $friend) {
            if ($friend['isOnline']) {
                echo '<li class="list-group-item d-flex justify-content-between align-items-center">
                                    <b>' . $friend['username'] . '</b>
                                </li>';
                $cont++;
            }
            if ($cont == 0) {
                echo '<li class="list-group-item d-flex justify-content-between align-items-center"><i>Nessun amico online :(</i></li>';
            }
        }
    } else {
        echo '<li class="list-group-item d-flex justify-content-between align-items-center"><i>Nessun amico online :(</i></li>';
    }
    echo '</ul>';
    ?>
</div>
<div class="tab-pane fade" id="nav-offline" role="tabpanel" aria-labelledby="nav-offline-tab">
    <?php
    // View Offline Friends
    echo '<ul id="results" class="list-group">';
    echo "<li class='list-group-item d-flex justify-content-center align-items-center'><h4><i>Your friends offline</i></h4></li>";
    if (!is_null($detailsFriends)) {
        $cont = 0;
        foreach ($detailsFriends as $friend) {
            if (!$friend['isOnline']) {
                echo '<li class="list-group-item d-flex justify-content-between align-items-center">
                                    <b>' . $friend['username'] . '</b>
                                </li>';
                $cont++;
            }
        }
        if ($cont == 0) {
            echo '<li class="list-group-item d-flex justify-content-between align-items-center"><i>Nessun amico offline :)</i></li>';
        }
    } else {
        echo '<li class="list-group-item d-flex justify-content-between align-items-center"><i>Nessun amico offline :)</i></li>';
    }
    echo '</ul>';
    ?>
</div>
<div class="tab-pane fade" id="nav-friend-requests" role="tabpanel" aria-labelledby="nav-friend-requests-tab">
    <?php
    // View User's sent friend requests
    echo '<ul id="results" class="list-group">';
    echo "<li class='list-group-item d-flex justify-content-center align-items-center'><h4><i>Your sent requests</i></h4></li>";
    if (!is_null($userPendingRequests)) {
        foreach ($userPendingRequests as $request) {
            echo '<li class="list-group-item d-flex justify-content-between align-items-center">
                                <b>' . $request['username'] . '</b>
                                <div>
                                    <a style="text-decoration: none;" class="cancelRequest" id=' . $request['userId'] . ' href="#">
                                        <span style="font-family: none; font-size: unset" class="badge badge-warning badge-pill">Cancel Request</span>
                                    </a>
                                </div>
                                </li>';
        }
    } else {
        echo '<li class="list-group-item d-flex justify-content-between align-items-center"><i>Non hai inviato nessuna richiesta :(</i></li>';
    }
    echo '</ul>';
    echo "<br><br>";

    // View User's received friend requests
    echo '<ul id="results" class="list-group">';
    echo "<li class='list-group-item d-flex justify-content-center align-items-center'><h4><i>Your recived requests</i></h4></li>";
    if (!is_null($friendPendingrequests)) {
        foreach ($friendPendingrequests as $request) {
            echo '<li class="list-group-item d-flex justify-content-between align-items-center">
                                <b>' . $request['username'] . '</b>
                                <div id="buttonForAcceptDecline">
                                    <a style="text-decoration: none;" class="acceptRequest" id=' . $request['userId'] . ' href="#">
                                        <span style="font-family: none; font-size: unset" class="badge badge-success badge-pill">Accept</span>
                                    </a>
                                    <a style="text-decoration: none;" class="declineRequest" id=' . $request['userId'] . ' href="#">
                                        <span style="font-family: none; font-size: unset" class="badge badge-danger badge-pill">Decline</span>
                                    </a>
                                </div>
                                </li>';
        }
    } else {
        echo '<li class="list-group-item d-flex justify-content-between align-items-center"><i>Nessuna nuova richiesta d\'amicizia trovata :(</i></li>';
    }
    echo '</ul>';
    ?>
    </ul>
</div>
<div class="tab-pane fade" id="nav-blocked" role="tabpanel" aria-labelledby="nav-blocked-tab">
    <?php
    // View Blocked Users
    echo "CACCA!";
    ?>
</div>