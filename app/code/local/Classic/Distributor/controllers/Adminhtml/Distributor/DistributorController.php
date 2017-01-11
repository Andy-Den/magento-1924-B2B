<?php
/**
 * Classic_Distributor extension
 * 
 * NOTICE OF LICENSE
 *
 * @category       Classic
 * @package        Classic_Distributor
 * @copyright      Copyright (c) 2015
  */
/**
 * Distributor admin controller
 *
 * @category    Classic
 * @package     Classic_Distributor
 * @author      Douglas Borella Ianitsky
 */
class Classic_Distributor_Adminhtml_Distributor_DistributorController extends Classic_Distributor_Controller_Adminhtml_Distributor
{
    /**
     * init the distributor
     *
     * @access protected
     * @return Classic_Distributor_Model_Distributor
     */
    protected function _initDistributor()
    {
        $distributorId  = (int) $this->getRequest()->getParam('id');
        $distributor    = Mage::getModel('classic_distributor/distributor');
        if ($distributorId) {
            $distributor->load($distributorId);
        }
        Mage::register('current_distributor', $distributor);
        return $distributor;
    }

    /**
     * default action
     *
     * @access public
     * @return void
     * @author Douglas Borella Ianitsky
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_title(Mage::helper('classic_distributor')->__('Distributor'))
             ->_title(Mage::helper('classic_distributor')->__('Distributors'));
        $this->renderLayout();
    }

    /**
     * grid action
     *
     * @access public
     * @return void
     * @author Douglas Borella Ianitsky
     */
    public function gridAction()
    {
        $this->loadLayout()->renderLayout();
    }

    /**
     * edit distributor - action
     *
     * @access public
     * @return void
     * @author Douglas Borella Ianitsky
     */
    public function editAction()
    {
        $distributorId    = $this->getRequest()->getParam('id');
        $distributor      = $this->_initDistributor();
        if ($distributorId && !$distributor->getId()) {
            $this->_getSession()->addError(
                Mage::helper('classic_distributor')->__('This distributor no longer exists.')
            );
            $this->_redirect('*/*/');
            return;
        }
        $data = Mage::getSingleton('adminhtml/session')->getDistributorData(true);
        if (!empty($data)) {
            $distributor->setData($data);
        }
        Mage::register('distributor_data', $distributor);
        $this->loadLayout();
        $this->_title(Mage::helper('classic_distributor')->__('Distributor'))
             ->_title(Mage::helper('classic_distributor')->__('Distributors'));
        if ($distributor->getId()) {
            $this->_title($distributor->getName());
        } else {
            $this->_title(Mage::helper('classic_distributor')->__('Add distributor'));
        }
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
        $this->renderLayout();
    }

