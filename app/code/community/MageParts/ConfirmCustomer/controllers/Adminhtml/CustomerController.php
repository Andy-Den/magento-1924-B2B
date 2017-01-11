<?php
/**
 * MageParts
 *
 * NOTICE OF LICENSE
 *
 * This code is copyrighted by MageParts and may not be reproduced
 * and/or redistributed without a written permission by the copyright
 * owners. If you wish to modify and/or redistribute this file please
 * contact us at info@mageparts.com for confirmation before doing
 * so. Please note that you are free to modify this file for personal
 * use only.
 *
 * If you wish to make modifications to this file we advice you to use
 * the "local" file scope in order to aviod conflicts with future updates.
 * For information regarding modifications see http://www.magentocommerce.com.
 *
 * DISCLAIMER
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE
 * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF
 * USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY
 * OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE
 * OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED
 * OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   MageParts
 * @package    MageParts_ConfirmCustomer
 * @copyright  Copyright (c) 2009 MageParts (http://www.mageparts.com/)
 * @author 	   MageParts Crew
 */

class MageParts_ConfirmCustomer_Adminhtml_CustomerController extends Mage_Adminhtml_Controller_Action
{

	protected function _initCustomer($idFieldName = 'customer_id')
	{
		$this->_title($this->__('Customers'))->_title($this->__('Manage Customers'));

		$customerId = (int) $this->getRequest()->getParam($idFieldName);
		$customer = Mage::getModel('customer/customer');

		if ($customerId) {
			$customer->load($customerId);
		}

		Mage::register('current_customer', $customer);
		return $this;
	}

	/**
	 * Approve a customer account
	 */
	public function approveAction()
	{
		$this->_initCustomer();

		// get customer id
    	$id = $this->getRequest()->getParam('customer_id');

		// attempt to load customer
		$model = Mage::getModel('customer/customer');

		$data = Mage::getSingleton('adminhtml/session')->getFormData(true);

		if (!empty($data)) {
			$model->setData($data);
		}

		$this->loadLayout();
		$this->_setActiveMenu('customer/fvets_salesrep');
		$this->renderLayout();
	}

	/**
	 * Approve a customer account
	 */
	public function saveAction()
	{
		$this->_initCustomer();

		// get customer id
		$id = $this->getRequest()->getParam('customer_id');

		// attempt to load customer
		$model = Mage::getModel('customer/customer');

		$approve = true;


		if ($id = Mage::app()->getRequest()->getPost('customer_id'))
		{
			try {
				$model->load($id);

				if (!$model->getId()) {
					Mage::getSingleton('adminhtml/session')->addError(Mage::helper('confirmcustomer')->__('This customer no longer exist or invalid customer id'));
					$approve = false;
				}
				else
				{

					$customerAlreadyApproved = $model->getData('mp_cc_is_approved') == MageParts_ConfirmCustomer_Helper_Data::STATE_APPROVED ? true : false;

					/**
					 * Approve disaprove customer
					 */
					// approve customer
					$model->setMpCcIsApproved($this->getRequest()->getPost('mp_cc_is_approved'));

					// send mail to customer confirming approval if the admin is approving the customer and not only changing other data;
					if (!$customerAlreadyApproved && $this->getRequest()->getPost('mp_cc_is_approved') == MageParts_ConfirmCustomer_Helper_Data::STATE_APPROVED) {
						$model->sendAccountApprovalEmail($model->getStoreId());
					}

					if ($this->getRequest()->getPost('mp_cc_is_approved') != MageParts_ConfirmCustomer_Helper_Data::STATE_APPROVED)
					{
						// add error message
						Mage::getSingleton('adminhtml/session')->addError(Mage::helper('confirmcustomer')->__('The customer has been disapproved'));
						$approve = false;
					}

					/**
					 * Sales Rep
					 */

					//defining commission
					if (!$model->getCommission() && $model->getCommission() == null) {
						$fixedComissionEnabled = Mage::getStoreConfig('comissions/general/fixed_comission', $model->getStoreId());
						if ($fixedComissionEnabled && $fixedComissionEnabled == 1) {
							$fixedComissionValue = Mage::getStoreConfig('comissions/general/fixed_comission_value', $model->getStoreId());
							if ($fixedComissionValue) {
								$model->setCommission($fixedComissionValue);
							}
						} else {
							$siteComissionValue = Mage::getStoreConfig('comissions/general/site_comission_value', $model->getStoreId());
							if ($siteComissionValue) {
								$model->setCommission($siteComissionValue);
							}
						}
					}
					
					$model->setFvetsSalesrep($this->getRequest()->getPost('fvets_salesrep'));

					/**
					 * Storeview
					 */

					//caso a store não esteja configurada para seleção de storeviews na aprovação, sete no usuário todas as stores do website
					$websiteId = $model->getWebsiteId();
					$website = Mage::getModel('core/website')->load($websiteId);
					$multiStoreWebsites = Mage::getStoreConfig('confirmcustomer/general/store_select');
					if (!$multiStoreWebsites || !in_array($website->getCode(), explode(',', $multiStoreWebsites))) {
						if (!$customerAlreadyApproved) {
							$storeIds = $website->getStoreIds();
							$model->setStoreView(implode(',', $storeIds));
						}
					} else {
						$model->setStoreView($this->getRequest()->getPost('store_view'));
					}

					/**
					 * Hive of activity
					 */

					$model->setHiveOfActivity($this->getRequest()->getPost('hive_of_activity'));

					/**
					 * ID ERP
					 */

					$otherCustomer = Mage::getModel('customer/customer')
						->getCollection()
						->addAttributeToSelect('id_erp')
						->addAttributeToFilter('id_erp',$this->getRequest()->getPost('id_erp'))
						->addAttributeToFilter('entity_id',array('neq' => $model->getId()))
						->addAttributeToFilter('website_id',$model->getWebsiteId())
						->load()
						->getFirstItem();

					if (!$otherCustomer->getId())
					{
						$model->setIdErp($this->getRequest()->getPost('id_erp'));
					}
					else
					{
						Mage::getSingleton('adminhtml/session')->addError(Mage::helper('confirmcustomer')->__('A user already exists with this ID ERP'));
						$approve = false;
						$model->setMpCcIsApproved(MageParts_ConfirmCustomer_Helper_Data::STATE_UNAPPROVED_DUPLICATED);
					}

					/**
					 * Group ID
					 */

					$model->setGroupId($this->getRequest()->getPost('group_id'));

					/**
					 * Restriction Group
					 */

					$restriction_group = (null !== $this->getRequest()->getPost('restriction_group')) ? array_flip($this->getRequest()->getPost('restriction_group')) : array();
					Mage::getResourceSingleton('fvets_catalogrestrictiongroup/catalogrestrictiongroup_customer')->saveCustomerRelation($model, $restriction_group);


					$conditions = (null !== $this->getRequest()->getPost('confirm_conditions')) ? 
						array_flip($this->getRequest()->getPost('confirm_conditions')): array();

					Mage::getResourceSingleton('fvets_payment/condition_customer')
						->saveCustomerRelation($model,$conditions);
					
					if ($approve)
					{
						Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('confirmcustomer')->__('The customer has been approved'));
					}

					$model->save();
				}

			}
			catch (Exception $e){
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
		}

		// redirect back to edit the customer
		$this->_redirect('*/*/approve', array('customer_id' => $id));
		return;
	}


