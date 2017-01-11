<?php
define('MAGENTOROOT', realpath(dirname(__FILE__)));
require_once '../../../app/Mage.php';
umask(0);
Mage::app('default');

ini_set('display_errors', 1);
ini_set('max_execution_time', 6000);

setDataSpecialPrice();

function setDataSpecialPrice(){
    Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

    $collection = Mage::getModel('catalog/product')->getCollection()
        ->addStoreFilter();

    echo "Total products found : ".count($collection);

    foreach ($collection as $product)
    {
// Both the Start & End Dates must be in MySQL DB Format
        $startDate = '2014-10-13';
        $endDate = '2014-10-23';

// Sets the Start Date
        $product->setSpecialFromDate($startDate);
        $product->setSpecialFromDateIsFormated(true);

// Sets the End Date
        $product->setSpecialToDate($endDate);
        $product->setSpecialToDateIsFormated(true);

        $product->save();
    }
    echo "<br/> Done!";
}
