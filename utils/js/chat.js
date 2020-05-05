//var fileSelect = document.getElementById('pictureInput');

$("#formSendMessage").on('submit', function (event) {
    event.preventDefault();

    if ($('#messageText').val() == '') {
        return;
    }

    $('#BTNSendMessage').html('<i class="fa fa-spinner" aria-hidden="true"></i>');

    var formData = new FormData();

    /* var files = fileSelect.files;
    for (var i = 0; i < files.length; i++) {
        var file = files[i];

        if (!file.type.match('image.*')) {
            continue;
        }

        formData.append('picture', file, file.name);
    } */

    formData.append('ajax', 'true');
    formData.append('messageText', $('#messageText').val());

    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'index.php?controller=messageController&action=sendMessage', true);

    /* xhr.onload = function () {
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
    }; */

    xhr.onreadystatechange = function () {
        if (this.readyState == 2 && this.status == 403) {
            //blocked
            $.redirect('index.php');
        }
        if (this.readyState == 4 && this.status == 200) {
            if (this.responseText == '1') {
                loadNewMessages();
                $('#messageText').val('');
            } else {
                $.notify("Error: Message not sent! Retry.", {
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
            $('#BTNSendMessage').html('<i class="fa fa-paper-plane" aria-hidden="true"></i>');
        }
    };

    xhr.send(formData);
});

$(document).ready(function () {
    loadNewMessages();
});

/* setInterval(function () {
    loadNewMessages();
}, 2000); */

function loadNewMessages() {
    $.ajax({
        type: "POST",
        url: "index.php",
        data: {
            'controller': 'messageController',
            'action': 'caricaMessaggi'
        },
        success: function (text) {
            /* if(text == 'blocked'){
                $.redirect('index.php');
            } */
            if (text != '') {
                $('#messaggi').append(text);
                $("#messaggi").animate({
                    scrollTop: $('#messaggi').prop("scrollHeight")
                }, 0);
            }
        }
    });
}

$('#chatDetails').on('click', function () {
    $('#rightMenu').load('index.php?controller=menuController&action=caricaMenuChat', function (responseTxt, statusTxt, xhr) {
        if (statusTxt == "success") {

            $("#close-sidebar-right").click(function () {
                $('#rightMenu').children().remove();
                $('.messaging').css('right', '0');
            });

            if ($(window).width() > 550) {
                $('.messaging').css('right', '500px');
                //$('.messaging').css('right', '0');
            }

            var height = $(window).height();
            $('.pre-scrollable-right').css('max-height', height - 195);

            $(window).on('resize', function () {
                var height = $(window).height();
                $('.pre-scrollable-right').css('max-height', height - 195);
            });

            aggiornaDescrizione();
            function aggiornaDescrizione(){
                $("#descriptionInput").on('click', function () {
                    let groupDescr = $(this).first().text();
                    let input = '<input id="descriptionInput" class="form-control search-menu" maxLength="1024" name="description" type="text" value="' + groupDescr + '">';
                    let parentElement = $(this).parent("li");
                    parentElement.children("span").remove();
                    parentElement.append(input);
                    parentElement.children("input").focus();
                    
                    $("#descriptionInput").on('blur', function () {
                        let groupDescr = $(this).first().val();

                        // TODO: ajax per aggiornare la descrizione

                        let parentElement = $(this).parent("li");
                        parentElement.children("input").remove();
                        parentElement.append("<span id='descriptionInput' style='padding: 0 20px 5px 20px'>" + groupDescr + "</span>");
                        aggiornaDescrizione();
                    });
                });
            }
        }
    });
});