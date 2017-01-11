<?php

class FVets_Datavalidate_Helper_Validate extends Mage_Core_Helper_Abstract
{
	protected $toName = 'TI - 4VETS';
	protected $toEmail;
	protected $websitesXWarehouses = array();

	public function executeDataValidation()
	{
		$websites = Mage::helper('datavalidate')->getWebsiteCollection();
		foreach ($websites as $website) {
			$enabled = $website->getConfig('datavalidate/general/enabled');
			if (!$enabled) {
				continue;
			}

			$this->toEmail = $website->getConfig('datavalidate/general/sendto');

			if (!$this->toEmail) {
				continue;
			}

			if (!array_key_exists($website->getId(), $this->getWebsitesXWarehouses())) {
				continue;
			}
			$wharehouseId = $this->getWebsitesXWarehouses()[$website->getId()];
			if (!$wharehouseId) {
				return;
			}
			try {
				if ($website->getConfig('datavalidate/general/salesreps_with_no_images_assigned')) {
					$this->salesrepsWithNoImagesAssigned($website->getId());
				}

				if ($website->getConfig('datavalidate/general/products_with_no_images_assigned')) {
					$this->productsWithNoImagesAssigned($website->getId());
				}

				if ($website->getConfig('datavalidate/general/products_with_no_storeview_scope_price')) {
					$this->productsWithNoStoreviewScopePrice($website->getId());
				}
				if ($website->getConfig('datavalidate/general/products_with_magento_native_price_zero')) {
					$this->productsWithMagentoNativePriceZero($website->getId());
				}
				if ($website->getConfig('datavalidate/general/products_with_no_categories_assigned')) {
					$this->productsWithNoCategoriesAssigned($website->getId());
				}
				if ($website->getConfig('datavalidate/general/products_with_different_prices_between_bigger_group_price_and_normal_price')) {
					$this->productsWithDifferentPricesBetweenBiggerGropuPriceAndNormalPrice($website->getId());
				}
				if ($website->getConfig('datavalidate/general/products_duplicated_id_erp_for_same_website')) {
					$this->productsDuplicatedIdErpForSameWebsite($website->getId());
				}
				if ($website->getConfig('datavalidate/general/products_managing_stock')) {
					$this->productsManagingStock($website->getId(), $wharehouseId);
				}
				if ($website->getConfig('datavalidate/general/products_not_managing_stock')) {
					$this->productsNotManagingStock($website->getId(), $wharehouseId);
				}
				if ($website->getConfig('datavalidate/general/products_without_stock')) {
					$this->productsWithoutStock($website->getId(), $wharehouseId);
				}
				if ($website->getConfig('datavalidate/general/products_without_website_attached')) {
					$this->productsWithNoWebsiteAttached();
				}
				if ($website->getConfig('datavalidate/general/customer_without_salesrep')) {
					$this->customerWithoutSalesrep($website->getId());
				}
				if ($website->getConfig('datavalidate/general/reps_without_email')) {
					$this->repsWithoutEmail($website->getId());
				}
				if ($website->getConfig('datavalidate/general/customers_with_stores_without_salesrep')) {
					$this->customersWithStoresWithoutSalesrep($website->getId());
				}
				if ($website->getConfig('datavalidate/general/customers_with_duplicated_iderp')) {
					$this->customersWithDuplicatedIdErp($website->getId());
				}
				if ($website->getConfig('datavalidate/general/customers_without_default_billing_or_shipping_address')) {
					$this->customerWithoutDefaultBillingOrShippingAddress($website->getId());
				}

				if ($website->getConfig('datavalidate/general/customers_approved_without_commission')) {

					$this->customerApprovedWithoutCommission($website->getId());
				}

			} catch (Exception $ex) {
				Mage::log('Erro ao realizar consultas de validação', null, 'fvets_datavalidate.log');
			}
		}
	}

	private function productsManagingStock($websiteId, $stockId)
	{
		$query = '/* 1. Selecionar produtos (ativos) que estão com flag de gerenciar estoque para uma warehouse específica (stock_id) */
					SELECT distinct(cpe.entity_id), cpe.sku
					/* seleciona produtos para um determinado website */
					FROM catalog_product_entity cpe
					JOIN catalog_product_website cpw ON cpw.product_id = cpe.entity_id AND cpw.website_id = ' . $websiteId . '
					/* seleciona atributos para produtos de um determinado website - Caso não hajam produtos setados na storeview deste website, é verificado na
					storeview 0 (default) */
					JOIN catalog_product_entity_int cpei1 ON cpei1.entity_id IN (CASE WHEN EXISTS (
					SELECT DISTINCT(cpei.entity_id)
					FROM catalog_product_entity_int cpei
					WHERE cpei.attribute_id = 96 AND cpei.entity_id = cpe.entity_id and cpei.entity_type_id = 4 AND cpei.value in (1, 2) AND cpei.store_id IN (
					SELECT cs.store_id
					FROM core_store cs
					WHERE cs.website_id = cpw.website_id)) THEN (
					SELECT DISTINCT(cpei.entity_id)
					FROM catalog_product_entity_int cpei
					WHERE cpei.attribute_id = 96 AND cpei.entity_id = cpe.entity_id and cpei.entity_type_id = 4 AND cpei.value = 1 AND cpei.store_id IN (
					SELECT cs.store_id
					FROM core_store cs
					WHERE cs.website_id = cpw.website_id)) ELSE (
					SELECT DISTINCT(cpei.entity_id)
					FROM catalog_product_entity_int cpei
					WHERE cpei.attribute_id = 96 AND cpei.entity_id = cpe.entity_id and cpei.entity_type_id = 4 AND cpei.value = 1 AND cpei.store_id = 0) END)
					/* filtra os produtos pelo flag de controle de estoque e pelo stock_id */
					JOIN cataloginventory_stock_item csi ON csi.product_id = cpe.entity_id
					WHERE csi.manage_stock = 1 AND csi.stock_id = ' . $stockId;

		$resource = Mage::getSingleton('core/resource');
		$readConnection = $resource->getConnection('core_read');

		$result = $readConnection->fetchAll($query);

		if (!$result) {
			return;
		}

		$msgBody = $this->formatValuesToHtml($result);

		$subject = (Mage::app()->getWebsite($websiteId)->getName() . ' - Produtos setados com flag para GERENCIAR estoque');
		$htmlSubject = '<h3>' . $subject . '</h3>';
		$message = $htmlSubject;
		$message .= $msgBody;
		$message = $this->addQueryStringToMessage($query, $message);
		$this->sendMail($subject, $message, $result);
	}

