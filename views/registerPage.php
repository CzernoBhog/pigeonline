<!DOCTYPE html>
<html>

<head>

    <title>PigeOnLine Sign up</title>
    <?php require_once('./utils/includeHead.php'); ?>
    <link href="./utils/css/singIn.css" rel="stylesheet">

    <style>

    </style>

</head>

<body class="text-center">

    <!-- CONTENUTO DELLA PAGINA ... -->

    <form class="form-signin" method="POST">
        <img class="mb-4" src="./utils/imgs/logoProgetto.jpg" alt="" width="80" height="80">
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
            <input placeholder="Password" id="inputPassword" minlength="6" maxlength="15" type="password" minlength="8" maxlength="50" name="password" class="form-control pwd" required>
            <span class="input-group-btn">
                <button style="height:46px" class="btn btn-default reveal" type="button">
                    <i id="eye" class="fas fa-eye"></i>
                </button>
            </span>
        </div>

        <p>Already registered? <a href="index.php?controller=utentiController&action=viewLogin">Sing in</a></p>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Sign up</button>
        <p class="mt-5 mb-3 text-muted">&copy; 2020-2021</p>
    </form>

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

    </script>

    <!-- JS -->
</body>

</html>