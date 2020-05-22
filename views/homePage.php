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
            <div class="container container-block">
                <div class="jumbotron">
                    <div class="row">
                        <div class="col-8" style="align-content: center; display: grid;">
                            <h1 class="display-4" style="margin: auto; font-size: 300%">Benvenuto <?= $user->getName() . " " . $user->getSurname(); ?>!</h1>
                        </div>
                        <div class="col-4">
                            <img class="fa-pull-right" style="width: 200px" src="./utils/imgs/piccioneScemo.png">
                        </div>
                    </div>

                    <br>

                    <p class="lead">Questa è la home page del nostro sito</p>
                    <p>Se vuoi farti un giro, sfrutta il menu a sinistra per entrare nella tua <b>Message Cloud</b> per scriverti da solo... molto triste, lo so</p>
                    <p>In alternativa puoi farti degli amici cliccando su <b>Friends</b> e cercando gli username dei tuoi amici per richiedergli l'amicizia, e una volta accettata vedere se sono online o offline e molto altro</p>
                    <p>Se hai già degli amici, allora creati, cliccando su <b>Chat</b>, una chat normale, un gruppo oppure un canale per condividere cose a persone, perchè è bello condividere cose a persone</p>
                    <p>Se vuoi, puoi sempre cambiare alcune impostazioni del profilo cliccando su <b>settings</b> in basso al menu di sinistra</p>

                    <hr class="my-4">

                    <p>Per altre informazioni puoi sempre <a href="mailto:pigeonline.project@gmail.com">contattarci</a> e sperare in una risposta, perchè essendo alle Hawaii a goderci la vita è dura trovare del tempo :)</p>
                    <p>Per il resto, buon divertimento :D</p>
                    <p>Lo staff :)</p>
                    <br><br><br>
                    <p>P.S: Non aprite due schede nello stesso browser, si bugga tutto e siamo troppo stupidi per capire il perchè ^^'</p>
                </div>
            </div>
        </main>

    </div>

    <div id="modal"></div>

    <!-- CONTENUTO DELLA PAGINA  -->

    <!-- JS -->

    <?php require_once('./utils/includeBody.php'); ?>
    <script src="./utils/js/menu.js"></script>

    <!-- JS -->
</body>

</html>