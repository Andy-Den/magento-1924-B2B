<?php

class FVets_BlockLinks_Block_Widget_Onepagecatalog_Category extends Mage_Catalog_Block_Product_List
{
	public function getToolbarHtml()
	{
		return null;
	}

	public function getAjaxAddToCartUrl($product, $additional = array()) {
		return $this->helper('checkout/cart')->getAddUrl($product, $additional);
	}
}

?>