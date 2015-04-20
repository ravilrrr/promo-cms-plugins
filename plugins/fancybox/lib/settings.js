$(document).ready(function() {
    var imgs = 'a[href$=".jpg"], a[href$=".jpeg"], a[href$=".gif"], a[href$=".png"], a.fancybox';
    $(imgs).attr("rel", "group");
    $(imgs).fancybox({
        openEffect	: 'none',
        closeEffect	: 'none', 
        prevEffect	: 'none',
        nextEffect	: 'none',
        helpers: {
            title : {
                type : 'float'
            }
        }
    });
    
    $(".fmodal").fancybox({
        maxWidth	: 700,
        maxHeight	: 700,
        fitToView	: false,
        autoSize	: true,
        closeClick	: false,
        openEffect	: 'none',
        closeEffect	: 'none'
    });
});