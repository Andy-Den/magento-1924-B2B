<?php
require_once './configIntegra.php';

require_once '../_functions/importPromotions.php';

require_once "{$pathBase}/lib/FVets/ImportPromotions/Aux/Constants.php";
require_once "{$pathBase}/lib/FVets/ImportPromotions/Aux/Functions.php";
require_once "{$pathBase}/lib/FVets/ImportPromotions/Abstracts/Controller.php";
require_once "{$pathBase}/lib/FVets/ImportPromotions/Interfaces/ICampaign.php";
require_once "{$pathBase}/lib/FVets/ImportPromotions/Abstracts/Campaign.php";
require_once "{$pathBase}/lib/FVets/ImportPromotions/Controllers/Importer.php";
require_once "{$pathBase}/lib/FVets/ImportPromotions/Entity/CampaignType1.php";
require_once "{$pathBase}/lib/FVets/ImportPromotions/Entity/CampaignType2.php";

/** Lê o arquivo CSV para montar o array dos produtos em promoção */
if ($local == true ):$lines = file("$testDirectoryImport/campanhas.csv", FILE_IGNORE_NEW_LINES);
else : $lines = file("$directoryImport/campanhas.csv", FILE_IGNORE_NEW_LINES); endif;

if (empty($lines)){
    echo "\n\n" . "arquivo csv vazio ou não existe" . "\n\n"; exit;
} else {
			cleanCampaigns();
    }

$promotionMultipleSkuHead = "Tipo: Progressiva\n";
$promotionMultipleSkuHead .= "IDs ERP|qtd Mínima|Desc\n";

$promotionSimpleSkuHead = "Tipo: Simples\n";
$promotionSimpleSkuHead .= "ID ERP|qtd Mínima|Desc\n";

$promotionBrindeSkuHead = "Tipo: Brinde\n";
$promotionBrindeSkuHead .= "ID ERP|Comprando|ID ERP Brinde|qtd Bon\n";

$actualLine = null;
$campaignController = new Controllers_Importer();

$priority = 2000;

foreach ($lines as $key => $value) {
    $i++;
    if ($key == 0) {
    } else {
        $temp = str_getcsv($value, '|', "'");

        $skuErp = $temp[2];
        $qtdMinima = $temp[3];
        $percentoDesconto = $temp[4];
        $prodBonificado = $temp[5];
        $qtdBonificado = $temp[6];
        $descQtdProdComprado = $temp[7];
        $dataInicio = date('Y-m-d', strtotime($currentDateFormated . ' - 1 day'));

        $skuErp = rtrim($skuErp, ",");
        $arraySkusLabel = explode(",",$skuErp);

        $totalSkusErp = count($arraySkusLabel);

        foreach($arraySkusLabel as $sl){
            $sl = ltrim($sl, '0');
            $skusLabel[] = $sl;
        }

        $strSkusList = implode(",",$skusLabel);
        $strSkusLine = $skuErp;

        /** soma 5 dias na data fim */
        $dataFim = date('Y-m-d', strtotime($currentDate . ' + 1 week'));

        $data = array(
            array(
                'from_date' => $dataInicio,
                'to_date' => $temp[1],
                'sku' => $skuErp,
                'qtdMinima' => $qtdMinima,
                'percentoDesconto' => $percentoDesconto,
                'prodBonificado' => $prodBonificado,
                'qtdBonificado' => $qtdBonificado,
                'descQtdProdComprado' => $descQtdProdComprado,
            )
        );

        /** monta um array de SKUs em promoção */
        $skusErp = explode(",", $skuErp);
        array_pop($skusErp); // remove o ultimo registro adicionado pela virgula vinda do ERP

        /** monta um array de diferente quantidades para separar nas regras */
        $qtdMinima = explode(",", $qtdMinima);
        $totalQtdMinima = count($qtdMinima);

        /** monta um array com os descontos a serem criados */
        $percentoDesconto = explode(",", $percentoDesconto);
        $totalPercentoDesconto = count($percentoDesconto);

        // Trata as variáveis para criar os logs
        $percentoDescontoLog = implode(',',$percentoDesconto);
        $qtdMinimaLog = implode(',',$qtdMinima);

        /**
         * Executa a funcao para adicionar as regras
         */
        if ($totalSkusErp > 1) {
            echo "Capanha para multiplos SKUs\n\n";

					try {
            setRegraMultiplosSku($storeId, $storeviewId, $totalSkusErp, $strSkusList, $strSkusLine, $qtdMinima, $percentoDesconto, $dataInicio, $resource, $writeConnection, $readConnection, $dataFim);
					} catch (Exception $ex) {
						//let the flow continues
					}

            $promotionMultipleSku .= "$skuErp|$qtdMinimaLog|$percentoDescontoLog\n";
        }

        elseif (($totalSkusErp == 1) && ($prodBonificado == NULL)) {
            echo "Campanha para apenas um SKU\n";

					try {
            setRegraSkuUnico($prodBonificado, $resource, $dataInicio, $percentoDesconto, $qtdMinima, $skuErp, $dataFim, $writeConnection, $readConnection, $storeId, $storeviewId);
					} catch (Exception $ex) {
						//let the flow continues
					}

            $promotionSimpleSku .= "$skuErp|$qtdMinimaLog|$percentoDescontoLog\n";

        }
        else {
            echo "Campanha para Sku com Brinde\n";

					try {
            setRegraBrinde($debug,$prodBonificado, $resource, $dataInicio, $qtdBonificado, $descQtdProdComprado, $skuErp, $dataFim, $writeConnection, $readConnection, $storeId, $storeviewId);
					} catch (Exception $ex) {
						//let the flow continues
					}

            $promotionBrindeSku .= "$skuErp|$descQtdProdComprado|$prodBonificado|$qtdBonificado\n";
        }
				$priority--;
    }
}

