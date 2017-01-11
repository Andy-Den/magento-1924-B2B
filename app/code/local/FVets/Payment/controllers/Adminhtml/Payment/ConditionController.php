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
 * Condition admin controller
 *
 * @category    FVets
 * @package     FVets_Payment
 */
class FVets_Payment_Adminhtml_Payment_ConditionController extends FVets_Payment_Controller_Adminhtml_Payment
{
    /**
     * init the condition
     *
     * @access protected
     * @return FVets_Payment_Model_Condition
     */
    protected function _initCondition()
    {
        $conditionId  = (int) $this->getRequest()->getParam('id');
        $condition    = Mage::getModel('fvets_payment/condition');
        if ($conditionId) {
            $condition->load($conditionId);
        }
        Mage::register('current_condition', $condition);
        return $condition;
    }

    /**
     * default action
     *
     * @access public
     * @return void
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_title(Mage::helper('fvets_payment')->__('Payment Conditions'))
             ->_title(Mage::helper('fvets_payment')->__('Conditions'));
        $this->renderLayout();
    }

    /**
     * grid action
     *
     * @access public
     * @return void
     */
    public function gridAction()
    {
        $this->loadLayout()->renderLayout();
    }

    /**
     * edit condition - action
     *
     * @access public
     * @return void
     */
    public function editAction()
    {
        $conditionId    = $this->getRequest()->getParam('id');
        $condition      = $this->_initCondition();
        if ($conditionId && !$condition->getId()) {
            $this->_getSession()->addError(
                Mage::helper('fvets_payment')->__('This condition no longer exists.')
            );
            $this->_redirect('*/*/');
            return;
        }
        $data = Mage::getSingleton('adminhtml/session')->getConditionData(true);
        if (!empty($data)) {
            $condition->setData($data);
        }
        Mage::register('condition_data', $condition);
        $this->loadLayout();
        $this->_title(Mage::helper('fvets_payment')->__('Payment Conditions'))
             ->_title(Mage::helper('fvets_payment')->__('Conditions'));
        if ($condition->getId()) {
            $this->_title($condition->getName());
        } else {
            $this->_title(Mage::helper('fvets_payment')->__('Add condition'));
        }
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
        $this->renderLayout();
    }