	/**
	 * Disapprove a customer account
	 */
	/*public function disapproveAction()
	{
		// get customer id
    	$id = $this->getRequest()->getParam('customer_id');

		// attempt to load customer
		$model = Mage::getModel('customer/customer');

		if($id) {
			try {
				$model->load($id);

				if (!$model->getId()) {
					Mage::getSingleton('adminhtml/session')->addError(Mage::helper('confirmcustomer')->__('This customer no longer exist or invalid customer id'));
				}
				else if (!$model->getMpCcIsApproved()) {
					Mage::getSingleton('adminhtml/session')->addError(Mage::helper('confirmcustomer')->__('This customer is already unapproved'));
				}
				else {
					// approve customer
					$model->setMpCcIsApproved(false)
						->save();

					// add success message
					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('confirmcustomer')->__('The customer has been disapproved'));
				}
			}
			catch (Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
		}

		$this->_redirect('adminhtml/customer/edit', array('id' => $id));
		return;
	}*/

	/**
	 * Approve multiple customer accounts at once
	 */
	public function massApproveAction()
	{
		// get customer ids
		$customersIds = $this->getRequest()->getParam('customer');

		// count of updated records
		$updatedCount = 0;

        if(!is_array($customersIds)) {
 			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select customer(s).'));
        } else {
			try {
				foreach ($customersIds as $customerId) {
					// load customer
                    $customer = Mage::getModel('customer/customer')->load($customerId);

					if ($customer->getMpCcIsApproved() != MageParts_ConfirmCustomer_Helper_Data::STATE_APPROVED) {
						// set customer as disapproved
						$customer->setMpCcIsApproved(MageParts_ConfirmCustomer_Helper_Data::STATE_APPROVED)
							->save();

						// send mail to customer confirming approval
						$customer->sendAccountApprovalEmail($customer->getStoreId());

						$updatedCount++;
					}
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(
					Mage::helper('adminhtml')->__('Total of %d record(s) were updated.', $updatedCount)
                );
            }
			catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }

        $this->_redirect('adminhtml/customer/index');
	}

	/**
	 * Disapprove multiple customer accounts at once
	 */
	public function massDisapproveAction()
	{
		// get customer ids
		$customersIds = $this->getRequest()->getParam('customer');

		// count of updated records
		$updatedCount = 0;

        if(!is_array($customersIds)) {
 			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select customer(s).'));
        } else {
			try {
				foreach ($customersIds as $customerId) {
					// load customer
                    $customer = Mage::getModel('customer/customer')->load($customerId);

					if ($customer->getMpCcIsApproved() == MageParts_ConfirmCustomer_Helper_Data::STATE_APPROVED) {
						// set customer as disapproved
						$customer->setMpCcIsApproved(MageParts_ConfirmCustomer_Helper_Data::STATE_UNAPPROVED_WAITING)
							->save();

						$updatedCount++;
					}
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(
					Mage::helper('adminhtml')->__('Total of %d record(s) were updated.', $updatedCount)
                );
            }
			catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }

        $this->_redirect('adminhtml/customer/index');
	}

	/**
	 * User permission checkup
	 *
	 * @return boolean
	 */
	protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('customer/approve');
    }

}