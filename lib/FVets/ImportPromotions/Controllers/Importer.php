<?php

class Controllers_Importer extends Abstracts_Controller
{
	function initCampaign($batchData)
	{
		$functions = new Aux_Functions();

		if (isset($batchData[$functions->getTranslatedToFrom('promotion_type')]))
		{
			$promotionType = $batchData[$functions->getTranslatedToFrom('promotion_type')];
		} else
		{
			$promotionType = null;
		}

		if (!$promotionType)
		{
			Mage::throwException('No campo "promotion_type", é obrigatório ter um valor válido');
		}
		$campaignType = $batchData[$functions->getTranslatedToFrom('promotion_type')];
		$campaignClassName = 'Entity_CampaignType' . $campaignType;

		$campaign = new $campaignClassName();
		return $campaign->initCampaign($batchData);
	}

	function cleanCampaigns(Interfaces_ICampaign $campaign)
	{
		return $campaign->cleanCampaigns();
	}

	function importCampaign(Interfaces_ICampaign $campaign)
	{
		try
		{
			$campaign->createRule();
			$campaign->createLabel();
		} catch (Exception $ex)
		{
			Mage::log($ex->__toString(), 'fvets_importpromotions.log', true);
			Mage::throwException($ex->__toString());
		}
	}
}