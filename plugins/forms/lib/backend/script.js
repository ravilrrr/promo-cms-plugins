$(document).ready(function(){
    
    $("#forms_elements tr").hover(function() {
          $(this.cells[0]).addClass('showDragHandle');
    }, function() {
          $(this.cells[0]).removeClass('showDragHandle');
    });
    
    $('#forms_elements').tableDnD({
        onDrop: function(table, row) {
            $.post(window.location.href, $.tableDnD.serialize());
        },
        dragHandle: ".dragHandle"
    });
    
    $(".forms-element-required-check").click(function() {
        var required = 'yes';
        if ($(this).hasClass('btn-primary')) {
            required = 'no';
        }
        
        $(this).toggleClass('btn-primary btn-default');
        
        $.post('', {forms_element_required_check: true, form_id: $(this).data('form-id'), element_id: $(this).data('element-id'), val: required});
        return false;
    });
    
    $(".forms-element-width-change").click(function() {
        var parent_div = $(this).closest('div');
        var f_id = parent_div.data('form-id');
        var e_id = parent_div.data('element-id');
        var w = $(this).text();
        
        parent_div.children('button').removeClass('active');
        $(this).addClass('active');
        
        $.post('', {forms_element_width_change: true, form_id: f_id, element_id: e_id, width: w});
        return false;
    });
});

function formsDemoMsg() {
    $.post('', {forms_demo_msg_close:true});
    return false;
}