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

    <!-- CONTENUTO DELLA PAGINA ... -->

    <div class="cover-container d-flex w-100 h-100 p-3 mx-auto flex-column">
        <header class="masthead mb-auto">
            <div class="inner">
                <h3 class="masthead-brand">PigeOnLine</h3>
                <nav class="nav nav-masthead justify-content-center">
                    <a class="nav-link active" href="#">Initial page</a>
                    <a class="nav-link" id="features" href="#">Features</a>
                </nav>
            </div>
        </header>

        <main role="main" class="inner cover">
            <img class="mb-4" src="./utils/imgs/logoProgetto.png" alt="" width="80" height="80">
            <h1 class="cover-heading">Chat with PigeOnLine.</h1>
            <p class="lead">PigeOnLine is an instant messaging web application.</p>
            <br><br>
            <p class="lead">
                <a style="margin-right: 10px" href="index.php?controller=utentiController&action=viewLogin" class="btn btn-lg btn-primary">Sign in</a>
                <a href="index.php?controller=utentiController&action=viewRegistration" class="btn btn-lg btn-primary">Sign up</a>
            </p>
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
    <script>
        $("#features").on("click", function(){
            for (let i = 5; i > 1; i--) {
                alert("Eliminazione di System32 fra " + i + " secondi");
            }
            alert("...");
            alert("Scherzavamo :P\nTi sei solo beccato un trojan :D")
        });
    </script>

    <!-- JS -->
</body>

</html>