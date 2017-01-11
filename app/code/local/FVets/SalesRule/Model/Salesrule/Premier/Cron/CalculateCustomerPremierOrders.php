<?php
class FVets_SalesRule_Model_Salesrule_Premier_Observer
{
	function calculatePremierAfterOrder($observer)
	{
		$customer = Mage::getSingleton('customer/session')->getCustomer();

		Mage::app()->setCurrentStore($customer->getStoreId());
		if (Mage::getStoreConfig('fvets_salesrule/premier/status'))
		{
			//Buscar todos os pedidos do mesmo mês do último pedido do cliente
			$orders = Mage::getModel('sales/order')
				->getCollection()
				->addFieldToFilter('customer_id', $customer->getId())
				->addFieldToFilter('created_at', array(array('lt' => date('Y') . '-' . ((int)date('m') + 1) . '-01 00:00:00')))
				->addFieldToFilter('created_at', array(array('gteq' => date('Y-m-d H:i:s', strtotime(date('Y-m') . '-01 00:00:00')))))
			;

			$weight = 0;
			$search_category = Mage::getStoreConfig('fvets_salesrule/premier/category');

			foreach ($orders as $order)
			{
				//Buscar somente produtos da premier
				foreach($order->getAllItems() as $item)
				{
					$product = $item->getProduct();
					$product_categories = $product->getCategoryIds();
					if (in_array($search_category, $product_categories))
					{
						$weight += $product->getWeight() * ($item->getQtyOrdered() - $item->getQtyCanceled());
					}
				}
			}

			if ($weight > 0)
			{
				$this->validatePromo($weight, $customer);
			}
		}
	}

	private function validatePromo($weight, $customer)
	{
		//Buscar as promoções da premier com o mesmo website do cliente
		$collection = Mage::getModel('salesrule/rule')
			->getCollection()
			->addFieldToFilter('rule_type', 2)
			->addWebsiteFilter($customer->getWebsiteId())
		;

		//Forma de ordenar a colection pelo sort_order. Não consegui fazer a ordenação pelo ->order
		/*$collection->getSelect()
			->reset(Zend_Db_Select::FROM)
			->from(array('main_table' => '(SELECT * from salesrule ORDER BY sort_order ASC)'))
		;*/

		//(SELECT * FROM salesrule ORDER BY sort_order ASC) AS `main_table`


		$collection->getSelect()
			->joinInner(
				array('premier' => $collection->getTable('fvets_salesrule/premier')),
				'premier.rule_id=main_table.rule_id'
			)
			->where('premier.from <= ?', $weight)
			->where('premier.to > ?', $weight)
			//->order('main_table.sort_order ASC')
			->group('premier.group')
		;

		//Adiciona o cliente na promoção com maior prioridade encontrada (a menor prioridade e a maior prioridade)
		foreach ($collection as $rule)
		{
			//Pego todos os customers do mesmo grupo da rule que o cliente pode ser inserido
			$collectionExisting = Mage::getModel('salesrule/rule')
				->getCollection()
				->addCustomerFilter($customer)
			;
			$collectionExisting->getSelect()
				->joinInner(
					array('premier' => $collection->getTable('fvets_salesrule/premier')),
					'premier.rule_id=main_table.rule_id AND premier.group = "'.$rule->getGroup().'"'
				)
				->order('main_table.sort_order ASC')
			;
			$ruleExisting = $collectionExisting->getFirstItem();

			//Se já existir outra rule do mesmo grupo da rule atual... (faz)
			//Senão, simplesmente adiciona essa nova rule
			if ($ruleExisting->getId())
			{
				//Se a nova rule for diferente da rule que já existe
				//E a prioridade da nova for maior do que da rule já existente
				//Exclui a que já existe para poder adicionar a nova rule
				if (($rule->getId() != $ruleExisting->getId()) AND ($rule->getSortOrder() < $ruleExisting->getSortOrder()))
				{
					$resource = Mage::getResourceSingleton('fvets_salesrule/salesrule_customer')
						->deletePremierCustomersByCustomerAndRuleRelation($ruleExisting, $customer);

					$data = array(
						$customer->getId() => array('position' => 0)
					);
					$salesruleCustomer = Mage::getResourceSingleton('fvets_salesrule/salesrule_customer')
						->savePremierSalesruleRelation($rule, $data);
				}
			}
			else
			{
				$data = array(
					$customer->getId() => array('position' => 0)
				);
				$salesruleCustomer = Mage::getResourceSingleton('fvets_salesrule/salesrule_customer')
					->savePremierSalesruleRelation($rule, $data);
			}
		}
	}
}