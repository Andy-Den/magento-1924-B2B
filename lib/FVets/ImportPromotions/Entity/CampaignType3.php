<?php

/**
 * Created by PhpStorm.
 * User: julio
 * Date: 10/2/15
 * Time: 7:27 PM
 */
//include_once '../Abstracts/Campaign.php';
//include_once '../Interfaces/ICampaign.php';

class Entity_CampaignType3 extends Abstracts_Campaign implements Interfaces_ICampaign
{
	protected $promotionType = 3;
	protected $defaultStopRulesProcessing = 1;
	protected $defaultLabelShortName = 'Promoção';
	protected $defaultLabelDescription = 'Compre acima de R$ %s reais e ganhe %s de desconto';
	protected $requiredFields = array('from_date', 'to_date', 'ids_erp', 'min_value', 'discount_ammount');
	protected $discountType = 'by_percent';

	function _initName()
	{
		$idsErpName = (count(explode(',', $this->getIdsErp())) > 20 ? 'manyIdsErp' : $this->getIdsErp());
		$name = $this->getWebsiteCode() . "_type_" . $this->promotionType . "_idserp_" . $idsErpName . "_minvalue_" . $this->getMinValue() . '_percent_' . $this->getDiscountAmmount();
		return $name;
	}

	function _initDescription()
	{
		if (substr_count($this->getLabelDescription(), '%s') == 2) {
			$description = $this->getHelper()->__($this->getLabelDescription(), $this->getMinValue(), $this->getDiscountAmmount() . '%');
		} else {
			$description = $this->getLabelDescription();
		}
		return $description;
	}

	function _initCondition()
	{
		if($this->getMaxValue()) {
			$condition = ('\'a:7:{s:4:"type";s:32:"salesrule/rule_condition_combine";s:9:"attribute";N;s:8:"operator";N;s:5:"value";s:1:"1";s:18:"is_value_processed";N;s:10:"aggregator";s:3:"all";s:10:"conditions";a:2:{i:0;a:7:{s:4:"type";s:42:"salesrule/rule_condition_product_subselect";s:9:"attribute";s:14:"base_row_total";s:8:"operator";s:2:">=";s:5:"value";s:' . strlen($this->getMinValue()) . ':"' . $this->getMinValue() . '";s:18:"is_value_processed";N;s:10:"aggregator";s:3:"all";s:10:"conditions";a:1:{i:0;a:5:{s:4:"type";s:32:"salesrule/rule_condition_product";s:9:"attribute";s:6:"id_erp";s:8:"operator";s:2:"()";s:5:"value";s:' . strlen($this->getIdsErp()) . ':"' . $this->getIdsErp() . '";s:18:"is_value_processed";b:0;}}}i:1;a:7:{s:4:"type";s:42:"salesrule/rule_condition_product_subselect";s:9:"attribute";s:14:"base_row_total";s:8:"operator";s:2:"<=";s:5:"value";s:' . strlen($this->getMaxValue()) . ':"' . $this->getMaxValue() . '";s:18:"is_value_processed";N;s:10:"aggregator";s:3:"all";s:10:"conditions";a:1:{i:0;a:5:{s:4:"type";s:32:"salesrule/rule_condition_product";s:9:"attribute";s:6:"id_erp";s:8:"operator";s:2:"()";s:5:"value";s:' . strlen($this->getIdsErp()) . ':"' . $this->getIdsErp() . '";s:18:"is_value_processed";b:0;}}}}}\'');
		} else {
			$condition = ('\'a:7:{s:4:"type";s:32:"salesrule/rule_condition_combine";s:9:"attribute";N;s:8:"operator";N;s:5:"value";s:1:"1";s:18:"is_value_processed";N;s:10:"aggregator";s:3:"all";s:10:"conditions";a:1:{i:0;a:7:{s:4:"type";s:42:"salesrule/rule_condition_product_subselect";s:9:"attribute";s:14:"base_row_total";s:8:"operator";s:2:">=";s:5:"value";s:' . strlen($this->getMinValue()) . ':"' . $this->getMinValue() . '";s:18:"is_value_processed";N;s:10:"aggregator";s:3:"all";s:10:"conditions";a:1:{i:0;a:5:{s:4:"type";s:32:"salesrule/rule_condition_product";s:9:"attribute";s:6:"id_erp";s:8:"operator";s:2:"()";s:5:"value";s:' . strlen($this->getIdsErp()) . ':"' . $this->getIdsErp() . '";s:18:"is_value_processed";b:0;}}}}}\'');
		}
		return $condition;
	}

	function _initAction()
	{
		$idsErpToApply = $this->getApplyTo() ? $this->getApplyTo() : $this->getIdsErp();
		$action = ('\'a:7:{s:4:"type";s:40:"salesrule/rule_condition_product_combine";s:9:"attribute";N;s:8:"operator";N;s:5:"value";s:1:"1";s:18:"is_value_processed";N;s:10:"aggregator";s:3:"all";s:10:"conditions";a:1:{i:0;a:5:{s:4:"type";s:32:"salesrule/rule_condition_product";s:9:"attribute";s:6:"id_erp";s:8:"operator";s:2:"()";s:5:"value";s:' . strlen($idsErpToApply) . ':"' . $idsErpToApply . '";s:18:"is_value_processed";b:0;}}}\'');
		return $action;
	}
}