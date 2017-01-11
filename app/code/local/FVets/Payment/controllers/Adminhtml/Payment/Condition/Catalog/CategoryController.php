<?php
/**
 * FVets_Payment extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       FVets
 * @package        FVets_Payment
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Condition - category controller
 * @category    FVets
 * @package     FVets_Payment
 * @author      Douglas Borella Ianitsky
 */
require_once ("Mage/Adminhtml/controllers/Catalog/CategoryController.php");
class FVets_Payment_Adminhtml_Payment_Condition_Catalog_CategoryController extends Mage_Adminhtml_Catalog_CategoryController
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
        $this->setUsedModuleName('FVets_Payment');
    }

    /**
     * conditions grid in the catalog page
     *
     * @access public
     * @return void
     * @author Douglas Borella Ianitsky
     */
    public function conditionsgridAction()
    {
        $this->_initCategory();
        $this->loadLayout();
        $this->getLayout()->getBlock('category.edit.tab.condition')
            ->setCategoryConditions($this->getRequest()->getPost('category_conditions', null));
        $this->renderLayout();
    }
}
