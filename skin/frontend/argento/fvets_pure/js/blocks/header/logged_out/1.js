jQuery(document).ready(function () {
    jQuery('div.search').click(function () {
        if (jQuery('div.search i.fa-search').css('display') == 'block') {
            jQuery('div.menu').css('display', 'none');
            jQuery('div.top-search').css('display', 'block');
            jQuery('div.search i.fa-search').css('display', 'none');
            jQuery('div.search i.fa-times').css('display', 'block');
        } else {
            jQuery('div.menu').css('display', 'block');
            jQuery('div.top-search').css('display', 'none');
            jQuery('div.search i.fa-search').css('display', 'block');
            jQuery('div.search i.fa-times').css('display', 'none');
        }
    });
});