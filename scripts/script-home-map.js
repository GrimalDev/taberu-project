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
})