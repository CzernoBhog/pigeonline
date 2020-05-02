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

    <?php
    $currentTimeBlock = $_SESSION['currentTimeBlock'];
    $inj_ID = $_SESSION['inj_ID'];
    $ip = $_SESSION['ip'];
    $TypeVuln = $_SESSION['typeVuln'];
    $timeRemaining = $currentTimeBlock === 3600 ? 3600 - $currentTimeBlock : 0 - $currentTimeBlock;
    $futureDate = date('Y-m-d H:i:s', 3600 + strtotime(date('Y-m-d H:i:s')) + $timeRemaining);
    ?>

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
            <p>You were blocked for an hour, time remaining: <b id='demo'></b></p>
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

    <script>
        var countDownDate = new Date('<?= $futureDate ?>').getTime();

        // Update the count down every 1 second
        var x = setInterval(function() {

            // Get today's date and time
            var now = new Date().getTime();

            // Find the distance between now and the count down date
            var distance = countDownDate - now;

            // Time calculations for days, hours, minutes and seconds
            var days = Math.floor(distance / (1000 * 60 * 60 * 24));
            var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((distance % (1000 * 60)) / 1000);

            // Display the result in the element with id="demo"
            document.getElementById("demo").innerHTML = minutes + "m " + seconds + "s ";

            // If the count down is finished, write some text
            if (distance < 0) {
                clearInterval(x);
                document.getElementById("demo").innerHTML = "EXPIRED, <a href='index.php'>go to initial page.</a>";
            }
        }, 1000);
    </script>

    <!-- JS -->
</body>

</html>