function stockWay(btn, way) {
    $(btn).parent('div').children('button').removeClass('btn-success').addClass('btn-default');
    $(btn).removeClass('btn-default').addClass('btn-success');
    if (way == 'internet') {
        $(".stock_from_computer").hide();
        $(".stock_from_internet").show();
    } else {
        $(".stock_from_internet").hide();
        $(".stock_from_computer").show();
    }
    
    return false;
}

function stResize(id, siteurl) {
    var st_result = $('#st_result');
    st_result.html('<img src="'+siteurl+'/plugins/stock/lib/load.gif" width="16" height="16" alt=""/>');
    $.ajax({
        type: 'POST', 
        data: { album_id: id, st_resize: true }
    }).done(function(data) {
        st_result.html(data);
    });
    return false;
}

function stAlbumEditSave(f) {
    var st_result = $('#st-edit-result');
    st_result.html('<img src="'+f.siteurl.value+'/plugins/stock/lib/load.gif" width="16" height="16" alt=""/>');
    $.ajax({
        type: 'POST', 
        data: $(f).serialize(),
        success: function(msg) {
            st_result.html(msg);
        }
    });
    return false;
}

if (typeof $.promo == 'undefined') $.promo = {};

$.promo.stock = {

    init: function() { },

    showEmbedCodes: function(name, alb_id) {
        $('#shortcode').val('{stock album="'+alb_id+'" img="'+name+'"}');
        $('#phpcode').val('<?php echo Stock::img('+alb_id+', "'+name+'"); ?>');
        $('#embedCodes').modal();
    }, 
    
    showEmbedCodesAlbum: function(alb_id) {
        $('#shortcode').val('{stock album="'+alb_id+'"}');
        $('#phpcode').val('<?php echo Stock::album('+alb_id+'); ?>');
        
        $('#shortcode-last').val('{stock album="'+alb_id+'" show="last"}');
        $('#phpcode-last').val('<?php echo Stock::last('+alb_id+'); ?>');
        
        $('#shortcode-last3').val('{stock album="'+alb_id+'" show="last" count="3"}');
        $('#phpcode-last3').val('<?php echo Stock::last('+alb_id+', 3); ?>');
        
        $('#shortcode-random').val('{stock album="'+alb_id+'" show="random"}');
        $('#phpcode-random').val('<?php echo Stock::random('+alb_id+'); ?>');
        $('#embedCodes').modal();
    }

};

function stockModal() {
    $('#stockModal').modal('toggle');
}

$(document).ready(function(){ 
    $.promo.stock.init();
    
    $('#stock_upload_variant button').click(function() {
        $('#stock_upload_variant button').removeClass('active');
        $(this).addClass('active');
    });
    
    $('#stockModal').on('show.bs.modal', function () {
        body = $(this).find('.modal-body');
        
        if (body.html() == '') {
            body.html('Loading...');
            $.ajax({
                type: 'POST', 
                data: { st_modal_albums: true }
            }).done(function(data) {
                body.html(data);
            });
        }
    });
    
    $(document).on('click', '.stock-modal-images', function() {
        album = $(this);
        id = album.attr('rel');
        result_box = album.next('.stock-modal-images-result');
      
        if (result_box.html() == '') {
            $.ajax({
                type: 'POST', 
                data: { album_id: id, st_modal_images: true }
            }).done(function(data) {
                result_box.html(data).show();
                album.removeClass('btn-info').addClass('btn-success');
            });
        } else {
            if (album.hasClass('btn-success')) {
                album.removeClass('btn-success').addClass('btn-info');
                result_box.hide();
            } else {
                album.removeClass('btn-info').addClass('btn-success');
                result_box.show();
            }
        }

        return false;
    });
});