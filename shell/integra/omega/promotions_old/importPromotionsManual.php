<?php
require_once './config.php';

/** Lê o arquivo CSV para montar o array dos produtos em promoção */
if ($local == true):$lines = file("$testDirectoryImport/promotions/manual_promotions.csv", FILE_IGNORE_NEW_LINES);
else : $lines = file("$directoryImport/promotions/manual_promotions.csv", FILE_IGNORE_NEW_LINES); endif;

if (empty($lines))
{
	echo "\n\n" . "arquivo csv vazio ou não existe" . "\n\n";
} else
{
	try
	{
		limpaCampanhasELabels($resource, $writeConnection, $storeView, $codeStore);

		$promotions = array();

		//lê todas as linhas do arquivo
		foreach ($lines as $key => $value)
		{
			$temp = str_getcsv($value, '|', "'");
			$tipoPromocao = $temp[0];
			$dataInicio = $temp[1];
			$dataFim = $temp[2];
			$idsErp = $temp[3];
			$idsErpArray = explode(',', $temp[3]);
			$qtdsMinima = $temp[4];
			$qtdsMinimaArray = explode(',', $temp[4]);
			$percentosDesconto = $temp[5];
			$percentosDescontoArray = explode(',', $temp[5]);
			$produtoBonificado = explode(',', $temp[6]);
			$qtdBonificado = explode(',', $temp[7]);

			//sem promoções de brinde, por enquanto
			//if ($tipoPromocao == 3) {
			//continue;
			//}

			/**
			 * Criando a regra para desconto em % para n skus
			 */
			//if ($tipoPromocao == 1 || $tipoPromocao == 2) {

			if (!$qtdsMinima)
			{
				continue;
			}
			//itera entre o array de skus informado na linha para e simula um "distinct" para que que não seja inseridas promoções iguais;
			//para promoções iguais para skus diferentes basta apenas criar uma regra e inserir ambos skus;
			if ($tipoPromocao == 1 || $tipoPromocao == 2)
			{
				for ($temp = 0; $temp < count(explode(',', $qtdsMinima)); $temp++)
				{
					$idErp = explode(',', $idsErp)[$temp];
					$indexDistinct = $tipoPromocao . ',' . $dataInicio . ',' . $dataFim . ',' . $qtdsMinimaArray[$temp] . ',' . $percentosDescontoArray[$temp] . ',' . $produtoBonificado[$temp] . ',' . $qtdBonificado[$temp];
					if (!$promotions[$indexDistinct])
					{
						$promotions[$indexDistinct] = array();
					}
					array_push($promotions[$indexDistinct], explode(',', $idsErp)[$temp]);
				}
			} elseif ($tipoPromocao == 3 || $tipoPromocao == 4)
			{
				$qtdsMinima = explode(',', $qtdsMinima)[0];
				$produtoBonificado = $produtoBonificado[0];
				$qtdBonificado = $qtdBonificado[0];

				$indexDistinct = $tipoPromocao . ',' . $idsErp . ',' . $qtdsMinima;
				if (!$promotions[$indexDistinct])
				{
					$promotions[$indexDistinct] = array();
				}
				if (!$promotions[$indexDistinct])
				{
					array_push($promotions[$indexDistinct], $dataInicio . ',' . $dataFim . ',' . $produtoBonificado . ',' . $qtdBonificado);
				}
			}
		}
		addPromotions($promotions, $storeviewId, $websiteId);

		Mage::helper('datavalidate')->sendSlackMessage($channel, "[Campanhas - Fim Integração] Integração manual da $codeStore finalizada");

	} catch (Exception $ex)
	{
		Mage::helper('datavalidate')->sendSlackMessage($errorChannel, "[Campanhas - Erro na integracao manual da $codeStore] Erro: (" . $ex->__toString() . ")");
	}
}