	private function productsNotManagingStock($websiteId, $stockId)
	{
		$query = '/* 2. Selecionar produtos (ativos) que estão com flag de "não gerenciar estoque" para uma warehouse específica (stock_id) */
SELECT DISTINCT(cpe.entity_id), cpe.sku
FROM
catalog_product_entity cpe
JOIN catalog_product_website cpw ON cpw.product_id = cpe.entity_id AND cpw.website_id = ' . $websiteId . '
/* seleciona atributos para produtos de um determinado website - Caso não hajam produtos setados na storeview deste website, é verificado na
storeview 0 (default) */
JOIN catalog_product_entity_int cpei1 ON cpei1.entity_id IN (CASE WHEN EXISTS (
SELECT DISTINCT(cpei.entity_id)
FROM catalog_product_entity_int cpei
WHERE cpei.attribute_id = 96 AND cpei.entity_id = cpe.entity_id and cpei.entity_type_id = 4 AND cpei.value in (1, 2) AND cpei.store_id IN (
SELECT cs.store_id
FROM core_store cs
WHERE cs.website_id = cpw.website_id)) THEN (
SELECT DISTINCT(cpei.entity_id)
FROM catalog_product_entity_int cpei
WHERE cpei.attribute_id = 96 AND cpei.entity_id = cpe.entity_id and cpei.entity_type_id = 4 AND cpei.value = 1 AND cpei.store_id IN (
SELECT cs.store_id
FROM core_store cs
WHERE cs.website_id = cpw.website_id)) ELSE (
SELECT DISTINCT(cpei.entity_id)
FROM catalog_product_entity_int cpei
WHERE cpei.attribute_id = 96 AND cpei.entity_id = cpe.entity_id and cpei.entity_type_id = 4 AND cpei.value = 1 AND cpei.store_id = 0) END)
/* filtra os produtos pela warehouse e que não gerenciam estoque */
JOIN cataloginventory_stock_item csi ON csi.product_id = cpe.entity_id AND csi.manage_stock = 0 AND cpe.type_id = "simple" AND csi.stock_id = ' . $stockId;

		$resource = Mage::getSingleton('core/resource');
		$readConnection = $resource->getConnection('core_read');

		$result = $readConnection->fetchAll($query);

		if (!$result) {
			return;
		}

		$msgBody = $this->formatValuesToHtml($result);

		$subject = (Mage::app()->getWebsite($websiteId)->getName() . ' - Produtos setados com flag para NÃO GERENCIAR estoque');
		$htmlSubject = '<h3>' . $subject . '</h3>';
		$message = $htmlSubject;
		$message .= $msgBody;
		$message = $this->addQueryStringToMessage($query, $message);
		$this->sendMail($subject, $message, $result);
	}

	private function productsWithoutStock($websiteId, $stockId)
	{
		$query = '/* 4. Selecionar produtos (habilitados) que estão sem estoque numa determinada wareshouse */
SELECT distinct(cpe.entity_id), cpe.sku
FROM
/* tabela principal de produtos */
catalog_product_entity cpe
/* filtrando produtos por website */
JOIN catalog_product_website cpw ON cpw.product_id = cpe.entity_id AND cpw.website_id = ' . $websiteId . '
/* seleciona atributos para produtos de um determinado website - Caso não hajam produtos setados na storeview deste website, é verificado na
storeview 0 (default) */
JOIN catalog_product_entity_int cpei1 ON cpei1.entity_id IN (CASE WHEN EXISTS (
SELECT DISTINCT(cpei.entity_id)
FROM catalog_product_entity_int cpei
WHERE cpei.attribute_id = 96 AND cpei.entity_id = cpe.entity_id and cpei.entity_type_id = 4 AND cpei.value in (1, 2) AND cpei.store_id IN (
SELECT cs.store_id
FROM core_store cs
WHERE cs.website_id = cpw.website_id)) THEN (
SELECT DISTINCT(cpei.entity_id)
FROM catalog_product_entity_int cpei
WHERE cpei.attribute_id = 96 AND cpei.entity_id = cpe.entity_id and cpei.entity_type_id = 4 AND cpei.value = 1 AND cpei.store_id IN (
SELECT cs.store_id
FROM core_store cs
WHERE cs.website_id = cpw.website_id)) ELSE (
SELECT DISTINCT(cpei.entity_id)
FROM catalog_product_entity_int cpei
WHERE cpei.attribute_id = 96 AND cpei.entity_id = cpe.entity_id and cpei.entity_type_id = 4 AND cpei.value = 1 AND cpei.store_id = 0) END)
JOIN cataloginventory_stock_item csi ON csi.product_id = cpe.entity_id AND csi.manage_stock = 1 AND csi.qty = 0 AND csi.stock_id = ' . $stockId;

		$resource = Mage::getSingleton('core/resource');
		$readConnection = $resource->getConnection('core_read');

		$result = $readConnection->fetchAll($query);

		if (!$result) {
			return;
		}

		$msgBody = $this->formatValuesToHtml($result);

		$subject = (Mage::app()->getWebsite($websiteId)->getName() . ' - Produtos que estão com estoque ZERADO');
		$htmlSubject = '<h3>' . $subject . '</h3>';
		$message = $htmlSubject;
		$message .= $msgBody;
		$message = $this->addQueryStringToMessage($query, $message);
		$this->sendMail($subject, $message, $result);
	}

