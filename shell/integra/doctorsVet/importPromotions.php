<?php
require_once './configIntegra.php';

require_once '../_functions/importPromotions.php';

$labelsContentArray = array('doctorsvet_promocao' => 'Promoção');

/** Lê o arquivo CSV para montar o array dos produtos em promoção */
if ($local == true):$lines = file("$testDirectoryImport/promotions/promotions.csv", FILE_IGNORE_NEW_LINES);
else : $lines = file("$directoryImport/promotions/promotions.csv", FILE_IGNORE_NEW_LINES); endif;

if (empty($lines)) {
	echo "\n\n" . "arquivo csv vazio ou não existe" . "\n\n";
} else {
	cleanCampaigns();

	$priority = 20;
	$lastRule = '';
	foreach ($lines as $key => $value) {
		$i++;
		$temp = str_getcsv($value, '|', "'");
		$dataInicio = $temp[0];
		$dataFim = $temp[1];
		$idErp = $temp[2];
		$idCategoria = $temp[3];
		$qtdMinima = $temp[4];
		$valorMinimo = $temp[5];
		$percentoDesconto = $temp[6];

		//verificando se campanhas devem ser adicionadas perante a data de agendamento das mesmas
		if ($dataInicio) {
			$dateStartPromotion = strtotime($dataInicio);
			if (strtotime($currentDate) < $dateStartPromotion) {
				continue;
			}
		} else {
			continue;
		}

		if ($dataFim) {
			$dateFinishPromotion = strtotime($dataFim);
			if (strtotime($currentDate) > $dateFinishPromotion) {
				continue;
			}
		} else {
			continue;
		}
		//fim

		//se não tem desconto definido no arquivo, não inserir campanha
		if (!$percentoDesconto) {
			continue;
		}

		//se não tem qtd minima nem id de categoria
		if (!$qtdMinima && !$idCategoria) {
			continue;
		}

		/**
		 * Criando a regra para desconto em % para um sku
		 */
		$sIdErp = strlen($idErp); // conta quantos caracteres possui e armazena na variavel
		$sIdCategoria = strlen($idCategoria); // conta quantos caracteres possui e armazena na variavel
		$sQtdMinima = strlen($qtdMinima); // conta quantos caracteres possui e armazena na variavel
		$sValorMinimo = strlen($valorMinimo); // conta quantos caracteres possui e armazena na variavel


		if (!empty($idErp)) {
			$nameRule = "doctorsVet_Promocao_idErp_" . $idErp . "_desconto_$percentoDesconto%_qtd_$qtdMinima";
			$nameRuleArray = explode('_', $nameRule);
			$lastNameRuleArray = explode('_', $lastRule);
			if ($lastRule && ($nameRuleArray[0] == $lastNameRuleArray[0]) && ($nameRuleArray[1] == $lastNameRuleArray[1]) && ($nameRuleArray[2] == $lastNameRuleArray[2]) && ($nameRuleArray[3] == $lastNameRuleArray[3]) && ($nameRuleArray[4] == $lastNameRuleArray[4])) {
				$priority--;
			} else {
				$priority = 20;
			}
			$lastRule = $nameRule;
			$description = "$percentoDesconto% de desconto na compra de $qtdMinima unidades";

			$condition = "'a:7:{s:4:\"type\";s:32:\"salesrule/rule_condition_combine\";s:9:\"attribute\";N;s:8:\"operator\";N;s:5:\"value\";s:1:\"1\";s:18:\"is_value_processed\";N;s:10:\"aggregator\";s:3:\"all\";s:10:\"conditions\";a:1:{i:0;a:7:{s:4:\"type\";s:38:\"salesrule/rule_condition_product_found\";s:9:\"attribute\";N;s:8:\"operator\";N;s:5:\"value\";s:1:\"1\";s:18:\"is_value_processed\";N;s:10:\"aggregator\";s:3:\"all\";s:10:\"conditions\";a:2:{i:0;a:5:{s:4:\"type\";s:32:\"salesrule/rule_condition_product\";s:9:\"attribute\";s:6:\"id_erp\";s:8:\"operator\";s:2:\"==\";s:5:\"value\";s:$sIdErp:\"$idErp\";s:18:\"is_value_processed\";b:0;}i:1;a:5:{s:4:\"type\";s:32:\"salesrule/rule_condition_product\";s:9:\"attribute\";s:14:\"quote_item_qty\";s:8:\"operator\";s:2:\">=\";s:5:\"value\";s:$sQtdMinima:\"$qtdMinima\";s:18:\"is_value_processed\";b:0;}}}}}'";
			$action = "'a:7:{s:4:\"type\";s:40:\"salesrule/rule_condition_product_combine\";s:9:\"attribute\";N;s:8:\"operator\";N;s:5:\"value\";s:1:\"1\";s:18:\"is_value_processed\";N;s:10:\"aggregator\";s:3:\"all\";s:10:\"conditions\";a:1:{i:0;a:5:{s:4:\"type\";s:32:\"salesrule/rule_condition_product\";s:9:\"attribute\";s:6:\"id_erp\";s:8:\"operator\";s:2:\"==\";s:5:\"value\";s:$sIdErp:\"$idErp\";s:18:\"is_value_processed\";b:0;}}}'";
		} else {

			if ($qtdMinima) {
				$priority--;
				$nameRule = "doctorsVet_Promocao_idCategoria_" . $idCategoria . "_desconto_$percentoDesconto%_qtd_$qtdMinima";
				$description = "$percentoDesconto% de desconto na compra acima de $qtdMinima unidade(s)";
				$condition = ('\'a:7:{s:4:"type";s:32:"salesrule/rule_condition_combine";s:9:"attribute";N;s:8:"operator";N;s:5:"value";s:1:"1";s:18:"is_value_processed";N;s:10:"aggregator";s:3:"all";s:10:"conditions";a:1:{i:0;a:7:{s:4:"type";s:42:"salesrule/rule_condition_product_subselect";s:9:"attribute";s:3:"qty";s:8:"operator";s:2:">=";s:5:"value";s:'.$sQtdMinima.':"'.$qtdMinima.'";s:18:"is_value_processed";N;s:10:"aggregator";s:3:"all";s:10:"conditions";a:1:{i:0;a:5:{s:4:"type";s:32:"salesrule/rule_condition_product";s:9:"attribute";s:12:"category_ids";s:8:"operator";s:2:"==";s:5:"value";s:'.$sIdCategoria.':"'.$idCategoria.'";s:18:"is_value_processed";b:0;}}}}}\'');
				$action = ('\'a:7:{s:4:"type";s:40:"salesrule/rule_condition_product_combine";s:9:"attribute";N;s:8:"operator";N;s:5:"value";s:1:"1";s:18:"is_value_processed";N;s:10:"aggregator";s:3:"all";s:10:"conditions";a:1:{i:0;a:5:{s:4:"type";s:32:"salesrule/rule_condition_product";s:9:"attribute";s:12:"category_ids";s:8:"operator";s:2:"==";s:5:"value";s:' . $sIdCategoria . ':"' . $idCategoria . '";s:18:"is_value_processed";b:0;}}}\'');
			} else {
				$priority--;
				$nameRule = "doctorsVet_Promocao_idCategoria_" . $idCategoria . "_desconto_$percentoDesconto%_vlr_$valorMinimo";
				$description = "$percentoDesconto% de desconto na compra acima de R$$valorMinimo,00 reais";
				$condition = '\'a:7:{s:4:"type";s:32:"salesrule/rule_condition_combine";s:9:"attribute";N;s:8:"operator";N;s:5:"value";s:1:"1";s:18:"is_value_processed";N;s:10:"aggregator";s:3:"all";s:10:"conditions";a:1:{i:0;a:7:{s:4:"type";s:42:"salesrule/rule_condition_product_subselect";s:9:"attribute";s:14:"base_row_total";s:8:"operator";s:2:">=";s:5:"value";s:'.$sValorMinimo.':"'.$valorMinimo.'";s:18:"is_value_processed";N;s:10:"aggregator";s:3:"all";s:10:"conditions";a:1:{i:0;a:5:{s:4:"type";s:32:"salesrule/rule_condition_product";s:9:"attribute";s:12:"category_ids";s:8:"operator";s:2:"==";s:5:"value";s:'.$sIdCategoria.':"'.$idCategoria.'";s:18:"is_value_processed";b:0;}}}}}\'';
				$action = ('\'a:7:{s:4:"type";s:40:"salesrule/rule_condition_product_combine";s:9:"attribute";N;s:8:"operator";N;s:5:"value";s:1:"1";s:18:"is_value_processed";N;s:10:"aggregator";s:3:"all";s:10:"conditions";a:1:{i:0;a:5:{s:4:"type";s:32:"salesrule/rule_condition_product";s:9:"attribute";s:12:"category_ids";s:8:"operator";s:2:"==";s:5:"value";s:' . $sIdCategoria . ':"' . $idCategoria . '";s:18:"is_value_processed";b:0;}}}\'');
			}
		}

		$values = "VALUES('$nameRule', '$description', STR_TO_DATE('$dataInicio','%Y-%m-%d'), STR_TO_DATE('$dataFim','%Y-%m-%d'), 0, 1, $condition, $action, 1, 1, NULL, $priority, 'by_percent', $percentoDesconto, NULL, 0, 0, 0, 0, 1, 1, 0, 0, NULL);";

		/**
		 * Verifica se a regra já existe
		 */
		$checkRule = "SELECT rule_id FROM ";
		$checkRule .= $resource->getTableName(salesrule);
		$checkRule .= " WHERE name = '$nameRule'";

		$idRule = $readConnection->fetchOne($checkRule);

		if (empty($idRule)) {
			$setDescount = "INSERT INTO ";
			$setDescount .= $resource->getTableName(salesrule);
			$setDescount .= " (name, description, from_date, to_date, uses_per_customer, is_active, conditions_serialized, actions_serialized, stop_rules_processing, is_advanced, product_ids, sort_order, simple_action, discount_amount, discount_qty, discount_step, simple_free_shipping, apply_to_shipping, times_used, is_rss, coupon_type, use_auto_generation, uses_per_coupon, promo_sku)";
			$setDescount .= $values;

			$writeConnection->query($setDescount);

			//adicionando labels de promoção
			$ruleNameArray = explode('_', $nameRule);
			$ruleType = strtolower($ruleNameArray[0] . '_' . $ruleNameArray[1]);
			if (!empty($idErp)) {
				$product = getProductByIdErp($idErp, $storeviewId, $readConnection);
				if ($product && $product->getSku()) {
					createLabels($product->getSku(), $labelsContentArray[$ruleType]);
				}
			} else {
				//adicionando todos os produtos de uma categoria
				$products = Mage::getModel('catalog/category')
					->setStoreId(explode(',', $storeId)[0])
					->load($idCategoria);
				$productslist = $products->getProductCollection()
					->addAttributeToSelect('sku')
					->addAttributeToFilter('status', 1);
				foreach ($productslist as $product) {
					//echo $product->getSku() . "\n";
					createLabels($product->getSku(), $labelsContentArray[$ruleType]);
				}
			}
			//

			// Pega o rule_id da ultima regra adicionada
			$getRuleId = "SELECT rule_id FROM ";
			$getRuleId .= $resource->getTableName(salesrule);
			$getRuleId .= " where name like 'doctorsVet%' ORDER BY rule_id DESC LIMIT 1;";

			$ruleId = $readConnection->fetchOne($getRuleId);

			/**
			 * Associa a regra com o website
			 */
			$associaRegraPromocao = "INSERT INTO ";
			$associaRegraPromocao .= $resource->getTableName(salesrule_website);
			$associaRegraPromocao .= " (rule_id, website_id) ";
			$associaRegraPromocao .= "VALUES ($ruleId, $websiteId);";

			$writeConnection->query($associaRegraPromocao);

			$groups = array(8);
			$iGroups = count($groups);
			$ln = 1;
			while ($ln <= $iGroups) {
				$groupId = $groups[$ln - 1];

				/**
				 * Alimenta a tabela de ligacao regra de promocao atributo do produto
				 */
				$ligacaoProductAtribute = "INSERT INTO ";
				$ligacaoProductAtribute .= $resource->getTableName(salesrule_product_attribute);
				$ligacaoProductAtribute .= " (rule_id, website_id, customer_group_id, attribute_id) ";
				$ligacaoProductAtribute .= "VALUES ($ruleId, $websiteId, $groupId, 74);";

				$writeConnection->query($ligacaoProductAtribute);

				/**
				 * Define os grupos de clientes que terão acesso a regra
				 */
				// adiciona regra para o grupo 0
				$grupoClienteRegra = "INSERT INTO ";
				$grupoClienteRegra .= $resource->getTableName(salesrule_customer_group);
				$grupoClienteRegra .= "(rule_id, customer_group_id) ";
				$grupoClienteRegra .= "VALUES ($ruleId,$groupId);";

				$writeConnection->query($grupoClienteRegra);

				$ln++; // incremento para contagem dos grupos de cliente
			}

		} else {
			echo ">>>> Essa regra já existe: $nameRule \n\n\n";
		}
	}
}