    /**
     * new condition action
     *
     * @access public
     * @return void
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * save condition - action
     *
     * @access public
     * @return void
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost('condition')) {
            try {
                $condition = $this->_initCondition();
                $condition->addData($data);
                $customers = $this->getRequest()->getPost('customers', -1);
                if ($customers != -1) {
                    $condition->setCustomersData(Mage::helper('adminhtml/js')->decodeGridSerializedInput($customers));
                }
				$categories = $this->getRequest()->getPost('category_ids', -1);
				if ($categories != -1) {
					$categories = explode(',', $categories);
					$categories = array_unique($categories);
					$condition->setCategoriesData($categories);
				}
                $excluded = $this->getRequest()->getPost('excluded_ids', -1);
                if ($excluded != -1) {
                    $excluded = explode(',', $excluded);
                    $excluded = array_unique($excluded);
                    $condition->setExcludedCategoriesData($excluded);
                }
                $condition->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('fvets_payment')->__('Condition was successfully saved')
                );
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $condition->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setConditionData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            } catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('fvets_payment')->__('There was a problem saving the condition.')
                );
                Mage::getSingleton('adminhtml/session')->setConditionData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('fvets_payment')->__('Unable to find condition to save.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * delete condition - action
     *
     * @access public
     * @return void
     */
    public function deleteAction()
    {
        if ( $this->getRequest()->getParam('id') > 0) {
            try {
                $condition = Mage::getModel('fvets_payment/condition');
                $condition->setId($this->getRequest()->getParam('id'))->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('fvets_payment')->__('Condition was successfully deleted.')
                );
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('fvets_payment')->__('There was an error deleting condition.')
                );
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                Mage::logException($e);
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('fvets_payment')->__('Could not find condition to delete.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * mass delete condition - action
     *
     * @access public
     * @return void
     */
    public function massDeleteAction()
    {
        $conditionIds = $this->getRequest()->getParam('condition');
        if (!is_array($conditionIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('fvets_payment')->__('Please select conditions to delete.')
            );
        } else {
            try {
                foreach ($conditionIds as $conditionId) {
                    $condition = Mage::getModel('fvets_payment/condition');
                    $condition->setId($conditionId)->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('fvets_payment')->__('Total of %d conditions were successfully deleted.', count($conditionIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('fvets_payment')->__('There was an error deleting conditions.')
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
     */
    public function massStatusAction()
    {
        $conditionIds = $this->getRequest()->getParam('condition');
        if (!is_array($conditionIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('fvets_payment')->__('Please select conditions.')
            );
        } else {
            try {
                foreach ($conditionIds as $conditionId) {
                $condition = Mage::getSingleton('fvets_payment/condition')->load($conditionId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d conditions were successfully updated.', count($conditionIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('fvets_payment')->__('There was an error updating conditions.')
                );
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * mass Apply to all costumers change - action
     *
     * @access public
     * @return void
     */
    public function massApplyToAllAction()
    {
        $conditionIds = $this->getRequest()->getParam('condition');
        if (!is_array($conditionIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('fvets_payment')->__('Please select conditions.')
            );
        } else {
            try {
                foreach ($conditionIds as $conditionId) {
                $condition = Mage::getSingleton('fvets_payment/condition')->load($conditionId)
                    ->setApplyToAll($this->getRequest()->getParam('flag_apply_to_all'))
                    ->setIsMassupdate(true)
                    ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d conditions were successfully updated.', count($conditionIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('fvets_payment')->__('There was an error updating conditions.')
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
	 * @author Ultimate Module Creator
	 */
	public function categoriesAction()
	{
		$this->_initCondition();
		$this->loadLayout();
		$this->renderLayout();
	}

	/**
	 * get child categories action
	 *
	 * @access public
	 * @return void
	 * @author Ultimate Module Creator
	 */
	public function categoriesJsonAction()
	{
		$this->_initCondition();
		$this->getResponse()->setBody(
			$this->getLayout()->createBlock('fvets_payment/adminhtml_condition_edit_tab_categories')
				->getCategoryChildrenJson($this->getRequest()->getParam('category'))
		);
	}

    /**
     * get categories action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function excludedAction()
    {
        $this->_initCondition();
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * get child categories action
     *
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function excludedJsonAction()
    {
        $this->_initCondition();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('fvets_payment/adminhtml_condition_edit_tab_excluded')
                ->getCategoryChildrenJson($this->getRequest()->getParam('excluded'))
        );
    }

    /**
     * get grid of customers action
     *
     * @access public
     * @return void
     */
    public function customersAction()
    {
        $this->_initCondition();
        $this->loadLayout();
        $this->getLayout()->getBlock('condition.edit.tab.customer')
            ->setConditionCustomers($this->getRequest()->getPost('condition_customers', null));
        $this->renderLayout();
    }

    /**
     * get grid of customers action
     *
     * @access public
     * @return void
     */
    public function customersgridAction()
    {
        $this->_initCondition();
        $this->loadLayout();
        $this->getLayout()->getBlock('condition.edit.tab.customer')
            ->setConditionCustomers($this->getRequest()->getPost('condition_customers', null));
        $this->renderLayout();
    }

    /**
     * export as csv - action
     *
     * @access public
     * @return void
     */
    public function exportCsvAction()
    {
        $fileName   = 'condition.csv';
        $content    = $this->getLayout()->createBlock('fvets_payment/adminhtml_condition_grid')
            ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export as MsExcel - action
     *
     * @access public
     * @return void
     */
    public function exportExcelAction()
    {
        $fileName   = 'condition.xls';
        $content    = $this->getLayout()->createBlock('fvets_payment/adminhtml_condition_grid')
            ->getExcelFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export as xml - action
     *
     * @access public
     * @return void
     */
    public function exportXmlAction()
    {
        $fileName   = 'condition.xml';
        $content    = $this->getLayout()->createBlock('fvets_payment/adminhtml_condition_grid')
            ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Check if admin has permissions to visit related pages
     *
     * @access protected
     * @return boolean
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/fvets_payment/condition');
    }
}
