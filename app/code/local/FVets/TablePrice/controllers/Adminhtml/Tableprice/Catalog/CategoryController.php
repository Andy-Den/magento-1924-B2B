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
 * Table Price - category controller
 * @category    FVets
 * @package     FVets_TablePrice
 * @author      Douglas Ianitsky
 */
require_once ("Mage/Adminhtml/controllers/Catalog/CategoryController.php");
class FVets_TablePrice_Adminhtml_Tableprice_Catalog_CategoryController extends Mage_Adminhtml_Catalog_CategoryController
{
    /**
     * construct
     *
     * @access protected
     * @return void
     * @author Douglas Ianitsky
     */
    protected function _construct()
    {
        // Define module dependent translate
        $this->setUsedModuleName('FVets_TablePrice');
    }

    /**
     * tablesprices grid in the catalog page
     *
     * @access public
     * @return void
     * @author Douglas Ianitsky
     */
    public function tablespricesgridAction()
    {
        $this->_initCategory();
        $this->loadLayout();
        $this->getLayout()->getBlock('category.edit.tab.tableprice')
            ->setCategoryTablesprices($this->getRequest()->getPost('category_tablesprices', null));
        $this->renderLayout();
    }
}
