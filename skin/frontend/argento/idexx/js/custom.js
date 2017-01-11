/**
 * Created by julio on 7/16/15.
 */
jQuery(document).ready(function () {

    //código para reajuste do tamanho das logos das categorias
//    var qtd = jQuery('.brands-list li').length;
//    if (qtd < 6) {
//        jQuery('.brands-list li').css('width', (100-(qtd*2))/qtd + '%');
//    }
    //fim

    if (jQuery('.cms-index-index.logged-out') != undefined) {
        var height = jQuery('.blocklinks-blocks.second').innerHeight();
        jQuery('.footer-cms-container').css('margin-top', height);
    }
});