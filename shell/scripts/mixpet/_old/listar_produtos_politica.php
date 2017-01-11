<?php
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 5/31/16
 * Time: 4:02 PM
 */

require_once './configScript.php';

$premierRules = Mage::getModel('fvets_salesrule/salesrule_premier')->getCollection();

echo "Rule ID|Website ID|Attribute Code|Values|Calculation Type|From|To\n";
foreach ($premierRules as $premierRule) {
	$salesruleId = $premierRule->getSalesruleId();

	$rule = Mage::getModel('salesrule/rule')->load($salesruleId);

	$unserialized = unserialize($rule->getConditionsSerialized());
	$condition = getRuleConditionProduct($unserialized);
	echo $salesruleId . "|" . implode(',', $rule->getWebsiteIds()) . "|" . $condition['attribute'] . "|" . $condition['value'] . "|" . $premierRule->getCalculationType() . "|" . $premierRule->getFrom() . "|" . $premierRule->getTo() . "\n";
}

function getRuleConditionProduct($rule)
{
	if (isset($rule['conditions'])) {
		foreach ($rule['conditions'] as $condition) {
			if ($condition['type'] == 'salesrule/rule_condition_product') {
				return $condition;
			} else {
				return getRuleConditionProduct($condition);
			}
		}
	}

	return false;
}