<!DOCTYPE html>
<html>

<head>
    <title>PigeOnLine</title>
    <?php require_once('./utils/includeHead.php'); ?>

    <!-- <link href="utils/css/cover.css" rel="stylesheet"> -->
    <link href="utils/css/menu.css" rel="stylesheet">
    <link href="utils/css/provaChat.css" rel="stylesheet">

    <style>
        .bd-placeholder-img {
            font-size: 1.125rem;
            text-anchor: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        @media (min-width: 768px) {
            .bd-placeholder-img-lg {
                font-size: 3.5rem;
            }
        }
    </style>

</head>

<body>

    <div class="page-wrapper chiller-theme toggled">
        <a id="show-sidebar" class="btn btn-sm btn-dark" href="#">
            <i class="fas fa-bars"></i>
        </a>
        <nav id="sidebar" class="sidebar-wrapper"></nav>

        <main class="page-content">
            <div class="container">
                <h1 class="mb-4">Benvenuto <b><?= $user->getUsername() ?></b></h1>
            </div><br>

            <div class="container">
                <img src="./utils/imgs/img_avatar.png" alt="Avatar">
                <span><?= $user->getUsername() ?></span>
                <p>Hello. How are you today?</p>
                <span class="time-right">11:00</span>
            </div>

            <div class="container darker" style="text-align: right">
                <img src="./utils/imgs/img_avatar.png" alt="Avatar" class="right">
                <span>Demeterca</span>
                <p class="right">Fine, You?</p>
                <span class="time-left">11:01</span>
            </div>

            <div class="container">
                <img src="./utils/imgs/img_avatar.png" alt="Avatar">
                <span><?= $user->getUsername() ?></span>
                <p>Ok</p>
                <span class="time-right">11:02</span>
            </div>

            <div class="container darker" style="text-align: right">
                <img src="./utils/imgs/img_avatar.png" alt="Avatar" class="right">
                <span>Demeterca</span>
                <p>Nah</p>
                <span class="time-left">11:05</span>
            </div>
        </main>


    </div>


    <!-- CONTENUTO DELLA PAGINA  -->

    <!-- JS -->

    <?php require_once('./utils/includeBody.php'); ?>

    <script>
        $(document).ready(function() {
            //load del menu sul div content e aggiunte del jquery per il funzionamento di modifica profilo, modifica indirizzi e inserisci utente gestiti attraverso div modale
            $("#sidebar").load("./index.php?controller=menuController&action=caricaMenu", function(responseTxt, statusTxt, xhr) {
                if (statusTxt == "success") {

                    $(".sidebar-dropdown > a").click(function() {
                        $(".sidebar-submenu").slideUp(200);
                        if (
                            $(this)
                            .parent()
                            .hasClass("active")
                        ) {
                            $(".sidebar-dropdown").removeClass("active");
                            $(this)
                                .parent()
                                .removeClass("active");
                        } else {
                            $(".sidebar-dropdown").removeClass("active");
                            $(this)
                                .next(".sidebar-submenu")
                                .slideDown(200);
                            $(this)
                                .parent()
                                .addClass("active");
                        }
                    });

                    $("#close-sidebar").click(function() {
                        $(".page-wrapper").removeClass("toggled");
                    });
                    $("#show-sidebar").click(function() {
                        $(".page-wrapper").addClass("toggled");
                    });

                    var height = $(window).height();
                    $('.sidebar-content').css('height', height - 45);
                    $('.pre-scrollable').css('max-height', height - 340);
                }
                if (statusTxt == "error") {
                    alert("Error: " + xhr.status + ": " + xhr.statusText);
                    alert("Error: " + xhr.responseText);
                }
            });

        });
    </script>

    <!-- JS -->
</body>

</html>