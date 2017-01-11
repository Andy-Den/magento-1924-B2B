<?php
/**
 * FVets_SalesRule extension
 * 
 * @category       FVets
 * @package        FVets_SalesRule
 */
/**
 * SalesRule module install script
 *
 * @category    FVets
 * @package     FVets_SalesRule
 * @author      Douglas Borella Ianitsky
 */
$this->startSetup();

$this->run('ALTER TABLE fvets_salesrule_premier ADD calculation_type VARCHAR(20) NOT NULL COMMENT \'Calculation Type\';');

$this->run('UPDATE fvets_salesrule_premier as t1 SET t1.calculation_type = \'weight\'');

$this->run('ALTER TABLE fvets_salesrule_premier DROP COLUMN attribute_id;');


$this->endSetup();
