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

        .my-custom-scrollbar {
            position: relative;
            height: 200px;
            overflow: auto;
        }

        .table-wrapper-scroll-y {
            display: block;
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
                    <a class="nav-link active" href="#">WAF page</a>
                    <a class="nav-link" href="index.php">Initial page</a>
                </nav>
            </div>
        </header>

        <main role="main" class="inner cover">
            <img class="mb-4" src="./utils/imgs/logoProgetto.png" alt="" width="80" height="80">
            <h1 class="cover-heading">PigeOnLine - WAF</h1>
            <h4>Web Application Firewall</h4>
            <p class="lead">If you think this block is an error please <a href="mailto:pigeonline.project@gmail.com">contact us</a> and make sure to include the block
                details (displayed in the box below), so we can assist you in troubleshooting the issue.</p>
            <p>You were stuck for an hour, time remaining: <b><?= $currentTimeBlock === 3600 ? date('H:i:s', 3600 - $currentTimeBlock) : date('H:i:s', 0 - $currentTimeBlock) ?></b></p>
            <br>
            <h2>Block details:</h2>
            <div class="table-wrapper-scroll-y my-custom-scrollbar">
                <table class="table table-striped" style="text-align: left">
                    <tr>
                        <td>Your IP:</td>
                        <td><span><?= $ip ?></span></td>
                    </tr>
                    <tr>
                        <td>URL:</td>
                        <td>
                            <span>
                                <?= $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>Your Browser: </td>
                        <td><span><?=/*  htmlspecialchars( */ $_SERVER['HTTP_USER_AGENT']/* , ENT_QUOTES, 'UTF-8') */ ?></span></td>
                    <tr>
                        <td>Block ID:</td>
                        <td>
                            <span>
                                <?= $inj_ID ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>Block reason:</td>
                        <td><span>An attempted <?= $TypeVuln ?> was detected and blocked.</span></td>
                    </tr>
                    <tr>
                        <td>Time remaining:</td><!-- non si capisce perchè da sempre 2 o 1 ore/a -->
                        <td><span><?= $currentTimeBlock === 3600 ? date('H:i:s', 3600 - $currentTimeBlock) : date('H:i:s', 0 - $currentTimeBlock) ?></span></td>
                    </tr>
                </table>
            </div>
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