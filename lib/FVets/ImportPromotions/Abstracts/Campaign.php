<?php

/**
 * Created by PhpStorm.
 * User: julio
 * Date: 10/2/15
 * Time: 7:43 PM
 */

//require_once '../../../../app/Mage.php';
//include_once '../Aux/Functions.php';

class Abstracts_Campaign extends Varien_Object
{

	protected $resource;
	protected $readConnection;
	protected $writeConnection;
	protected $functions;
	protected $helper;

	protected $prefixCampaignName = 'manual_';

	function initCampaign($params)
	{
		if (!isset($params['website_code']))
		{
			Mage::throwException('No campo "website_code", é obrigatório ter um valor válido');
		}

		$this->_initResources();

		foreach ($params as $key => $param)
		{
			$key = $this->getFunctions()->getTranslatedFromTo($key);
			if ($key)
			{
				$this->setData($key, $param);
			}
		}
		$this->_initWebsite();
		$this->_initStoreviews();
		$this->_validate();
		$this->_initDefaultValues();

		$this->setData('name', $this->getPrefixCampaignName() . $this->_initName());
		$this->setData('description', $this->_initDescription());
		$this->setData('condition', $this->_initCondition());
		$this->setData('action', $this->_initAction());
		$this->setData('values', $this->_initValues());

		return $this;
	}

	private function _initWebsite()
	{
		$this->setWebsite($this->getFunctions()->getWebsiteByCode($this->getWebsiteCode()));
	}

	private function _initStoreviews()
	{
		$stores = $this->getWebsite()->getStoreCollection();

		$storesArray = array();
		foreach ($stores as $store)
		{
			$storesArray[] = $store->getId();
		}

		$this->setStoreviews(implode(',', $storesArray));
	}

	function _initValues()
	{
		$values = "VALUES('{$this->getName()}', '{$this->getDescription()}', STR_TO_DATE('{$this->getFromDate()}','{$this->getDatePattern()}'), STR_TO_DATE('{$this->getToDate()}','{$this->getDatePattern()}'), 0, 1, {$this->getCondition()}, {$this->getAction()}, {$this->getStopRulesProcessing()}, 1, NULL, {$this->getSortOrder()}, '{$this->discountType}', {$this->getDiscountAmmount()}, NULL, {$this->getDiscountStep()}, 0, 0, 0, 0, 1, 0, 0, '{$this->getPromoSku()}', 3, {$this->getIsMix()});";
		return $values;
	}

	function getResource()
	{
		return $this->resource;
	}

	function getReadConnection()
	{
		return $this->readConnection;
	}

	function getWriteConnection()
	{
		return $this->writeConnection;
	}

	function getFunctions()
	{
		return $this->functions;
	}

	function getHelper()
	{
		return $this->helper;
	}

	function createRule()
	{
		$this->_beforeSave();

		/**
		 * Verifica se a regra já existe
		 */
		$checkRule = "SELECT rule_id FROM ";
		$checkRule .= $this->getResource()->getTableName('salesrule');
		$checkRule .= " WHERE name = '{$this->getData('name')}'";

		$idRule = $this->getReadConnection()->fetchOne($checkRule);

		if (empty($idRule))
		{
			$setDescount = "INSERT INTO ";
			$setDescount .= $this->getResource()->getTableName('salesrule');
			$setDescount .= " (name, description, from_date, to_date, uses_per_customer, is_active, conditions_serialized, actions_serialized, stop_rules_processing, is_advanced, product_ids, sort_order, simple_action, discount_amount, discount_qty, discount_step, simple_free_shipping, apply_to_shipping, times_used, is_rss, coupon_type, use_auto_generation, uses_per_coupon, promo_sku, rule_type, is_mix)";
			$setDescount .= $this->getData('values');

			$this->getWriteConnection()->query($setDescount);
		}

		// Pega o rule_id da ultima regra adicionada
		$ruleId = $this->getLastRuleIdAdded();

		/**
		 * Associa a regra com o website
		 */
		$associaRegraPromocao = "INSERT IGNORE INTO ";
		$associaRegraPromocao .= $this->getResource()->getTableName('salesrule_website');
		$associaRegraPromocao .= " (rule_id, website_id) ";
		$associaRegraPromocao .= "VALUES ($ruleId, {$this->getWebsite()->getId()});";

		$this->getWriteConnection()->query($associaRegraPromocao);

		//defininr quais grupos de clientes serão vinculados
		$groups = array(1);
		$iGroups = count($groups);
		$ln = 1;
		while ($ln <= $iGroups)
		{
			$groupId = $groups[$ln - 1];

			/**
			 * Alimenta a tabela de ligacao regra de promocao atributo do produto
			 */
			$ligacaoProductAtribute = "INSERT IGNORE INTO ";
			$ligacaoProductAtribute .= $this->getResource()->getTableName('salesrule_product_attribute');
			$ligacaoProductAtribute .= " (rule_id, website_id, customer_group_id, attribute_id) ";
			$ligacaoProductAtribute .= "VALUES ($ruleId, {$this->getWebsite()->getId()}, $groupId, 74);";

			$this->getWriteConnection()->query($ligacaoProductAtribute);

			/**
			 * Define os grupos de clientes que terão acesso a regra
			 */
			// adiciona regra para todos os grupos de cliente da distribuidora

			$customerGroups = Mage::getModel('customer/group')->getCollection()
				->addFieldToFilter('website_id', $this->getWebsite()->getId());

			foreach ($customerGroups as $customerGroup)
			{
				$customerGroupId = $customerGroup->getCustomerGroupId();

				$grupoClienteRegra = "INSERT IGNORE INTO ";
				$grupoClienteRegra .= $this->getResource()->getTableName('salesrule_customer_group');
				$grupoClienteRegra .= "(rule_id, customer_group_id) ";
				$grupoClienteRegra .= "VALUES ($ruleId, $customerGroupId);";
				$this->getWriteConnection()->query($grupoClienteRegra);
			}

			$ln++; // incremento para contagem dos grupos de cliente
		}
		//echo "+";

	}

