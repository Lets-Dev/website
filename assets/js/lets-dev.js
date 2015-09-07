function getWallpaper(source, blur) {
    var url, css;
    switch (source) {
        case 'manager':
            url = '../includes/queries/wallpaper_of_the_day.php';
            break;
        default:
            url = 'includes/queries/wallpaper_of_the_day.php';
    }
    $.getJSON(url, function (data) {
        if (blur ==true)
            css = {
                'background': 'url(' + data.url + ') no-repeat fixed',
                'background-size': 'cover',
                'background-position': 'center center',
                'z-index':-1,
                '-webkit-filter': 'blur(5px)',
                '-moz-filter': 'blur(5px)',
                '-o-filter': 'blur(5px)',
                '-ms-filter': 'blur(5px)',
                'filter': 'blur(5px)',
                'content': ''
            }
        else
            css = {
                'background': 'url(' + data.url + ') no-repeat fixed',
                'background-size': 'cover',
                'background-position': 'center center'
            }
        $('#wallpaper').css(css)
    });
}
function changeTitle(title) {
    document.title = title;
}
function suggestShortcut() {
    $.post('../includes/queries/teams.php', {
            action: 'shortname',
            step: 'suggest',
            name: $('#fullname').val()
        },
        function (data) {
            $('#shortname').val(data.messages)
        })
}

function checkShortcut() {
    var shortname = $('#shortname');
    $.post('../includes/queries/teams.php', {
            action: 'shortname',
            step: 'check',
            name: $('#fullname').val(),
            shortname: shortname.val()
        },
        function (data) {
            if (data.status === "error") {
                shortname.closest('.form-group').removeClass('has-success').addClass('has-error');
                $('button').attr('disabled', 'disabled');
            }
            else {
                shortname.tooltip('hide').closest('.form-group').removeClass('has-error');
                $('button').removeAttr('disabled');
            }
        })
}
function joinTeam(team) {
    var button = $(event.target);
    button.attr("disabled", "disabled");
    $.post('../includes/queries/teams.php', {
        action: 'join',
        team: team
    }, function (data) {
        var i;
        for (i = 0; i < data.messages.length; i++)
            toastr[data.status](data.messages[i]);
        if (data.status == "success") {
            event.target.className = '';
            button.addClass('btn btn-flat btn-success').html('<i class="fa fa-check"></i> Demande envoyée').attr('disabled', 'disabled')
        }
    })

}
function readNotifications() {
    $.post('../includes/queries/notifications', {
        action: "mark_as_read"
    }, function (data) {
        $('#notification_count').remove()
    })
}

function viewAllNotifications() {
    $.post('../includes/queries/notifications', {
        action: "view_all"
    }, function (data) {
        $('#notifications').html(data.display)
    })
}

toastr.options = {
    "progressBar": true,
    "positionClass": "toast-bottom-right"
}