	private function productsWithMagentoNativePriceZero($websiteId)
	{
		$query = '/* 5. Selecionar produtos (ativos) que estão com o preço GERAL(price) zerado nas storeviews */
SELECT distinct(cpe.entity_id), cpe.sku
FROM catalog_product_entity cpe
JOIN catalog_product_website cpw ON cpw.product_id = cpe.entity_id AND cpw.website_id = ' . $websiteId . '
/* aux. seleciona atributos para produtos de um determinado website - Caso não hajam produtos setados na storeview deste website, é verificado na
storeview 0 (default) */
JOIN catalog_product_entity_int cpei1 ON cpei1.entity_id IN (CASE WHEN EXISTS (
SELECT DISTINCT(cpei.entity_id)
FROM catalog_product_entity_int cpei
WHERE cpei.attribute_id = 96 AND cpei.entity_id = cpe.entity_id and cpei.entity_type_id = 4 AND cpei.value in (1, 2) AND cpei.store_id IN (
SELECT cs.store_id
FROM core_store cs
WHERE cs.website_id = cpw.website_id)) THEN (
SELECT DISTINCT(cpei.entity_id)
FROM catalog_product_entity_int cpei
WHERE cpei.attribute_id = 96 AND cpei.entity_id = cpe.entity_id and cpei.entity_type_id = 4 AND cpei.value = 1 AND cpei.store_id IN (
SELECT cs.store_id
FROM core_store cs
WHERE cs.website_id = cpw.website_id)) ELSE (
SELECT DISTINCT(cpei.entity_id)
FROM catalog_product_entity_int cpei
WHERE cpei.attribute_id = 96 AND cpei.entity_id = cpe.entity_id and cpei.entity_type_id = 4 AND cpei.value = 1 AND cpei.store_id = 0) END)
JOIN catalog_product_entity_decimal cped ON cped.attribute_id = 75 AND cped.store_id IN (
SELECT cs.store_id
FROM core_store cs
WHERE cs.website_id = cpw.website_id) AND cped.value = 0';

		$resource = Mage::getSingleton('core/resource');
		$readConnection = $resource->getConnection('core_read');

		$result = $readConnection->fetchAll($query);

		if (!$result) {
			return;
		}

		$msgBody = $this->formatValuesToHtml($result);

		$subject = (Mage::app()->getWebsite($websiteId)->getName() . ' - Produtos que estão com PREÇO ZERO no campo de preço DEFAULT');
		$htmlSubject = '<h3>' . $subject . '</h3>';
		$message = $htmlSubject;
		$message .= $msgBody;
		$message = $this->addQueryStringToMessage($query, $message);
		$this->sendMail($subject, $message, $result);
	}

	private function productsWithNoStoreviewScopePrice($websiteId)
	{
		$query = '/* 6. Selecionar produtos (ativos) que não possuem preço GERAL(price) setado a nível de storeview */
SELECT DISTINCT(cpe.entity_id), cpe.sku
FROM catalog_product_entity cpe
JOIN catalog_product_website cpw ON cpw.product_id = cpe.entity_id AND cpw.website_id = ' . $websiteId . '
/* aux. seleciona atributos para produtos de um determinado website - Caso não hajam produtos setados na storeview deste website, é verificado na
storeview 0 (default) */
JOIN catalog_product_entity_int cpei1 ON cpei1.entity_id IN (CASE WHEN EXISTS (
SELECT DISTINCT(cpei.entity_id)
FROM catalog_product_entity_int cpei
WHERE cpei.attribute_id = 96 AND cpei.entity_id = cpe.entity_id and cpei.entity_type_id = 4 AND cpei.value in (1, 2) AND cpei.store_id IN (
SELECT cs.store_id
FROM core_store cs
WHERE cs.website_id = cpw.website_id)) THEN (
SELECT DISTINCT(cpei.entity_id)
FROM catalog_product_entity_int cpei
WHERE cpei.attribute_id = 96 AND cpei.entity_id = cpe.entity_id and cpei.entity_type_id = 4 AND cpei.value = 1 AND cpei.store_id IN (
SELECT cs.store_id
FROM core_store cs
WHERE cs.website_id = cpw.website_id)) ELSE (
SELECT DISTINCT(cpei.entity_id)
FROM catalog_product_entity_int cpei
WHERE cpei.attribute_id = 96 AND cpei.entity_id = cpe.entity_id and cpei.entity_type_id = 4 AND cpei.value = 1 AND cpei.store_id = 0) END)
WHERE (
SELECT COUNT(*)
FROM catalog_product_entity_decimal cped
WHERE cped.attribute_id = 75 AND cped.entity_id = cpe.entity_id AND cped.store_id IN (
SELECT cs.store_id
FROM core_store cs
WHERE cs.website_id = cpw.website_id)) = 0';

		$resource = Mage::getSingleton('core/resource');
		$readConnection = $resource->getConnection('core_read');

		$result = $readConnection->fetchAll($query);

		if (!$result) {
			return;
		}

		$msgBody = $this->formatValuesToHtml($result);

		$subject = (Mage::app()->getWebsite($websiteId)->getName() . ' - Produtos que estão sem preço setado a nível de Storeview');
		$htmlSubject = '<h3>' . $subject . '</h3>';
		$message = $htmlSubject;
		$message .= $msgBody;
		$message = $this->addQueryStringToMessage($query, $message);
		$this->sendMail($subject, $message, $result);
	}

