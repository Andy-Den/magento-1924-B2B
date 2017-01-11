<?php

include_once(Mage::getBaseDir('lib') . '/FVets/ImportPromotions/Interfaces/ICampaign.php');
include_once(Mage::getBaseDir('lib') . '/FVets/ImportPromotions/Abstracts/Campaign.php');
include_once(Mage::getBaseDir('lib') . '/FVets/ImportPromotions/Abstracts/Controller.php');
include_once(Mage::getBaseDir('lib') . '/FVets/ImportPromotions/Entity/CampaignType1.php');
include_once(Mage::getBaseDir('lib') . '/FVets/ImportPromotions/Entity/CampaignType2.php');
include_once(Mage::getBaseDir('lib') . '/FVets/ImportPromotions/Entity/CampaignType3.php');
include_once(Mage::getBaseDir('lib') . '/FVets/ImportPromotions/Entity/CampaignType4.php');
include_once(Mage::getBaseDir('lib') . '/FVets/ImportPromotions/Aux/Functions.php');
include_once(Mage::getBaseDir('lib') . '/FVets/ImportPromotions/Controllers/Importer.php');
include_once(Mage::getBaseDir('lib') . '/FVets/ImportPromotions/Aux/Constants.php');

class FVets_ImportPromotions_Model_Dataflow_Convert_Adapter_Salesrule
	extends Mage_Eav_Model_Convert_Adapter_Entity
{
	/**
	 * Save row
	 *
	 * @return boolean
	 */
	public function saveRow(array $importData)
	{
		$campaignController = new Controllers_Importer();
		$campaign = $campaignController->initCampaign($importData);
		$campaignController->importCampaign($campaign);

		return true;
	}
}