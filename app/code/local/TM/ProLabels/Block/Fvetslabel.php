<?php

class TM_ProLabels_Block_Fvetslabel extends Mage_Core_Block_Template
{
	public function __construct()
	{
		parent::__construct();
		$this->setTemplate('tm/prolabels/label/label.phtml');
	}

	public function _toHtml()
	{
		return parent::_toHtml();
	}

	public function getShortDescription($product, $mode = 'product')
	{
		if (!Mage::getStoreConfig("prolabels/general/enabled")) {
			return;
		}

		if ($this->getProduct()) {
			$product = $this->getProduct();
		}

		if ($this->getMode()) {
			$mode = $this->getMode();
		}

		$model = Mage::getModel('prolabels/label');
		$labelsData = $model->getLabelsData($product->getId(), $mode);

		$html = '';
		foreach ($labelsData as $data) {
			if ($data['system_label'] == 1) {
				continue;
			}
			$_labelText = $data[$mode . "_image_text"];
			if (empty($_labelText)) {
				continue;
			}
			$html = $_labelText;
		}
		return $html;
	}

	public function getDescription($product, $mode = 'product')
	{
		if (!Mage::getStoreConfig("prolabels/general/enabled")) {
			return;
		}

		if ($this->getProduct()) {
			$product = $this->getProduct();
		}

		if ($this->getMode()) {
			$mode = $this->getMode();
		}

		$model = Mage::getModel('prolabels/label');
		$labelsData = $model->getLabelsData($product->getId(), $mode);

		$html = '';
		foreach ($labelsData as $data) {
			if ($data['system_label'] == 1) {
				continue;
			}
			$_altText = $data[$mode . "_alt_text"];
			if (empty($_altText)) {
				continue;
			}
			$html .= $_altText . '<br/>';
		}
		return $html;
	}

	public function getLabels($product, $mode = 'product') {
		if (!Mage::getStoreConfig("prolabels/general/enabled")) {
			return;
		}

		if ($this->getProduct()) {
			$product = $this->getProduct();
		}

		if ($this->getMode()) {
			$mode = $this->getMode();
		}

		$model = Mage::getModel('prolabels/label');
		$labelsData = $model->getLabelsData($product->getId(), $mode);

		$html = '';
		$labels = array();
		foreach ($labelsData as $data) {
			if ($data['system_label'] == 1) {
				continue;
			}

			if (isset($labels[$data['category_image_text']])) {
				array_push($labels[$data['category_image_text']], $data);
			} else {
				$labels[$data['category_image_text']] = array($data);
			}

		}
		return $labels;
	}

	public function getUrlImage($product, $mode = 'product')
	{
		if (!Mage::getStoreConfig("prolabels/general/enabled")) {
			return;
		}

		if ($this->getProduct()) {
			$product = $this->getProduct();
		}

		if ($this->getMode()) {
			$mode = $this->getMode();
		}

		$model = Mage::getModel('prolabels/label');
		$labelsData = $model->getLabelsData($product->getId(), $mode);

		$html = '';
		foreach ($labelsData as $data) {
			if ($data['system_label'] == 1) {
				continue;
			}
			$labelImg = $data[$mode . "_image"];
			if (empty($labelImg)) {
				continue;
			}
			$html = Mage::getBaseUrl('media') . 'prolabel/' . $labelImg;
		}
		return $html;
	}

	public function validateCustomerGroup()
	{
		$customerGroupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
		//se o código do grupo do cliente é 11 (grupo dos clientes sem promoção)
		if ($customerGroupId == 11) {
			return false;
		} else {
			return true;
		}
	}
}