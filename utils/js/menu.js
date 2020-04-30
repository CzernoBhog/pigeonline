$(document).ready(function () {
    //load del menu sul div content e aggiunte del jquery per il funzionamento di modifica profilo, modifica indirizzi e inserisci utente gestiti attraverso div modale
    loadMenu();

    /* setInterval(function () {
        loadMenu();
    }, 5000);  */
});

function loadMenu() {
    $("#sidebar").load("./index.php?controller=menuController&action=caricaMenu", function (responseTxt, statusTxt, xhr) {
        if (statusTxt == "success") {

            $(".sidebar-dropdown > a").click(function () {
                $(".sidebar-submenu").slideUp(200);
                if (
                    $(this)
                        .parent()
                        .hasClass("active")
                ) {
                    $(".sidebar-dropdown").removeClass("active");
                    $(this)
                        .parent()
                        .removeClass("active");
                } else {
                    $(".sidebar-dropdown").removeClass("active");
                    $(this)
                        .next(".sidebar-submenu")
                        .slideDown(200);
                    $(this)
                        .parent()
                        .addClass("active");
                }
            });

            $("#close-sidebar").click(function () {
                $(".page-wrapper").removeClass("toggled");
                $('.messaging').css('left', '0');
                $('.messaging').css('right', '0');
            });

            $("#show-sidebar").click(function () {
                $(".page-wrapper").addClass("toggled");
                if ($(window).width() > 550) {
                    $('.messaging').css('left', '260px');
                    $('.messaging').css('right', '0');
                }
            });

            var height = $(window).height();
            $('.sidebar-content').css('height', height - 45);
            $('.pre-scrollable').css('max-height', height - 341);

            $(window).on('resize', function () {
                var height = $(window).height();
                $('.sidebar-content').css('height', height - 45);
                $('.pre-scrollable').css('max-height', height - 341);
            });

            $('#searchBar').on('keyup', function () {
                var value = $(this).val().toLowerCase();
                var chats = $('.pre-scrollable li');
                for (i = 0; i < chats.length; i++) {
                    var chatName = $('.usernameChat')[i].innerText.toLowerCase();
                    if (chatName.includes(value)) {
                        $(chats[i]).show();
                    } else {
                        $(chats[i]).hide();
                    }
                }
            });

            $('.addChat').on('click', function () {
                $("#modal").load("./index.php?controller=chatController&action=mostraModaleAddChat", function (responseTxt, statusTxt, xhr) {
                    if (statusTxt == "success") {
                        $('#addChatModal').modal('show');

                        $(".custom-file-input").on("change", function () {
                            var fileName = $(this).val().split("\\").pop();
                            $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
                            readURL(this);
                        });

                        $(function () {
                            window.fs_test = $('#selectChat').fSelect();
                            window.fs_test = $('#selectChats').fSelect();
                        });

                        $('#chatTypeSelection').on('click', function () {
                            var chatType = $('input[name=chatType]:checked', this).val();

                            switch (chatType) {
                                case '1':
                                    $('#selectFriend').removeAttr('hidden');
                                    $('#selectFriends').attr('hidden', true);
                                    break;

                                case '2':
                                case '3':
                                    $('#selectFriends').removeAttr('hidden');
                                    $('#selectFriend').attr('hidden', true);
                                    break;

                                default:
                                    alert('Not a valid type of chat');
                                    break;
                            }

                        });

                        captureFormNewChat();
                    }
                    if (statusTxt == "error") {
                        alert("Errore imprevisto, riprovare")
                    }
                });
            });

            $('#usrSettings').on('click', function () {
                $("#modal").load("./index.php?action=mostraUserSettings", function (responseTxt, statusTxt, xhr) {
                    if (statusTxt == "success") {
                        $('#modalUsrSettings').modal('show');
                        $(".custom-file-input").on("change", function () {
                            var fileName = $(this).val().split("\\").pop();
                            $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
                            readURL(this);
                        });
                        captureFormUserSettings();
                        $(".reveal").on('click', function () {
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

                        $("#inputMood").blur(function() {
                            controlInputStringFormat('inputMood', $("#inputMood").val());
                        });
                
                        $("#inputPassword").blur(function() {
                            controlInputPasswordFormat('inputPassword', $("#inputPassword").val());
                        });
                    }
                    if (statusTxt == "error") {
                        alert("Errore imprevisto, riprovare")
                    }
                });
            });
        }
        if (statusTxt == "error") {
            //alert("Error: " + xhr.status + ": " + xhr.statusText);
        }
    });
}

/* setInterval(function () {
    $.ajax({
        url: 'index.php',
        type: 'POST',
        data: {
            'action': 'updateActivity'
        }
    });
}, 5000)  */

function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $('#imgPicture').attr('src', e.target.result);
            $('#imgPictureGroup').attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]);
    }
}

