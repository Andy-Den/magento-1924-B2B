<?php

class FVets_Salesrep_Block_Adminhtml_Salesrep_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'fvets_salesrep';
        $this->_controller = 'adminhtml_salesrep';
        $this->_updateButton(
            'save',
            'label',
            Mage::helper('fvets_salesrep')->__('Save Sales Rep')
        );
        /*$this->_updateButton(
            'delete',
            'label',
            Mage::helper('fvets_salesrep')->__('Delete Sales Rep')
        );*/
        $this->_addButton(
            'saveandcontinue',
            array(
                'label'   => Mage::helper('fvets_salesrep')->__('Save And Continue Edit'),
                'onclick' => 'saveAndContinueEdit()',
                'class'   => 'save',
            ),
            -100
        );
        $this->_formScripts[] = "
            function saveAndContinueEdit() {
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";

		$this->_removeButton('delete');

        if (!Mage::getSingleton('admin/session')->isAllowed('salesrep/edit')) {
            $this->_removeButton('saveandcontinue');
            $this->_removeButton('save');
        }
    }

    /**
     * get the edit form header
     *
     * @access public
     * @return string
     * @author Douglas Borella Ianitsky
     */
    public function getHeaderText()
    {
        if (Mage::registry('fvets_salesrep') && Mage::registry('fvets_salesrep')->getId()) {
            return Mage::helper('fvets_salesrep')->__(
                "Edit Sales Rep '%s'",
                $this->escapeHtml(Mage::registry('fvets_salesrep')->getName())
            );
        } else {
            return Mage::helper('fvets_salesrep')->__('Add Sales Rep');
        }
    }
}
