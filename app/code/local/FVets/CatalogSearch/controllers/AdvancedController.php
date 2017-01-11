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
 *
 * @category   Mage
 * @package    Mage_CatalogSearch
 * @module     Catalog
 */

require_once 'Mage/CatalogSearch/controllers/AdvancedController.php';

class FVets_CatalogSearch_AdvancedController extends Mage_CatalogSearch_AdvancedController
{

	public function indexAction()
	{
		$this->loadLayout();
		$this->_initLayoutMessages('catalogsearch/session');
		$this->renderLayout();
	}

	public function resultAction()
	{
		$this->loadLayout();
		try
		{
			/*$idsErp = $this->getRequest()->getParam('id_erp');
			if ($idsErp)
			{
				$idsErpArray = array('id_erp' => explode(',', str_replace(' ', '', $idsErp)));
				Mage::getSingleton('customer/session')->setAdvancedSearchIdsErp($idsErpArray);
			} else
			{
				$idsErpArray = Mage::getSingleton('customer/session')->getAdvancedSearchIdsErp();
			}

			if ($idsErpArray)
			{
				Mage::getSingleton('catalogsearch/advanced')->addFilters($idsErpArray);
			}*/

			$params = $this->getRequest()->getParams();
			foreach ($params as $key => $value)
			{
				$value = explode(',', str_replace(' ', '', $value));
				if (count($value) > 1)
				{
					$params[$key] = $value;
				}
			}

			Mage::getSingleton('catalogsearch/advanced')->addFilters($params);

		} catch (Mage_Core_Exception $e)
		{
			Mage::getSingleton('catalogsearch/session')->addError($e->getMessage());
			$this->_redirectError(
				Mage::getModel('core/url')
					->setQueryParams($this->getRequest()->getQuery())
					->getUrl('*/*/')
			);
		}
		$this->_initLayoutMessages('catalog/session');
		$this->renderLayout();
	}
}
