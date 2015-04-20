function guestbookAdd(f) {
    var submitButton = $(f).find('input:submit').first();
    var textButton = submitButton.val();
    
    submitButton.prop("disabled", true).val(submitButton.data('loading-text'));
    
    $.post('', $(f).serialize(), function(data) {
        if (data.result == 'success') {
            $('.guestbook-form').after(data.template);
            f.reset();
        } else {
            alert(data.result);
        }
        submitButton.removeProp("disabled").val(textButton);
    }, "json");
    
    return false;
}

$(document).ready(function(){
    $('.guestbook-show-form').click(function(){
        $('form.guestbook-hide').show();
        $(this).hide();
        return false;
    });
});