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

class MageParts_ConfirmCustomer_Block_Adminhtml_Customer_Edit extends Mage_Adminhtml_Block_Customer_Edit
{

	public function __construct()
	{
		parent::__construct();

		if ($this->getCustomerId()) {

			// get current customer object
			$customer = Mage::registry('current_customer');

			// add approve button
			$this->_addButton('approve', array(
				'label' => Mage::helper('confirmcustomer')->__('Approve'),
				'onclick' => 'setLocation(\'' . $this->getUrl('confirmcustomer/adminhtml_customer/approve', array('customer_id' => $this->getCustomerId())) . '\')',
				'class' => 'save',
			), 0);

			//allow any user to save customer
			$this->_addButton('save_and_continue', array(
				'label' => Mage::helper('customer')->__('Save and Continue Edit'),
				'onclick' => 'saveAndContinueEdit(\'' . $this->_getSaveAndContinueUrl() . '\')',
				'class' => 'save'
			), 10);
		}
	}

	protected function _getSaveAndContinueUrl()
	{
		return $this->getUrl('*/*/save', array(
			'_current'  => true,
			'back'      => 'edit',
			'tab'       => '{{tab_id}}'
		));
	}

   /* public function __construct()
    {
		parent::__construct();
 
        if ($this->getCustomerId() &&
            Mage::getSingleton('admin/session')->isAllowed('customer/approve')) {

			// get current customer object
        	$customer = Mage::registry('current_customer');

			// add approve button
			if (!$customer->getMpCcIsApproved()) {
				$this->_addButton('approve', array(
					'label' => Mage::helper('confirmcustomer')->__('Approve'),
					'onclick' => 'setLocation(\'' . $this->getUrl('confirmcustomer/adminhtml_customer/approve', array('customer_id' => $this->getCustomerId())) . '\')',
					'class' => 'save',
				), 0);
			}
			else {
				// add disapprove button
				$this->_addButton('disapprove', array(
					'label' => Mage::helper('confirmcustomer')->__('Disapprove'),
					'onclick' => 'setLocation(\'' . $this->getUrl('confirmcustomer/adminhtml_customer/disapprove', array('customer_id' => $this->getCustomerId())) . '\')',
					'class' => 'delete',
				), 0);
			}
        }
    }*/

}
