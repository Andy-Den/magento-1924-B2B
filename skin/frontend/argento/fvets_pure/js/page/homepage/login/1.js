/**
 * Created by julio on 2/2/15.
 */
//<![CDATA[
jQuery(document).ready(function () {
    if (jQuery('body').hasClass('logged-out')) {
        if (jQuery('.featherlight-login') != undefined) {
            jQuery('.featherlight-login').bind('click', function (element) {
                element.preventDefault();
                jQuery.featherlight(jQuery('#newsletter-featherlight-login'));
            })
        }
    }
});
//]]>