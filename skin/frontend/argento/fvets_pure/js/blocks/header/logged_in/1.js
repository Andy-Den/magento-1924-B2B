jQuery(document).ready(function() {
    subHeaderPosition();
    jQuery(document).scroll(function(){
        subHeaderPosition();
    });
});

function subHeaderPosition() {
    if (jQuery('.header').offset() != undefined) {
        if (jQuery(document).scrollTop() >= jQuery('.header').outerHeight(true) + jQuery('.header-container').offset().top) {
            jQuery('.sub-header').addClass('fixed-nav');
        } else {
            jQuery('.sub-header').removeClass('fixed-nav');
        }
    }
}