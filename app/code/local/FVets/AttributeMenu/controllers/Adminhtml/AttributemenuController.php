<?php
/**
 * FVets_AttributeMenu extension
 * 
 * @category       FVets
 * @package        FVets_AttributeMenu
 * @copyright      Copyright (c) 2015
 */
/**
 * AttributeMenu admin controller
 *
 * @category    FVets
 * @package     FVets_AttributeMenu
 * @author      Ultimate Module Creator
 */
class FVets_AttributeMenu_Adminhtml_AttributemenuController extends FVets_AttributeMenu_Controller_Adminhtml_AttributeMenu
{
    /**
     * init the attributemenu
     *
     * @access protected
     * @return FVets_AttributeMenu_Model_Attributemenu
     */
    protected function _initAttributemenu()
    {
        $attributemenuId  = (int) $this->getRequest()->getParam('id');
        $attributemenu    = Mage::getModel('fvets_attributemenu/attributemenu');
        if ($attributemenuId) {
            $attributemenu->load($attributemenuId);
        }
        Mage::register('current_attributemenu', $attributemenu);
        return $attributemenu;
    }

    /**
     * default action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_title(Mage::helper('fvets_attributemenu')->__('Attribute Menu'))
             ->_title(Mage::helper('fvets_attributemenu')->__('AttributesMenu'));
        $this->renderLayout();
    }

    /**
     * grid action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function gridAction()
    {
        $this->loadLayout()->renderLayout();
    }

    /**
     * edit attributemenu - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function editAction()
    {
        $attributemenuId    = $this->getRequest()->getParam('id');
        $attributemenu      = $this->_initAttributemenu();
        if ($attributemenuId && !$attributemenu->getId()) {
            $this->_getSession()->addError(
                Mage::helper('fvets_attributemenu')->__('This attributemenu no longer exists.')
            );
            $this->_redirect('*/*/');
            return;
        }
        $data = Mage::getSingleton('adminhtml/session')->getAttributemenuData(true);
        if (!empty($data)) {
            $attributemenu->setData($data);
        }
        Mage::register('attributemenu_data', $attributemenu);
        $this->loadLayout();
        $this->_title(Mage::helper('fvets_attributemenu')->__('Attribute Menu'))
             ->_title(Mage::helper('fvets_attributemenu')->__('AttributesMenu'));
        if ($attributemenu->getId()) {
            $this->_title($attributemenu->getName());
        } else {
            $this->_title(Mage::helper('fvets_attributemenu')->__('Add attributemenu'));
        }
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
        $this->renderLayout();
    }

    /**
     * new attributemenu action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * save attributemenu - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost('attributemenu')) {
            try {
                $attributemenu = $this->_initAttributemenu();
                $attributemenu->addData($data);
                $attributemenu->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('fvets_attributemenu')->__('AttributeMenu was successfully saved')
                );
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $attributemenu->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setAttributemenuData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            } catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('fvets_attributemenu')->__('There was a problem saving the attributemenu.')
                );
                Mage::getSingleton('adminhtml/session')->setAttributemenuData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('fvets_attributemenu')->__('Unable to find attributemenu to save.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * delete attributemenu - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function deleteAction()
    {
        if ( $this->getRequest()->getParam('id') > 0) {
            try {
                $attributemenu = Mage::getModel('fvets_attributemenu/attributemenu');
                $attributemenu->setId($this->getRequest()->getParam('id'))->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('fvets_attributemenu')->__('AttributeMenu was successfully deleted.')
                );
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('fvets_attributemenu')->__('There was an error deleting attributemenu.')
                );
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                Mage::logException($e);
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('fvets_attributemenu')->__('Could not find attributemenu to delete.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * mass delete attributemenu - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function massDeleteAction()
    {
        $attributemenuIds = $this->getRequest()->getParam('attributemenu');
        if (!is_array($attributemenuIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('fvets_attributemenu')->__('Please select attributesmenu to delete.')
            );
        } else {
            try {
                foreach ($attributemenuIds as $attributemenuId) {
                    $attributemenu = Mage::getModel('fvets_attributemenu/attributemenu');
                    $attributemenu->setId($attributemenuId)->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('fvets_attributemenu')->__('Total of %d attributesmenu were successfully deleted.', count($attributemenuIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('fvets_attributemenu')->__('There was an error deleting attributesmenu.')
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
     * @author Ultimate Module Creator
     */
    public function massStatusAction()
    {
        $attributemenuIds = $this->getRequest()->getParam('attributemenu');
        if (!is_array($attributemenuIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('fvets_attributemenu')->__('Please select attributesmenu.')
            );
        } else {
            try {
                foreach ($attributemenuIds as $attributemenuId) {
                $attributemenu = Mage::getSingleton('fvets_attributemenu/attributemenu')->load($attributemenuId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d attributesmenu were successfully updated.', count($attributemenuIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('fvets_attributemenu')->__('There was an error updating attributesmenu.')
                );
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * mass Attribute change - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function massAttributeAction()
    {
        $attributemenuIds = $this->getRequest()->getParam('attributemenu');
        if (!is_array($attributemenuIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('fvets_attributemenu')->__('Please select attributesmenu.')
            );
        } else {
            try {
                foreach ($attributemenuIds as $attributemenuId) {
                $attributemenu = Mage::getSingleton('fvets_attributemenu/attributemenu')->load($attributemenuId)
                    ->setAttribute($this->getRequest()->getParam('flag_attribute'))
                    ->setIsMassupdate(true)
                    ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d attributesmenu were successfully updated.', count($attributemenuIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('fvets_attributemenu')->__('There was an error updating attributesmenu.')
                );
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * export as csv - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function exportCsvAction()
    {
        $fileName   = 'attributemenu.csv';
        $content    = $this->getLayout()->createBlock('fvets_attributemenu/adminhtml_attributemenu_grid')
            ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export as MsExcel - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function exportExcelAction()
    {
        $fileName   = 'attributemenu.xls';
        $content    = $this->getLayout()->createBlock('fvets_attributemenu/adminhtml_attributemenu_grid')
            ->getExcelFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export as xml - action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function exportXmlAction()
    {
        $fileName   = 'attributemenu.xml';
        $content    = $this->getLayout()->createBlock('fvets_attributemenu/adminhtml_attributemenu_grid')
            ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Check if admin has permissions to visit related pages
     *
     * @access protected
     * @return boolean
     * @author Ultimate Module Creator
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/fvets_attributemenu/attributemenu');
    }

	public function getAttributeOptionsAction()
	{
		$content = $this->loadLayout()->getLayout()->getBlock('attributemenu_value')->toHtml();
		echo $content;
	}
}
