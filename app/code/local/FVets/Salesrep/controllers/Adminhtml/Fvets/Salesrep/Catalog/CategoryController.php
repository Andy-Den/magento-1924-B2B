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
 * Sales Rep - category controller
 * @category    FVets
 * @package     FVets_Salesrep
 * @author      Douglas Borella Ianitsky
 */
require_once ("Mage/Adminhtml/controllers/Catalog/CategoryController.php");
class FVets_Salesrep_Adminhtml_Fvets_Salesrep_Catalog_CategoryController extends Mage_Adminhtml_Catalog_CategoryController
{
    /**
     * construct
     *
     * @access protected
     * @return void
     * @author Douglas Borella Ianitsky
     */
    protected function _construct()
    {
        // Define module dependent translate
        $this->setUsedModuleName('FVets_Salesrep');
    }

    /**
     * salesreps grid in the catalog page
     *
     * @access public
     * @return void
     * @author Douglas Borella Ianitsky
     */
    public function salesrepsgridAction()
    {
        $this->_initCategory();
        $this->loadLayout();
        $this->getLayout()->getBlock('category.edit.tab.salesrep')
            ->setCategorySalesreps($this->getRequest()->getPost('category_salesreps', null));
        $this->renderLayout();
    }
}
