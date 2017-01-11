<?php
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 10/1/15
 * Time: 5:26 PM
 */

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

class FVets_ImportPromotions_Model_Dataflow_Convert_Parser_Salesrulecsv extends Mage_Dataflow_Model_Convert_Parser_Csv
{

	public function parse()
	{
		parent::parse();
		$this->_validateFileLayout();
	}

	private function _validateFileLayout()
	{
		$lineCount = 1;
		$totalLines = count($this->getBatchImportModel()->getIdCollection());
		$errors = false;

		$campaignController = new Controllers_Importer();

		foreach ($this->getBatchImportModel()->getIdCollection() as $item)
		{
			try
			{
				$importModel = $this->getBatchImportModel()->load($item);
				$batchData = $importModel->getBatchData();
				$batchData['website_code'] = $this->getVars()['website_code'];
				$batchData['default_sort_order'] = $totalLines - $lineCount;

				$campaign = $campaignController->initCampaign($batchData);

				if ($lineCount == $totalLines)
				{
					$campaignController->cleanCampaigns($campaign);
				}

				$importModel->setBatchData($batchData);
				$importModel->save();

				$lineCount++;
			} catch (Exception $ex)
			{
				//echo '[line ' . $lineCount . '] (' . implode('|', $batchData) . ')<br>';
				echo "[line " . $lineCount . " ]";
				//
				echo utf8_decode($ex->getMessage());
				echo '<br>';

				$lineCount++;
				$errors = true;
			}
		}

		if ($errors)
		{
			die(utf8_decode("<p style='color: red;'>* O arquivo só poderá ser importado se não houver nenhum erro de estrutura.</p>"));
		}
	}

}