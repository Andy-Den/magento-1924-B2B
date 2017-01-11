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
 * Table Price admin controller
 *
 * @category    FVets
 * @package     FVets_TablePrice
 * @author      Douglas Ianitsky
 */
class FVets_TablePrice_Adminhtml_TablepriceController extends FVets_TablePrice_Controller_Adminhtml_TablePrice
{
    /**
     * init the table price
     *
     * @access protected
     * @return FVets_TablePrice_Model_Tableprice
     */
    protected function _initTableprice()
    {
        $tablepriceId  = (int) $this->getRequest()->getParam('id');
        $tableprice    = Mage::getModel('fvets_tableprice/tableprice');
        if ($tablepriceId) {
            $tableprice->load($tablepriceId);
        }
        Mage::register('current_tableprice', $tableprice);
        return $tableprice;
    }

    /**
     * default action
     *
     * @access public
     * @return void
     * @author Douglas Ianitsky
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_title(Mage::helper('fvets_tableprice')->__('Tabela de preços'))
             ->_title(Mage::helper('fvets_tableprice')->__('Tables Prices'));
        $this->renderLayout();
    }

    /**
     * grid action
     *
     * @access public
     * @return void
     * @author Douglas Ianitsky
     */
    public function gridAction()
    {
        $this->loadLayout()->renderLayout();
    }

    /**
     * edit table price - action
     *
     * @access public
     * @return void
     * @author Douglas Ianitsky
     */
    public function editAction()
    {
        $tablepriceId    = $this->getRequest()->getParam('id');
        $tableprice      = $this->_initTableprice();
        if ($tablepriceId && !$tableprice->getId()) {
            $this->_getSession()->addError(
                Mage::helper('fvets_tableprice')->__('This table price no longer exists.')
            );
            $this->_redirect('*/*/');
            return;
        }
        $data = Mage::getSingleton('adminhtml/session')->getTablepriceData(true);
        if (!empty($data)) {
            $tableprice->setData($data);
        }
        Mage::register('tableprice_data', $tableprice);
        $this->loadLayout();
        $this->_title(Mage::helper('fvets_tableprice')->__('Tabela de preços'))
             ->_title(Mage::helper('fvets_tableprice')->__('Tables Prices'));
        if ($tableprice->getId()) {
            $this->_title($tableprice->getName());
        } else {
            $this->_title(Mage::helper('fvets_tableprice')->__('Add table price'));
        }
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
        $this->renderLayout();
    }

