<?php
/**
 * Sales Rep admin controller
 *
 * @category    FVets
 * @package     FVets_Salesrep
 */
class FVets_Salesrep_Adminhtml_Fvets_SalesrepController extends FVets_Salesrep_Controller_Adminhtml_Salesrep
{
    /**
     * init the sales rep
     *
     * @access protected
     * @return FVets_Salesrep_Model_Salesrep
     */
    protected function _initSalesrep()
    {
        $salesrepId  = (int) $this->getRequest()->getParam('id');
        $salesrep    = Mage::getModel('fvets_salesrep/salesrep');
        if ($salesrepId) {
            $salesrep->load($salesrepId);
        }
        Mage::register('fvets_salesrep', $salesrep);
        return $salesrep;
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
        $this->_title(Mage::helper('fvets_salesrep')->__('Sales Representative'))
             ->_title(Mage::helper('fvets_salesrep')->__('Sales Representatives'));
        $this->_setActiveMenu('customer/fvets_salesrep');
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
     * edit sales rep - action
     *
     * @access public
     * @return void
     */
    public function editAction()
    {
        $salesrepId = $this->getRequest()->getParam('id');
        $salesrep      = $this->_initSalesrep();
        if ($salesrepId && !$salesrep->getId()) {
            $this->_getSession()->addError(
                Mage::helper('fvets_salesrep')->__('This sales rep no longer exists.')
            );
            $this->_redirect('*/*/');
            return;
        }

        $path = 'fvets' . DS . 'salesrep' . DS . $salesrep->getId() . '.jpg';
		if (file_exists(Mage::getBaseDir('media') . DS . $path)) {
        	$salesrep->setImage($path);
        }

        $data = Mage::getSingleton('adminhtml/session')->getSalesrepData(true);
        
        if (!empty($data)) {
            $salesrep->setData($data);
        }
        Mage::register('salesrep_data', $salesrep);
        
        $this->loadLayout();
        $this->_title(Mage::helper('fvets_salesrep')->__('Sales Representative'))
             ->_title(Mage::helper('fvets_salesrep')->__('Sales Representatives'));
        if ($salesrep->getId()) {
            $this->_title($salesrep->getName());
        } else {
            $this->_title(Mage::helper('fvets_salesrep')->__('Add sales rep'));
        }
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
        $this->renderLayout();
    }

    /**
     * new sales rep action
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
     * save sales rep - action
     *
     * @access public
     * @return void
     * @author Douglas Borella Ianitsky
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost('salesrep')) {

			//Salvar views do representante.
			if(isset($data['stores'])) {
				if(in_array('0',$data['stores'])){
					$data['store_id'] = '0';
				}
				else{
					$data['store_id'] = implode(",", $data['stores']);
				}
				unset($data['stores']);
			}
        
            try {
                $salesrep = $this->_initSalesrep();
                $salesrep->addData($data);
                $categories = $this->getRequest()->getPost('category_ids', -1);
                if ($categories != -1) {
                    $categories = explode(',', $categories);
                    $categories = array_unique($categories);
                    $salesrep->setCategoriesData($categories);
                }

				$regions = $this->getRequest()->getPost('regions', -1);
				if ($regions != -1) {
					$salesrep->setRegionsData(Mage::helper('adminhtml/js')->decodeGridSerializedInput($regions));
				}

                try {
                	$salesrep->save();

									if (isset($_FILES['image']['name']) and (file_exists($_FILES['image']['tmp_name'])))
									{
										try
										{
											$uploader = Mage::getModel('core/file_uploader', 'image');
											$uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
											$uploader->setAllowRenameFiles(false);
											$uploader->setFilesDispersion(false);

											$path = Mage::getBaseDir('media') . DS . 'fvets' . DS . 'salesrep';
											$filename = $salesrep->getId() . '.jpg';
											$uploader->save($path, $filename);

											//Remove old salesrep image
											Mage::helper('fvets_salesrep')->deleteResizedImages($salesrep->getId());
										} catch (Exception $e)
										{
											Mage::getSingleton('adminhtml/session')->addError(Mage::helper('fvets_salesrep')->__('The sales representative image was not saved.'));
										}
									} else {
                    	if (!empty($data['image']['delete'])) {
                        	$path = Mage::getBaseDir('media') . DS . 'fvets' . DS . 'salesrep' . DS . $salesrep->getId() . '.jpg';
	                        @unlink($path);

												//Remove old salesrep image
												Mage::helper('fvets_salesrep')->deleteResizedImages($salesrep->getId());
    	                }
        	        }
                } catch (Exception $e) {
                	Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                	Mage::getSingleton('adminhtml/session')->setFormData($data);

                	if ($data['id']) {
                    	$this->_redirect('*/*/edit', array('id' => $data['id']));
                	} else {
                    	$this->_redirect('*/*/new');
                	}
            	}
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('fvets_salesrep')->__('Sales Rep was successfully saved')
                );
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $salesrep->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setSalesrepData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            } catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('fvets_salesrep')->__('There was a problem saving the sales rep.')
                );
                Mage::getSingleton('adminhtml/session')->setSalesrepData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('fvets_salesrep')->__('Unable to find sales rep to save.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * delete sales rep - action
     *
     * @access public
     * @return void
     * @author Douglas Borella Ianitsky
     */
    public function deleteAction()
    {
        if ( $this->getRequest()->getParam('id') > 0) {
            try {
                $salesrep = Mage::getModel('fvets_salesrep/salesrep');
                $salesrep->setId($this->getRequest()->getParam('id'))->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('fvets_salesrep')->__('Sales Rep was successfully deleted.')
                );
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('fvets_salesrep')->__('There was an error deleting sales rep.')
                );
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                Mage::logException($e);
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('fvets_salesrep')->__('Could not find sales rep to delete.')
        );
        $this->_redirect('*/*/');
    }

    /**
     * mass delete sales rep - action
     *
     * @access public
     * @return void
     * @author Douglas Borella Ianitsky
     */
    public function massDeleteAction()
    {
        $salesrepIds = $this->getRequest()->getParam('salesrep');
        if (!is_array($salesrepIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('fvets_salesrep')->__('Please select sales representatives to delete.')
            );
        } else {
            try {
                foreach ($salesrepIds as $salesrepId) {
                    $salesrep = Mage::getModel('fvets_salesrep/salesrep');
                    $salesrep->setId($salesrepId)->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('fvets_salesrep')->__('Total of %d sales representatives were successfully deleted.', count($salesrepIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('fvets_salesrep')->__('There was an error deleting sales representatives.')
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
        $salesrepIds = $this->getRequest()->getParam('salesrep');
        if (!is_array($salesrepIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('fvets_salesrep')->__('Please select sales representatives.')
            );
        } else {
            try {
                foreach ($salesrepIds as $salesrepId) {
                $salesrep = Mage::getSingleton('fvets_salesrep/salesrep')->load($salesrepId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d sales representatives were successfully updated.', count($salesrepIds))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('fvets_salesrep')->__('There was an error updating sales representatives.')
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
     * @author Douglas Borella Ianitsky
     */
    public function categoriesAction()
    {
        $this->_initSalesrep();
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * get child categories action
     *
     * @access public
     * @return void
     * @author Douglas Borella Ianitsky
     */
    public function categoriesJsonAction()
    {
        $this->_initSalesrep();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('fvets_salesrep/adminhtml_salesrep_edit_tab_categories')
                ->getCategoryChildrenJson($this->getRequest()->getParam('category'))
        );
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
		$this->_initSalesrep();
		$this->loadLayout();
		$this->getLayout()->getBlock('salesrep.edit.tab.region')
			->setSalesrepRegions($this->getRequest()->getPost('salesrep_regions', null));
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
		$this->_initSalesrep();
		$this->loadLayout();
		$this->getLayout()->getBlock('salesrep.edit.tab.region')
			->setSalesrepRegions($this->getRequest()->getPost('salesrep_regions', null));
		$this->renderLayout();
	}

	/**
	 * get grid of customers action
	 *
	 * @access public
	 * @return void
	 * @author Douglas Borella Ianitsky
	 */
	public function customersAction()
	{
		$this->_initSalesrep();
		$this->loadLayout();
		$this->getLayout()->getBlock('salesrep.edit.tab.customer')
			->setSalesrepCustomers($this->getRequest()->getPost('salesrep_customers', null));
		$this->renderLayout();
	}

	/**
	 * get grid of customers action
	 *
	 * @access public
	 * @return void
	 * @author Douglas Borella Ianitsky
	 */
	public function customersgridAction()
	{
		$this->_initSalesrep();
		$this->loadLayout();
		$this->getLayout()->getBlock('salesrep.edit.tab.customer')
			->setSalesrepCustomers($this->getRequest()->getPost('salesrep_customers', null));
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
        $fileName   = 'salesrep.csv';
        $content    = $this->getLayout()->createBlock('fvets_salesrep/adminhtml_salesrep_grid')
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
        $fileName   = 'salesrep.xls';
        $content    = $this->getLayout()->createBlock('fvets_salesrep/adminhtml_salesrep_grid')
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
        $fileName   = 'salesrep.xml';
        $content    = $this->getLayout()->createBlock('fvets_salesrep/adminhtml_salesrep_grid')
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
        return Mage::getSingleton('admin/session')->isAllowed('admin/customer/fvets_salesrep');
    }
}
