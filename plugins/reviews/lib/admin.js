$(document).ready(function(){
    $('.reviews-impotant-check').click(function(){
        $(this).toggleClass('btn-success').toggleClass('btn-default');
        
        if ($(this).hasClass('btn-success')) important = 1;
        else important = 0;

        $.post('', {val: important, reviews_important_id: $(this).data('id')});
        
        return false;
    });
    
    $('.reviews-check').click(function(){
        $(this).hide().parent('td').parent('tr').removeClass('warning');
        $.post('', {reviews_check_id: $(this).data('id')});
        return false;
    });
       
    $('.reviews-delete').on('ifChanged', function() { 
        $(this).parent('td').parent('tr').toggleClass('error-color-reviews');
        
        if ($(':checkbox:checked').length > 0) {

            $('.delete-reviews-button').removeClass('disabled').prop('disabled', false).addClass('btn-danger');
        } else { 
            
            $('.delete-reviews-button').addClass('disabled').prop('disabled', true).removeClass('btn-danger');
        }
    });
    
    $('.check-all').on('ifChanged', function(){
        if (this.checked) {
            $('table tr td').find(':checkbox').iCheck('check');
        } else {
            $('table tr td').find(':checkbox').iCheck('uncheck');
        }
        
        if ($(':checkbox:checked').length > 0) {
            $('table tr').addClass('error-color-reviews');
            $('.delete-reviews-button').removeClass('disabled').prop('disabled', false).addClass('btn-danger');
        } else { 
            $('table tr').removeClass('error-color-reviews');
            $('.delete-reviews-button').addClass('disabled').prop('disabled', true).removeClass('btn-danger');
        }
    }); 
});