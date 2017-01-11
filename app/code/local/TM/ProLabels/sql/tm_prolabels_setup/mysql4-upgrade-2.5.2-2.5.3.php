<?php
$installer = $this;

$installer->startSetup();

$installer->run("
ALTER TABLE {$this->getTable('prolabels/label')}
    ADD COLUMN `category_alt_text` VARCHAR(200) NULL,
    ADD COLUMN `product_alt_text` VARCHAR(200) NULL;
");

$installer->endSetup();