    /**
     * new distributor action
     *
     * @access public
     * @return void
     * @author Douglas Borella Ianitsky
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * save distributor - action
     *
     * @access public
     * @return void
     * @author Douglas Borella Ianitsky
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost('distributor')) {
            try {
                $distributor = $this->_initDistributor();
                $distributor->addData($data);
                $regions = $this->getRequest()->getPost('regions', -1);
                if ($regions != -1) {
                    $distributor->setRegionsData(Mage::helper('adminhtml/js')->decodeGridSerializedInput($regions));
                }
                $distributor->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('classic_distributor')->__('Distributor was successfully saved')
                );
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $distributor->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setDistributorData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            } catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('classic_distributor')->__('There was a problem saving the distributor.')
                );
                Mage::getSingleton('adminhtml/session')->setDistributorData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('classic_distributor')->__('Unable to find distributor to save.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * delete distributor - action
     *
     * @access public
     * @return void
     * @author Douglas Borella Ianitsky
     */
    public function deleteAction()
    {
        if ( $this->getRequest()->getParam('id') > 0) {
            try {
                $distributor = Mage::getModel('classic_distributor/distributor');
                $distributor->setId($this->getRequest()->getParam('id'))->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('classic_distributor')->__('Distributor was successfully deleted.')
                );
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('classic_distributor')->__('There was an error deleting distributor.')
                );
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                Mage::logException($e);
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('classic_distributor')->__('Could not find distributor to delete.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * mass delete distributor - action
     *
     * @access public
     * @return void
     * @author Douglas Borella Ianitsky
     */
    public function massDeleteAction()
    {
        $distributorIds = $this->getRequest()->getParam('distributor');
        if (!is_array($distributorIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('classic_distributor')->__('Please select distributors to delete.')
            );
        } else {
            try {
                foreach ($distributorIds as $distributorId) {
                    $distributor = Mage::getModel('classic_distributor/distributor');
                    $distributor->setId($distributorId)->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('classic_distributor')->__('Total of %d distributors were successfully deleted.', count($distributorIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('classic_distributor')->__('There was an error deleting distributors.')
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
     * @author Douglas Borella Ianitsky
     */
    public function massStatusAction()
    {
        $distributorIds = $this->getRequest()->getParam('distributor');
        if (!is_array($distributorIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('classic_distributor')->__('Please select distributors.')
            );
        } else {
            try {
                foreach ($distributorIds as $distributorId) {
                $distributor = Mage::getSingleton('classic_distributor')->load($distributorId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d distributors were successfully updated.', count($distributorIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('classic_distributor')->__('There was an error updating distributors.')
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
     * @author Douglas Borella Ianitsky
     */
    public function massWebsiteAction()
    {
        $distributorIds = $this->getRequest()->getParam('distributor');
        if (!is_array($distributorIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('classic_distributor')->__('Please select distributors.')
            );
        } else {
            try {
                foreach ($distributorIds as $distributorId) {
                $distributor = Mage::getSingleton('classic_distributor')->load($distributorId)
                    ->setWebsite($this->getRequest()->getParam('flag_website'))
                    ->setIsMassupdate(true)
                    ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d distributors were successfully updated.', count($distributorIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('classic_distributor')->__('There was an error updating distributors.')
                );
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * get grid of regions action
     *
     * @access public
     * @return void
     * @author Douglas Borella Ianitsky
     */
    public function regionsAction()
    {
        $this->_initDistributor();
        $this->loadLayout();
        $this->getLayout()->getBlock('distributor.edit.tab.region')
            ->setDistributorRegions($this->getRequest()->getPost('distributor_regions', null));
        $this->renderLayout();
    }

    /**
     * get grid of regions action
     *
     * @access public
     * @return void
     * @author Douglas Borella Ianitsky
     */
    public function regionsgridAction()
    {
        $this->_initDistributor();
        $this->loadLayout();
        $this->getLayout()->getBlock('distributor.edit.tab.region')
            ->setDistributorRegions($this->getRequest()->getPost('distributor_regions', null));
        $this->renderLayout();
    }

    /**
     * export as csv - action
     *
     * @access public
     * @return void
     * @author Douglas Borella Ianitsky
     */
    public function exportCsvAction()
    {
        $fileName   = 'distributor.csv';
        $content    = $this->getLayout()->createBlock('classic_distributor/adminhtml_distributor_grid')
            ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export as MsExcel - action
     *
     * @access public
     * @return void
     * @author Douglas Borella Ianitsky
     */
    public function exportExcelAction()
    {
        $fileName   = 'distributor.xls';
        $content    = $this->getLayout()->createBlock('classic_distributor/adminhtml_distributor_grid')
            ->getExcelFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export as xml - action
     *
     * @access public
     * @return void
     * @author Douglas Borella Ianitsky
     */
    public function exportXmlAction()
    {
        $fileName   = 'distributor.xml';
        $content    = $this->getLayout()->createBlock('classic_distributor/adminhtml_distributor_grid')
            ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Check if admin has permissions to visit related pages
     *
     * @access protected
     * @return boolean
     * @author Douglas Borella Ianitsky
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('classic/distributor');
    }
}
