// Filtro per la ricerca di utenti online, offline o tutti

//cerca utenti
$('#search').on('click', function () {

    let filter = $('#searchFriend').val();
    let format = /[`!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~]/;
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

//accetta/rifiuta richieste
eventAcceptDeclineButtons();

function eventAcceptDeclineButtons() {
    var dataArray;

    $('.acceptRequest').on('click', function () {
        dataArray = {
            'controller': 'friendsController',
            'action': 'acceptDeclineRequest',
            'accepted': true,
            'friendId': this.id
        }
        ajaxAcceptdeclineRequest(dataArray);
    });

    $('.declineRequest').on('click', function () {
        dataArray = {
            'controller': 'friendsController',
            'action': 'acceptDeclineRequest',
            'accepted': false,
            'friendId': this.id
        }
        ajaxAcceptdeclineRequest(dataArray);
    });
}

function ajaxAcceptdeclineRequest(dataArray) {
    var div = $('#buttonForAcceptDecline').clone();
    $('#buttonForAcceptDecline').html('<span style="font-family: none; font-size: unset" class="badge badge-warning badge-pill">Sending...</span>');
    $.ajax({
        url: 'index.php',
        type: 'POST',
        data: dataArray,
        success: function (result) {
            if (result == 'success') {
                $('#buttonForAcceptDecline').html('<span style="font-family: none; font-size: unset" class="badge badge-success badge-pill">Done</span>');
            } else {
                $('#buttonForAcceptDecline').html(div.children());
                eventAcceptDeclineButtons();
            }
        },
        error: function (result) {
            $('#buttonForAcceptDecline').html(div.children());
            eventAcceptDeclineButtons();
        }
    });
}

$(document).ready(function () {
    $('#nav-tabContent').load("index.php?controller=friendsController&action=viewFriendsPage", function (responseTxt, statusTxt, xhr) {
        if (statusTxt == "success") {
            let idActiveElement = $('.nav-item.nav-link.active').attr('href');
            $(idActiveElement).addClass('show active');
        }
    });

    setInterval(function () {
        $('#nav-tabContent').load("index.php?controller=friendsController&action=viewFriendsPage", function (responseTxt, statusTxt, xhr) {
            if (statusTxt == "success") {
                let idActiveElement = $('.nav-item.nav-link.active').attr('href');
                $(idActiveElement).addClass('show active');
            }
        });

    }, 3000);
});