	private function productsWithNoCategoriesAssigned($websiteId)
	{
		$query = '/* 9. Selecionar produtos que estão sem categorias vinculadas */
SELECT DISTINCT(cpe.entity_id), cpe.sku
FROM catalog_product_entity cpe
JOIN catalog_product_website cpw ON cpw.product_id = cpe.entity_id AND cpw.website_id = ' . $websiteId . '
JOIN catalog_product_entity_int cpei1 ON cpei1.entity_id IN (CASE WHEN EXISTS (
SELECT DISTINCT(cpei.entity_id)
FROM catalog_product_entity_int cpei
WHERE cpei.attribute_id = 96 AND cpei.entity_id = cpe.entity_id and cpei.entity_type_id = 4 AND cpei.value in (1, 2) AND cpei.store_id IN (
SELECT cs.store_id
FROM core_store cs
WHERE cs.website_id = cpw.website_id)) THEN (
SELECT DISTINCT(cpei.entity_id)
FROM catalog_product_entity_int cpei
WHERE cpei.attribute_id = 96 AND cpei.entity_id = cpe.entity_id and cpei.entity_type_id = 4 AND cpei.value = 1 AND cpei.store_id IN (
SELECT cs.store_id
FROM core_store cs
WHERE cs.website_id = cpw.website_id)) ELSE (
SELECT DISTINCT(cpei.entity_id)
FROM catalog_product_entity_int cpei
WHERE cpei.attribute_id = 96 AND cpei.entity_id = cpe.entity_id and cpei.entity_type_id = 4 AND cpei.value = 1 AND cpei.store_id = 0) END)
WHERE (
SELECT COUNT(*)
FROM catalog_category_product ccp
WHERE ccp.product_id = cpe.entity_id) = 0';

		$resource = Mage::getSingleton('core/resource');
		$readConnection = $resource->getConnection('core_read');

		$result = $readConnection->fetchAll($query);

		if (!$result) {
			return;
		}

		$msgBody = $this->formatValuesToHtml($result);

		$subject = (Mage::app()->getWebsite($websiteId)->getName() . ' - Produtos que estão sem pelo menos uma categoria vinculada');
		$htmlSubject = '<h3>' . $subject . '</h3>';
		$message = $htmlSubject;
		$message .= $msgBody;
		$message = $this->addQueryStringToMessage($query, $message);
		$this->sendMail($subject, $message, $result);
	}

	private function productsWithNoImagesAssigned($websiteId)
	{
		$query = '/* 10. Selecionar produtos que estão sem imagem definida */
SELECT DISTINCT(cpe.entity_id), cpe.sku
FROM
catalog_product_entity cpe
JOIN catalog_product_website cpw ON cpw.product_id = cpe.entity_id AND cpw.website_id = ' . $websiteId . '
JOIN catalog_product_entity_int cpei1 ON cpei1.entity_id IN (CASE WHEN EXISTS (
SELECT DISTINCT(cpei.entity_id)
FROM catalog_product_entity_int cpei
WHERE cpei.attribute_id = 96 AND cpei.entity_id = cpe.entity_id AND cpei.entity_type_id = 4 AND cpei.value in (1, 2) AND cpei.store_id IN (
SELECT cs.store_id
FROM core_store cs
WHERE cs.website_id = cpw.website_id)) THEN (
SELECT DISTINCT(cpei.entity_id)
FROM catalog_product_entity_int cpei
WHERE cpei.attribute_id = 96 AND cpei.entity_id = cpe.entity_id AND cpei.entity_type_id = 4 AND cpei.value = 1 AND cpei.store_id IN (
SELECT cs.store_id
FROM core_store cs
WHERE cs.website_id = cpw.website_id)) ELSE (
SELECT DISTINCT(cpei.entity_id)
FROM catalog_product_entity_int cpei
WHERE cpei.attribute_id = 96 AND cpei.entity_id = cpe.entity_id AND cpei.entity_type_id = 4 AND cpei.value = 1 AND cpei.store_id = 0) END)
WHERE NOT EXISTS (
SELECT *
FROM catalog_product_entity_varchar cpev
WHERE cpev.attribute_id = 85 AND cpev.entity_id = cpe.entity_id)';

		$resource = Mage::getSingleton('core/resource');
		$readConnection = $resource->getConnection('core_read');

		$result = $readConnection->fetchAll($query);

		if (!$result) {
			return;
		}

		$msgBody = $this->formatValuesToHtml($result);

		$subject = (Mage::app()->getWebsite($websiteId)->getName() . ' - Produtos que estão sem imagens cadastradas');
		$htmlSubject = '<h3>' . $subject . '</h3>';
		$message = $htmlSubject;
		$message .= $msgBody;
		$message = $this->addQueryStringToMessage($query, $message);
		$this->sendMail($subject, $message, $result);
	}

