jQuery(document).ready(function() {
    jQuery('.logged-out .easyslide-link, .logged-out .featherlight-login').each(function(el) {
        jQuery(this).unbind('click');
    });

    jQuery('.logged-out .easyslide-link, .logged-out .featherlight-login').click(function (el) {
        el.preventDefault();
        sendAfterLoginUrl(jQuery(this).attr('href'));

        jQuery.featherlight(jQuery('#account-login-box'));
        return false;

    });
});

function sendAfterLoginUrl(url) {
    jQuery.post(
        'customer/account/loginBoxSendAfterUrlPost',
        {url: url});
}