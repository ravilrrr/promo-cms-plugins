function formsSend(f) {
    $.post('', $(f).serialize(), function(data) {
        if (data.result == 'success') {
            alert(data.message);
            f.reset();
            if (typeof $.fancybox != 'undefined') $.fancybox.close();
        } else {
            alert(data.result);
        }
    }, "json");
}