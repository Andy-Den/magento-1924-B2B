<?php

require_once '../../app/Mage.php';
umask(0);
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

ini_set('display_errors', 1);
ini_set('max_execution_time', 600);

$_toNewCode = 'modelo';
$_toNewName = 'Modelo';

$_createNewWebsite = true;
//Se $_createNewWebsite for "false"
$_copyToWebsiteCode = 'vetcenter';
//end
$_createNewStore = true;
//Se $_createNewStore for "false"
$_copyToStoreName = 'Vet Center Store';
//end
$_fromStoreviewCode = 'compet';

$_toCategoryName = 'MARCAS';
$_toWebSiteName = $_toNewName . ' Website';
$_toStoreName = $_toNewName . ' Store';
$_toStoreViewName = $_toNewName;

$_copyCmsAndStaticBlocks = false;