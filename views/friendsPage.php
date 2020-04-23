<!DOCTYPE html>
<html>

<head>
    <title>PigeOnLine</title>
    <?php require_once('./utils/includeHead.php'); ?>

    <link href="utils/css/menu.css" rel="stylesheet">
    <link href="utils/css/text.css" rel="stylesheet">


</head>

<body style="background-color: #7c7e83" class="text-center">
    <!-- #bfbfbf  -  #b3b3b3  -  #a6a6a6 -->

    <div class="page-wrapper chiller-theme toggled">
        <a id="show-sidebar" class="btn btn-sm btn-dark" href="#" style="z-index: 10">
            <i class="fas fa-bars"></i>
        </a>
        <nav id="sidebar" class="sidebar-wrapper"></nav>

        <main class="page-content">

            <div class="sidebar-search">
                <div>
                    <div class="input-group">
                        <input id="searchFriend" type="text" class="form-control search-menu" placeholder="Cerca un utente per aggiungerlo ai tuoi amici!">
                        <a href="#" id="search" class="input-group-append">
                            <span class="input-group-text">
                                <i class="fa fa-search" aria-hidden="true"></i>
                            </span>
                        </a>
                    </div>
                    <ul id="results" class="list-group">

                    </ul>
                </div>
            </div>

            <?php

            if ($detailsFriends === null) {
                echo '<div style="padding-top: 10%"><p>Non hai ancora amici?<br>Cercali dalla barra di ricerca e inizia a chattare!</p>
                <br><img style="width: 100px" src="./utils/imgs/dino.png"></div>';
            } else {
                foreach ($detailsFriends as $detailsFriend) {
                    // Stampa della lista amici dell'utente
                    echo $detailsFriend['username'];

                    // Da sistemare graficamente
                    switch ($detailsFriend['privacyLevel']) {
                        case 'Normal':
                            echo ($detailsFriend['isOnline']) ? ': Online' : ': Offline - Last Activity: ' . $detailsFriend["lastActivity"];
                            break;

                        case 'Restricted':
                            echo ($detailsFriend['isOnline']) ? ': Online' : ': Offline';
                            break;

                        case 'Hidden':
                            break;
                    }
                }
            }

            ?>

        </main>

    </div>


    <!-- CONTENUTO DELLA PAGINA  -->

    <!-- JS -->

    <?php require_once('./utils/includeBody.php'); ?>
    <script src="./utils/js/menu.js"></script>

    <script>
        // Filtro per la ricerca di utenti online, offline o tutti

        //cerca utenti
        $('#search').on('click', function() {

            let filter = $('#searchFriend').val();
            let format = /[`!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~]/;
            if (format.test(filter)) {
                document.getElementById('searchFriend').setCustomValidity('Carattere non valido');
                return;
            } else {
                document.getElementById('searchFriend').setCustomValidity('');
            }

            $.ajax({
                url: 'index.php',
                type: 'POST',
                data: {
                    'filter': filter,
                    'action': 'searchUser'
                },
                success: function(result) {
                    var obj = JSON.parse(result);
                    var stringOpt = '<div style="" class=search-scrollable>';
                    if (obj == null) {
                        stringOpt += "<li class='list-group-item d-flex justify-content-between align-items-center'><i>Nessun utente trovato :(</i></li>";
                    } else {
                        for (var i = 0; i < obj.length; i++) { //costruisco la stringa con le opzioni per la select
                            stringOpt += '<li class="list-group-item d-flex justify-content-between align-items-center"><b>' + obj[i].username + '</b><a href="#"><span style="font-family: none; font-size: unset" class="badge badge-success badge-pill">send request</span></a></li>'
                        }
                    }
                    stringOpt += '</div><a style="color: black" id="closeSearchW" href="#"><i class="fa fa-window-close fa-pull-left; width: 30px; height: 30px" style="padding-left: 3px;"></i></a>';
                    $("#results").html(stringOpt);
                    $('#closeSearchW').on('click', function() {
                        $("#results").html('');
                        $('#searchFriend').val('');
                    });
                }
            });
        });
    </script>

    <!-- JS -->
</body>

</html>