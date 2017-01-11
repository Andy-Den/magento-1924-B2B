<?php

//Load Magento API
require_once '../../../../../app/Mage.php';
Mage::app();

//First we load the model
$model = Mage::getModel('fvets_salesrule/salesrule_premier_cron_customersWithAutomaticPremierPolicy');

//Then execute the task
$model->run();