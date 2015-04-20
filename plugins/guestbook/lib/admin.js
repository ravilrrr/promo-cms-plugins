$(document).ready(function(){
    $('.guestbook-impotant-check').click(function(){
        $(this).toggleClass('btn-success').toggleClass('btn-default');
        
        if ($(this).hasClass('btn-success')) important = 1;
        else important = 0;

        $.post('', {val: important, guestbook_important_id: $(this).data('id')});
        
        return false;
    });
    
    $('.guestbook-check').click(function(){
        $(this).hide().parent('td').parent('tr').removeClass('warning');
        $.post('', {guestbook_check_id: $(this).data('id')});
        return false;
    });
       
    $('.guestbook-delete').on('ifChanged', function() { 
        $(this).parent('td').parent('tr').toggleClass('error-color-guestbook');
        
        if ($(':checkbox:checked').length > 0) {

            $('.delete-guestbook-button').removeClass('disabled').prop('disabled', false).addClass('btn-danger');
        } else { 
            
            $('.delete-guestbook-button').addClass('disabled').prop('disabled', true).removeClass('btn-danger');
        }
    });
    
    $('.check-all').on('ifChanged', function(){
        if (this.checked) {
            $('table tr td').find(':checkbox').iCheck('check');
        } else {
            $('table tr td').find(':checkbox').iCheck('uncheck');
        }
        
        if ($(':checkbox:checked').length > 0) {
            $('table tr').addClass('error-color-guestbook');
            $('.delete-guestbook-button').removeClass('disabled').prop('disabled', false).addClass('btn-danger');
        } else { 
            $('table tr').removeClass('error-color-guestbook');
            $('.delete-guestbook-button').addClass('disabled').prop('disabled', true).removeClass('btn-danger');
        }
    }); 
});