jQuery(document).ready(function() {
	bindCartQty();
	bindCoupon();
});

function bindCartQty()
{
	if (jQuery('.qty') != undefined)
	{
		var qtyArray = new Array();
		jQuery('.qty').each(function(index, value)
		{
			qtyArray[index] = jQuery(this).val();
		});

		jQuery('.qtyminus,.qtyplus').click(function()
		{
			jQuery('.qty').each(function(index, value)
			{
				if (jQuery(this).val() != qtyArray[index])
					{
						jQuery('.btn-product-'+jQuery(this).data('id')).removeClass('disabled');
					}
					else
					{
						jQuery('.btn-product-'+jQuery(this).data('id')).addClass('disabled');
					}
			});
		});
	}
}

function bindCoupon()
{
 if (jQuery('#coupon_code') != undefined)
 {
 		var value = jQuery('#coupon_code').val();
		jQuery('#coupon_code').keyup(function () {
			if (jQuery(this).val() != value)
			{
				jQuery('.btn-applyCoupon').removeClass('disabled');
			}
			else
			{
				jQuery('.btn-applyCoupon').addClass('disabled');
			}
		});
 }
}