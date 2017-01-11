/**
 * Created by julio on 7/16/15.
 */
jQuery(document).ready(function () {
    if (jQuery('.cms-index-index.logged-out') != undefined) {
        var height = jQuery('.blocklinks-blocks.second').outerHeight(true);
        jQuery('.footer-cms-container').css('margin-top', height);
    }
});