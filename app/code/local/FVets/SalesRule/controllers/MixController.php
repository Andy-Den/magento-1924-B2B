<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_CatalogSearch
 * @copyright  Copyright (c) 2006-2014 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog Search Controller
 */
class FVets_Salesrule_MixController extends Mage_Core_Controller_Front_Action
{
	/**
	 * Initialize requested category object
	 *
	 * @return Mage_Catalog_Model_Category
	 */
	protected function _initRule()
	{
		$ruleId = (int) $this->getRequest()->getParam('id', false);
		if (!$ruleId) {
			return false;
		}

		$rule = Mage::getModel('salesrule/rule')
			->setStoreId(Mage::app()->getStore()->getId())
			->load($ruleId);

		Mage::register('current_rule', $rule);

		return $rule;
	}
	/**
	 * Retrieve catalog session
	 *
	 * @return Mage_Catalog_Model_Session
	 */
	protected function _getSession()
	{
		return Mage::getSingleton('catalog/session');
	}

	/**
	 * Display search result
	 */
	public function indexAction()
	{

		$rule = $this->_initRule();

		$this->loadLayout();

		$this->_initLayoutMessages('catalog/session');

		$this->renderLayout();
	}
}
