$(document).ready(function() {
    var spoiler = $('.spoiler-head');
    
    spoiler.click(function() {
        $(this).toggleClass('current');
        $(this).next('.spoiler-body').slideToggle(100);
    });
});