	private function productsDuplicatedIdErpForSameWebsite($websiteId)
	{
		$query = '/* Procura todos os produtos com mesmo ID_ERP para o mesmo website */

SELECT cpev_name.value as name, cpev.value as ID_ERP, group_concat(distinct(cpev.entity_id)) AS duplicated, group_concat(distinct(cpe.sku)) AS SKU, group_concat(distinct(cpev.store_id)) AS store_id, cw.name as website
FROM catalog_product_entity_varchar AS cpev
LEFT JOIN core_store AS cs ON cs.store_id = cpev.store_id
LEFT JOIN catalog_product_entity_varchar as cpev_name ON cpev_name.entity_id = cpev.entity_id AND cpev_name.entity_type_id = 4 AND cpev_name.attribute_id = 71
LEFT JOIN catalog_product_entity_int as cpei_status ON cpei_status.entity_id = cpev.entity_id AND cpei_status.entity_type_id = 4 AND cpei_status.attribute_id = 96
LEFT JOIN core_website as cw ON cs.website_id = cw.website_id
LEFT JOIN catalog_product_entity as cpe ON cpe.entity_id = cpev.entity_id
WHERE cpev.entity_type_id = 4
AND cpev.attribute_id = 185
AND cs.store_id NOT IN (0, 1)
AND cpei_status.value = 1
GROUP BY cpev.value, cs.website_id
HAVING COUNT(DISTINCT(cpev.entity_id)) > 1
ORDER BY `cpev`.`value` ASC';

		$resource = Mage::getSingleton('core/resource');
		$readConnection = $resource->getConnection('core_read');

		$result = $readConnection->fetchAll($query);

		if (!$result) {
			return;
		}

		$msgBody = $this->formatValuesToHtml($result);

		$subject = (Mage::app()->getWebsite($websiteId)->getName() . ' - Produtos com mesmo ID_ERP para o mesmo website');
		$htmlSubject = '<h3>' . $subject . '</h3>';
		$message = $htmlSubject;
		$message .= $msgBody;
		$message = $this->addQueryStringToMessage($query, $message);
		$this->sendMail($subject, $message, $result);
	}

	private function productsWithDifferentPricesBetweenBiggerGropuPriceAndNormalPrice($websiteId)
	{
		$query = '/* Procura todos os produtos do website onde o preço do maior group_price for diferente do preço do produto.
Antes de rodar precisa setar todos os preços para nível de store view. */

SELECT cpe.entity_id, cpe.sku,  round(cpegp.value, 2) as group_price, round(cped.value, 2) as product_price, cped.store_id
FROM catalog_product_entity as cpe
INNER JOIN (SELECT cpegp2.entity_id as entity_id, MAX(cpegp2.value) as value, cpegp2.website_id as website_id FROM catalog_product_entity_group_price as cpegp2 GROUP BY cpegp2.entity_id, cpegp2.website_id AND cpegp2.website_id = ' . $websiteId . ') as cpegp ON cpegp.entity_id = cpe.entity_id
INNER JOIN catalog_product_entity_decimal as cped ON cped.attribute_id = 75 AND cped.entity_id = cpe.entity_id AND round(cpegp.value, 2) != round(cped.value, 2)
INNER JOIN core_store AS cs ON cs.store_id = cped.store_id AND cpegp.website_id = cs.website_id
WHERE cped.store_id NOT IN(0,1)
AND cpe.sku IS NOT NULL
ORDER BY cpe.entity_id ASC';

		$resource = Mage::getSingleton('core/resource');
		$readConnection = $resource->getConnection('core_read');

		$result = $readConnection->fetchAll($query);

		if (!$result) {
			return;
		}

		$msgBody = $this->formatValuesToHtml($result);

		$subject = (Mage::app()->getWebsite($websiteId)->getName() . ' - Procura todos os produtos onde o preço do maior group_price for diferente do preço do produto.');
		$htmlSubject = '<h3>' . $subject . '</h3>';
		$message = $htmlSubject;
		$message .= $msgBody;
		$message = $this->addQueryStringToMessage($query, $message);
		$this->sendMail($subject, $message, $result);
	}

	private function productsWithNoWebsiteAttached()
	{
		$query = '/* Procura todos os produtos que não possuem um website vinculado */

SELECT DISTINCT(cpe.entity_id), cpe.sku, cpev.value as name
FROM catalog_product_entity cpe
join catalog_product_entity_varchar cpev on cpev.entity_id = cpe.entity_id and cpev.entity_type_id = 4 and cpev.attribute_id = 71
WHERE cpe.entity_id NOT IN (
SELECT cpw.product_id
FROM catalog_product_website cpw
WHERE cpw.product_id = cpe.entity_id)';

		$resource = Mage::getSingleton('core/resource');
		$readConnection = $resource->getConnection('core_read');

		$result = $readConnection->fetchAll($query);

		if (!$result) {
			return;
		}

		$msgBody = $this->formatValuesToHtml($result);

		$subject = ('GLOBAL - Procura todos os produtos que não possuem um website vinculado.');
		$htmlSubject = '<h3>' . $subject . '</h3>';
		$message = $htmlSubject;
		$message .= $msgBody;
		$message = $this->addQueryStringToMessage($query, $message);
		$this->sendMail($subject, $message, $result);
	}

