/**
 * Created by julio on 7/21/15.
 */
function organizeAllSiteProlabel(el) {
    var a = jQuery(el);
    a.trigger("hover");
    var spanElement = a.find("span.block");

    var parentLeft = (spanElement.parent().offset().left);
    var windowWidth = jQuery(document).width();
    if ((parentLeft + 351) > windowWidth) {
        spanElement.css({'right': windowWidth - parentLeft});
        spanElement.css({'left': 'auto'});
        spanElement.css({'width': '351px'});
        //spanElement.css({'margin': '0 auto'});
    } else {
        spanElement.css({'left': parentLeft + 'px'});
        spanElement.css({'width': 351 + 'px'});
    }
    spanElement.css({'top': (spanElement.parent().offset().top - jQuery(window).scrollTop() + 35) + 'px'});
    a.parent().css({'z-index':1000});

    if (windowWidth <= 600) {
        spanElement.addClass('mobile');
    }
}

var labelVisible = false;

function initProlabels() {

    jQuery(".campaign-label span.tooltip").on('mouseover', function() {
        //clear all labels and label blocks
        jQuery(".campaign-label").css({'z-index':10});
        jQuery(".campaign-label span.tooltip span.block").css({'display':'none'});
        //end

        //show specific label block box
        jQuery(this).find('span.block').css({'display':'inline'});
        organizeAllSiteProlabel(this);
        labelVisible = this;
        //end
    });

    jQuery(".campaign-label span.tooltip span.block").on('mouseout', function() {
        jQuery(this).parent().parent().css({'z-index':10});
        jQuery(this).css({'display':'none'});
        labelVisible = false;
    });

    jQuery(document).click(function(e) {
        var container = jQuery(".campaign-label span.block");
        if(!container.is(e.target) && container.has(e.target).length === 0) // ... nor a descendant of the container
        {
            container.hide();
        }
    });
}

jQuery(document).ready(function () {
    initProlabels();
});

jQuery(window).scroll(function() {
    if (labelVisible)
        organizeAllSiteProlabel(labelVisible);
});