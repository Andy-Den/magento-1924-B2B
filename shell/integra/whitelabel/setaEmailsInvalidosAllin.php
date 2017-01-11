<?php
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 5/14/15
 * Time: 11:18 AM
 */
require_once __DIR__ . '/../../../app/Mage.php';

umask(0);
Mage::app('default');

ini_set('display_errors', 1);
ini_set('max_execution_time', 6000);
umask(0);

$scriptName = '[All In]';


$stringReturn = '';

$trashCollection = Mage::getModel('fvets_allin/trash')->getCollection()
	->addFieldToFilter('status', 0);

$count = 0;
$helper = Mage::helper('fvets_allin');

foreach ($trashCollection as $trash)
{
	$account = Mage::getModel('fvets_allin/account')->load($trash->getIdAccount());
	$websiteId = $account->getWebsiteId();
	try
	{
		$ok = Mage::getModel('fvets_allin/customers')->remoteCleanTrash($account->getId(), $trash->getEmail(), false);
		if ($ok)
		{
			$trash->setStatus(1);
			$trash->save();
			$count++;
		}
	} catch (Exception $ex)
	{
		//criar log
		$helper->log("[seta_emails_invalidos_allin] " . $ex->__toString());
	}
}

$stringReturn = 'Customers Atualizados para "Inválidos" no Allin: ' . $count;
Mage::helper('datavalidate')->sendSlackMessage('scripts', $scriptName . " <". $_SERVER['PHP_SELF'] . ">\n\n" . $stringReturn);