function resetFileInput(id, src) {
    $('#' + id).attr('src', src);
    $('#labelInput').html('Choose photo');
}

//catturo il form user settings
function captureFormUserSettings() {
    var form = document.getElementById('formProfilo');
    var fileSelect = document.getElementById('pictureInput');
    var uploadButton = document.getElementById('saveProfilo');

    form.onsubmit = function (event) {
        event.preventDefault();

        uploadButton.innerHTML = 'Uploading...';

        var files = fileSelect.files;
        var formData = new FormData();
        for (var i = 0; i < files.length; i++) {
            var file = files[i];

            if (!file.type.match('image.*')) {
                continue;
            }

            formData.append('picture', file, file.name);
        }

        formData.append('username', $('#inputUsername').val());
        formData.append('password', $('#inputPassword').val());
        formData.append('mood', $('#inputMood').val());
        formData.append('pl', $('#inputPL').val());

        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'index.php?action=aggiornaProfilo', true);

        xhr.onload = function () {
            if (xhr.status === 200) {
                uploadButton.innerHTML = 'Upload';
            } else {
                $('#errorType').text("Immagine non supportata");
                $.notify("Error: Image not supported!", {
                    animate: {
                        enter: 'animated flipInY',
                        exit: 'animated flipOutX'
                    },
                    type: 'danger',
                    z_index: 2000
                });
                setTimeout(function () {
                    $.notifyClose();
                }, 2000);
            }
        };

        xhr.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                if (this.responseText == '1') {
                    $.notify("Succes: Settings saved!", {
                        animate: {
                            enter: 'animated flipInY',
                            exit: 'animated flipOutX'
                        },
                        type: 'success',
                        z_index: 2000
                    });
                    setTimeout(function () {
                        $.notifyClose();
                    }, 2000);
                    $('#modalUsrSettings').modal('hide');
                    loadMenu();
                } else {
                    $.notify("Error: Operation failed!", {
                        animate: {
                            enter: 'animated flipInY',
                            exit: 'animated flipOutX'
                        },
                        type: 'danger',
                        z_index: 2000
                    });
                    setTimeout(function () {
                        $.notifyClose();
                    }, 2000);
                }
            }
        };

        xhr.send(formData);
    }
}

function captureFormNewChat() {
    var form = document.getElementById('formChat');
    var fileSelect = document.getElementById('pictureInput');
    var uploadButton = document.getElementById('btnAdd');

    form.onsubmit = function (event) {
        event.preventDefault();

        uploadButton.innerHTML = 'Uploading...';

        var files = fileSelect.files;
        var formData = new FormData();
        for (var i = 0; i < files.length; i++) {
            var file = files[i];

            if (!file.type.match('image.*')) {
                continue;
            }

            formData.append('photo', file, file.name);
        }

        formData.append('chatType', $('input[name=chatType]:checked').val());
        formData.append('isSecret', $("#secret").is(':checked'));
        formData.append('name', $('#inputName').val());
        formData.append('description', $('#inputDescription').val());
        if($('input[name=chatType]:checked').val() === '1'){
            formData.append('users', $('#selectChat').val());
        } else {
            for (let i = 0; i < $('#selectChats').val().length; i++) {
                formData.append('users[]', $('#selectChats').val()[i]);
            } 
            //formData.append('users[]', $('#selectChats').val());
        }
        
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'index.php?controller=chatController&action=createChat', true);

        xhr.onload = function () {
            if (xhr.status === 200) {
                uploadButton.innerHTML = 'Upload';
            } else {
                $('#errorType').text("Immagine non supportata");
                $.notify("Error: Image not supported!", {
                    animate: {
                        enter: 'animated flipInY',
                        exit: 'animated flipOutX'
                    },
                    type: 'danger',
                    z_index: 2000
                });
                setTimeout(function () {
                    $.notifyClose();
                }, 2000);
            }
        };

        xhr.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                if (this.responseText == 'true') {
                    $.notify("Succes: Chat created!", {
                        animate: {
                            enter: 'animated flipInY',
                            exit: 'animated flipOutX'
                        },
                        type: 'success',
                        z_index: 2000
                    });
                    setTimeout(function () {
                        $.notifyClose();
                    }, 2000);
                    $('#addChatModal').modal('hide');
                    loadMenu();
                } else {
                    $.notify("Error: "+ this.responseText +"!", {
                        animate: {
                            enter: 'animated flipInY',
                            exit: 'animated flipOutX'
                        },
                        type: 'danger',
                        z_index: 2000
                    });
                    setTimeout(function () {
                        $.notifyClose();
                    }, 2000);
                }
            }
        };

        xhr.send(formData);
    }
}

function controlInputStringFormat(field, string) {
    var format = /[`<>]/;
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