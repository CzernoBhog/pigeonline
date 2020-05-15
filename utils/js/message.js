function messagesEvent() {
    $('input[type=image]').on('click', function () {
        fetch($(this).attr('filePath'))
            .then(resp => resp.blob())
            .then(blob => {
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.style.display = 'none';
                a.href = url;
                // the filename you want
                a.download = $(this).attr('fileName');
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
            })
            .catch(() => errorNotify('Error downloading file!'));

    });

    $('.file').attrchange('polling', {
        //pollingInterval: 0000, 
        callback: function (changes) {
            var properties = $(this).attrchange('getProperties');
            //alert('Status: ' + properties.status + '. Attribute Name: ' + event.attributeName + ' Prev Value: ' + event.oldValue + ' New Value: ' + event.newValue);      
            var properties = $('.file').attrchange('disconnect').attrchange('getProperties');
            for (var keys in changes) {
                $(this).attr(keys, changes[keys].oldValue);
            }
            setTimeout(function () {
                var properties = $('.file').attrchange('reconnect').attrchange('getProperties');
            }, 1000)
        }
    });

    $('.jumbotron').on('contextmenu', function (e) {
        let idUser = $(this).attr("id");
        $("#context-menu").children().attr("id", idUser);
        if(e.pageY + 192 > $(window).height()){
            var top = e.pageY - (e.pageY + 210 - $(window).height());
        }else{
            var top = e.pageY;
        }
        if(e.pageX + 159 > $(window).width()){
            var left = e.pageX - (e.pageX + 180 - $(window).width());
        }else{
            var left = e.pageX;
        }
        $("#context-menu").css({
            display: "block",
            top: top,
            left: left
        }).addClass("show");
        return false; //blocks default Webbrowser right click menu
    });

    //chiude il right menu quando clicco al di fuori di esso
    $(document).on("click", function () {
        $('#context-menu').removeClass("show").hide();
    });
}
messagesEvent();

$('#uploadFileInChat').on('change', function () {
    var fileName = $(this).val().split("\\").pop();
    $('#fileName').html(fileName);
    readURL(this);
    $('#fileText').val('');
    $('#modalSendFile').modal('show');
});

$("#formUploadFile").on('submit', function (event) {
    event.preventDefault();
    var formData = new FormData(this);
    var input = document.getElementById('uploadFileInChat');
    formData.append('file', input.files[0], input.files[0].name);
    $.ajax({
        url: "index.php?controller=messageController&action=sendMessage",
        type: "POST",
        data: formData,
        contentType: false,
        cache: false,
        processData: false,
        success: function (result) {
            if (result == 'blocked') {
                $.redirect('index.php');
            }
            if (result == 'success') {
                loadNewMessages();
                $('#modalSendFile').modal('hide');
            } else {
                errorNotify('Error: File not uploaded! Retry.');
            }
        },
        error: function (e) {
            errorNotify('Error: File not uploaded! Retry.');
        }
    });
});

$("#formSendMessage").on('submit', function (event) {
    event.preventDefault();

    if ($('#messageText').val() == '') {
        return;
    }

    $('#BTNSendMessage').html('<i class="fa fa-spinner" aria-hidden="true"></i>');

    var formData = new FormData();
    formData.append('ajax', 'true');
    formData.append('messageText', $('#messageText').val());

    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'index.php?controller=messageController&action=sendMessage', true);

    xhr.onreadystatechange = function () {
        if (this.readyState == 2 && this.status == 403) {
            //blocked
            $.redirect('index.php');
        }
        if (this.readyState == 4 && this.status == 200) {
            if (this.responseText == 'success') {
                loadNewMessages();
                $('#messageText').val('');
            } else {
                errorNotify("Error: Message not sent! Retry.");
            }
            $('#BTNSendMessage').html('<i class="fa fa-paper-plane" aria-hidden="true"></i>');
        }
    };

    xhr.send(formData);
});

function loadNewMessages() {
    $.ajax({
        type: "POST",
        url: "index.php",
        data: {
            'controller': 'messageController',
            'action': 'caricaMessaggi',
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
                messagesEvent();
            }
        }
    });
}