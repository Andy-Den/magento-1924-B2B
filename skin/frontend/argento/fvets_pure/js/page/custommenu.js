jQuery(document).ready(function() {
    jQuery('.menu').mouseover(function()
    {
        jQuery('.wp-custom-menu-popup').css({'min-height' : jQuery('#custommenu').outerHeight() + 'px'});
    });
});