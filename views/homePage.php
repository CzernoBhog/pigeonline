<!DOCTYPE html>
<html>

<head>
    <title>PigeOnLine</title>
    <?php require_once('./utils/includeHead.php'); ?>

    <link href="utils/css/cover.css" rel="stylesheet">

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

<body class="text-center">

    <?php
    $user = \models\DAOUser::getUser(array('userId' => $_SESSION['id']));
    ?>
    <!-- CONTENUTO DELLA PAGINA ... -->

    <div class="cover-container d-flex w-100 h-100 p-3 mx-auto flex-column">
        <header class="masthead mb-auto">
            <div class="inner">
                <h3 class="masthead-brand">PigeOnLine</h3>
                <nav class="nav nav-masthead justify-content-center">
                    <a class="nav-link active" href="#">Home page</a>
                    <a class="nav-link" href="index.php">Log out</a>
                </nav>
            </div>
        </header>

        <main role="main" class="inner cover">
            <img class="mb-4" src="./utils/imgs/logoProgetto.png" alt="" width="80" height="80">
            <h1 class="cover-heading">PiegOnLine home</h1>
            <p class="lead">Benvenuto <?= $user->getUsername() ?></p>
            <br><br>
        </main>

        <footer class="mastfoot mt-auto">
            <div class="inner">
                <p>By <a href="#">@Vego</a> and <a href="#">@Tonno</a>.</p>
            </div>
        </footer>
    </div>

    <!-- CONTENUTO DELLA PAGINA  -->

    <!-- JS -->

    <?php require_once('./utils/includeBody.php'); ?>

    <!-- JS -->
</body>

</html>