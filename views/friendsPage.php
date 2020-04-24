<!DOCTYPE html>
<html>

<head>
    <title>PigeOnLine</title>
    <?php require_once('./utils/includeHead.php'); ?>

    <link href="utils/css/menu.css" rel="stylesheet">
    <link href="utils/css/text.css" rel="stylesheet">
    <link href="utils/css/friendTab.css" rel="stylesheet">

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
                    <ul id="results" class="list-group"></ul>
                </div>
            </div>

            <section id="tabs">
                <div class="container">
                    <div style="display: block" class="row">
                        <div class="col-xs-12 ">
                            <nav>
                                <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
                                    <a class="nav-item nav-link active" id="nav-all-friends-tab" data-toggle="tab" href="#nav-all-friends" role="tab" aria-controls="nav-all-friends" aria-selected="true">All Friends</a>
                                    <a class="nav-item nav-link" id="nav-online-tab" data-toggle="tab" href="#nav-online" role="tab" aria-controls="nav-online" aria-selected="false">Online</a>
                                    <a class="nav-item nav-link" id="nav-offline-tab" data-toggle="tab" href="#nav-offline" role="tab" aria-controls="nav-offline" aria-selected="false">Offline</a>
                                    <a class="nav-item nav-link" id="nav-friend-requests-tab" data-toggle="tab" href="#nav-friend-requests" role="tab" aria-controls="nav-friend-requests" aria-selected="false">Requests</a>
                                    <a class="nav-item nav-link" id="nav-blocked-tab" data-toggle="tab" href="#nav-blocked" role="tab" aria-controls="nav-blocked" aria-selected="false">Blocked</a>
                                </div>
                            </nav>
                            <div class="tab-content py-3 px-3 px-sm-0" id="nav-tabContent"></div>
                        </div>
                    </div>
                </div>
            </section>

        </main>

    </div>


    <!-- CONTENUTO DELLA PAGINA  -->

    <!-- JS -->

    <?php require_once('./utils/includeBody.php'); ?>
    <script src="./utils/js/menu.js"></script>
    <script src="./utils/js/friend.js"></script>

    <!-- JS -->
</body>

</html>