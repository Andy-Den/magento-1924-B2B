<?php
require_once './configIntegra.php';
require_once '../_functions/importPromotions.php';


$files = array();

/** Lê o arquivo CSV para montar o array dos produtos em promoção */
if ($local == true)
{
	$files[] = file("$testDirectoryImport/manual_promotions.csv", FILE_IGNORE_NEW_LINES);
} else
{
	$files[] = file("$directoryImport/manual_promotions.csv", FILE_IGNORE_NEW_LINES);
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

			$promotions = array();

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

				array_push($promotions, $params);
			}
			addPromotions($promotions, $storeviewId, $websiteId);

			Mage::helper('datavalidate')->sendSlackMessage($channel, "[Campanhas - Fim Integração] Integração manual da $codeStore finalizada");
		}
	} catch (Exception $ex)
	{
		echo $ex;
		Mage::helper('datavalidate')->sendSlackMessage($errorChannel, "[Campanhas - Erro na integracao manual da $codeStore] Erro: (" . $ex->__toString() . ")");
	}
}

function addPromotions($promotions, $storeviewId, $websiteId, $indexAttributes = array('id_erp'))
{
	global $resource;
	global $readConnection;
	global $writeConnection;
	global $storeviewId;

	//just for peclam
	$storeViewAll = $storeviewId;

	global $codeStore;

	$labelsContentArray = array($codeStore . '_promocao' => 'Promoção', $codeStore . '_brinde' => 'Brinde');
	$tiposPromoArray = array(1 => 'promocao', 2 => 'brinde', 3 => 'promocao', 4 => 'promocao', 5 => 'brinde');
	$stopRulesProcessing = true;

	foreach ($promotions as $promotion)
	{
		$tipoPromocao = $promotion['tipo_promocao'];

		$sQtdMinima = strlen($promotion['qtd_minima']);
		$sIdsErp = strlen($promotion['ids_erp']);
		$sValorMinimo = strlen($promotion['valor_minimo']);
		$sIdCategoria = strlen($promotion['id_categoria']);
		$priority = getPriority();

		$condition = "'a:7:{s:4:\"type\";s:32:\"salesrule/rule_condition_combine\";s:9:\"attribute\";N;s:8:\"operator\";N;s:5:\"value\";s:1:\"1\";s:18:\"is_value_processed\";N;s:10:\"aggregator\";s:3:\"all\";s:10:\"conditions\";a:1:{i:0;a:7:{s:4:\"type\";s:38:\"salesrule/rule_condition_product_found\";s:9:\"attribute\";N;s:8:\"operator\";N;s:5:\"value\";s:1:\"1\";s:18:\"is_value_processed\";N;s:10:\"aggregator\";s:3:\"all\";s:10:\"conditions\";a:2:{i:0;a:5:{s:4:\"type\";s:32:\"salesrule/rule_condition_product\";s:9:\"attribute\";s:14:\"quote_item_qty\";s:8:\"operator\";s:2:\">=\";s:5:\"value\";s:$sQtdMinima:\"{$promotion['qtd_minima']}\";s:18:\"is_value_processed\";b:0;}i:1;a:5:{s:4:\"type\";s:32:\"salesrule/rule_condition_product\";s:9:\"attribute\";s:6:\"id_erp\";s:8:\"operator\";s:2:\"()\";s:5:\"value\";s:$sIdsErp:\"{$promotion['ids_erp']}\";s:18:\"is_value_processed\";b:0;}}}}}'";
		$action = "'a:7:{s:4:\"type\";s:40:\"salesrule/rule_condition_product_combine\";s:9:\"attribute\";N;s:8:\"operator\";N;s:5:\"value\";s:1:\"1\";s:18:\"is_value_processed\";N;s:10:\"aggregator\";s:3:\"all\";s:10:\"conditions\";a:1:{i:0;a:5:{s:4:\"type\";s:32:\"salesrule/rule_condition_product\";s:9:\"attribute\";s:6:\"id_erp\";s:8:\"operator\";s:2:\"()\";s:5:\"value\";s:$sIdsErp:\"{$promotion['ids_erp']}\";s:18:\"is_value_processed\";b:0;}}}'";

		if ($tipoPromocao == 1)
		{
			if (count(explode(',', $promotion['ids_erp'])) > 20)
			{
				$idsErpDesc = 'varios-skus';
			} else
			{
				$idsErpDesc = str_replace(',', '-', $promotion['ids_erp']);
			}

			$nameRule = $codeStore . "_" . $tiposPromoArray[$tipoPromocao] . "_tipo_" . $tipoPromocao . "_idserp_" . $idsErpDesc . "_qtdmin_" . $promotion['qtd_minima'] . '_percento_' . $promotion['percento_desconto'];
			$description = "{$promotion['percento_desconto']}% de desconto na compra de {$promotion['qtd_minima']} unidades";
			$values = "VALUES('$nameRule', '$description', STR_TO_DATE('{$promotion['data_inicio']}','%Y/%m/%d'), STR_TO_DATE('{$promotion['data_fim']}','%Y/%m/%d'), 0, 1, $condition, $action, $stopRulesProcessing, 1, NULL, {$priority}, 'by_percent', {$promotion['percento_desconto']}, null, 0, 0, 0, 0, 0, 1, 0, 0, NULL);";
		} elseif ($tipoPromocao == 2)
		{
			if (count(explode(',', $promotion['ids_erp'])) > 20)
			{
				$idsErpDesc = 'varios-skus';
			} else
			{
				$idsErpDesc = str_replace(',', '-', $promotion['ids_erp']);
			}

			$nameRule = $codeStore . "_" . $tiposPromoArray[$tipoPromocao] . "_tipo_" . $tipoPromocao . "_idserp_" . $idsErpDesc . "_qtdmin_" . $promotion['qtd_minima'] . '_leve_' . ($promotion['qtd_bonificado']) . '_unidades_do_produto_' . $promotion['produtos_bonificados'];
			$product = getProductByIdErpWebsite($promotion['produtos_bonificados']);
			$product->load();
			$description = ("Na compra de {$promotion['qtd_minima']}, leve " . ($promotion['qtd_bonificado']) . " do produto " . '"' . $product->getName() . '"');
			$promotionGiftType = 'ampromo_items';
			$skuProdutoBonificado = getSkuByIdErp($promotion['produtos_bonificados'], $storeviewId, $resource, $readConnection);

			if (!$skuProdutoBonificado)
			{
				continue;
			}

			$skuProdutoBonificado = empty($skuProdutoBonificado) ? 'NULL' : $skuProdutoBonificado;

			$values = "VALUES ('$nameRule', '$description', STR_TO_DATE('{$promotion['data_inicio']}','%Y/%m/%d'), STR_TO_DATE('{$promotion['data_fim']}','%Y/%m/%d'), 0, 1, $condition, $action, $stopRulesProcessing, 1, NULL, $priority, '$promotionGiftType', {$promotion['qtd_bonificado']}, NULL, {$promotion['qtd_minima']}, 0, 0, 0, 1, 1, 0, 0, '$skuProdutoBonificado');";

			//promoção do tipo 3: por valor comprado
		} elseif ($tipoPromocao == 3)
		{
			if (count(explode(',', $promotion['ids_erp'])) > 20)
			{
				$idsErpDesc = 'varios-skus';
			} else
			{
				$idsErpDesc = str_replace(',', '-', $promotion['ids_erp']);
			}
			$nameRule = $codeStore . "_" . $tiposPromoArray[$tipoPromocao] . "_tipo_" . $tipoPromocao . "_idserp_" . $idsErpDesc . "_vlrmin_" . $promotion['valor_minimo'] . '_percento_' . $promotion['percento_desconto'];
			$description = "{$promotion['percento_desconto']}% de desconto na compra acima de R$ {$promotion['valor_minimo']},00 reais";
			$condition = '\'a:7:{s:4:"type";s:32:"salesrule/rule_condition_combine";s:9:"attribute";N;s:8:"operator";N;s:5:"value";s:1:"1";s:18:"is_value_processed";N;s:10:"aggregator";s:3:"all";s:10:"conditions";a:1:{i:0;a:7:{s:4:"type";s:38:"salesrule/rule_condition_product_found";s:9:"attribute";N;s:8:"operator";N;s:5:"value";s:1:"1";s:18:"is_value_processed";N;s:10:"aggregator";s:3:"all";s:10:"conditions";a:2:{i:0;a:5:{s:4:"type";s:32:"salesrule/rule_condition_product";s:9:"attribute";s:6:"id_erp";s:8:"operator";s:2:"()";s:5:"value";s:' . $sIdsErp . ':"' . $promotion['ids_erp'] . '";s:18:"is_value_processed";b:0;}i:1;a:5:{s:4:"type";s:32:"salesrule/rule_condition_product";s:9:"attribute";s:20:"quote_item_row_total";s:8:"operator";s:2:">=";s:5:"value";s:' . $sValorMinimo . ':"' . $promotion['valor_minimo'] . '";s:18:"is_value_processed";b:0;}}}}}\'';
			$action = '\'a:7:{s:4:"type";s:40:"salesrule/rule_condition_product_combine";s:9:"attribute";N;s:8:"operator";N;s:5:"value";s:1:"1";s:18:"is_value_processed";N;s:10:"aggregator";s:3:"all";s:10:"conditions";a:1:{i:0;a:5:{s:4:"type";s:32:"salesrule/rule_condition_product";s:9:"attribute";s:6:"id_erp";s:8:"operator";s:2:"()";s:5:"value";s:' . $sIdsErp . ':"' . $promotion['ids_erp'] . '";s:18:"is_value_processed";b:0;}}}\'';
			$values = "VALUES('$nameRule', '$description', STR_TO_DATE('{$promotion['data_inicio']}','%Y/%m/%d'), STR_TO_DATE('{$promotion['data_fim']}','%Y/%m/%d'), 0, 1, $condition, $action, $stopRulesProcessing, 1, NULL, $priority, 'by_percent', {$promotion['percento_desconto']}, NULL, 0, 0, 0, 0, 1, 1, 0, 0, NULL);";

		} elseif ($tipoPromocao == 4)
		{
			$nameRule = $codeStore . "_" . $tiposPromoArray[$tipoPromocao] . "_tipo_" . $tipoPromocao . "_idcategoria_" . $promotion['id_categoria'] . "_vlrmin_" . $promotion['valor_minimo'] . '_percento_' . $promotion['percento_desconto'];
			$description = "{$promotion['percento_desconto']}% de desconto na compra acima de R$ {$promotion['valor_minimo']},00 reais";
			$condition = "'a:7:{s:4:\"type\";s:32:\"salesrule/rule_condition_combine\";s:9:\"attribute\";N;s:8:\"operator\";N;s:5:\"value\";s:1:\"1\";s:18:\"is_value_processed\";N;s:10:\"aggregator\";s:3:\"all\";s:10:\"conditions\";a:1:{i:0;a:7:{s:4:\"type\";s:38:\"salesrule/rule_condition_product_found\";s:9:\"attribute\";N;s:8:\"operator\";N;s:5:\"value\";s:1:\"1\";s:18:\"is_value_processed\";N;s:10:\"aggregator\";s:3:\"all\";s:10:\"conditions\";a:2:{i:0;a:5:{s:4:\"type\";s:32:\"salesrule/rule_condition_product\";s:9:\"attribute\";s:12:\"category_ids\";s:8:\"operator\";s:2:\"==\";s:5:\"value\";s:$sIdCategoria:\"{$promotion['id_categoria']}\";s:18:\"is_value_processed\";b:0;}i:1;a:5:{s:4:\"type\";s:32:\"salesrule/rule_condition_product\";s:9:\"attribute\";s:20:\"quote_item_row_total\";s:8:\"operator\";s:2:\">=\";s:5:\"value\";s:$sValorMinimo:\"{$promotion['valor_minimo']}\";s:18:\"is_value_processed\";b:0;}}}}}'";
			$action = "'a:7:{s:4:\"type\";s:40:\"salesrule/rule_condition_product_combine\";s:9:\"attribute\";N;s:8:\"operator\";N;s:5:\"value\";s:1:\"1\";s:18:\"is_value_processed\";N;s:10:\"aggregator\";s:3:\"all\";s:10:\"conditions\";a:1:{i:0;a:5:{s:4:\"type\";s:32:\"salesrule/rule_condition_product\";s:9:\"attribute\";s:12:\"category_ids\";s:8:\"operator\";s:2:\"==\";s:5:\"value\";s:$sIdCategoria:\"{$promotion['id_categoria']}\";s:18:\"is_value_processed\";b:0;}}}'";
			$values = "VALUES('$nameRule', '$description', STR_TO_DATE('{$promotion['data_inicio']}','%Y/%m/%d'), STR_TO_DATE('{$promotion['data_fim']}','%Y/%m/%d'), 0, 1, $condition, $action, 1, 1, NULL, $priority, 'by_percent', {$promotion['data_inicio']}, NULL, 0, 0, 0, 0, 1, 1, 0, 0, NULL);";

		} elseif ($tipoPromocao == 5)
		{
			if (count(explode(',', $promotion['ids_erp'])) > 20)
			{
				$idsErpDesc = 'varios-skus';
			} else
			{
				$idsErpDesc = str_replace(',', '-', $promotion['ids_erp']);
			}

			$nameRule = $codeStore . "_" . $tiposPromoArray[$tipoPromocao] . "_tipo_" . $tipoPromocao . "_idserp_" . $idsErpDesc . "_vlrmin_" . $promotion['valor_minimo'] . '_leve_' . ($promotion['qtd_bonificado']) . '_unidades_do_produto_' . $promotion['produtos_bonificados'];
			$product = getProductByIdErpWebsite($promotion['produtos_bonificados']);
			$product->load();
			$description = ("Na compra de R$ {$promotion['valor_minimo']} ou mais, leve " . ($promotion['qtd_bonificado']) . " do produto " . '"' . $product->getName() . '"');
			$promotionGiftType = 'ampromo_items';
			$skuProdutoBonificado = getSkuByIdErp($promotion['produtos_bonificados'], $storeviewId, $resource, $readConnection);

			if (!$skuProdutoBonificado)
			{
				continue;
			}

			$skuProdutoBonificado = empty($skuProdutoBonificado) ? 'NULL' : $skuProdutoBonificado;

			$condition = '\'a:7:{s:4:"type";s:32:"salesrule/rule_condition_combine";s:9:"attribute";N;s:8:"operator";N;s:5:"value";s:1:"1";s:18:"is_value_processed";N;s:10:"aggregator";s:3:"all";s:10:"conditions";a:1:{i:0;a:7:{s:4:"type";s:38:"salesrule/rule_condition_product_found";s:9:"attribute";N;s:8:"operator";N;s:5:"value";s:1:"1";s:18:"is_value_processed";N;s:10:"aggregator";s:3:"all";s:10:"conditions";a:2:{i:0;a:5:{s:4:"type";s:32:"salesrule/rule_condition_product";s:9:"attribute";s:6:"id_erp";s:8:"operator";s:2:"()";s:5:"value";s:' . $sIdsErp . ':"' . $promotion['ids_erp'] . '";s:18:"is_value_processed";b:0;}i:1;a:5:{s:4:"type";s:32:"salesrule/rule_condition_product";s:9:"attribute";s:20:"quote_item_row_total";s:8:"operator";s:2:">=";s:5:"value";s:' . $sValorMinimo . ':"' . $promotion['valor_minimo'] . '";s:18:"is_value_processed";b:0;}}}}}\'';
			$action = '\'a:7:{s:4:"type";s:40:"salesrule/rule_condition_product_combine";s:9:"attribute";N;s:8:"operator";N;s:5:"value";s:1:"1";s:18:"is_value_processed";N;s:10:"aggregator";s:3:"all";s:10:"conditions";a:1:{i:0;a:5:{s:4:"type";s:32:"salesrule/rule_condition_product";s:9:"attribute";s:6:"id_erp";s:8:"operator";s:2:"()";s:5:"value";s:' . $sIdsErp . ':"' . $promotion['ids_erp'] . '";s:18:"is_value_processed";b:0;}}}\'';

			$values = "VALUES ('$nameRule', '$description', STR_TO_DATE('{$promotion['data_inicio']}','%Y/%m/%d'), STR_TO_DATE('{$promotion['data_fim']}','%Y/%m/%d'), 0, 1, $condition, $action, $stopRulesProcessing, 1, NULL, $priority, '$promotionGiftType', {$promotion['qtd_bonificado']}, 1, 0, 0, 0, 0, 1, 1, 0, 0, '$skuProdutoBonificado');";

		} else {
			continue;
		}

		/**
		 * Verifica se a regra já existe
		 */
		$checkRule = "SELECT rule_id FROM ";
		$checkRule .= $resource->getTableName(salesrule);
		$checkRule .= " WHERE name = '$nameRule'";

		$idRule = $readConnection->fetchOne($checkRule);

		if (empty($idRule))
		{
			$setDescount = "INSERT INTO ";
			$setDescount .= $resource->getTableName(salesrule);
			$setDescount .= " (name, description, from_date, to_date, uses_per_customer, is_active, conditions_serialized, actions_serialized, stop_rules_processing, is_advanced, product_ids, sort_order, simple_action, discount_amount, discount_qty, discount_step, simple_free_shipping, apply_to_shipping, times_used, is_rss, coupon_type, use_auto_generation, uses_per_coupon, promo_sku)";
			$setDescount .= $values;

			$writeConnection->query($setDescount);

			//adicionando labels de promoção
			$ruleNameArray = explode('_', $nameRule);
			$ruleType = strtolower($ruleNameArray[0] . '_' . $ruleNameArray[1]);

			if ($tipoPromocao == 1)
			{
				foreach (explode(',', $promotion['ids_erp']) as $item)
				{
					$product = getProductByIdErp($item, $storeviewId, $readConnection);
					if ($product && $product->getSku())
					{
						createLabels($product->getSku(), $nameRule, $ruleType, $labelsContentArray[$ruleType], $description, $storeViewAll);
					}
				}
			} elseif ($tipoPromocao == 2)
			{
				foreach (explode(',', $promotion['ids_erp']) as $item)
				{
					$product = getProductByIdErp($item, $storeviewId, $readConnection);
					if ($product && $product->getSku())
					{
						createLabels($product->getSku(), $nameRule, $ruleType, $labelsContentArray[$ruleType], $description, $storeViewAll);
					}
				}
			} elseif ($tipoPromocao == 3)
			{
				$idsErpArray = explode(',', $promotion['ids_erp']);
				foreach ($idsErpArray as $item)
				{
					$product = getProductByIdErp($item, $storeviewId, $readConnection);
					if ($product && $product->getSku())
					{
						createLabels($product->getSku(), $nameRule, $ruleType, $labelsContentArray[$ruleType], $description, $storeViewAll);
					}
				}
			} elseif ($tipoPromocao == 4)
			{
				//adicionando todos os produtos de uma categoria
				$products = Mage::getModel('catalog/category')
					->setStoreId(explode(',', $storeViewAll)[0])
					->load($promotion['id_categoria']);
				$productslist = $products->getProductCollection()
					->addAttributeToSelect('sku')
					->addAttributeToFilter('status', 1);
				foreach ($productslist as $product)
				{
					//echo $product->getSku() . "\n";
					createLabels($product->getSku(), $nameRule, $ruleType, $labelsContentArray[$ruleType], $description, $storeViewAll);
				}
			} elseif ($tipoPromocao == 5)
			{
				$idsErpArray = explode(',', $promotion['ids_erp']);
				foreach ($idsErpArray as $item)
				{
					$product = getProductByIdErp($item, $storeviewId, $readConnection);
					if ($product && $product->getSku())
					{
						createLabels($product->getSku(), $nameRule, $ruleType, $labelsContentArray[$ruleType], $description, $storeViewAll);
					}
				}
			}
		}

		// Pega o rule_id da ultima regra adicionada
		$getRuleId = "SELECT rule_id FROM ";
		$getRuleId .= $resource->getTableName(salesrule);
		$getRuleId .= " where name like '$codeStore%' ORDER BY rule_id DESC LIMIT 1;";

		$ruleId = $readConnection->fetchOne($getRuleId);

		/**
		 * Associa a regra com o website
		 */
		$associaRegraPromocao = "INSERT IGNORE INTO ";
		$associaRegraPromocao .= $resource->getTableName(salesrule_website);
		$associaRegraPromocao .= " (rule_id, website_id) ";
		$associaRegraPromocao .= "VALUES ($ruleId, $websiteId);";

		$writeConnection->query($associaRegraPromocao);

		//forma antiga de criar o vinculo da regra com o grupo do cliente
		//start #biscoito para peclam
		$groups = array(1,2,3);
		$iGroups = count($groups);
		$ln = 1;
		while ($ln <= $iGroups) {
			$groupId = $groups[$ln - 1];

			$ligacaoProductAtribute = "INSERT IGNORE INTO ";
			$ligacaoProductAtribute .= $resource->getTableName(salesrule_product_attribute);
			$ligacaoProductAtribute .= " (rule_id, website_id, customer_group_id, attribute_id) ";
			$ligacaoProductAtribute .= "VALUES ($ruleId, $websiteId, $groupId, 185);";

			$writeConnection->query($ligacaoProductAtribute);

			$grupoClienteRegra = "INSERT IGNORE INTO ";
			$grupoClienteRegra .= $resource->getTableName(salesrule_customer_group);
			$grupoClienteRegra .= "(rule_id, customer_group_id) ";
			$grupoClienteRegra .= "VALUES ($ruleId,$groupId);";

			$writeConnection->query($grupoClienteRegra);

			$ln++;
		}
		//end

		//inserindo o vinculo da regra com o grupo do cliente
		//start

		//snippet para buscar os ids dos atributos utilizados na campanha
		$indexAttributesIds = array();
		foreach($indexAttributes as $indexAttribute) {
			$attrCodeQry = "SELECT attribute_id FROM ";
			$attrCodeQry .= $resource->getTableName(eav_attribute);
			$attrCodeQry .= " where entity_type_id = 4 and attribute_code = '$indexAttribute';";
			$attrId = $readConnection->fetchOne($attrCodeQry);

			$indexAttributesIds[] = $attrId;
		}

		//buscar todos os customer_groups do website da campanha
		$customerGroups = Mage::getModel('customer/group')->getCollection()
			->addFieldToFilter('website_id', $websiteId);

		foreach ($customerGroups as $customerGroup)
		{
			$customerGroupId = $customerGroup->getCustomerGroupId();

			//cria o vínculo (índice) do atributo utilizado na campanha
			foreach ($indexAttributesIds as $indexAttributeId)
			{
				$ligacaoProductAtribute = "INSERT IGNORE INTO ";
				$ligacaoProductAtribute .= $resource->getTableName(salesrule_product_attribute);
				$ligacaoProductAtribute .= " (rule_id, website_id, customer_group_id, attribute_id) ";
				$ligacaoProductAtribute .= "VALUES ($ruleId, $websiteId, $customerGroupId, $indexAttributeId);";

				$writeConnection->query($ligacaoProductAtribute);
			}

			//vincula a regra criada com o grupo de cliente
			$grupoClienteRegra = "INSERT IGNORE INTO ";
			$grupoClienteRegra .= $resource->getTableName(salesrule_customer_group);
			$grupoClienteRegra .= "(rule_id, customer_group_id) ";
			$grupoClienteRegra .= "VALUES ($ruleId, $customerGroupId);";
			$writeConnection->query($grupoClienteRegra);
		}
		//fim

		echo "+";
	}
}

function getPriority()
{
	global $priority;
	$newPriority = $priority;
	$priority--;
	return $newPriority;
}