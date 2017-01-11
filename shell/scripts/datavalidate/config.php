<?php
require_once '../../../app/Mage.php';
require_once '../../../lib/Zend/Mail/Transport/Sendmail.php';
umask(0);
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

ini_set('display_errors', 1);
ini_set('max_execution_time', 600);

$resource = Mage::getSingleton('core/resource');

$readConnection = $resource->getConnection('core_read');

$writeConnection = $resource->getConnection('core_write');

$deniedWebsites = array(1, 3);
$websitesId = getWebsitesIds();
$deniedData = array('emailPattern' => '@4vets.com.br');
//$collectionType = array('product' => 'catalog/product', 'customer' => 'customer/customer');
$collectionType = array('product' => 'catalog/product', 'customer' => 'customer/customer');

$currentDate = date('Y-m-d');

$directoryExport = "./exportedData/";

function toFile($websiteCode, $fileType, $data)
{
	if (!$data) {
		return;
	}
	global $directoryExport, $currentDate;
	$myfile = fopen($directoryExport . $websiteCode . "_" . $currentDate . "_" . $fileType . ".csv", "w") or die("Unable to open file!");
	fwrite($myfile, $data);
	fclose($myfile);
}

function collectionToStringFormated($collection)
{
	$finalData = '';
	if (is_array($collection)) {
		$header = '';
		foreach ($collection as $item) {
			if (is_string($item)) {
				break;
			}
			foreach ($item as $attributeLabel => $attributeValue) {
				$header .= ($attributeLabel . '|');
			}
			$header = rtrim($header, "|");
			break;
		}

		foreach ($collection as $item) {
			if (is_string($item)) {
				$finalData .= $item . "\n";
				continue;
			}
			foreach ($item as $attributeLabel => $attributeValue) {
				$finalData .= $attributeValue . "|";
			}
			$finalData = rtrim($finalData, "|");
			$finalData .= "\n";
		}
		return ($header . $finalData);
	}

	if (count($collection) == 0) {
		return '';
	}

	$entityDataString = '';
	$header = '';
	$headerDone = false;

	foreach ($collection as $item) {

		$attributesCollection = $item->getData();
		foreach ($attributesCollection as $key => $attributeValue) {
			if (!$headerDone) {
				$header .= $key . '|';
			}
			$entityDataString .= (($item->getData($key)) . '|');
		}
		$headerDone = true;
		$entityDataString .= "\n";
	}

	$finalData = ($header . "\n" . $entityDataString);

	return $finalData;
}

function getWebsiteCode($websiteId)
{
	return Mage::getModel('core/website')->load($websiteId)->getCode();
}

function getStoreidsByWebsiteId($websiteId)
{
	$collection = Mage::getModel('core/store')->getCollection()
		->addFieldToFilter('website_id', $websiteId);
	return $collection->toOptionArray();
}

function getWebsitesIds()
{
	global $deniedWebsites;
	$websites = Mage::getModel('core/website')->getCollection()
		->addFieldToSelect('website_id')
		->addFieldToFilter('website_id', array('nin' => $deniedWebsites));
	$websites = $websites->toOptionArray();
	$arrayReturn = array();
	foreach ($websites as $website) {
		$arrayReturn[] = $website['value'];
	}
	return $arrayReturn;
}

function sendMail($toEmail, $toName, $subject, $message)
{
	try {
		$emailTemplate = Mage::getModel('core/email_template');
		$emailTemplate->setSenderEmail(Mage::getStoreConfig('trans_email/ident_custom2/email', 1));
		$emailTemplate->setSenderName(Mage::getStoreConfig('trans_email/ident_custom2/name', 1));
		$emailTemplate->setTemplateSubject($subject);
		$emailTemplate->setTemplateText($message);

		$emailTemplate->send($toEmail, $toName, array());

		return true;
	} catch (Exception $ex) {
		return $ex->__toString();
	}
}