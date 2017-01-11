jQuery(document).ready(function() {

    if(jQuery('.col2-right-layout .sidebar').children().size() < 2){
        jQuery('.col2-right-layout .sidebar').remove();
        jQuery('.col2-right-layout .col-main').removeClass('span9').addClass('row-fluid');
    }

    jQuery('.products-grid .item').on({
        mouseenter: function() {
            jQuery(this).find('.btn.btn-cart').removeClass('btn-warning').addClass('btn-success');
        },
        mouseleave: function() {
            jQuery(this).find('.btn.btn-cart').removeClass('btn-success').addClass('btn-warning');
        }
    });
    // Support for AJAX loaded modal window.
    // Focuses on first input textbox after it loads the window.
    jQuery('[data-toggle="ajaxModal"]').click(function(e) {
        e.preventDefault();
        var url = jQuery(this).attr('href');
        if (url.indexOf('#') == 0) {
            jQuery(url).modal('open');
        } else {
            jQuery("#loading-mask").show();
            jQuery.get(url, function(data) {
                jQuery('<div class="modal hide fade">' + data + '</div>').modal();
            }).success(function() {
                jQuery("#loading-mask").hide();
                jQuery('input:text:visible:first').focus();
            }).error(function() {
                jQuery('#loading-mask').hide();
            });
        }
    });
    jQuery('#billing\\:day').on('keypress', function() {
        if (jQuery(this).val().length == 1) {
            console.log('blur and focus');
            jQuery('#billing\\:month').focus();
        }
    });
    jQuery('#billing\\:month').on('keypress', function() {
        if (jQuery(this).val().length == 1) {
            jQuery('#billing\\:year').focus();
        }
    });

    //TEXT SEO catalog left column
    jQuery('.category_seo_readmore').bind({
        click: function() {
            jQuery('.category_seo_hidden').toggle("fast", function() {
                if (jQuery('.category_seo_hidden').is (':visible')){
                    jQuery('.category_seo_readmore').html('Fechar');
                } else {
                    jQuery('.category_seo_readmore').html('Leia mais');
                }
            });
        }
    });

    jQuery('.button-register-popup').bind({
        click: function() {
            getOverlay('/PrivateSales/register', 'cadastro', 'inline');
            return false;
        }
    });

    jQuery('.button-login-popup').bind({
        click: function() {
            getOverlay('/PrivateSales/register', 'login', 'inline');
            return false;
        }
    });

});

jQuery(window).load(function() {
    /**
     *  Images LazyLoad
     */
    jQuery("img.lazy").lazyload({
        skip_invisible : false
    });
});


function getOverlay(page, form, showin) {
	jQuery.ajax({
		url: page,
		data: 'form=' + form + '&showin=' + showin,
		beforeSend: function() {
			jQuery('#loading-mask').show();
		},
		success: function(data) {
			initOverlay(data);
			jQuery('#loading-mask').hide();
            jQuery(document).scrollTop(0);
		},
        error: function(xhr, ajaxOptions, thrownError) {
            //alert(xhr.status);
            //alert(thrownError);
            alert('Desculpe, houve um erro!');
            jQuery('#loading-mask').hide();
        }
	});
	return false;
}

function initOverlay(data)
{
	jQuery('#overlay .inner').html('<a href="javascript://" class="closeButton" onclick="jQuery(\'#overlay .inner\').html(\'\');jQuery(\'#overlay\').hide()"><img src="/skin/frontend/4vets/default/img/btn-overlay-close.png" /></a><div class="content"></div>');
	jQuery('#overlay .inner .content').html(data);
	jQuery('#overlay').show();
  jQuery('#overlay').css({'min-height':jQuery(document).height()+'px'});
  if (jQuery('#overlay .inner').height() < jQuery(window).height())
  {
	  jQuery('#overlay .inner').css({'margin-top' : (jQuery(window).height() / 2) - (jQuery('#overlay .inner').height() / 2) + 'px' });
  } else {
    jQuery('#overlay .inner').css({'margin-top' : '40px'});
    jQuery('#overlay .inner .content').css({'max-height' : jQuery(window).height() - 120 + 'px'});
  }

	jQuery(window).bind("resize", function(){
	  if (jQuery('#overlay .inner').height() < jQuery(window).height())
    {
  	  jQuery('#overlay .inner').css({'margin-top' : (jQuery(window).height() / 2) - (jQuery('#overlay .inner').height() / 2) + 'px' });
    }
	});
}

function resizeOverlay()
{
  jQuery('#overlay .inner').animate({'margin-top' : (jQuery(window).height() / 2) - (jQuery('#overlay .inner').height() / 2) + 'px' });
}

function showForgotSection(it, box) {
	var vis = (box.checked) ? "block" : "none";
	document.getElementById(it).style.display = vis;
}

function listingSum(char, type)
{
    var quantity = jQuery('.qty'+char).val();
    if (type == 'sum')
    {
        quantity++;
    } else {
        if (quantity > 1) {
            quantity--;
        }
    }
    jQuery('.qty'+char).val(quantity);
}
















function changeItensToPruchase() {
    var fullPrice = 0;
    jQuery('.products-page-add-to-cart tbody tr').each(function () {
        var row = jQuery(this);

        var tierPrices = jQuery.parseJSON(row.attr('data-tier'));

        var multiplier = 1;
        var qty = parseInt(row.find('.qty').val());

        if (row.find('.special-price').size() > 0) {
            var price = parseFloat(row.find('.special-price .price').text().replace('R$', '').replace(',', '.')).toFixed(2);
        } else {
            var price = parseFloat(row.find('.regular-price .price').text().replace('R$', '').replace(',', '.')).toFixed(2);
        }


        jQuery.each(tierPrices, function () {
            var tier = this;
            if (tier.price_qty <= qty) {
                price = parseFloat(tier.price);
            }
        });


        if (row.find('.use-box').size() == 1 && row.find('.use-box').is(':checked')) {
            multiplier = parseInt(row.find('.use-box').attr('data-value'));
        }
        if (row.find('.qty').size() == 0) {
            fullPrice += 0;
        } else {
            if (parseInt(row.find('.qty').val()) > 0) {
                row.addClass('available');
            } else {
                row.removeClass('available');
            }
            fullPrice += multiplier * price * qty;
        }
    });
    jQuery('.final-price .price').text('R$' + addPoints(fullPrice.toFixed(2).replace('.', ',')));

}

function addPoints(nStr) {
    nStr += '';
    x = nStr.split(',');
    x1 = x[0];
    x2 = x.length > 1 ? ',' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + '.' + '$2');
    }
    return x1 + x2;
}