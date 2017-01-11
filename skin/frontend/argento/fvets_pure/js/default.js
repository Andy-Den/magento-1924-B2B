jQuery(document).ready(function() {
    bindQty();
    bindMasks();
    bindLazy();
    bindCartPopup();
    bindProductAddtocartForm();
    jQuery(document).trigger('resizeBoxes');
});

jQuery(window).load(function(){
    jQuery(document).trigger('resizeBoxes');
});

function bindQty()
{
    // This button will increment the value
    jQuery('.qtyplus').each(function() {
        jQuery(this).click(function(e) {
            if(e.handled !== true)
            {
                // Stop acting like a button
                e.preventDefault();
                // Get the field name
                fieldName = jQuery(this).attr('field');
                // Get its current value
                var currentVal = parseInt(jQuery('input[id='+fieldName+']').val());
                // Get increments
                var increments = parseInt(jQuery('input[id='+fieldName+']').attr('data-increments'));
                // If is not undefined
                if (!isNaN(currentVal)) {
                    // Increment
                    jQuery('input[id='+fieldName+']').val(currentVal + increments);
                } else {
                    // Otherwise put a 0 there
                    jQuery('input[id='+fieldName+']').val(0);
                }
                e.handled = true;
            }
        });
    });

    jQuery(".qtyminus").each(function() {
        jQuery(this).click(function(e) {
            if (e.handled !== true) {
                // Stop acting like a button
                e.preventDefault();
                // Get the field name
                fieldName = jQuery(this).attr('field');
                // Get its current value
                var currentVal = parseInt(jQuery('input[id=' + fieldName + ']').val());
                // Get increments
                var increments = parseInt(jQuery('input[id='+fieldName+']').attr('data-increments'));
                // If it isn't undefined or its greater than 0
                if (!isNaN(currentVal) && currentVal > increments) {
                    // Decrement one
                    jQuery('input[id=' + fieldName + ']').val(currentVal - increments);
                } else {
                    // Otherwise put a 0 there
                    jQuery('input[id=' + fieldName + ']').val(increments);
                }
                e.handled = true;
            }
        });

    });

}

function bindMasks() {
    /** All input type tel have masked */
    jQuery('input[type="tel"]').phoneMaskBrazil();

    var options =  {
        onComplete: function(val, e, field, options) {
            if (jQuery(field).data('actionbutton'))
            {
                activateDoToActionButton(jQuery(field).data('actionbutton'));
            }
        },
        onKeyPress: function(val, e, field, options){
            if (jQuery(field).data('actionbutton'))
            {
                deactivateDoToActionButton(jQuery(field).data('actionbutton'));
            }
        }
    };

    jQuery(".mask-cpf").mask('999.999.999-99', options);
    jQuery(".mask-cnpj").mask('99.999.999/9999-99', options);
    jQuery(".mask-zip").mask('99999-999', options);
}

/** Ativar ou desativar botões de ação */

function activateDoToActionButton(button) {
    jQuery(button).removeClass('disabled');
}

function deactivateDoToActionButton(button) {
    jQuery(button).addClass('disabled');
}

/** Lazy Load */

function bindLazy() {
    jQuery("img.lazy").lazyload({
        skip_invisible : false,
        threshold : 200,
        load: function() {
            jQuery(document).trigger('resizeBoxes');
        }
    });
}

/** funcao para adicionar uma quantidade especifica no carrinho direto na pagina de catalogo */
function addToCart(url, id) {

    var qty = jQuery('#qty-'+id).val();

    if (isNumeric(qty) && qty > 0) {
        url += 'qty/' + qty + '/';

        return setLocation(url);
    } else {
        var uniqid = 'alert_' + Math.floor(Math.random()* 1000000);
        jQuery('body').append('<div id="'+uniqid+'" style="position:fixed;padding:10px;background:red;color:white;font-weight: bold">Por favor, insira uma quantidade válida para este produto!</div>');
        //jQuery('#'+uniqid).css('top',jQuery(jQuery('#qty-'+id)).offset().top + 'px');
        jQuery('#'+uniqid).css('top',jQuery(jQuery('#qty-'+id)).position().top + 'px');
        jQuery('#'+uniqid).css('left',jQuery(jQuery('#qty-'+id)).offset().left + 'px');
        setTimeout(function(){ jQuery('#'+uniqid).remove(); }, 3000);
        return null;
    }

}

function isNumeric(n) {
    return !isNaN(parseFloat(n)) && isFinite(n);
}

function bindProductAddtocartForm()
{
    //para página de produtos
    if (jQuery('#product_addtocart_form') != undefined)
    {
        jQuery('#product_addtocart_form').bind( "submit", function(event) {
            addToCart(jQuery(this).attr('action'), jQuery(this).find('input[name="product"]').val());
            return false;
        });
    }

    //para página de catálogo
    var formProductListItems = jQuery('.products-list .item .form-container form');
    if (formProductListItems) {
        formProductListItems.each(function(index) {
            jQuery(this).bind("submit", function(event) {
                //jQuery(this).find('.btnAddCart button').click();
                var productId = jQuery(this).find('input[name="quantity"]').attr("id").split('-')[1];
                addToCart(jQuery(this).attr('action'), productId);
                return false;
            });
        });
    }
}

function bindCartPopup() {
    jQuery('#header-cart').bind('DOMNodeInserted DOMNodeRemoved', function(event) {
        doAnim(".headerCartTop", "tada");

        if (jQuery('#header-cart-mobile') != undefined)
        {
            jQuery('#header-cart-mobile .summary-qty').html(jQuery('#header-cart .summary-qty').html());
            doAnim("#header-cart-mobile", "tada");
        }
    });
    setTimeout("bindCartPopup()", 1000);
}


/* http://daneden.github.io/animate.css/ */
function doAnim(element, animation) {
    jQuery(element).removeClass(animation + ' animated').addClass(animation + ' animated').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){
        jQuery(this).removeClass(animation + ' animated');
    });
};

function subHeaderPosition() {

    if (jQuery('.header').offset() != undefined) {
        if (jQuery(document).scrollTop() > jQuery('.header').outerHeight() + jQuery('.header').offset().top) {
            jQuery('.sub-header').addClass('fixed');
        } else {
            jQuery('.sub-header').removeClass('fixed');
        }
    }
}

//reseta o input de quantidades e reseta a cor do botão de add ao carrinho
function restartQtyInput(el) {
    jQuery('#header-cart').bind('DOMNodeInserted DOMNodeRemoved', function(event) {
        fieldName = jQuery(el).attr("id").substr(4);
        jQuery('input[id=qty-'+fieldName+']').val(1);
        jQuery('form[id=product_addtocart_form_'+fieldName+'] .btnAddCart button.button > span').css('background', 'none repeat scroll 0 0 #b9b9b9');
    });
    setTimeout("bindCartPopup()", 1000);
}