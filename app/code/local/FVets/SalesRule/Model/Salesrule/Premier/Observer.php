<?php
class FVets_SalesRule_Model_Salesrule_Premier_Observer
{
	function calculatePremierAfterOrder($observer)
	{
		$customer = Mage::getSingleton('customer/session')->getCustomer();

		if (Mage::getStoreConfig('fvets_salesrule/premier/status'))
		{
			//Buscar todos os pedidos do mesmo mês do último pedido do cliente
			$orders = Mage::getModel('sales/order')
				->getCollection()
				->addFieldToFilter('customer_id', $customer->getId())
				->addFieldToFilter('created_at', array(array('lt' => date('Y') . '-' . ((int)date('m') + 1) . '-01 00:00:00')))
				->addFieldToFilter('created_at', array(array('gteq' => date('Y-m-d H:i:s', strtotime(date('Y-m') . '-01 00:00:00')))))
			;

			$groups = array();

			foreach ($orders as $order)
			{
				//Buscar somente produtos da premier
				foreach($order->getAllItems() as $item)
				{
					$product = $item->getProduct();
					$policy_group = $product->getPremierPolicyGroup();//Retorna o id do atributo (Tavelz seja melhor para usar junto com as promos)
					if (isset($policy_group) && $policy_group != '')
					{
						$qty = $item->getQtyOrdered() - $item->getQtyCanceled();
						$weight = $product->getWeight() * $qty;
						if (isset($groups[$policy_group]))
						{
							$groups[$policy_group]['weight'] += $weight;
							$groups[$policy_group]['qty'] += $qty;
						}
						else
						{
							$groups[$policy_group] = array('weight' => $weight, 'qty' => $qty);
						}
					}
				}
			}

			foreach($groups as $group => $data)
			{
				$this->validatePromo($group, $data, $customer);
			}
		}
	}

	//@TODO Buscar as promoções pelo grupo da premier setado
	private function validatePromo($group, $data, $customer)
	{
		//Buscar as promoções da premier com o mesmo website do cliente e com o mesmo grupo dos itens
		$collection = Mage::getModel('salesrule/rule')
			->getCollection()
			->addFieldToFilter('rule_type', 2)
			->addWebsiteFilter($customer->getWebsiteId())
		;


		$collection->getSelect()
			->joinInner(
				array('premier' => $collection->getTable('fvets_salesrule/premier')),
				'premier.salesrule_id=main_table.rule_id'
			)
			->where('premier.from <= IF (premier.calculation_type = \'weight\', '.$data['weight'].', '.$data['qty'].')')
			->where('(premier.to > IF (premier.calculation_type = \'weight\', '.$data['weight'].', '.$data['qty'].') OR premier.to = 0)')
			->where('premier.group = ?', $group)
			->order('main_table.sort_order ASC')
			->limit('1')
		;

		$rule = $collection->getFirstItem();

		if (!$rule->getId())
		{
			return;
		}

		//Adiciona o cliente na promoção com maior prioridade encontrada (a menor prioridade é a maior prioridade)

		//Pego todos os customers do mesmo grupo da rule que o cliente pode ser inserido
		$collectionExisting = Mage::getModel('salesrule/rule')
			->getCollection()
			->addCustomerFilter($customer)
		;
		$collectionExisting->getSelect()
			->joinInner(
				array('premier' => $collection->getTable('fvets_salesrule/premier')),
				'premier.salesrule_id=main_table.rule_id AND premier.group = "'.$rule->getGroup().'"'
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