    /**
     * new table price action
     *
     * @access public
     * @return void
     * @author Douglas Ianitsky
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * save table price - action
     *
     * @access public
     * @return void
     * @author Douglas Ianitsky
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost('tableprice')) {
            try {
                $tableprice = $this->_initTableprice();
                $tableprice->addData($data);
                $categories = $this->getRequest()->getPost('category_ids', -1);
                if ($categories != -1) {
                    $categories = explode(',', $categories);
                    $categories = array_unique($categories);
                    $tableprice->setCategoriesData($categories);
                }
                $tableprice->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('fvets_tableprice')->__('Table Price was successfully saved')
                );
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $tableprice->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setTablepriceData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            } catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('fvets_tableprice')->__('There was a problem saving the table price.')
                );
                Mage::getSingleton('adminhtml/session')->setTablepriceData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('fvets_tableprice')->__('Unable to find table price to save.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * delete table price - action
     *
     * @access public
     * @return void
     * @author Douglas Ianitsky
     */
    public function deleteAction()
    {
        if ( $this->getRequest()->getParam('id') > 0) {
            try {
                $tableprice = Mage::getModel('fvets_tableprice/tableprice');
                $tableprice->setId($this->getRequest()->getParam('id'))->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('fvets_tableprice')->__('Table Price was successfully deleted.')
                );
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('fvets_tableprice')->__('There was an error deleting table price.')
                );
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                Mage::logException($e);
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('fvets_tableprice')->__('Could not find table price to delete.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * mass delete table price - action
     *
     * @access public
     * @return void
     * @author Douglas Ianitsky
     */
    public function massDeleteAction()
    {
        $tablepriceIds = $this->getRequest()->getParam('tableprice');
        if (!is_array($tablepriceIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('fvets_tableprice')->__('Please select tables prices to delete.')
            );
        } else {
            try {
                foreach ($tablepriceIds as $tablepriceId) {
                    $tableprice = Mage::getModel('fvets_tableprice/tableprice');
                    $tableprice->setId($tablepriceId)->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('fvets_tableprice')->__('Total of %d tables prices were successfully deleted.', count($tablepriceIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('fvets_tableprice')->__('There was an error deleting tables prices.')
                );
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * mass status change - action
     *
     * @access public
     * @return void
     * @author Douglas Ianitsky
     */
    public function massStatusAction()
    {
        $tablepriceIds = $this->getRequest()->getParam('tableprice');
        if (!is_array($tablepriceIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('fvets_tableprice')->__('Please select tables prices.')
            );
        } else {
            try {
                foreach ($tablepriceIds as $tablepriceId) {
                $tableprice = Mage::getSingleton('fvets_tableprice/tableprice')->load($tablepriceId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d tables prices were successfully updated.', count($tablepriceIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('fvets_tableprice')->__('There was an error updating tables prices.')
                );
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * mass Customer Group ID change - action
     *
     * @access public
     * @return void
     * @author Douglas Ianitsky
     */
    public function massCustomerGroupIdAction()
    {
        $tablepriceIds = $this->getRequest()->getParam('tableprice');
        if (!is_array($tablepriceIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('fvets_tableprice')->__('Please select tables prices.')
            );
        } else {
            try {
                foreach ($tablepriceIds as $tablepriceId) {
                $tableprice = Mage::getSingleton('fvets_tableprice/tableprice')->load($tablepriceId)
                    ->setCustomerGroupId($this->getRequest()->getParam('flag_customer_group_id'))
                    ->setIsMassupdate(true)
                    ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d tables prices were successfully updated.', count($tablepriceIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('fvets_tableprice')->__('There was an error updating tables prices.')
                );
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * get categories action
     *
     * @access public
     * @return void
     * @author Douglas Ianitsky
     */
    public function categoriesAction()
    {
        $this->_initTableprice();
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * get child categories action
     *
     * @access public
     * @return void
     * @author Douglas Ianitsky
     */
    public function categoriesJsonAction()
    {
        $this->_initTableprice();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('fvets_tableprice/adminhtml_tableprice_edit_tab_categories')
                ->getCategoryChildrenJson($this->getRequest()->getParam('category'))
        );
    }

    /**
     * export as csv - action
     *
     * @access public
     * @return void
     * @author Douglas Ianitsky
     */
    public function exportCsvAction()
    {
        $fileName   = 'tableprice.csv';
        $content    = $this->getLayout()->createBlock('fvets_tableprice/adminhtml_tableprice_grid')
            ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export as MsExcel - action
     *
     * @access public
     * @return void
     * @author Douglas Ianitsky
     */
    public function exportExcelAction()
    {
        $fileName   = 'tableprice.xls';
        $content    = $this->getLayout()->createBlock('fvets_tableprice/adminhtml_tableprice_grid')
            ->getExcelFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export as xml - action
     *
     * @access public
     * @return void
     * @author Douglas Ianitsky
     */
    public function exportXmlAction()
    {
        $fileName   = 'tableprice.xml';
        $content    = $this->getLayout()->createBlock('fvets_tableprice/adminhtml_tableprice_grid')
            ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Check if admin has permissions to visit related pages
     *
     * @access protected
     * @return boolean
     * @author Douglas Ianitsky
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('customer/group/fvets_tableprice/tableprice');
    }
}
