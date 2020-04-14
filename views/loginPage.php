<!DOCTYPE html>
<html>

<head>

    <title>PigeOnLine Sing in</title>
    <?php require_once('./utils/includeHead.php'); ?>
    <link href="./utils/css/singIn.css" rel="stylesheet">

</head>

<body class="text-center">

    <!-- CONTENUTO DELLA PAGINA ... -->

    <form class="form-signin">
        <img class="mb-4" src="./utils/imgs/logoProgetto.jpg" alt="" width="80" height="80">
        <h1 class="h3 mb-3 font-weight-normal">Please sign in</h1>
        <label for="inputEmail" class="sr-only">Username</label>
        <input type="email" id="inputEmail" class="form-control" placeholder="Email address" required autofocus>
        <label for="inputPassword" class="sr-only">Password</label>
        <input type="password" id="inputPassword" class="form-control" placeholder="Password" required>
        <!-- <div class="checkbox mb-3">
            <label>
                <input type="checkbox" value="remember-me"> Remember me
            </label>
        </div> -->
        <p>New user? <a href="index.php?controller=utentiController&action=viewRegistration">create an account</a></p>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
        <p class="mt-5 mb-3 text-muted">&copy; 2020-2021</p>
    </form>

    <!-- CONTENUTO DELLA PAGINA  -->

    <!-- JS -->

    <?php require_once('./utils/includeBody.php'); ?>

    <!-- JS -->
</body>

</html>