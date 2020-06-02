var timer = null, bool = false;
$('#messageText').on('keyup', function () {
    clearTimeout(timer);
    timer = setTimeout(function () {
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

var chatMembers;
$(document).ready(function () {
    chatMembers = $(".span-group-members").text();
    loadNewMessages();
});

setInterval(function () {
    loadNewMessages();
    checkWhoIsTyping();
}, 3000); 

function checkWhoIsTyping(timestamp) {
    $.ajax({
        type: "POST",
        url: "index.php",
        data: {
            "controller": "chatController",
            "action": "checkWhoIsTyping",
            "timestamp": timestamp
        },
        success: function (data) {
            if (data === "blocked") {
                $.redirect('index.php');
            } else if (data === "none" || data == '[]') {
                $('.span-group-members').text(chatMembers);
            } else {
                data = JSON.parse(data);
                string = "";
                for (let index = 0; index < data.length; index++) {
                    string += data[index]['username'];
                    if (index == data.length - 1) {
                        if (data.length > 1)
                            string += ' are typing...'
                        else
                            string += ' is typing...'
                    } else {
                        string += ', ';
                    }
                }
                $('.span-group-members').text(string);
            }
        }
    });
}

var interval, scrollPoint;
buttonOpenMenuDetailsChat();

function buttonOpenMenuDetailsChat() {
    $('#chatDetails').on('click', function () {
        reloadChatMenu();
        interval = setInterval(function () {
            scrollPoint = $("#chatMenuContent").scrollTop();
            chatMenuUpdates();
        }, 2000);
        $('#chatDetails').off();
    });
}

function chatMenuUpdates() {
    $.ajax({
        type: "POST",
        url: "index.php?controller=menuController&action=chatMenuUpdates",
        data: {
            'title': $('.chatTitle').text().trim(),
            'description': $('.chatDescription').text().trim(),
            'photo': $('#chatPhoto').attr('src').trim()
        },
        success: function (updates) {
            updates = JSON.parse(updates);

            if (typeof updates['chat'] !== 'undefined') {
                if (updates['chat'].hasOwnProperty('title')) {
                    $('.chatTitle').html(updates['chat']['title']);
                }
                if (updates['chat'].hasOwnProperty('description')) {
                    $('.chatDescription').html(updates['chat']['description']);
                }
                if (updates['chat'].hasOwnProperty('photo')) {
                    $('#chatPhoto').attr('src', updates['chat']['photo']);
                }
            }

            //let key = Object.keys(updates);
            if (typeof updates['members'] !== 'undefined') {
                $('#members').html(updates['members'])
                filterMember();
                eventMemberList();
            }
        }
    });
}

function reloadChatMenu() {
    $('#rightMenu').load('index.php?controller=menuController&action=caricaMenuChat', function (responseTxt, statusTxt, xhr) {
        if (statusTxt == "success") {

            filterMember();

            $("#chatMenuContent").animate({
                scrollTop: scrollPoint
            }, 0);

            $("#close-sidebar-right").click(function () {
                $('#rightMenu').children().remove();
                $('.messaging').css('right', '0');
                clearInterval(interval);
                buttonOpenMenuDetailsChat();
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
                    let input = '<textarea id="descriptionInput" class="form-control" maxLength="1024" name="description" autofocus>' + groupDescr.trim() + '</textarea>';
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
                        parentElement.append("<span id='descriptionInput' style='padding: 0 20px 5px 20px'>" + groupDescr.trim() + "</span>");
                        aggiornaDescrizione();

                        interval = setInterval(function () {
                            scrollPoint = $("#chatMenuContent").scrollTop();
                            chatMenuUpdates();
                        }, 2000);
                    });
                });
            }

            aggiornaTitolo();
            function aggiornaTitolo() {
                $("#titleInput").on('click', function () {
                    let title = $(this).text();
                    let input = '<input id="titleInput" class="form-control search-menu" maxLength="30" name="title" type="text" value="' + title.trim() + '">';
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
                        parentElement.prepend("<a id='titleInput' href='#'>" + title.trim() + "</a>");
                        aggiornaTitolo();

                        interval = setInterval(function () {
                            scrollPoint = $("#chatMenuContent").scrollTop();
                            chatMenuUpdates();
                        }, 2000);
                    });
                });
            }

            eventMemberList();

            $('#abbandona').on('click', function () {
                var userId = $(this).attr('userId');
                $.ajax({
                    url: 'index.php',
                    type: 'POST',
                    data: {
                        'action': 'removeUserFromChat',
                        'controller': 'chatController'
                    },
                    success: function (result) {
                        if (result == 'success') {
                            $.redirect('index.php', { 'action': 'viewHomePage' });
                        } else {
                            errorNotify();
                        }
                    },
                    error: function (result) {
                        errorNotify();
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
                            errorNotify();
                        }
                    },
                    error: function (e) {
                        errorNotify();
                    }
                });
            });

            $('#addUser').on('click', function () {
                loadModalAddUser();
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
                errorNotify();
            }
        },
        error: function (result) {
            errorNotify();
        }
    });
}

function errorNotify(text = "Error: Operation failed!") {
    $.notify(text, {
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

function loadModalAddUser() {
    $("#modal").load("./index.php?controller=chatController&action=mostraModaleAddUser", function (responseTxt, statusTxt, xhr) {
        if (statusTxt == "success") {
            $('#addChatModal').modal('show');

            $(function () {
                window.fs_test = $('#selectUsers').fSelect();
            });

            $("#formAddUser").on('submit', function (event) {
                event.preventDefault();
                $.ajax({
                    url: "index.php?controller=chatController&action=addUserFromChat",
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
                            errorNotify();
                        }
                    },
                    error: function (e) {
                        errorNotify();
                    }
                });
            });
        }
        if (statusTxt == "error") {
            alert("Errore imprevisto, riprovare")
        }
    });
}

function eventMemberList() {
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

    $('.removeUser').on('click', function () {
        var userId = $(this).attr('userId');
        $.ajax({
            url: 'index.php',
            type: 'POST',
            data: {
                'userId': userId,
                'action': 'removeUserFromChat',
                'controller': 'chatController'
            },
            success: function (result) {
                if (result == 'blocked') {
                    $.redirect('index.php');
                }
                if (result == 'success') {
                    reloadChatMenu();
                } else {
                    errorNotify();
                }
            },
            error: function (result) {
                errorNotify();
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
}