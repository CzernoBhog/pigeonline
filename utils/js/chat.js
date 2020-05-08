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

var timer = null, bool = false;
$('#messageText').on('keyup', function() {
    clearTimeout(timer);
    timer = setTimeout(function() {
        isTyping(false)
    }, 3000);
    if (bool) {
        return;
    } else {
        bool = true;
        isTyping(bool);
    }
});

function isTyping(typing) {
    if (!typing)
        bool = false;

    $.ajax({
        type: "POST",
        url: "index.php",
        data: {
            "controller": "chatController",
            "action": "updateIsTyping",
            "isTyping": typing
        },
        success: function (data) {
            //console.debug(data);
        }
    });
}

$(document).ready(function () {
    loadNewMessages();
});

setInterval(function () {
    loadNewMessages();
    checkWhoIsTyping();
}, 3000);

function checkWhoIsTyping() {
    $.ajax({
        type: "POST",
        url: "index.php",
        data: {
            "controller": "chatController",
            "action": "checkWhoIsTyping"
        },
        success: function (data) {
            //$('.spanTyping').val();
        }
    });
}

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

var interval, scrollPoint, preFilterText;
$('#chatDetails').on('click', function () {
    reloadChatMenu();
    /* interval = setInterval(function () {
        preFilterText = $("#searchMembers").val();
        scrollPoint = $("#chatMenuContent").scrollTop();
        reloadChatMenu();
    }, 2000); */
});

