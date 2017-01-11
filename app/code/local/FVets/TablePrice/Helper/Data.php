<?php
/**
 * FVets_TablePrice extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       FVets
 * @package        FVets_TablePrice
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * TablePrice default helper
 *
 * @category    FVets
 * @package     FVets_TablePrice
 * @author      Douglas Ianitsky
 */
class FVets_TablePrice_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * convert array to options
     *
     * @access public
     * @param $options
     * @return array
     * @author Douglas Ianitsky
     */
    public function convertOptions($options)
    {
        $converted = array();
        foreach ($options as $option) {
            if (isset($option['value']) && !is_array($option['value']) &&
                isset($option['label']) && !is_array($option['label'])) {
                $converted[$option['value']] = $option['label'];
            }
        }
        return $converted;
    }

	/**
	 * get tableprice by customer and product
	 *
	 * @access public
	 * @param $customer Mage_Customer_Model_Customer
	 * @param $product Mage_Catalog_Model_Product
	 * @return varchar
	 * @author Douglas Ianitsky
	 */
	public function getTablepriceByCustomerAndProduct($customer, $product)
	{
		$_group = Mage::getModel('customer/group')->load($customer->getGroupId());

		if (!$_group->getMultipleTable())
		{
			return $_group->getIdTabela();
		}

		$rootCategory = Mage::getModel('catalog/category')
			->load(Mage::app()->getStore()->getRootCategoryId());

		if ($rootCategory->getLevel() != 2)
		{
			$category = Mage::getModel('catalog/category')
				->getCollection()
				->addAttributeToFilter('entity_id', array('in' => $product->getCategoryIds()))
				->addAttributeToFilter('level', '2')
				->addFieldToFilter('path', array('like' => $rootCategory->getPath() . '/%'))
				->getFirstItem();
		}
		else
		{
			$category = $rootCategory;
		}

		//Se não encontrar nenhuma categoria, é que algo deu errado, daí fudeu
		if (!$category->getId())
		{
			return false;
		}

		$tableprice = Mage::getModel('fvets_tableprice/tableprice')->getCollection()
			->addCategoryToFilter($category->getId())
			->addFieldToFilter('customer_group_id', $_group->getId())
			->getFirstItem();
		;

		return $tableprice;
	}

}
