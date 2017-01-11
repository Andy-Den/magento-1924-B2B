jQuery(document).ready(function () {
    productBind();

});

function productBind()
{
    jQuery('.quantity-all-box .qty-action').unbind();
    jQuery('.quantity-all-box .qty-action').bind({
        click: function() {
            if (jQuery(this).hasClass('sum')) {
                jQuery('.'+jQuery(this).attr('data-boxname')+' .quantity').val(parseInt(jQuery('.'+jQuery(this).attr('data-boxname')+' .quantity').val()) + 1);
            }
            if (jQuery(this).hasClass('subtraction')) {
                if(parseInt(jQuery('.'+jQuery(this).attr('data-boxname')+' .quantity').val()) > 0)
                    jQuery('.'+jQuery(this).attr('data-boxname')+' .quantity').val(parseInt(jQuery('.'+jQuery(this).attr('data-boxname')+' .quantity').val()) - 1);
            }
            changeItensToPruchase();
        }
    })

    jQuery('.button-add-to-cart2').unbind();
    jQuery('.button-add-to-cart2').bind({
        click: function() {
            /*jQuery.ajax({
             url: '/ajaxcart/index/add/qty/'+jQuery('.'+jQuery(this).attr('data-boxname')+' .quantity').val()+'/product/'+jQuery(this).attr('data-id'),
             beforeSend: function() {
             if (jQuery('.header-wrapper').is('.fixed-top')) {
             jQuery('.header-wrapper .cart-bt .cart-table').slideDown();
             } else {
             jQuery('.cart-bt .cart-table').slideDown();
             }
             jQuery('.ajax-image').show();
             jQuery('.cart-bt').addClass('hovered');
             },
             success: function(data) {
             //var returnData = jQuery.parseJSON(data);
             //messages += returnData.message + '<br />';
             reloadCart();
             }
             });*/
            addToCartById(jQuery(this).attr('data-id'), jQuery('.'+jQuery(this).attr('data-boxname')+' .quantity').val());

        }
    })
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