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
 * Adminhtml observer
 *
 * @category    FVets
 * @package     FVets_TablePrice
 * @author      Douglas Ianitsky
 */
class FVets_TablePrice_Model_Adminhtml_Customer_Observer
{
	/**
	 * Save if group has multiple table price
	 *
	 * @access protected
	 * @param Varien_Event_Observer $observer
	 * @return bool
	 * @author Douglas Ianitsky
	 */
	public function addCustomerGroupHasMultipleTable($observer)
	{
		$multiple_table = Mage::app()->getRequest()->getParam('multiple_table');

		$observer->getCustomerGroup()->setMultipleTable($multiple_table);

		$id_tabela = Mage::app()->getRequest()->getParam('id_tabela');

		$observer->getCustomerGroup()->setIdTabela($id_tabela);

	}
}
