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
        $('#wallpaper').css({'background':'url('+data.url+') no-repeat fixed','background-size':'cover', 'background-position':'center center'})
    });
}
function changeTitle(title) {
    document.title = title;
}