<?php
/**
 * FVets_SalesRule extension
 *
 * @category       FVets
 * @package        FVets_SalesRule
 */
/**
 * Adminhtml observer
 *
 * @category    FVets
 * @package     FVets_SalesRule
 * @author      Douglas Borella Ianitsky
 */
class FVets_SalesRule_Model_Adminhtml_Observer
{
    /**
     * check if tab can be added
     *
     * @access protected
     * @param Mage_Catalog_Model_Product $product
     * @return bool
     * @author Douglas Borella Ianitsky
     */
    protected function _canAddTab($product)
    {
        if ($product->getId()) {
            return true;
        }
        if (!$product->getAttributeSetId()) {
            return false;
        }
        $request = Mage::app()->getRequest();
        if ($request->getParam('type') == 'configurable') {
            if ($request->getParam('attributes')) {
                return true;
            }
        }
        return false;
    }

    /**
     * add the salesrule tab to customers
     *
     * @access public
     * @param Varien_Event_Observer $observer
     * @return FVets_SalesRule_Model_Adminhtml_Observer
     * @author Douglas Borella Ianitsky
     */
    public function addCustomerSalesruleBlock($observer)
    {
        $block = $observer->getEvent()->getBlock();
        if ($block instanceof Mage_Adminhtml_Block_Customer_Edit_Tabs) {
            $block->addTabAfter(
                'salesrules',
                array(
                    'label' => Mage::helper('fvets_salesrule')->__('Special SalesRules'),
                    'url'   => Mage::helper('adminhtml')->getUrl(
                        'adminhtml/salesrule_customer/salesrules',
                        array('_current' => true)
                    ),
                    'class' => 'ajax',
                ),
                'addresses'
            );
        }
        return $this;
    }

	/**
	 * save salesrule - customer relation
	 * @access public
	 * @param Varien_Event_Observer $observer
	 * @return FVets_SalesRule_Model_Adminhtml_Observer
	 * @author Douglas Borella Ianitsky
	 */
	public function saveSalesruleCustomerData($observer)
	{
		$post = Mage::app()->getRequest()->getPost('customers', -1);
		if ($post != '-1') {
			$post = Mage::helper('adminhtml/js')->decodeGridSerializedInput($post);
			$salesrule = $observer->getRule();
			$salesruleCustomer = Mage::getResourceSingleton('fvets_salesrule/salesrule_customer')
				->saveSalesruleRelation($salesrule, $post);
		}
		return $this;
	}

    /**
     * save salesrule - customer relation
     * @access public
     * @param Varien_Event_Observer $observer
     * @return FVets_SalesRule_Model_Adminhtml_Observer
     * @author Douglas Borella Ianitsky
     */
    public function saveCustomerSalesruleData($observer)
    {
        $post = Mage::app()->getRequest()->getPost('salesrules', -1);
        if ($post != '-1') {
            $post = Mage::helper('adminhtml/js')->decodeGridSerializedInput($post);
            $customer = Mage::registry('current_customer');
            $salesruleCustomer = Mage::getResourceSingleton('fvets_salesrule/salesrule_customer')
                ->saveCustomerRelation($customer, $post);
        }
        return $this;
    }

	/**
	 * exclude salesrule - customer relation
	 * @access public
	 * @param Varien_Event_Observer $observer
	 * @return FVets_SalesRule_Model_Adminhtml_Observer
	 * @author Douglas Borella Ianitsky
	 */
	public function deleteGroupSalesruleData($observer)
	{
		if ($observer->getRule()->getId())
		{
			$model = Mage::getModel('fvets_salesrule/salesrule_customer_group')->load($observer->getRule()->getId());

			$salesruleCustomer = Mage::getResourceSingleton('fvets_salesrule/salesrule_customer_group')
				->delete($model);

			$observer->getRule()->unsetData('customer_group_ids');
			$observer->getRule()->setData('customer_group_ids', Mage::app()->getRequest()->getPost('customer_group_ids'));
		}
	}

	/**
	 * save salesrule - customer relation
	 * @access public
	 * @param Varien_Event_Observer $observer
	 * @return FVets_SalesRule_Model_Adminhtml_Observer
	 * @author Douglas Borella Ianitsky
	 */
	public function saveSalesrulePremierData($observer)
	{
		$post = Mage::app()->getRequest()->getPost('premier', -1);
		if ($post != '-1') {
			$salesrule = $observer->getRule();
			if ($salesrule->getRuleType() == '2')
			{
				$salesrulePremier = Mage::getResourceSingleton('fvets_salesrule/salesrule_premier')
					->saveSalesruleRelation($salesrule, $post);
			}
		}
		return $this;
	}

