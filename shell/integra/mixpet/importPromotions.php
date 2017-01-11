<?php

require_once './configIntegra.php';

require_once '../_functions/importPromotions.php';

require_once "{$pathBase}/lib/FVets/ImportPromotions/Aux/Constants.php";
require_once "{$pathBase}/lib/FVets/ImportPromotions/Aux/Functions.php";
require_once "{$pathBase}/lib/FVets/ImportPromotions/Abstracts/Controller.php";
require_once "{$pathBase}/lib/FVets/ImportPromotions/Interfaces/ICampaign.php";
require_once "{$pathBase}/lib/FVets/ImportPromotions/Abstracts/Campaign.php";
require_once "{$pathBase}/lib/FVets/ImportPromotions/Entity/CampaignType1.php";
require_once "{$pathBase}/lib/FVets/ImportPromotions/Entity/CampaignType2.php";
require_once "{$pathBase}/lib/FVets/ImportPromotions/Entity/CampaignType3.php";
require_once "{$pathBase}/lib/FVets/ImportPromotions/Entity/CampaignType4.php";
require_once "{$pathBase}/lib/FVets/ImportPromotions/Controllers/Importer.php";

$files = array();
$campaignController = new Controllers_Importer();

/** Lê o arquivo CSV para montar o array dos produtos em promoção */
if ($local == true)
{
	$files[] = file("$testDirectoryImport/promotions/promotions.csv", FILE_IGNORE_NEW_LINES);
} else
{
	$files[] = file("$directoryImport/promotions/promotions.csv", FILE_IGNORE_NEW_LINES);
}

$countEmptyFiles = 0;
foreach ($files as $file)
{
	if (empty($file))
	{
		$countEmptyFiles++;
	}
}

$priority = 1000;

if (count($files) == $countEmptyFiles)
{
	echo "\n\n" . "não existe nenhum arquivo csv de campanhas" . "\n\n";
} else
{
	try
	{
		foreach ($files as $file)
		{
			if (!$file)
			{
				continue;
			}

			cleanCampaigns();

			$promotions = array();

			$priority = count($file);

			//lê todas as linhas do arquivo
			foreach ($file as $key => $value)
			{
				$temp = str_getcsv($value, '|', "'");

				$params = array();
				$params['tipo_promocao'] = $temp[0];
				$params['data_inicio'] = $temp[1];
				$params['data_fim'] = $temp[2];
				$params['ids_erp'] = $temp[3];
				$params['qtd_minima'] = $temp[4];
				$params['percento_desconto'] = $temp[5];
				$params['produtos_bonificados'] = $temp[6];
				$params['qtd_bonificado'] = $temp[7];
				$params['valor_minimo'] = $temp[8];
				$params['id_categoria'] = $temp[9];

				$dateTime = DateTime::createFromFormat('Y/m/d', $params['data_inicio']);
				$dataInicio = $dateTime->format('d/m/Y');

				$dateTime = DateTime::createFromFormat('Y/m/d', $params['data_fim']);
				$dataFim = $dateTime->format('d/m/Y');

				$idErp = (int)$skuErp;
				$importData = array("tp_promocao" => $params['tipo_promocao'], "website_code" => $codeStore, "dt_inicio" => $dataInicio, "dt_fim" => $dataFim, "ids_erp" => $params['ids_erp'], "qtd_minima" => (int)$params['qtd_minima'], "vlr_minimo" => $params['valor_minimo'], "pct_desconto" => (int)$params['percento_desconto'], "id_erp_brinde" => (int)$params['produtos_bonificados'], "qtd_brindes" => (int)$params['qtd_bonificado'], "id_categoria" => $params['id_categoria'], "prefix_campaign_name" => "", "prioridade" => $priority);

				$campaign = $campaignController->initCampaign($importData);
				$campaignController->importCampaign($campaign);

				$priority--;
			}
			Mage::helper('datavalidate')->sendSlackMessage($channel, "[Campanhas - Fim Integração] Integração manual da $codeStore finalizada");
		}
	} catch (Exception $ex)
	{
		echo $ex;
		Mage::helper('datavalidate')->sendSlackMessage($errorChannel, "[Campanhas - Erro na integracao manual da $codeStore] Erro: (" . $ex->__toString() . ")");
	}
}