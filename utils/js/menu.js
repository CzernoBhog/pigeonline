$(document).ready(function () {
    //load del menu sul div content e aggiunte del jquery per il funzionamento di modifica profilo, modifica indirizzi e inserisci utente gestiti attraverso div modale
    loadMenu();

    setInterval(function () {
        loadMenu();
    }, 5000);
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
        }
        if (statusTxt == "error") {
            alert("Error: " + xhr.status + ": " + xhr.statusText);
        }
    });
}

setInterval(function(){
    $.ajax({
        url: 'index.php',
        type: 'POST',
        data: {
            'action': 'updateActivity'
        }
    });
}, 5000)