	public function loadSalesrulePremierRelation($observer)
	{
		$rule = $observer->getRule();
		$premier = Mage::getModel('fvets_salesrule/salesrule_premier')->load($rule->getId(), 'salesrule_id');

		if ($premier->getId())
		{
			foreach ($premier->getData() as $key => $value)
			{
				$rule->{'setPremier'.ucfirst($key)}($value);
			}
		}
	}

	public function saveCustomerFormData($observer)
	{
		/** @var $customer Mage_Customer_Model_Customer */
		$customer = $observer->getCustomer();

		/** @var $customerForm Mage_Customer_Model_Form */
		$customerForm = Mage::getModel('customer/form');
		$customerForm->setEntity($customer)
			->setFormCode('adminhtml_customer_promo_form')
			->ignoreInvisible(false)
		;

		$formData = $customerForm->extractData($observer->getRequest());

		$customer->addData($formData);
	}

	public function saveCustomerPremierData($observer)
	{
		/** @var $customer Mage_Customer_Model_Customer */
		$customer = $observer->getCustomer();

		/** @var $customerForm Mage_Customer_Model_Form */
		$customerForm = Mage::getModel('customer/form');
		$customerForm->setEntity($customer)
			->setFormCode('adminhtml_customer_premier')
			->ignoreInvisible(false)
		;

		$formData = $customerForm->extractData($observer->getRequest());

		$customer->addData($formData);
	}

	public function createLabels($observer)
	{
		$rule = $observer->getRule();

		$helper = Mage::helper('fvets_salesrule/label');

		$cleanLabels = $rule->getData('clean_labels');
		if ($cleanLabels !== null && $cleanLabels == '') {
			$helper->cleanAllLabels($rule->getId());
		}

		if (!$rule->getData('create_labels') || !$rule->getData('create_labels_text')) {
			return;
		}

		$categories = array();
		$idsErp = array();
		if ($rule->getIsActive()) {

			$conditions = $rule->getConditions()->asArray();

			foreach ($conditions['conditions'] as $_conditions):
				foreach ($_conditions['conditions'] as $_condition):
					$attribute = $_condition['attribute'];
					$string = explode(',', $_condition['value']);
					for ($i = 0; $i < count($string); $i++) {
						if($attribute == 'category_ids') {
							$categories[] = trim($string[$i]);
						} elseif($attribute == 'id_erp') {
							if(!in_array($string[$i], $idsErp)) {
								$idsErp[] = trim($string[$i]);
							}
						}
					}
				endforeach;
			endforeach;
		}

        $ruleWebsites = $rule->getWebsiteIds();
        $websiteId = array_shift($ruleWebsites);

        if (!empty($idsErp)) {
            foreach ($idsErp as $idErp) {
				$product = $this->getProductByIdErp($idErp, $websiteId);
                $helper->saveLabel($rule->getId(), $product->getId(), $rule->getData('create_labels_text'));
            }
        }

		if (!empty($categories)) {
			foreach ($categories as $category) {
				$storeIds = Mage::getModel('core/website')->load($websiteId)->getStoreIds();

				$products = Mage::getModel('catalog/category')
					->setStoreId(array_shift($storeIds))
					->load($category);
				$productslist = $products->getProductCollection()
					->addAttributeToSelect('sku')
					->addAttributeToSelect('id_erp')
					->addAttributeToFilter('status', 1);

				foreach ($productslist as $product)
				{
					$helper->saveLabel($rule->getId(), $product->getId(), $rule->getData('create_labels_text'));
				}
			}
		}
		return;
	}

	private function getProductByIdErp($idErp, $websiteId)
	{

		$stores = Mage::getModel('core/website')->load($websiteId)->getStoreCollection();

		foreach ($stores as $store)
		{
			$select = Mage::getModel('catalog/product')->getCollection()
				->addWebsiteFilter($websiteId)
				->setStoreId($store->getId())
				->addAttributeToFilter('id_erp', $idErp);

			$product = $select
				->getFirstItem();
			if ($product->getId())
			{
				return $product->setStoreId($store->getId())->load($product->getId());
			}
		}
		return null;
	}
}
