var headerLastIteration;
jQuery(document).ready(function () {
    jQuery(this).scroll(function () {
        if (jQuery(this).scrollTop() >= jQuery("#header").outerHeight()) {
            if (headerLastIteration != 'bigger')
            {
                headerLastIteration = 'bigger';
                jQuery('#fixed-menu').slideDown();
            }

        } else {
            if (headerLastIteration != 'little')
            {
                headerLastIteration = 'little';
                jQuery('#fixed-menu').slideUp();
            }
        }
    });
});