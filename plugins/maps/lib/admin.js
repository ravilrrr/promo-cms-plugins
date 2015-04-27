function mapsSettingsSave(f) {
    var map_result = $('#maps-settings-result');
    map_result.html('<img src="'+f.siteurl.value+'/plugins/maps/lib/load.gif" width="16" height="16" alt=""/>');
    $.ajax({
        type: 'POST', 
        data: $(f).serialize(),
        success: function(msg) {
            map_result.html(msg);
        }
    });
    return false;
}