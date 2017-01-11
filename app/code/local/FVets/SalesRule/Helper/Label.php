<?php

class FVets_SalesRule_Helper_Label extends Mage_Core_Helper_Abstract
{
    public function saveLabel($ruleId, $productId, $shortName)
    {
        $this->cleanLabels($ruleId, $productId);

        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
        $salesruleLabel = "INSERT IGNORE INTO ";
        $salesruleLabel .= "fvets_salesrule_label";
        $salesruleLabel .= "(salesrule_id, product_id, short_name) ";
        $salesruleLabel .= "VALUES ($ruleId, $productId, '$shortName');";
        $connection->query($salesruleLabel);
    }

    private function cleanLabels($salesruleId, $productId)
    {
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
        $removeRegraPromocao = "DELETE s from fvets_salesrule_label s ";
        $removeRegraPromocao .= " WHERE s.salesrule_id = {$salesruleId} and s.product_id = {$productId}";

        $connection->query($removeRegraPromocao);
    }

    public function cleanAllLabels($salesruleId)
    {
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
        $removeRegraPromocao = "DELETE s from fvets_salesrule_label s ";
        $removeRegraPromocao .= "WHERE s.salesrule_id = {$salesruleId}";

        $connection->query($removeRegraPromocao);
    }
}