	private function customerWithoutSalesrep($websiteid)
	{
		$query = "/* Procura clientes que nao possuem um representante vinculado */

				SELECT
					customer_iderp.value as customer_id_erp,
					`e` . *,
					`at_fvets_salesrep`.`value` AS `fvets_salesrep`
				FROM
					`customer_entity` AS `e`
						LEFT JOIN
					`customer_entity_varchar` AS `at_fvets_salesrep` ON (`at_fvets_salesrep`.`entity_id` = `e`.`entity_id`)
						AND (`at_fvets_salesrep`.`attribute_id` = '148')
					LEFT JOIN
					`customer_entity_varchar` AS `customer_iderp` ON (`customer_iderp`.`entity_id` = `e`.`entity_id`)
						AND (`customer_iderp`.`attribute_id` = '183')
				WHERE
					(`e`.`entity_type_id` = '1')
						AND (`e`.`website_id` = '{$websiteid}')
						AND ((at_fvets_salesrep.value IS NULL)
						OR (((at_fvets_salesrep.value = 'fvets_salesrep')
						OR (at_fvets_salesrep.value = ''))))
						AND (`e`.`mp_cc_is_approved` = '".MageParts_ConfirmCustomer_Helper_Data::STATE_APPROVED."')
		";

		$resource = Mage::getSingleton('core/resource');
		$readConnection = $resource->getConnection('core_read');

		$result = $readConnection->fetchAll($query);

		if (!$result) {
			return;
		}

		$msgBody = $this->formatValuesToHtml($result);

		$subject = (Mage::app()->getWebsite($websiteid)->getName() . ' - Clientes que nao possuem um representante vinculado.');
		$htmlSubject = '<h3>' . $subject . '</h3>';
		$message = $htmlSubject;
		$message .= $msgBody;
		$message = $this->addQueryStringToMessage($query, $message);
		$this->sendMail($subject, $message, $result);
	}

	private  function customersWithStoresWithoutSalesrep($websiteid)
	{

		$query = "SELECT
					at_stores.value as customer_stores,
					GROUP_CONCAT(salesrep.store_id) salesrep_stores,
					at_fvets_salesrep.value as customer_salesrep,
					GROUP_CONCAT(salesrep.id_erp) salesrep_id_erp,
					`e` . entity_id as customer_id,
					customer_iderp.value as customer_id_erp,
					`e` . email as email,
					`e` . website_id as website_id,
					`e` . group_id as group_id
				FROM
					`customer_entity` AS `e`
						LEFT JOIN
					`customer_entity_text` AS `at_stores` ON (`at_stores`.`entity_id` = `e`.`entity_id`)
						AND (`at_stores`.`attribute_id` = '191')
						LEFT JOIN
					`customer_entity_varchar` AS `at_fvets_salesrep` ON (`at_fvets_salesrep`.`entity_id` = `e`.`entity_id`)
						AND (`at_fvets_salesrep`.`attribute_id` = '148')
						LEFT JOIN
					`customer_entity_varchar` AS `customer_iderp` ON (`customer_iderp`.`entity_id` = `e`.`entity_id`)
						AND (`customer_iderp`.`attribute_id` = '183')
						INNER JOIN
					`fvets_salesrep` as `salesrep` ON FIND_IN_SET(salesrep.id, at_fvets_salesrep.value)
				WHERE
					at_stores.value != salesrep.store_id
						AND NOT FIND_IN_SET(at_stores.value, salesrep.store_id)
						AND e.mp_cc_is_approved = '".MageParts_ConfirmCustomer_Helper_Data::STATE_APPROVED."'
						AND e.website_id = {$websiteid}
				GROUP BY e.entity_id
				ORDER BY e.entity_id DESC";

		$resource = Mage::getSingleton('core/resource');
		$readConnection = $resource->getConnection('core_read');

		$results = $readConnection->fetchAll($query);

		$errors = array();

		foreach($results as $result)
		{
			$customer_stores = explode(',', $result['customer_stores']);
			$salesrep_stores = explode(',', $result['salesrep_stores']);

			foreach ($customer_stores as $store)
			{
				if (!in_array($store, $salesrep_stores))
				{
					$errors[] = $result;
					continue 2;
				}
			}
		}

		if (count($errors) <= 0)
		{
			return;
		}

		$msgBody = $this->formatValuesToHtml($errors);

		$subject = (Mage::app()->getWebsite($websiteid)->getName() . ' - clientes com grupos de acesso sem representantes.');
		$htmlSubject = '<h3>' . $subject . '</h3>';
		$message = $htmlSubject;
		$message .= $msgBody;
		$message = $this->addQueryStringToMessage($query, $message);
		//file_put_contents('export.csv', $this->str_putcsv($results));
		$this->sendMail($subject, $message, $results);

	}

