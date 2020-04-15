<!DOCTYPE html>
<html>

<head>

    <title>PigeOnLine Sign in</title>
    <?php require_once('./utils/includeHead.php'); ?>
    <link href="./utils/css/cover.css" rel="stylesheet">
    <link href="./utils/css/singIn.css" rel="stylesheet">

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

    <!-- CONTENUTO DELLA PAGINA ... -->

    <div class="cover-container d-flex w-100 h-100 p-3 mx-auto flex-column">
        <header class="masthead mb-auto">
            <div class="inner">
                <h3 class="masthead-brand">PigeOnLine</h3>
                <nav class="nav nav-masthead justify-content-center">
                    <a class="nav-link active" href="#">Sign in</a>
                    <a class="nav-link" href="index.php">First page</a>
                </nav>
            </div>
        </header>

        <main role="main" class="inner cover">
            <form class="form-signin">
                <input value="<?= $waf->getCSRF() ?>" hidden>
                <img class="mb-4" src="./utils/imgs/logoProgetto.png" alt="" width="80" height="80">
                <h1 class="h3 mb-3 font-weight-normal">Please sign in</h1>
                <label for="inputEmail" class="sr-only">Username</label>
                <input type="text" id="inputEmail" class="form-control" placeholder="Username" <?= $username != null ? "value='$username'" : null ?> required autofocus>
                <label for="inputPassword" class="sr-only">Password</label>
                <input type="password" id="inputPassword" class="form-control" placeholder="Password" required>
                <div class="mastfoot mt-auto">
                    <p>New user? <a href="index.php?controller=utentiController&action=viewRegistration">create an account</a></p>
                </div>
                <button style="margin-bottom: 50px" class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
            </form>
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