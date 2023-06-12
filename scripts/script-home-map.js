//Here is the only place a jquery is used. It is used to resize the map on the home page.
//Jquery has a special library called imagemapster that allows you to easily interract with an image map.
//This code is not entirely mine. A huge thanks to this stack overflow post for helping me figure out how to resize the map:
// https://stackoverflow.com/questions/23642773/using-the-onclick-event-to-fill-a-selected-region-in-an-image-map-with-color-via
$(document).ready(function() {
    const image = $('#img_ID');

    image.mapster({
        fillColor: 'f66b0e',
        singleSelect: true,
        clickNavigate: true
    });

    var resizing,
        body= $(body),
        win=$(window),
        diffW=win.width() - image.width(),
        lastw=win.innerWidth(),
        lasth=win.innerHeight();

    var resize = function() {
        var win= $(window),
            width=win.width(), height=win.height();
        // only try to resize every 200 ms
        if (resizing) {
            return;
        }
        if (lastw !== width || lasth !== height) {
            resizing=true;
            image.mapster('resize',width-diffW,0,100);
            lastw=width;
            lasth=height;
            setTimeout(function() {
                resizing=false;
                resize();
            },200);
        } else {

        }
    };
    $(window).bind('resize',resize);
});