	private function customersWithDuplicatedIdErp($websiteid)
	{
		$query = "/* Procura clientes com o mesmo ID ERP */

				SELECT
				customer_iderp_1.value as id_erp,
				customer_iderp_2.value as id_erp,
				`e1`.entity_id as entity_id_1,
				`e1`.email as email_1,
				`e2`.entity_id as entity_id_2,
				`e2`.email as email_2
			FROM
				`customer_entity` AS `e1`,
				`customer_entity` AS `e2`
					LEFT JOIN
				`customer_entity_varchar` AS `customer_iderp_1` ON (`customer_iderp_1`.`attribute_id` = '183')
					LEFT JOIN
				`customer_entity_varchar` AS `customer_iderp_2` ON (`customer_iderp_2`.`attribute_id` = '183')
			WHERE
				(`e1`.`entity_type_id` = '1' AND `e2`.`entity_type_id` = '1')
				AND (`customer_iderp_1`.`entity_id` = `e1`.`entity_id`)
				AND (`customer_iderp_2`.`entity_id` = `e2`.`entity_id`)
				AND (`e1`.`website_id` = '{$websiteid}' AND `e2`.`website_id` = '{$websiteid}')
				AND (`customer_iderp_1`.value = `customer_iderp_2`.value)
				AND (`e1`.`mp_cc_is_approved` = '".MageParts_ConfirmCustomer_Helper_Data::STATE_APPROVED."' AND `e2`.`mp_cc_is_approved` = '".MageParts_ConfirmCustomer_Helper_Data::STATE_APPROVED."')
				AND (e1.entity_id <> e2.entity_id);
		";

		$resource = Mage::getSingleton('core/resource');
		$readConnection = $resource->getConnection('core_read');

		$result = $readConnection->fetchAll($query);

		if (!$result) {
			return;
		}

		$msgBody = $this->formatValuesToHtml($result);

		$subject = (Mage::app()->getWebsite($websiteid)->getName() . ' - Clientes com o mesmo ID ERP.');
		$htmlSubject = '<h3>' . $subject . '</h3>';
		$message = $htmlSubject;
		$message .= $msgBody;
		$message = $this->addQueryStringToMessage($query, $message);
		$this->sendMail($subject, $message, $result);
	}

	private function repsWithoutEmail($websiteid)
	{
		$storeIds = Mage::getModel('core/website')->load($websiteid)->getStoreIds();
		$storeId = reset($storeIds);

		$query = "/* Procura representantes sem um email cadastrado */
select * from fvets_salesrep fs where (fs.email is null or fs.email = '') and find_in_set($storeId, store_id)";

		$resource = Mage::getSingleton('core/resource');
		$readConnection = $resource->getConnection('core_read');

		$result = $readConnection->fetchAll($query);

		if (!$result) {
			return;
		}

		$msgBody = $this->formatValuesToHtml($result);

		$subject = (Mage::app()->getWebsite($websiteid)->getName() . ' - Procura representantes sem um email cadastrado.');
		$htmlSubject = '<h3>' . $subject . '</h3>';
		$message = $htmlSubject;
		$message .= $msgBody;
		$message = $this->addQueryStringToMessage($query, $message);
		$this->sendMail($subject, $message, $result);
	}

	private function customerWithoutDefaultBillingOrShipping($websiteid)
	{
		$query = "/* Clientes sem endereço padrão de cobrança ou envio */
		SELECT `e`.*, `at_default_billing`.`value` AS `default_billing`, `at_default_shipping`.`value` AS `default_shipping`
		FROM `customer_entity` AS `e`
		LEFT JOIN `customer_entity_int` AS `at_default_billing` ON (`at_default_billing`.`entity_id` = `e`.`entity_id`) AND (`at_default_billing`.`attribute_id` = '13')
		LEFT JOIN `customer_entity_int` AS `at_default_shipping` ON (`at_default_shipping`.`entity_id` = `e`.`entity_id`) AND (`at_default_shipping`.`attribute_id` = '14')
		WHERE (`e`.`entity_type_id` = '1') AND ((at_default_billing.value IS NULL) OR (at_default_shipping.value IS NULL)) AND (`e`.`mp_cc_is_approved` = '".MageParts_ConfirmCustomer_Helper_Data::STATE_APPROVED."') AND (`e`.`website_id` = '{$websiteid}')";

		$resource = Mage::getSingleton('core/resource');
		$readConnection = $resource->getConnection('core_read');

		$result = $readConnection->fetchAll($query);

		if (!$result) {
			return;
		}

		$msgBody = $this->formatValuesToHtml($result);

		$subject = (Mage::app()->getWebsite($websiteid)->getName() . ' - Clientes sem endereço padrão de cobrança ou envio.');
		$htmlSubject = '<h3>' . $subject . '</h3>';
		$message = $htmlSubject;
		$message .= $msgBody;
		$message = $this->addQueryStringToMessage($query, $message);
		$this->sendMail($subject, $message, $result);
	}

	private function salesrepsWithNoImagesAssigned($websiteid) {
		$storeIds = Mage::getModel('core/website')->load($websiteid)->getStoreIds();
		$salesreps = Mage::getModel('fvets_salesrep/salesrep')->getCollection();

		$arrayTemp = array();
		foreach ($storeIds as $storeId) {
			$arrayTemp[] = array('finset' => $storeId);
		}

		$salesreps->addFieldToFilter('store_id', $arrayTemp);

		$result = '';
		foreach($salesreps as $salesrep) {
			$imgPath = Mage::getBaseDir('media') . '/fvets/salesrep/' . $salesrep->getId() . '.jpg';

			if (!file_exists($imgPath)) {
				$result .= $salesrep->getId() . '|' . $salesrep->getName() . "<br>";
			}
		}

		if (!$result) {
			return;
		}

		$msgBody = $result;

		$subject = (Mage::app()->getWebsite($websiteid)->getName() . ' - Representantes sem foto.');
		$htmlSubject = '<h3>' . $subject . '</h3>';
		$message = $htmlSubject;
		$message .= $msgBody;

		$this->sendMail($subject, $message, $result);
	}

