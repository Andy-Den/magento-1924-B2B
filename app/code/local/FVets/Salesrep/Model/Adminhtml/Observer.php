<?php
/**
 * FVets_Salesrep extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       FVets
 * @package        FVets_Salesrep
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Adminhtml observer
 *
 * @category    FVets
 * @package     FVets_Salesrep
 * @author      Douglas Borella Ianitsky
 */
class FVets_Salesrep_Model_Adminhtml_Observer
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
     * add the salesrep tab to categories
     *
     * @access public
     * @param Varien_Event_Observer $observer
     * @return FVets_Salesrep_Model_Adminhtml_Observer
     * @author Douglas Borella Ianitsky
     */
    public function addCategorySalesrepBlock($observer)
    {
        $tabs = $observer->getEvent()->getTabs();
        $content = $tabs->getLayout()->createBlock(
            'fvets_salesrep/adminhtml_catalog_category_tab_salesrep',
            'category.salesrep.grid'
        )->toHtml();
        $serializer = $tabs->getLayout()->createBlock(
            'adminhtml/widget_grid_serializer',
            'category.salesrep.grid.serializer'
        );
        $serializer->initSerializerBlock(
            'category.salesrep.grid',
            'getSelectedSalesreps',
            'salesreps',
            'category_salesreps'
        );
        $serializer->addColumnInputName('position');
        $content .= $serializer->toHtml();
        $tabs->addTab(
            'salesrep',
            array(
                'label'   => Mage::helper('fvets_salesrep')->__('Sales Representatives'),
                'content' => $content,
            )
        );
        return $this;
    }

    /**
     * save sales rep - category relation
     *
     * @access public
     * @param Varien_Event_Observer $observer
     * @return FVets_Salesrep_Model_Adminhtml_Observer
     * @author Douglas Borella Ianitsky
     */
    public function saveCategorySalesrepData($observer)
    {
        $post = Mage::app()->getRequest()->getPost('salesreps', -1);
        if ($post != '-1') {
            $post = Mage::helper('adminhtml/js')->decodeGridSerializedInput($post);
            $category = Mage::registry('category');
            $salesrepCategory = Mage::getResourceSingleton('fvets_salesrep/category')
                ->saveCategoryRelation($category, $post);
        }
        return $this;
    }

	/**
	 * add the integrarep tab to customers
	 *
	 * @access public
	 * @param Varien_Event_Observer $observer
	 * @return FVets_Payment_Model_Adminhtml_Observer
	 */
	public function addCustomerIntegrarepBlock($observer)
	{
		$block = $observer->getEvent()->getBlock();
		if ($block instanceof Mage_Adminhtml_Block_Customer_Edit_Tabs) {
			$block->addTabAfter(
				'integrarep',
				array(
					'label' => Mage::helper('fvets_payment')->__('Integrarep'),
					'content'   => $block->getLayout()->createBlock('fvets_salesrep/adminhtml_customer_edit_tab_integrarep')->initForm()->toHtml(),
				),
				'addresses'
			);
		}
		return $this;
	}

	public function saveCustomerIntegrarepData($observer)
	{
		/** @var $customer Mage_Customer_Model_Customer */
		$customer = $observer->getCustomer();

		/** @var $customerForm Mage_Customer_Model_Form */
		$customerForm = Mage::getModel('customer/form');
		$customerForm->setEntity($customer)
			->setFormCode('adminhtml_customer_integrarep')
			->ignoreInvisible(false)
		;

		$formData = $customerForm->extractData($observer->getRequest());

		$customer->addData($formData);
	}
}
