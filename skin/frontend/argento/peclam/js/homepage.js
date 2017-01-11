jQuery(document).ready(function() {
    if (jQuery('.brandContainer') != undefined)
    {
        var qty = jQuery('.brandContainer .brands-list li').length;


        //console.log((100 / qty) + '%');

        jQuery('.brandContainer .brands-list li').css({'width' : (100 / qty) + '%'});
        jQuery('.brandContainer .brands-list li').css({'min-width' : '55px'});
    }

    jQuery('.logged-out .easyslide-link').each(function(el) {
        jQuery(this).unbind('click');
    });

    jQuery('.logged-out .easyslide-link').click(function (el) {
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

jQuery(window).load(function() {
    if (jQuery('.easycatalogimg') != undefined)
    {
        var maxHeight = 0;
        jQuery('.easycatalogimg .item').each(function() {
            if (jQuery(this).height() > maxHeight) {
                maxHeight = jQuery(this).height();
            }
        })
        jQuery('.easycatalogimg .item').height(maxHeight);
    }


    if (jQuery('.advantages') != undefined)
    {
        var maxHeight = 0;
        jQuery('.advantages .item').each(function() {
            if (jQuery(this).height() > maxHeight) {
                maxHeight = jQuery(this).height();
            }
        })
        jQuery('.advantages .item').height(maxHeight);
    }
});
