function getWallpaper() {
    $.getJSON('includes/queries/wallpaper_of_the_day.php', function (data) {
        $('#wallpaper').css({'background':'url('+data.url+') no-repeat fixed','background-size':'cover', 'background-position':'center center'})
    });
}