function addPromotions($promotions, $storeviewId, $websiteId)
{
	global $resource;
	global $readConnection;
	global $writeConnection;
	global $storeViewAll;

	$labelsContentArray = array('omega_promocao' => 'Promoção', 'omega_brinde' => 'Brinde');
	$tiposPromoArray = array(1 => 'promocao', 2 => 'promocao', 3 => 'brinde', 4 => 'brinde');
	$stopRulesProcessing = true;

	$priorityHelper = array();

	foreach ($promotions as $key => $promotion)
	{
		$tipoPromocao = explode(',', $key)[0];

		if ($tipoPromocao == 1 || $tipoPromocao == 2)
		{
			$dataInicio = explode(',', $key)[1];
			$dataFim = explode(',', $key)[2];

			$idsErp = '';
			foreach ($promotion as $item)
			{
				$idsErp .= $item . ',';
			}
			$idsErp = rtrim($idsErp, ",");
			$qtdMinima = explode(',', $key)[3];
			$percentoDesconto = explode(',', $key)[4];
			$qtdProdutoBonificado = explode(',', $key)[6];

			$sIdsErp = strlen($idsErp); // conta quantos caracteres possui e armazena na variavel
			$sQtdMinima = strlen($qtdMinima); // conta quantos caracteres possui e armazena na variavel
		} else
		{
			$dataInicio = explode(',', $promotion[0])[0];
			$dataFim = explode(',', $promotion[0])[1];

			$keyToArray = explode(',', $key);
			unset($keyToArray[0]);
			unset($keyToArray[count($keyToArray)]);
			$idsErp = implode(',', $keyToArray);

			$keyToArray = explode(',', $key);
			$qtdMinima = explode(',', $key)[count($keyToArray) - 1];

			$produtosBonificados = explode(',', $promotion[0])[2];
			$qtdProdutoBonificado = explode(',', $promotion[0])[3];

			$sIdsErp = strlen($idsErp); // conta quantos caracteres possui e armazena na variavel
			$sQtdMinima = strlen($qtdMinima); // conta quantos caracteres possui e armazena na variavel
		}

		$condition = "'a:7:{s:4:\"type\";s:32:\"salesrule/rule_condition_combine\";s:9:\"attribute\";N;s:8:\"operator\";N;s:5:\"value\";s:1:\"1\";s:18:\"is_value_processed\";N;s:10:\"aggregator\";s:3:\"all\";s:10:\"conditions\";a:1:{i:0;a:7:{s:4:\"type\";s:38:\"salesrule/rule_condition_product_found\";s:9:\"attribute\";N;s:8:\"operator\";N;s:5:\"value\";s:1:\"1\";s:18:\"is_value_processed\";N;s:10:\"aggregator\";s:3:\"all\";s:10:\"conditions\";a:2:{i:0;a:5:{s:4:\"type\";s:32:\"salesrule/rule_condition_product\";s:9:\"attribute\";s:14:\"quote_item_qty\";s:8:\"operator\";s:2:\">=\";s:5:\"value\";s:$sQtdMinima:\"$qtdMinima\";s:18:\"is_value_processed\";b:0;}i:1;a:5:{s:4:\"type\";s:32:\"salesrule/rule_condition_product\";s:9:\"attribute\";s:6:\"id_erp\";s:8:\"operator\";s:2:\"()\";s:5:\"value\";s:$sIdsErp:\"$idsErp\";s:18:\"is_value_processed\";b:0;}}}}}'";
		$action = "'a:7:{s:4:\"type\";s:40:\"salesrule/rule_condition_product_combine\";s:9:\"attribute\";N;s:8:\"operator\";N;s:5:\"value\";s:1:\"1\";s:18:\"is_value_processed\";N;s:10:\"aggregator\";s:3:\"all\";s:10:\"conditions\";a:1:{i:0;a:5:{s:4:\"type\";s:32:\"salesrule/rule_condition_product\";s:9:\"attribute\";s:6:\"id_erp\";s:8:\"operator\";s:2:\"()\";s:5:\"value\";s:$sIdsErp:\"$idsErp\";s:18:\"is_value_processed\";b:0;}}}'";

		if ($tipoPromocao == 1 || $tipoPromocao == 2)
		{
			$nameRule = "omega" . "_" . $tiposPromoArray[$tipoPromocao] . "_tipo_" . $tipoPromocao . "_idserp_" . str_replace(',', '-', $idsErp) . "_qtdmin_" . $qtdMinima . '_percento_' . $percentoDesconto;
			$description = "$percentoDesconto% de desconto na compra de $qtdMinima unidades";

			$priority = 20;
			if (array_key_exists($tipoPromocao . ',' . implode(',', $promotion), $priorityHelper))
			{
				$priority = $priorityHelper[$tipoPromocao . ',' . implode(',', $promotion)] - 1;
				$priorityHelper[$tipoPromocao . ',' . implode(',', $promotion)] = $priority;
			} else
			{
				$priorityHelper[$tipoPromocao . ',' . implode(',', $promotion)] = $priority;
			}

			$values = "VALUES('$nameRule', '$description', STR_TO_DATE('$dataInicio','%d/%m/%Y'), STR_TO_DATE('$dataFim','%d/%m/%Y'), 0, 1, $condition, $action, $stopRulesProcessing, 1, NULL, $priority, 'by_percent', $percentoDesconto, null, 0, 0, 0, 0, 0, 1, 0, 0, NULL);";
		} elseif ($tipoPromocao == 3 || $tipoPromocao == 4)
		{
			
			switch ($tipoPromocao)
			{
				case 3:
					$nameRule = "omega" . "_" . $tiposPromoArray[$tipoPromocao] . "_tipo_" . $tipoPromocao . "_idserp_" . str_replace(',', '-', $idsErp) . "_qtdmin_" . $qtdMinima . '_leve_' . ($qtdProdutoBonificado) . '_unidades_do_produto_' . $produtosBonificados;
					$product = getProductByIdErpWebsite($produtosBonificados);
					$product->load();
					$description = ("Na compra de $qtdMinima, leve " . ($qtdProdutoBonificado) . " do produto " . '"' . $product->getName() . '"');
					break;
				case 4:
					$nameRule = "omega" . "_" . $tiposPromoArray[$tipoPromocao] . "_tipo_" . $tipoPromocao . "_idserp_" . str_replace(',', '-', $idsErp) . "_qtdmin_" . $qtdMinima . '_leve_' . ($qtdMinima + $qtdProdutoBonificado) . '_unidades_do_mesmo_produto';
					$description = ("Na compra de $qtdMinima, leve " . ($qtdMinima + $qtdProdutoBonificado));
					break;
			}
			if ($tipoPromocao == 3)
			{
				$promotionGiftType = 'ampromo_items';
				$skuProdutoBonificado = getSkuByIdErp($produtosBonificados, $storeviewId, $resource, $readConnection);
				if (!$skuProdutoBonificado)
				{
					continue;
				}
			} else
			{
				$promotionGiftType = 'ampromo_product';
				$skuProdutoBonificado = '';
			}
			$priority = 20;
			if (array_key_exists($tipoPromocao . ',' . $idsErp, $priorityHelper))
			{
				$priority = $priorityHelper[$tipoPromocao . ',' . $idsErp] - 1;
				$priorityHelper[$tipoPromocao . ',' . $idsErp] = $priority;
			} else
			{
				$priorityHelper[$tipoPromocao . ',' . $idsErp] = $priority;
			}

			$skuProdutoBonificado = empty($skuProdutoBonificado) ? 'NULL' : $skuProdutoBonificado;

			$values = "VALUES ('$nameRule', '$description', STR_TO_DATE('$dataInicio','%d/%m/%Y'), STR_TO_DATE('$dataFim','%d/%m/%Y'), 0, 1, $condition, $action, $stopRulesProcessing, 1, NULL, $priority, '$promotionGiftType', $qtdProdutoBonificado, NULL, $qtdMinima, 0, 0, 0, 1, 1, 0, 0, '$skuProdutoBonificado');";
		} else
		{
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

			if ($tipoPromocao == 1 || $tipoPromocao == 2)
			{
				foreach ($promotion as $item)
				{
					$product = getProductByIdErp($item, $storeviewId, $readConnection);
					if ($product && $product->getSku())
					{
						createLabels($product->getSku(), $nameRule, $ruleType, $labelsContentArray[$ruleType], $description, $storeViewAll);
					}
				}
			} else
			{
				$idsErpArray = explode(',', $idsErp);
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
		$getRuleId .= " where name like 'omega%' ORDER BY rule_id DESC LIMIT 1;";

		$ruleId = $readConnection->fetchOne($getRuleId);

		/**
		 * Associa a regra com o website
		 */
		$associaRegraPromocao = "INSERT IGNORE INTO ";
		$associaRegraPromocao .= $resource->getTableName(salesrule_website);
		$associaRegraPromocao .= " (rule_id, website_id) ";
		$associaRegraPromocao .= "VALUES ($ruleId, $websiteId);";

		$writeConnection->query($associaRegraPromocao);

		//defininr quais grupos de clientes serão vinculados
		$groups = array(1);
		$iGroups = count($groups);
		$ln = 1;
		while ($ln <= $iGroups)
		{
			$groupId = $groups[$ln - 1];

			/**
			 * Alimenta a tabela de ligacao regra de promocao atributo do produto
			 */
			$ligacaoProductAtribute = "INSERT IGNORE INTO ";
			$ligacaoProductAtribute .= $resource->getTableName(salesrule_product_attribute);
			$ligacaoProductAtribute .= " (rule_id, website_id, customer_group_id, attribute_id) ";
			$ligacaoProductAtribute .= "VALUES ($ruleId, $websiteId, $groupId, 74);";

			$writeConnection->query($ligacaoProductAtribute);

			/**
			 * Define os grupos de clientes que terão acesso a regra
			 */
			// adiciona regra para todos os grupos de cliente da distribuidora

			$customerGroups = Mage::getModel('customer/group')->getCollection()
				->addFieldToFilter('website_id', $websiteId);

			foreach ($customerGroups as $customerGroup)
			{
				$customerGroupId = $customerGroup->getCustomerGroupId();

				$grupoClienteRegra = "INSERT IGNORE INTO ";
				$grupoClienteRegra .= $resource->getTableName(salesrule_customer_group);
				$grupoClienteRegra .= "(rule_id, customer_group_id) ";
				$grupoClienteRegra .= "VALUES ($ruleId, $customerGroupId);";
				$writeConnection->query($grupoClienteRegra);
			}

			$ln++; // incremento para contagem dos grupos de cliente
		}
	}
}