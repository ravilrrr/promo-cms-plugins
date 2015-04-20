function questionAdd(f) {
    var submitButton = $(f).find('input:submit').first();
    var textButton = submitButton.val();
    
    submitButton.prop("disabled", true).val(submitButton.data('loading-text'));
    
    $.post('', $(f).serialize(), function(data) {
        if (data.result == 'success') {
            $('.question-form').after(data.template);
            f.reset();
        } else {
            alert(data.result);
        }
        submitButton.removeProp("disabled").val(textButton);
    }, "json");
    
    return false;
}

$(document).ready(function(){
    $('.question-show-form').click(function(){
        $('form.question-hide').show();
        $(this).hide();
        return false;
    });
});