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

$this->run('ALTER TABLE salesrule CHANGE first_sale_only only_on_sale INT;');

$this->endSetup();
