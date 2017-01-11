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

$this->run('ALTER TABLE fvets_salesrule_premier MODIFY COLUMN `group` INT(11) NULL COMMENT \'Premier rules group\';');

$this->endSetup();
