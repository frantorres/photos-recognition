
jQuery(document).ready(function($) {
    /*if (wp.media) {
        wp.media.view.Modal.prototype.on('open', function() {
            showLabels();
        });
    }*/

});

jQuery( document ).on( 'click', '.prwp-ajax-labelsHtmlUl-link', function(e) {
    e.preventDefault();
    var el=jQuery(this);
    var elwrapper=el.parent();

    elwrapper.html('Loading...');

    var data = {
        'action': 'prwp_generateLabels',
        'postid': elwrapper.data('id')
    }; //TODO Nonce https://codex.wordpress.org/Function_Reference/check_ajax_referer

    jQuery.post(ajaxurl, data, function (response) {
        elwrapper.html(response);
    });
});

function showLabels(){
    jQuery('.prwp-ajax-labelsHtmlUl').each(function () {
        var elwrapper=jQuery(this);

        elwrapper.html('Loading...');

        var data = {
            'action': 'prwp_showLabels',
            'postid': elwrapper.data('id')
        }; //TODO Nonce https://codex.wordpress.org/Function_Reference/check_ajax_referer

        jQuery.post(ajaxurl, data, function (response) {
            elwrapper.html(response);
        });
    });
}

