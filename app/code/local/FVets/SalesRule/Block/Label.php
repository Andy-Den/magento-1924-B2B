<?php

/**
 * Created by PhpStorm.
 * User: julio
 * Date: 10/6/15
 * Time: 5:45 PM
 */
class FVets_SalesRule_Block_Label extends Mage_Core_Block_Template
{

	protected $customer;

	public function __construct()
	{
		parent::__construct();
		$this->setTemplate('catalog/product/label/1.phtml');
	}

	public function _toHtml()
	{
		return parent::_toHtml();
	}

	public function getLabelData()
	{
		if (!$this->getProduct()) {
			return null;
		}

		if (!$this->getCustomer() || !$this->getCustomer()->getId()) {
			return null;
		}

		$labelsCollection = Mage::getModel('fvets_salesrule/label')
			->getCollection()
		;

		$labelsCollection
			->getSelect()
			->join(array("s" => 'salesrule'), "s.rule_id = main_table.salesrule_id", array('description', 'is_mix'))
			->join(array("sw" => 'salesrule_website'), "s.rule_id = sw.rule_id", array('website_id'))
			->joinleft(array("cei" => 'customer_entity_int'), "cei.attribute_id = 217 and entity_type_id = 1 and cei.entity_id = " . $this->getCustomer()->getId(), array())
			->where("main_table.product_id = {$this->getProduct()->getId()} and sw.website_id =
				" . Mage::app()->getWebsite()->getId() .
				//active validation
				" and s.is_active = 1".
				//check flag to block customer campaign
				" and (cei.value is null or cei.value = 0)" .
				//date validation
				" and
				((s.from_date is null and s.to_date is null)
				or (s.from_date is null and now() < s.to_date)
				or (now() > s.from_date and s.to_date is null)
				or (now() between s.from_date and DATE_FORMAT(s.to_date, '%y-%m-%d 23:59:59')))".
				//customer validation - se a regra não for aplicada para todos,
				//verifica se a regra esta vinculada para o customer em questao;
				//Caso não esteja, verificar se o grupo do usuário está vinculado à regra.
				" and  if (s.apply_to_all = 1, true, if((select 1 from fvets_salesrule_customer fsc where fsc.salesrule_id = s.rule_id and fsc.customer_id = " . $this->getCustomer()->getId() . " limit 1), true, (select 1 from salesrule_customer_group scg where scg.rule_id = s.rule_id and scg.customer_group_id = " . $this->getCustomer()->getGroupId() . ")))")
			->order('s.sort_order DESC');

		$labels = array();

		//echo $labelsCollection->getSelect()->__toString();

		foreach ($labelsCollection as $label)
		{
			if (isset($labels[$label['short_name']]))
			{
				array_push($labels[$label['short_name']], $label->getData());
			} else
			{
				$labels[$label['short_name']] = array($label->getData());
			}
		}

		return $labels;
	}

	public function validateCustomerGroup()
	{
		$customerGroupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
		//se o código do grupo do cliente é 11 (grupo dos clientes sem promoção)
		if ($customerGroupId == 11)
		{
			return false;
		} else
		{
			return true;
		}
	}

	private function getCustomer()
	{
		if (!$this->customer)
		{
			$this->customer = Mage::getSingleton('customer/session')->getCustomer();
		}
		return $this->customer;
	}
}