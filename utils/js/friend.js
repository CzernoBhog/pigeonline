// Filtro per la ricerca di utenti online, offline o tutti

//cerca utenti
$('#search').on('click', function () {

    var filter = $('#searchFriend').val();
    var format = /[`!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~]/;
    if (format.test(filter)) {
        document.getElementById('searchFriend').setCustomValidity('Carattere non valido');
        return;
    } else {
        document.getElementById('searchFriend').setCustomValidity('');
    }

    $.ajax({
        url: 'index.php',
        type: 'POST',
        data: {
            'filter': filter,
            'action': 'searchUser',
            'controller': 'friendsController'
        },
        success: function (result) {
            var obj = JSON.parse(result);
            var stringOpt = '<div class=search-scrollable>';
            if (obj == null || obj.length == 0) {
                stringOpt += "<li class='list-group-item d-flex justify-content-between align-items-center'><i>Nessun utente trovato :(</i></li>";
            } else {
                for (var i = 0; i < obj.length; i++) { //costruisco la stringa con le opzioni per la select
                    stringOpt += '<li class="list-group-item d-flex justify-content-between align-items-center">\
                                            <b>' + obj[i].username + '</b>\
                                            <a style="text-decoration: none;" class="friendRequest" id=' + obj[i].userId + ' href="#">\
                                                <span style="font-family: none; font-size: unset" class="badge badge-success badge-pill">send request</span>\
                                            </a>\
                                          </li>'
                }
            }
            stringOpt += '</div><a style="color: black" id="closeSearchW" href="#"><i class="fa fa-window-close fa-pull-left; width: 30px; height: 30px" style="padding-left: 3px;"></i></a>';
            $("#results").html(stringOpt);

            $('#closeSearchW').on('click', function () {
                $("#results").html('');
                $('#searchFriend').val('');
            });

            $('.friendRequest').on('click', function () {
                var friendId = this.id;
                $('#' + friendId).html('<span style="font-family: none; font-size: unset" class="badge badge-warning badge-pill">sending</span>');
                $.ajax({
                    url: 'index.php',
                    type: 'POST',
                    data: {
                        'friendId': friendId,
                        'action': 'friendRequest',
                        'controller': 'friendsController'
                    },
                    success: function (result) {
                        if (result == 'success') {
                            $('#' + friendId).html('<span style="font-family: none; font-size: unset" class="badge badge-success badge-pill">sent</span>');
                            $('#' + friendId).css("pointer-events", "none"); // disabilita gli eventi del mouse
                        } else {
                            $('#' + friendId).html('<span style="font-family: none; font-size: unset" class="badge badge-danger badge-pill">resend request</span>');
                        }
                    },
                    error: function (result) {
                        $('#' + friendId).html('<span style="font-family: none; font-size: unset" class="badge badge-danger badge-pill">resend request</span>');
                    }
                });
            });
        }
    });
});

//chiude il right menu quando clicco al di fuori di esso
$(document).on("click", function () {
    $('#context-menu').removeClass("show").hide();
});

//accetta/rifiuta richieste
function eventAcceptDeclineCancelBlockButtons() {
    var dataArray;

    $('.acceptRequest').on('click', function () {
        dataArray = {
            'controller': 'friendsController',
            'action': 'acceptDeclineRequest',
            'accepted': true,
            'friendId': this.id
        };
        ajaxAcceptdeclineCancelBlockRequest(dataArray);
    });

    $('.declineRequest').on('click', function () {
        dataArray = {
            'controller': 'friendsController',
            'action': 'acceptDeclineRequest',
            'accepted': false,
            'friendId': this.id
        };
        ajaxAcceptdeclineCancelBlockRequest(dataArray);
    });

    $('.cancelRequest').on('click', function () {
        dataArray = {
            'controller': 'friendsController',
            'action': 'cancelRequest',
            'friendId': this.id
        };
        ajaxAcceptdeclineCancelBlockRequest(dataArray);
    });

    $('.blockUser').on('click', function () {
        dataArray = {
            'controller': 'blockedsController',
            'action': 'blockUser',
            'userId': this.id
        };
        ajaxAcceptdeclineCancelBlockRequest(dataArray);
    });

    $('.unblock').on('click', function () {
        dataArray = {
            'controller': 'blockedsController',
            'action': 'unblockUser',
            'userId': this.id
        };
        ajaxAcceptdeclineCancelBlockRequest(dataArray);
    });

    //right click menu
    $('.jumbotron').on('contextmenu', function (e) {
        let idUser = $(this).attr("id");
        $("#context-menu").children().attr("id", idUser);
        var top = e.pageY;
        var left = e.pageX;
        $("#context-menu").css({
            display: "block",
            top: top,
            left: left
        }).addClass("show");
        return false; //blocks default Webbrowser right click menu
    });
}

function ajaxAcceptdeclineCancelBlockRequest(dataArray) {
    $('#buttonForAcceptDecline').html('<span style="font-family: none; font-size: unset" class="badge badge-warning badge-pill">Sending...</span>');
    $.ajax({
        url: 'index.php',
        type: 'POST',
        data: dataArray,
        success: function (result) {
            if (result == 'success') {
                $('#' + dataArray.action).html('<span style="font-family: none; font-size: unset" class="badge badge-success badge-pill">Done</span>');
            } else {
                $('#' + dataArray.action).html('<span style="font-family: none; font-size: unset" class="badge badge-danger badge-pill">Error</span>');
            }
        },
        error: function (result) {
            $('#' + dataArray.action).html('<span style="font-family: none; font-size: unset" class="badge badge-danger badge-pill">Error</span>');
        }
    });
}

$(document).ready(function () {
    $('#nav-tabContent').load("index.php?controller=friendsController&action=viewFriendsPage", function (responseTxt, statusTxt, xhr) {
        if (statusTxt == "success") {
            var idActiveElement = $('.nav-item.nav-link.active').attr('href');
            $(idActiveElement).addClass('show active');
            eventAcceptDeclineCancelBlockButtons();
        }
    });

    /* setInterval(function () {
        $('#nav-tabContent').load("index.php?controller=friendsController&action=viewFriendsPage", function (responseTxt, statusTxt, xhr) {
            if (statusTxt == "success") {
                var idActiveElement = $('.nav-item.nav-link.active').attr('href');
                $(idActiveElement).addClass('show active');
                eventAcceptDeclineCancelBlockButtons();
            }
        });

    }, 3000);  */
});