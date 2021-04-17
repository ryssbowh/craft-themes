function install(btn) {
    btn.attr('disabled', true);
    btn.next().show();
    $.ajax({
        method: 'post',
        url: btn.attr('href'), 
        dataType: 'json',
        headers: {
            'X-CSRF-Token': Craft.csrfTokenValue
        }
    }).done((res) => {
        Craft.cp.displayNotice(res.message);
    }).fail((res) => {
        Craft.cp.displayError(res.responseJSON.error);
    }).always(() => {
        btn.next().hide();
        btn.attr('disabled', false);
    })
}

$(function() { 
    $('.install').click(function (e) {
        e.preventDefault();
        install($(this));
    });
});