<?php
define('MAGENTOROOT', realpath(dirname(__FILE__)));
require_once '../../../app/Mage.php';
umask(0);
Mage::app('default');


function getUniqueCode($length = "")
{
    $code = md5(uniqid(rand(), true));
    if ($length != "") return substr($code, 0, $length);
    else return $code;
}

Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
atualizaPadrao();

function atualizaPadrao() {
    $website_ids = array(1,2);
    $is_in_stock = 1;

    $product_collection = Mage::getModel('catalog/product')->getCollection();
    foreach($product_collection as $product) {
        $product->setWebsiteIds($website_ids);
        $product->setIsInStock($is_in_stock);
        $product->save();
    }
}

exit;
