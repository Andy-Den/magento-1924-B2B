<?php
/**
 * FVets_CatalogRestrictionGroup extension
 * 
 * @category       FVets
 * @package        FVets_CatalogRestrictionGroup
 * @copyright      Copyright (c) 2016
 */
/**
 * Restriction Group admin controller
 *
 * @category    FVets
 * @package     FVets_CatalogRestrictionGroup
 * @author      Douglas Ianitsky
 */
class FVets_CatalogRestrictionGroup_Adminhtml_CatalogrestrictiongroupController extends FVets_CatalogRestrictionGroup_Controller_Adminhtml_CatalogRestrictionGroup
{
    /**
     * init the restriction group
     *
     * @access protected
     * @return FVets_CatalogRestrictionGroup_Model_Catalogrestrictiongroup
     */
    protected function _initCatalogrestrictiongroup()
    {
        $catalogrestrictiongroupId  = (int) $this->getRequest()->getParam('id');
        $catalogrestrictiongroup    = Mage::getModel('fvets_catalogrestrictiongroup/catalogrestrictiongroup');
        if ($catalogrestrictiongroupId) {
            $catalogrestrictiongroup->load($catalogrestrictiongroupId);
        }
        Mage::register('current_catalogrestrictiongroup', $catalogrestrictiongroup);
        return $catalogrestrictiongroup;
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
        $this->_title(Mage::helper('fvets_catalogrestrictiongroup')->__('Restriction Group'))
             ->_title(Mage::helper('fvets_catalogrestrictiongroup')->__('Restriction Groups'));
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
     * edit restriction group - action
     *
     * @access public
     * @return void
     * @author Douglas Ianitsky
     */
    public function editAction()
    {
        $catalogrestrictiongroupId    = $this->getRequest()->getParam('id');
        $catalogrestrictiongroup      = $this->_initCatalogrestrictiongroup();
        if ($catalogrestrictiongroupId && !$catalogrestrictiongroup->getId()) {
            $this->_getSession()->addError(
                Mage::helper('fvets_catalogrestrictiongroup')->__('This restriction group no longer exists.')
            );
            $this->_redirect('*/*/');
            return;
        }
        $data = Mage::getSingleton('adminhtml/session')->getCatalogrestrictiongroupData(true);
        if (!empty($data)) {
            $catalogrestrictiongroup->setData($data);
        }
        Mage::register('catalogrestrictiongroup_data', $catalogrestrictiongroup);
        $this->loadLayout();
        $this->_title(Mage::helper('fvets_catalogrestrictiongroup')->__('Restriction Group'))
             ->_title(Mage::helper('fvets_catalogrestrictiongroup')->__('Restriction Groups'));
        if ($catalogrestrictiongroup->getId()) {
            $this->_title($catalogrestrictiongroup->getName());
        } else {
            $this->_title(Mage::helper('fvets_catalogrestrictiongroup')->__('Add restriction group'));
        }
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
        $this->renderLayout();
    }

    /**
     * new restriction group action
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
     * save restriction group - action
     *
     * @access public
     * @return void
     * @author Douglas Ianitsky
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost('catalogrestrictiongroup')) {
            try {
                $catalogrestrictiongroup = $this->_initCatalogrestrictiongroup();
                $catalogrestrictiongroup->addData($data);
                $products = $this->getRequest()->getPost('products', -1);
                if ($products != -1) {
                    $catalogrestrictiongroup->setProductsData(Mage::helper('adminhtml/js')->decodeGridSerializedInput($products));
                }
                $customers = $this->getRequest()->getPost('customers', -1);
                if ($customers != -1) {
                    $catalogrestrictiongroup->setCustomersData(Mage::helper('adminhtml/js')->decodeGridSerializedInput($customers));
                }
                $catalogrestrictiongroup->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('fvets_catalogrestrictiongroup')->__('Restriction Group was successfully saved')
                );
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $catalogrestrictiongroup->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setCatalogrestrictiongroupData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            } catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('fvets_catalogrestrictiongroup')->__('There was a problem saving the restriction group.')
                );
                Mage::getSingleton('adminhtml/session')->setCatalogrestrictiongroupData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('fvets_catalogrestrictiongroup')->__('Unable to find restriction group to save.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * delete restriction group - action
     *
     * @access public
     * @return void
     * @author Douglas Ianitsky
     */
    public function deleteAction()
    {
        if ( $this->getRequest()->getParam('id') > 0) {
            try {
                $catalogrestrictiongroup = Mage::getModel('fvets_catalogrestrictiongroup/catalogrestrictiongroup');
                $catalogrestrictiongroup->setId($this->getRequest()->getParam('id'))->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('fvets_catalogrestrictiongroup')->__('Restriction Group was successfully deleted.')
                );
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('fvets_catalogrestrictiongroup')->__('There was an error deleting restriction group.')
                );
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                Mage::logException($e);
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('fvets_catalogrestrictiongroup')->__('Could not find restriction group to delete.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * mass delete restriction group - action
     *
     * @access public
     * @return void
     * @author Douglas Ianitsky
     */
    public function massDeleteAction()
    {
        $catalogrestrictiongroupIds = $this->getRequest()->getParam('catalogrestrictiongroup');
        if (!is_array($catalogrestrictiongroupIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('fvets_catalogrestrictiongroup')->__('Please select restriction groups to delete.')
            );
        } else {
            try {
                foreach ($catalogrestrictiongroupIds as $catalogrestrictiongroupId) {
                    $catalogrestrictiongroup = Mage::getModel('fvets_catalogrestrictiongroup/catalogrestrictiongroup');
                    $catalogrestrictiongroup->setId($catalogrestrictiongroupId)->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('fvets_catalogrestrictiongroup')->__('Total of %d restriction groups were successfully deleted.', count($catalogrestrictiongroupIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('fvets_catalogrestrictiongroup')->__('There was an error deleting restriction groups.')
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
        $catalogrestrictiongroupIds = $this->getRequest()->getParam('catalogrestrictiongroup');
        if (!is_array($catalogrestrictiongroupIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('fvets_catalogrestrictiongroup')->__('Please select restriction groups.')
            );
        } else {
            try {
                foreach ($catalogrestrictiongroupIds as $catalogrestrictiongroupId) {
                $catalogrestrictiongroup = Mage::getSingleton('fvets_catalogrestrictiongroup/catalogrestrictiongroup')->load($catalogrestrictiongroupId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d restriction groups were successfully updated.', count($catalogrestrictiongroupIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('fvets_catalogrestrictiongroup')->__('There was an error updating restriction groups.')
                );
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * mass Website change - action
     *
     * @access public
     * @return void
     * @author Douglas Ianitsky
     */
    public function massWebsiteIdAction()
    {
        $catalogrestrictiongroupIds = $this->getRequest()->getParam('catalogrestrictiongroup');
        if (!is_array($catalogrestrictiongroupIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('fvets_catalogrestrictiongroup')->__('Please select restriction groups.')
            );
        } else {
            try {
                foreach ($catalogrestrictiongroupIds as $catalogrestrictiongroupId) {
                $catalogrestrictiongroup = Mage::getSingleton('fvets_catalogrestrictiongroup/catalogrestrictiongroup')->load($catalogrestrictiongroupId)
                    ->setWebsiteId($this->getRequest()->getParam('flag_website_id'))
                    ->setIsMassupdate(true)
                    ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d restriction groups were successfully updated.', count($catalogrestrictiongroupIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('fvets_catalogrestrictiongroup')->__('There was an error updating restriction groups.')
                );
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * get grid of products action
     *
     * @access public
     * @return void
     * @author Douglas Ianitsky
     */
    public function productsAction()
    {
        $this->_initCatalogrestrictiongroup();
        $this->loadLayout();
        $this->getLayout()->getBlock('catalogrestrictiongroup.edit.tab.product')
            ->setCatalogrestrictiongroupProducts($this->getRequest()->getPost('catalogrestrictiongroup_products', null));
        $this->renderLayout();
    }

    /**
     * get grid of products action
     *
     * @access public
     * @return void
     * @author Douglas Ianitsky
     */
    public function productsgridAction()
    {
        $this->_initCatalogrestrictiongroup();
        $this->loadLayout();
        $this->getLayout()->getBlock('catalogrestrictiongroup.edit.tab.product')
            ->setCatalogrestrictiongroupProducts($this->getRequest()->getPost('catalogrestrictiongroup_products', null));
        $this->renderLayout();
    }

    /**
     * get grid of customers action
     *
     * @access public
     * @return void
     * @author Douglas Ianitsky
     */
    public function customersAction()
    {
        $this->_initCatalogrestrictiongroup();
        $this->loadLayout();
        $this->getLayout()->getBlock('catalogrestrictiongroup.edit.tab.customer')
            ->setCatalogrestrictiongroupCustomers($this->getRequest()->getPost('catalogrestrictiongroup_customers', null));
        $this->renderLayout();
    }

    /**
     * get grid of customers action
     *
     * @access public
     * @return void
     * @author Douglas Ianitsky
     */
    public function customersgridAction()
    {
        $this->_initCatalogrestrictiongroup();
        $this->loadLayout();
        $this->getLayout()->getBlock('catalogrestrictiongroup.edit.tab.customer')
            ->setCatalogrestrictiongroupCustomers($this->getRequest()->getPost('catalogrestrictiongroup_customers', null));
        $this->renderLayout();
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
        $fileName   = 'catalogrestrictiongroup.csv';
        $content    = $this->getLayout()->createBlock('fvets_catalogrestrictiongroup/adminhtml_catalogrestrictiongroup_grid')
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
        $fileName   = 'catalogrestrictiongroup.xls';
        $content    = $this->getLayout()->createBlock('fvets_catalogrestrictiongroup/adminhtml_catalogrestrictiongroup_grid')
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
        $fileName   = 'catalogrestrictiongroup.xml';
        $content    = $this->getLayout()->createBlock('fvets_catalogrestrictiongroup/adminhtml_catalogrestrictiongroup_grid')
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
        return Mage::getSingleton('admin/session')->isAllowed('catalog/fvets_catalogrestrictiongroup/catalogrestrictiongroup');
    }
}
