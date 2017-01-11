/* http://daneden.github.io/animate.css/ */
function doAnim(element, animation) {
    jQuery(element).removeClass(animation + ' animated').addClass(animation + ' animated').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){
        jQuery(this).removeClass(animation + ' animated');
    });
};