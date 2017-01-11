<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */

$this->startSetup();

$this->updateAttribute('catalog_product', 'amrolepermissions_owner', 'source_model', 'core/source_rolepermissions_admins');

$this->endSetup();
