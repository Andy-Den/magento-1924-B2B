<?php

class FVets_Catalog_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function renderPresentationAttributeOptionFields($product)
	{
		$_productPresentation = null;
		$_multiplyFactorId = null;
		$_multiplyFactor = null;
		$increments = null;
		$product = $product->load($product->getId());
		$_options = $product->getOptions();
		foreach ($_options as $key => $_option) {
			if (!$_option->getIsRequire()) {
				return array('html' => '', 'increments' => 1);
			}
			$_productPresentation = $key;
			foreach ($_option->getValues() as $key => $item) {
				$tmpMultiplyFactor = $item->getMultiplyFactor();

				if ((!$_multiplyFactor) || ($tmpMultiplyFactor && $tmpMultiplyFactor < $_multiplyFactor)) {
					$_multiplyFactorId = $key;
					$_multiplyFactor = $tmpMultiplyFactor;
				}
			}
		}
		$_html = '';
		if (!empty($_options)) {
			if (!$_multiplyFactor || $_multiplyFactor <= 0) {
				$_multiplyFactor = 1;
				$increments = 1;
			} else {
				$increments = $_multiplyFactor;
			}

			$_html = '<input type="hidden" value="' . $_productPresentation . '" id="product_options_id_' . $product->getId() . '"/> <input type="hidden" value="' . $_multiplyFactorId . '" id="product_option_value_id_' . $product->getId() . '"/>';
		}
		return array('html' => $_html, 'increments' => $increments);
	}

	public function getIncrements()
	{
		return $this->increments;
	}

	public function canShowProductPage() {
		
	}
}