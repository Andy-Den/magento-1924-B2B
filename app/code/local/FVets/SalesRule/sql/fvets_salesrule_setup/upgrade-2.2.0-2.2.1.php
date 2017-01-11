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

$this->run('ALTER TABLE salesrule ADD days_from_last_order INT NOT NULL COMMENT \'Dias que a promoção pode rodar após a última compra.\';');

$this->endSetup();
