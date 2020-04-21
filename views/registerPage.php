<!DOCTYPE html>
<html>

<head>

    <title>PigeOnLine Sign up</title>
    <?php require_once('./utils/includeHead.php'); ?>
    <link href="./utils/css/singIn.css" rel="stylesheet">
    <link href="./utils/css/cover.css" rel="stylesheet">

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
                    <a class="nav-link active" href="#">Sign up</a>
                    <a class="nav-link" href="index.php">First page</a>
                </nav>
            </div>
        </header>

        <main role="main" class="inner cover">
            <form id="registration" class="form-signin" action="index.php" method="POST">
                <input name="action" value="registraUtente" hidden>

                <img class="mb-4" src="./utils/imgs/logoProgetto.png" alt="" width="80" height="80">
                <h1 class="h3 mb-3 font-weight-normal">Please sign up</h1>

                <label for="inputName" class="sr-only">Name</label>
                <input type="text" id="inputName" minlength="2" maxlength="15" class="form-control" placeholder="Name" name="name" required autofocus>

                <label for="inputSurname" class="sr-only">Surname</label>
                <input type="text" id="inputSurname" minlength="2" maxlength="15" class="form-control" placeholder="Surname" name="surname" required>

                <label for="inputEmail" class="sr-only">Email</label>
                <input type="text" id="inputEmail" class="form-control" placeholder="Email" name="email" required>

                <label for="inputUsername" class="sr-only">Username</label>
                <input type="text" id="inputUsername" minlength="5" maxlength="15" class="form-control" placeholder="Username" name="username" required>

                <div class="input-group">
                    <input placeholder="Password" id="inputPassword" minlength="6" maxlength="15" type="password" name="password" class="form-control pwd" required>
                    <span class="input-group-btn">
                        <button style="height:46px" class="btn btn-default reveal" type="button">
                            <i id="eye" class="fas fa-eye"></i>
                        </button>
                    </span>
                </div>

                <div class="mastfoot mt-auto">
                    <p>Already registered? <a href="index.php?controller=utentiController&action=viewLogin">Sign in</a></p>
                </div>
                <button class="btn btn-lg btn-primary btn-block" type="submit">Sign up</button>
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

    <script>
        $(".reveal").on('click', function() {
            var $eye = $('#eye');
            var $pwd = $(".pwd");

            if ($pwd.attr('type') === 'password') {
                $pwd.attr('type', 'text');
                $eye.attr('class', 'fas fa-eye-slash');
            } else {
                $pwd.attr('type', 'password');
                $eye.attr('class', 'fas fa-eye');
            }
        });

        $("#inputEmail").blur(function() {
            var email = $("#inputEmail").val();
            var format = /[ <>]/;
            if (format.test(email)) {
                document.getElementById('inputEmail').setCustomValidity('Carattere non valido');
                return;
            }
            $.ajax({
                type: 'post',
                url: 'index.php',
                data: {
                    'action': 'controlloEmail',
                    'email': email
                },
                success: function(response) {
                    if (response == 'true') {
                        document.getElementById('inputEmail').setCustomValidity('');
                    } else {
                        document.getElementById('inputEmail').setCustomValidity(response);
                    }
                },

                error: function(response) {
                    document.getElementById('inputEmail').setCustomValidity('Email non valida');
                }
            });
        });

        $("#inputUsername").blur(function() {
            var username = $("#inputUsername").val();
            var format = /[`!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~]/;
            if (format.test(username)) {
                document.getElementById('inputUsername').setCustomValidity('Carattere non valido');
                return;
            }
            $.ajax({
                type: 'post',
                url: 'index.php',
                data: {
                    'action': 'controlloUsername',
                    'username': username
                },
                success: function(response) {
                    if (response == 'true') {
                        document.getElementById('inputUsername').setCustomValidity('');
                    } else {
                        document.getElementById('inputUsername').setCustomValidity(response);
                    }
                },

                error: function(response) {
                    document.getElementById('inputUsername').setCustomValidity('Username non valido');
                }
            });
        });

        $("#inputName").blur(function() {
            controlInputStringFormat('inputName', $("#inputName").val());
        });

        $("#inputSurname").blur(function() {
            controlInputStringFormat('inputSurname', $("#inputSurname").val());
        });

        $("#inputPassword").blur(function() {
            controlInputPasswordFormat('inputPassword', $("#inputPassword").val());
        });

        function controlInputStringFormat(field, string) {
            var format = /[`!@#$%^&*()_+\-=\[\]{};':"\\|,<>\/?~]/;
            if (format.test(string)) {
                document.getElementById(field).setCustomValidity('Carattere non valido');
            } else {
                document.getElementById(field).setCustomValidity('');
            }
        }

        function controlInputPasswordFormat(field, string) {
            var format = /[ `*()+\-=\[\]{};':"\\|,<>\/~]/;
            if (format.test(string)) {
                document.getElementById(field).setCustomValidity('Carattere non valido');
            } else {
                document.getElementById(field).setCustomValidity('');
            }
        }
    </script>

    <!-- JS -->
</body>

</html>