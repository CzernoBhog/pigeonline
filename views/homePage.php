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
        <main class="page-content"></main>

    </div>

    <div id="modal"></div>

    <!-- CONTENUTO DELLA PAGINA  -->

    <!-- JS -->

    <?php require_once('./utils/includeBody.php'); ?>
    <script src="./utils/js/menu.js"></script>

    <!-- JS -->
</body>

</html>