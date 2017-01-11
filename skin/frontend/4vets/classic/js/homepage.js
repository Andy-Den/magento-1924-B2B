jQuery(document).ready(function() {
    jQuery(window).bind('scroll', function() {
        if (jQuery(this).scrollTop() >= jQuery('.block2').offset().top - 100)
        {
            jQuery('.menu-products').removeClass('active');
            jQuery('.menu-about').addClass('active');
        }
        else if (jQuery(this).scrollTop() >= jQuery('.block3').offset().top - 100)
        {
            jQuery('.menu-products').addClass('active');
            jQuery('.menu-about').removeClass('active');
        } else {
            jQuery('.menu-products').removeClass('active');
            jQuery('.menu-about').removeClass('active');
        }
    });


    jQuery('.menu.login, .menu.login a').bind('click', function() {
        jQuery('#new-users').slideUp('fast');
        jQuery('#login-box').slideDown('fast', function() {
            resizeOverlay();
        });
        jQuery('#email_address')[0].focus();
    });

    jQuery('.menu.menu-products, .menu.menu-products a').bind('click', function() {
        jQuery('html, body').animate({scrollTop: jQuery('#produtos').offset().top}, 500);
    });

    jQuery('.menu.menu-about, .menu.menu-about a').bind('click', function() {
        jQuery('html, body').animate({scrollTop: jQuery('#quem-somos').offset().top}, 500);
    });


    jQuery('#quicklogin-inline #new-users').show();
    jQuery('#quicklogin-inline #login-box').hide();
    resizeMainContainer1();
    jQuery(window).resize(function() {
        resizeMainContainer1();
        resizeBlockNews()
    });

});

function resizeMainContainer1() {
    jQuery('.main-container1').css({'height' : jQuery(window).height() - jQuery('.magestore-bannerslider').height() + 35 + 'px'});

    jQuery('.block2 .text').animate({'margin-top' : (jQuery('.block2 .container').height() / 2) -  (jQuery('.block2 .text').height() / 2) + 'px' });

    setTimeout('resizeMainContainer1()', 1000);
}