	private function customerApprovedWithoutCommission($websiteId) 
	{

		$query = " /* Procura por clientes que estão aprovados e sem o valor da comissão associado */

				SELECT 
					entity_id 
				FROM 
					customer_entity
				WHERE 
					website_id = '{$websiteId}' AND mp_cc_is_approved = '".MageParts_ConfirmCustomer_Helper_Data::STATE_APPROVED."' and is_active = 1
					AND entity_id IN (
						SELECT 
							entity_id 
						FROM 
							customer_entity_varchar 
						WHERE 
							attribute_id = 186 AND `value` IN('SITE', 'ERP')
					) 

					AND entity_id NOT IN (

						SELECT 
							entity_id 
						FROM 
							customer_entity_decimal 
						WHERE 
							attribute_id = 216

					)";
		try {
			$resource = Mage::getSingleton('core/resource');
			$readConnection = $resource->getConnection('core_read');

			$result = $readConnection->fetchAll($query);			
			
			if (!$result) {
				return;
			}

			$msgBody = $this->formatValuesToHtml($result);

			$subject = (Mage::app()->getWebsite($websiteId)->getName() . ' - Clientes ativos e aprovados sem valor de comissão.');
			$htmlSubject = '<h3>' . $subject . '</h3>';
			$message = $htmlSubject;
			$message .= $msgBody;
			$message = $this->addQueryStringToMessage($query, $message);

			$this->sendMail($subject, $message, $result);

		} catch (Exception $e) {
			echo $e->getMessage();
		}		
	}

	private function formatValuesToHtml($query)
	{
		$fieldsHeader = array();
		$fieldsBody = array();

		foreach ($query as $queryKey => $line) {
			foreach ($line as $lineKey => $columns) {
				array_push($fieldsHeader, $lineKey);
			}
			break;
		}

		foreach ($query as $queryKey => $line) {
			$arrayTemp = array();
			foreach ($line as $lineKey => $columns) {
				array_push($arrayTemp, $columns);
			}
			array_push($fieldsBody, $arrayTemp);
		}

		return $this->toTableHtml($fieldsHeader, $fieldsBody);
	}

	private function sendMail($subject, $message, $data = null)
	{
		try {
			$emailTemplate = Mage::getModel('core/email_template');
			$emailTemplate->setSenderEmail(Mage::getStoreConfig('trans_email/ident_custom2/email', 1));
			$emailTemplate->setSenderName(Mage::getStoreConfig('trans_email/ident_custom2/name', 1));
			$emailTemplate->setTemplateSubject($subject);
			$emailTemplate->setTemplateText($message);

			if (isset($data))
			{
				$attachment = $emailTemplate->getMail()->createAttachment($this->str_putcsv($data));
				$attachment->type = 'text/csv';
				$attachment->disposition = Zend_Mime::DISPOSITION_INLINE;
				$attachment->encoding    = Zend_Mime::ENCODING_BASE64;
				$attachment->filename = 'report.csv';
			}

			$emailTemplate->send($this->toEmail, $this->toName, array());

			return true;
		} catch (Exception $ex) {
			return $ex->__toString();
		}
	}

	private function str_putcsv($input, $delimiter = ',', $enclosure = '"')
	{
		// Open a memory "file" for read/write...
		$fp = fopen('php://temp', 'r+');
		// ... write the $input array to the "file" using fputcsv()...
		fputcsv($fp, array_keys($input[0]), $delimiter, $enclosure);

		foreach ($input as $data)
		{
			fputcsv($fp, $data, $delimiter, $enclosure);
		}
		// ... rewind the "file" so we can read what we just wrote...
		rewind($fp);
		// ... read the entire line into a variable...
		$data = fread($fp, 1048576);
		// ... close the "file"...
		fclose($fp);
		// ... and return the $data to the caller, with the trailing newline from fgets() removed.
		return rtrim($data, "\n");
	}

	private function toTableHtml($fieldsHeader, $fieldsBody)
	{
		$html = "<table style='1px solid black'>";
		$html .= '<tr>';
		foreach ($fieldsHeader as $field) {
			$html .= '<th>' . $field . '</th>';
		}
		$html .= '</tr>';
		foreach ($fieldsBody as $fields) {
			$html .= '<tr>';
			foreach ($fields as $field) {
				$html .= '<td>';
				$html .= $field;
				$html .= '</td>';
			}
			$html .= '</tr>';
		}
		$html .= '</table>';

		return $html;
	}

	private function addQueryStringToMessage($query, $message)
	{
		return $message . '<br><br><p>Sql executado:</p><br><br><p>' . $query . '</p>';
	}

	private function getWebsitesXWarehouses()
	{
		if (!$this->websitesXWarehouses)
		{
			$warehouses = Mage::getModel('warehouse/warehouse')->getCollection();
			$data = array();
			foreach ($warehouses as $warehouse)
			{
				$store = $warehouse->getStores()[0];
				if ($store)
				{
					$website = Mage::getModel('core/store')->load($store)->getWebsite();
					$data[$website->getId()] = $warehouse->getWarehouseId();
				}
			}
			$this->websitesXWarehouses = $data;
		}
		return $this->websitesXWarehouses;
	}
}