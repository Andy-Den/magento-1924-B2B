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

if(Mage::helper('core')->isModuleEnabled('MageFM_Customer')){
	class MageParts_ConfirmCustomer_Block_Adminhtml_Customer_Grid_Tmp extends MageFM_Customer_Block_Adminhtml_Customer_Grid {}
} else {
	class MageParts_ConfirmCustomer_Block_Adminhtml_Customer_Grid_Tmp extends Mage_Adminhtml_Block_Customer_Grid {}
}

class MageParts_ConfirmCustomer_Block_Adminhtml_Customer_Grid extends MageParts_ConfirmCustomer_Block_Adminhtml_Customer_Grid_Tmp
{
	
    protected function _prepareColumns()
    {
		// add "Approved" column to customer grid
		$this->addColumnAfter('mp_cc_is_approved', array(
			'header'    => Mage::helper('confirmcustomer')->__('Approved'),
			'index'     => 'mp_cc_is_approved',
			'type'      => 'options',
			'options'   => Mage::helper('confirmcustomer')->getApprovalStates()
		), 'website_id');



		$link= Mage::helper('adminhtml')->getUrl('confirmcustomer/adminhtml_customer/approve/') .'customer_id/$entity_id';
		$this->addColumnAfter('mp_cc_approve', array(
			'header'   => $this->helper('catalog')->__('Action'),
			'width'    => 15,
			'sortable' => false,
			'filter'   => false,
			'type'     => 'action',
			'actions'  => array(
				array(
					'url'     => $link,
					'caption' => $this->helper('catalog')->__('Aprovar / Desaprovar'),
				),
			)
		), 'website_id');

		// add default grid columns
		$parentPrepareColumns = parent::_prepareColumns();

		//removendo coluna de grupo
		$this->removeColumn('group');
		return $parentPrepareColumns;
    }

	protected function _prepareMassaction()
    {
		// add default mass actions
		parent::_prepareMassaction();

		// add mass action "approve"
        /*$this->getMassactionBlock()->addItem('approve', array(
             'label'    => Mage::helper('confirmcustomer')->__('Approve'),
             'url'      => $this->getUrl('confirmcustomer/adminhtml_customer/massApprove'),
             'confirm'  => Mage::helper('customer')->__('Are you sure?')
        ));

        // add mass action "disapprove"
        $this->getMassactionBlock()->addItem('disapprove', array(
             'label'    => Mage::helper('confirmcustomer')->__('Disapprove'),
             'url'      => $this->getUrl('confirmcustomer/adminhtml_customer/massDisapprove'),
             'confirm'  => Mage::helper('customer')->__('Are you sure?')
        ));*/

        return $this;
    }

	public function setCollection($collection)
	{
		//Não mostra usuários que estão inativos, somente se o filtro de usuários inativos for usado.
		$collection->addAttributeToSelect('mp_cc_is_approved');
		$filter = $this->helper('adminhtml')->prepareFilterString($this->getParam($this->getVarNameFilter(), null));
		if (!array_key_exists('mp_cc_is_approved', $filter))
		{
			$collection->getSelect()->where('mp_cc_is_approved != 4');
		}

		parent::setCollection($collection);
	}
	
}
