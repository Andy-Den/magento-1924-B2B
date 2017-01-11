jQuery(document).ready(function () {
    jQuery('.login-box-marker').each(function (index) {
        jQuery(this).click(function (el) {
            el.preventDefault();
            jQuery.featherlight(jQuery('#account-login-box'));
            return false;
        });
    });
});