	function createLabel($idErp = null)
	{
		if ($idErp)
		{
			$product = $this->getFunctions()->getProductByIdErp($idErp, $this->getWebsite()->getId());
			if ($product && $product->getSku())
			{
				$ruleId = $this->getLastRuleIdAdded();
				$this->saveLabel($ruleId, $product->getId(), $this->getLabelShortName());
			}
		} else
		{
			foreach (explode(',', $this->getIdsErp()) as $item)
			{
				$product = $this->getFunctions()->getProductByIdErp(trim($item), $this->getWebsite()->getId());
				if ($product && $product->getSku())
				{
					$ruleId = $this->getLastRuleIdAdded();
					$this->saveLabel($ruleId, $product->getId(), $this->getLabelShortName());
				}
			}
		}
		$this->_afterSave();
	}

	private function saveLabel($ruleId, $productId, $shortName)
	{
		$salesruleLabel = "INSERT IGNORE INTO ";
		$salesruleLabel .= $this->getResource()->getTableName('fvets_salesrule_label');
		$salesruleLabel .= "(salesrule_id, product_id, short_name) ";
		$salesruleLabel .= "VALUES ($ruleId, $productId, '$shortName');";
		$this->getWriteConnection()->query($salesruleLabel);
	}

	private function getLastRuleIdAdded()
	{
		// Pega o rule_id da ultima regra adicionada
		$getRuleId = "SELECT rule_id FROM ";
		$getRuleId .= $this->getResource()->getTableName('salesrule');
		$getRuleId .= " where name like '{$this->getPrefixCampaignName()}{$this->getWebsiteCode()}%' ORDER BY rule_id DESC LIMIT 1;";
		$ruleId = $this->getReadConnection()->fetchOne($getRuleId);
		return $ruleId;
	}

	function cleanCampaigns()
	{
		/**
		 * Remove todas as regras deste website
		 */
		$removeRegraPromocao = "DELETE s from salesrule s ";
		$removeRegraPromocao .= "join salesrule_website sw on sw.rule_id = s.rule_id
														 WHERE (s.rule_type = 3 and sw.website_id = " . $this->getWebsite()->getWebsiteId() . ")" .
														 " or (s.name LIKE '" . $this->getPrefixCampaignName() . ($this->getWebsiteCode() . "%") . "')";

		$this->writeConnection->query($removeRegraPromocao);

		//labels serão removidos por cascata
	}

	function _validate()
	{
		$errors = array();
		foreach ($this->requiredFields as $field)
		{
			if (!$this->getData($field) || $this->getData($field) == 'NULL')
			{
				$errors[] = '<p>O campo <strong>"' . $this->getFunctions()->getTranslatedToFrom($field) . '"</strong> é obrigatório para o tipo de campanha ' . $this->promotionType . '</p>';
			}
		}

		if (!empty($errors))
		{
			Mage::throwException(implode('', $errors));
		}
	}

	protected function _initResources()
	{
		$this->resource = Mage::getSingleton('core/resource');
		$this->readConnection = $this->resource->getConnection('core_read');
		$this->writeConnection = $this->resource->getConnection('core_write');
		$this->functions = new Aux_Functions();
		$this->helper = Mage::helper('fvets_importpromotions');
	}


	protected function _initDefaultValues()
	{
		if (!$this->getStopRulesProcessing())
		{
			$this->setStopRulesProcessing($this->defaultStopRulesProcessing);
		}

		if (!$this->getSortOrder())
		{
			$this->setSortOrder($this->getDefaultSortOrder());
		}

		if (!$this->getLabelShortName())
		{
			$this->setLabelShortName($this->defaultLabelShortName);
		}

		if (!$this->getLabelDescription())
		{
			$this->setLabelDescription($this->defaultLabelDescription);
		}

		if (!$this->getDiscountAmmount())
		{
			$this->setDiscountAmmount($this->defaultDiscountAmmount);
		}

		if (!$this->getPromoSku())
		{
			$this->setPromoSku('NULL');
		}

		if (!$this->getDiscountStep())
		{
			$this->setDiscountStep(0);
		}

		if($this->getPrefixCampaignName() === null) {
			$this->setPrefixCampaignName($this->prefixCampaignName);
		}

		if ($this->getIdsErp() && count(explode(',', $this->getIdsErp())) > 1) {
			$this->setIsMix(1);
		} else {
			$this->setIsMix(0);
		}
	}

	protected function getDatePattern()
	{
		return Aux_Constants::$datePattern;
	}

	protected function _beforeSave()
	{

	}

	protected function _afterSave()
	{

	}
}