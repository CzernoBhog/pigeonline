//var fileSelect = document.getElementById('pictureInput');

$("#formSendMessage").on('submit', function (event) {
    if ($('#messageText').val() == '') {
        return;
    }

    event.preventDefault();

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

$(document).ready(function(){
    loadNewMessages();
});

setInterval(function () {
    loadNewMessages();
}, 2000); 

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
            if(text != ''){
                $('#messaggi').append(text);
                $("#messaggi").animate({
                    scrollTop: $('#messaggi').prop("scrollHeight")
                }, 0);
            }
        }
    });
}