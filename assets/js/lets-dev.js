function getWallpaper(source) {
    var url;
    switch (source) {
        case 'manager':
            url = '../includes/queries/wallpaper_of_the_day.php';
            break;
        default:
            url = 'includes/queries/wallpaper_of_the_day.php';
    }
    $.getJSON(url, function (data) {
        $('#wallpaper').css({
            'background': 'url(' + data.url + ') no-repeat fixed',
            'background-size': 'cover',
            'background-position': 'center center'
        })
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

(function ($) {
    $.fn.markdown.messages['fr'] = {
        'Bold': "Gras",
        'Italic': "Italique",
        'Heading': "Titre",
        'URL/Link': "Insérer un lien HTTP",
        'Image': "Insérer une image",
        'List': "Liste à puces",
        'Preview': "Voir",
        'strong text': "texte important",
        'emphasized text': "texte souligné",
        'heading text': "texte d'entête",
        'enter link description here': "entrez la description du lien ici",
        'Insert Hyperlink': "Insérez le lien hypertexte",
        'enter image description here': "entrez la description de l'image ici",
        'Insert Image Hyperlink': "Insérez le lien hypertexte de l'image",
        'enter image title here': "entrez le titre de l'image ici",
        'list text here': "texte à puce ici"
    };
}(jQuery))

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
        button.removeAttr("disabled")
    })

}