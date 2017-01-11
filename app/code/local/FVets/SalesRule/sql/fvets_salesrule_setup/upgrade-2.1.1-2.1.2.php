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

$this->run('UPDATE fvets_salesrule_premier SET `group` = 386 WHERE `group` = \'Golden Gatos Pacoteira\'');
$this->run('UPDATE fvets_salesrule_premier SET `group` = 384 WHERE `group` = \'Nutrição Clínica\'');
$this->run('UPDATE fvets_salesrule_premier SET `group` = 382 WHERE `group` = \'Vitta Natural\'');
$this->run('UPDATE fvets_salesrule_premier SET `group` = 387 WHERE `group` = \'Golden Cão\'');
$this->run('UPDATE fvets_salesrule_premier SET `group` = 385 WHERE `group` = \'Golden Gatos Sacaria\'');
$this->run('UPDATE fvets_salesrule_premier SET `group` = 383 WHERE `group` = \'PremieR\'');

$this->endSetup();