/*
 * Function para setar regra de desconto em porcentagem para SKU unico
 */

function setRegraBrinde($debug,$prodBonificado, $resource, $dataInicio, $qtdBonificado, $descQtdProdComprado, $skuErp, $dataFim, $writeConnection, $readConnection, $storeId, $storeviewId) {
		//$idErp = Mage::getModel('catalog/product')->setStoreId($storeviewId)->loadByAttribute($prodBonificado, 'sku')->getIdErp();
		global $codeStore;
		global $priority;
		global $campaignController;

		$dateTime = DateTime::createFromFormat('Y-m-d', $dataInicio);
		$dataInicio = $dateTime->format('d/m/Y');

		$dateTime = DateTime::createFromFormat('Y-m-d', $dataFim);
		$dataFim = $dateTime->format('d/m/Y');

		$idErp = (int)$skuErp;
		$importData = array("tp_promocao" => 2, "website_code" => $codeStore, "dt_inicio" => $dataInicio, "dt_fim" => $dataFim, "ids_erp" => $idErp, "qtd_minima" => (int)$descQtdProdComprado, "id_erp_brinde" => (int)$prodBonificado, "qtd_brindes" => (int)$qtdBonificado, "prioridade" => $priority, "prefix_campaign_name" => "");

		$campaign = $campaignController->initCampaign($importData);
		$campaignController->importCampaign($campaign);
}

function setRegraSkuUnico($prodBonificado, $resource, $dataInicio, $percentoDesconto, $qtdMinima, $skuErp, $dataFim, $writeConnection, $readConnection, $storeId, $storeviewId)
{
	global $codeStore;
	global $priority;
	global $campaignController;

	$dateTime = DateTime::createFromFormat('Y-m-d', $dataInicio);
	$dataInicio = $dateTime->format('d/m/Y');

	$dateTime = DateTime::createFromFormat('Y-m-d', $dataFim);
	$dataFim = $dateTime->format('d/m/Y');

	$skuErp = ltrim($skuErp, '0');

	foreach ($qtdMinima as $key => $regra)
	{
		$percentoDescontoUnico = $percentoDesconto[$key];

		$importData = array("tp_promocao" => 1, "website_code" => $codeStore, "dt_inicio" => $dataInicio, "dt_fim" => $dataFim, "ids_erp" => $skuErp, "qtd_minima" => (int)$regra, "pct_desconto" => (float)$percentoDescontoUnico, "prioridade" => $priority, "prefix_campaign_name" => "");

		$campaign = $campaignController->initCampaign($importData);
		$campaignController->importCampaign($campaign);

		$priority--;
	}
}


function setRegraMultiplosSku($storeId, $storeviewId, $totalSkusErp, $strSkusList, $strSkusLine, $qtdMinima, $percentoDesconto, $dataInicio, $resource, $writeConnection, $readConnection, $dataFim)
{
	global $codeStore;
	global $priority;
	global $campaignController;

	$dateTime = DateTime::createFromFormat('Y-m-d', $dataInicio);
	$dataInicio = $dateTime->format('d/m/Y');

	$dateTime = DateTime::createFromFormat('Y-m-d', $dataFim);
	$dataFim = $dateTime->format('d/m/Y');

	//normalizando skus erp
	$arraySkusErp = array();
	foreach(explode(',', $strSkusLine) as $skuErp) {
		$arraySkusErp[] = ltrim($skuErp, '0');
	}

    foreach ($qtdMinima as $key => $regra) {
			$percentoDescontoUnico = $percentoDesconto[$key];
			$importData = array("tp_promocao" => 1, "website_code" => $codeStore, "dt_inicio" => $dataInicio, "dt_fim" => $dataFim, "ids_erp" => implode(',', $arraySkusErp), "qtd_minima" => (int)$regra, "pct_desconto" => (float)$percentoDescontoUnico, "prioridade" => $priority, "prefix_campaign_name" => "", "desc_etiqueta" => "Compre acima de " . $regra . " unidade(s) do MIX e ganhe " . $percentoDescontoUnico . "&#37; de desconto");
			$campaign = $campaignController->initCampaign($importData);
			$campaignController->importCampaign($campaign);
			$priority--;
    }
}