function reloadChatMenu() {
    $('#rightMenu').load('index.php?controller=menuController&action=caricaMenuChat', function (responseTxt, statusTxt, xhr) {
        if (statusTxt == "success") {

            $("#searchMembers").val(preFilterText);
            filterMember();

            $("#chatMenuContent").animate({
                scrollTop: scrollPoint
            }, 0);

            $("#close-sidebar-right").click(function () {
                $('#rightMenu').children().remove();
                $('.messaging').css('right', '0');
                clearInterval(interval);
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
                if ($('.messaging').width() < 350) {
                    $('.messaging').css('right', '0');
                }
            });

            if ($('.messaging').width() < 350) {
                $('.messaging').css('right', '0');
            }

            aggiornaDescrizione();
            function aggiornaDescrizione() {
                $("#descriptionInput").on('click', function () {
                    let groupDescr = $(this).first().text();
                    let input = '<input id="descriptionInput" class="form-control search-menu" maxLength="1024" name="description" type="text" value="' + groupDescr + '">';
                    let parentElement = $(this).parent("li");
                    parentElement.children("span").remove();
                    parentElement.append(input);
                    parentElement.children("input").focus();
                    clearInterval(interval);

                    $("#descriptionInput").on('blur', function () {
                        let groupDescr = $(this).first().val();

                        ajaxUpdateInfoChat('description', groupDescr);

                        let parentElement = $(this).parent("li");
                        parentElement.children("input").remove();
                        parentElement.append("<span id='descriptionInput' style='padding: 0 20px 5px 20px'>" + groupDescr + "</span>");
                        //riavvio il set interval una volta aggiornato
                        interval = setInterval(function () {
                            preFilterText = $("#searchMembers").val();
                            scrollPoint = $("#chatMenuContent").scrollTop();
                            reloadChatMenu();
                        }, 2000);
                        aggiornaDescrizione();
                    });
                });
            }

            aggiornaTitolo();
            function aggiornaTitolo() {
                $("#titleInput").on('click', function () {
                    let title = $(this).text();
                    let input = '<input id="titleInput" class="form-control search-menu" maxLength="30" name="title" type="text" value="' + title + '">';
                    let parentElement = $(this).parent("div.sidebar-brand");
                    parentElement.children("#titleInput").remove();
                    parentElement.prepend(input);
                    parentElement.children("input").focus();
                    clearInterval(interval);

                    $("#titleInput").on('blur', function () {
                        let title = $(this).val();

                        ajaxUpdateInfoChat('title', title);

                        let parentElement = $(this).parent("div.sidebar-brand");
                        parentElement.children("#titleInput").remove();
                        parentElement.append("<a id='titleInput' href='#'>" + title + "</a>");
                        //riavvio il set interval una volta aggiornato
                        interval = setInterval(function () {
                            preFilterText = $("#searchMembers").val();
                            scrollPoint = $("#chatMenuContent").scrollTop();
                            reloadChatMenu();
                        }, 2000);
                        aggiornaTitolo();
                    });
                });
            }

            $("#searchMembers").on('click', function () {
                clearInterval(interval);
            });

            $("#searchMembers").on('blur', function () {
                interval = setInterval(function () {
                    preFilterText = $("#searchMembers").val();
                    scrollPoint = $("#chatMenuContent").scrollTop();
                    reloadChatMenu();
                }, 2000);
            });


            $('.friendRequest').on('click', function () {
                var friendId = $(this).attr('userId');
                $('#friendRequest' + friendId).attr('title', 'Request sent')
                $('#friendRequest' + friendId).html('<i style="color: yellow" class="fas fa-user-clock"></i>');
                $.ajax({
                    url: 'index.php',
                    type: 'POST',
                    data: {
                        'friendId': friendId,
                        'action': 'friendRequest',
                        'controller': 'friendsController'
                    },
                    success: function (result) {
                        if (result == 'blocked') {
                            $.redirect('index.php');
                        }
                        if (result == 'success') {
                            $('#friendRequest' + friendId).css("pointer-events", "none"); // disabilita gli eventi del mouse
                        } else {
                            $('#friendRequest' + friendId).attr('title', 'Error, resend request')
                            $('#friendRequest' + friendId).html('<i style="color: red" class="fas fa-user-plus"></i>');
                        }
                    },
                    error: function (result) {
                        $('#friendRequest' + friendId).attr('title', 'Error, resend request')
                        $('#friendRequest' + friendId).html('<i style="color: red" class="fas fa-user-plus"></i>');
                    }
                });
            });

            $('.addRemoveAdmin').on('click', function () {
                var userId = $(this).attr('userId');
                $.ajax({
                    url: 'index.php',
                    type: 'POST',
                    data: {
                        'userId': userId,
                        'action': 'addRemoveAdmin',
                        'controller': 'chatController'
                    },
                    success: function (result) {
                        if (result == 'blocked') {
                            $.redirect('index.php');
                        }
                        if (result == 'added') {
                            $('#addRemoveAdmin' + userId).html('<i class="fas fa-star"></i>');
                            $('#addRemoveAdmin' + userId).attr('title', 'Remove Admin');

                        } else if (result == 'removed') {
                            $('#addRemoveAdmin' + userId).html('<i class="far fa-star"></i>');
                            $('#addRemoveAdmin' + userId).attr('title', 'Make Admin');
                        } else {
                            if ($('#addRemoveAdmin' + userId).attr('title') == 'Make Admin') {
                                $('#addRemoveAdmin' + userId).html('<i style="color: red" class="fas fa-star"></i>');
                                $('#addRemoveAdmin' + userId).attr('title', 'Make Admin');
                            } else {
                                $('#addRemoveAdmin' + userId).html('<i style="color: red" class="fas fa-star"></i>');
                                $('#addRemoveAdmin' + userId).attr('title', 'Remove Admin');
                            }
                        }
                    },
                    error: function (result) {
                        if ($('#addRemoveAdmin' + userId).attr('title') == 'Make Admin') {
                            $('#addRemoveAdmin' + userId).html('<i style="color: red" class="fas fa-star"></i>');
                            $('#addRemoveAdmin' + userId).attr('title', 'Make Admin');
                        } else {
                            $('#addRemoveAdmin' + userId).html('<i style="color: red" class="fas fa-star"></i>');
                            $('#addRemoveAdmin' + userId).attr('title', 'Remove Admin');
                        }
                    }
                });
            });

            $('#searchMembers').on('keyup', function () {
                filterMember();
            });

            //upload photo
            $('#changePhoto').on('change', function () {
                $("#formGroupPhoto").submit();
            });

            $("#formGroupPhoto").on('submit', function (event) {
                event.preventDefault();
                $.ajax({
                    url: "index.php?controller=chatController&action=updateInfoChat&type=pathToChatPhoto",
                    type: "POST",
                    data: new FormData(this),
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function (result) {
                        if (result == 'blocked') {
                            $.redirect('index.php');
                        }
                        if (result == 'success') {
                            reloadChatMenu();
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
                    },
                    error: function (e) {
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
                });
            });

        }   // chiusura IF success

    });  // chiusura load
}

function filterMember() {
    if (typeof $('#searchMembers').val() != "undefined") {
        var value = $('#searchMembers').val().toLowerCase().trim();
        var members = $('.pre-scrollable-right li');
        for (i = 0; i < members.length; i++) {
            if ($('.usernameMember')[i] == null) {
                return;
            }
            var memberName = $('.usernameMember')[i].innerText.toLowerCase().trim();
            if (memberName.includes(value)) {
                $(members[i]).show();
            } else {
                $(members[i]).hide();
            }
        }
    }
}

function ajaxUpdateInfoChat(type, value) {
    $.ajax({
        url: 'index.php',
        type: 'POST',
        data: {
            'type': type,
            'action': 'updateInfoChat',
            'controller': 'chatController',
            'value': value
        },
        success: function (result) {
            if (result == 'blocked') {
                $.redirect('index.php');
            }
            if (result == 'success') {
                reloadChatMenu();
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
        },
        error: function (result) {
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
    });
}

