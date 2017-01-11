<?php
$installer = $this;
$installer->startSetup();


$installer->addAttribute("customer", "credit_limit", array(
    "type" => "decimal",
    "backend" => "",
    "label" => "Limite de crédito do cliente",
    "input" => "text",
    "visible" => true,
    "required" => false,
    "default" => "",
    "frontend" => "",
    "unique" => false,
    "note" => "",
    'backend_type' => 'decimal',
    'frontend_input' => 'text',

));

$attribute = Mage::getSingleton("eav/config")->getAttribute("customer", "credit_limit");

$used_in_forms = array();

$used_in_forms[] = "adminhtml_customer";
$attribute->setData("used_in_forms", $used_in_forms)
    ->setData("is_used_for_customer_segment", true)
    ->setData("is_system", 0)
    ->setData("is_user_defined", 1)
    ->setData("is_visible", 1)
    ->setData("sort_order", 100);
$attribute->save();

$installer->addAttribute("customer", "payment_block", array(
    "type" => "decimal",
    "backend" => "",
    "label" => "Título vencido",
    "input" => "text",
    "visible" => true,
    "required" => false,
    "default" => "",
    "frontend" => "",
    "unique" => false,
    "note" => "",
    'backend_type' => 'decimal',
    'frontend_input' => 'text',

));

$attribute = Mage::getSingleton("eav/config")->getAttribute("customer", "payment_block");

$used_in_forms = array();

$used_in_forms[] = "adminhtml_customer";
$attribute->setData("used_in_forms", $used_in_forms)
    ->setData("is_used_for_customer_segment", true)
    ->setData("is_system", 0)
    ->setData("is_user_defined", 1)
    ->setData("is_visible", 1)
    ->setData("sort_order", 100);
$attribute->save();

$installer->endSetup();