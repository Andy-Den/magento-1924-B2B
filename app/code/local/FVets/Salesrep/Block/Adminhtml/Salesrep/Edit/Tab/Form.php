<?php
/**
 * FVets_Salesrep extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       FVets
 * @package        FVets_Salesrep
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Sales Rep edit form tab
 *
 * @category    FVets
 * @package     FVets_Salesrep
 * @author      Douglas Borella Ianitsky
 */
class FVets_Salesrep_Block_Adminhtml_Salesrep_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare the form
     *
     * @access protected
     * @return FVets_Salesrep_Block_Adminhtml_Salesrep_Edit_Tab_Form
     * @author Douglas Borella Ianitsky
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('salesrep_');
        $form->setFieldNameSuffix('salesrep');
        $this->setForm($form);
        $fieldset = $form->addFieldset(
            'salesrep_form',
            array('legend' => Mage::helper('fvets_salesrep')->__('Sales Rep'))
        );

		$model = Mage::registry('fvets_salesrep');

		if ($model->getId()) {
			$fieldset->addField('id', 'hidden', array(
				'name' => 'id',
			));
		}


		if (!Mage::app()->isSingleStoreMode()) {
			$fieldset->addField('store_id', 'multiselect', array(
				'name' => 'stores[]',
				'label' => Mage::helper('fvets_salesrep')->__('Store View'),
				'title' => Mage::helper('fvets_salesrep')->__('Store View'),
				'required' => true,
				'values' => Mage::getSingleton('adminhtml/system_store')
					->getStoreValuesForForm(false, true),
			));
		}
		else {
			$fieldset->addField('store_id', 'hidden', array(
				'name' => 'stores[]',
				'value' => Mage::app()->getStore(true)->getId()
			));
		}

        $fieldset->addField(
            'name',
            'text',
            array(
                'label' => Mage::helper('fvets_salesrep')->__('Name'),
                'name'  => 'name',
            'required'  => true,
            'class' => 'required-entry',

           )
        );


		$fieldset->addField(
			'email',
			'text',
			array(
				'label' => Mage::helper('fvets_salesrep')->__('E-mail'),
				'name' => 'email',
			)
		);

		$array = array(
			'label' => Mage::helper('fvets_salesrep')->__('Id ERP'),
			'name' => 'id_erp',
		);
		if ($model->getId())
		{
			$array['readonly'] = true;
		}
		$fieldset->addField(
			'id_erp',
			'text',
			$array
		);

		$fieldset->addField(
			'telephone',
			'text',
			array(
				'label' => Mage::helper('fvets_salesrep')->__('Telephone'),
				'name' => 'telephone',
			)
		);

		$fieldset->addField(
			'image',
			'image',
			array(
				'label' => Mage::helper('fvets_salesrep')->__('Image'),
				'name' => 'image',
				'required' => false,
			)
		);

        $fieldset->addField(
            'status',
            'select',
            array(
                'label'  => Mage::helper('fvets_salesrep')->__('Status'),
                'name'   => 'status',
                'values' => array(
                    array(
                        'value' => 1,
                        'label' => Mage::helper('fvets_salesrep')->__('Enabled'),
                    ),
                    array(
                        'value' => 0,
                        'label' => Mage::helper('fvets_salesrep')->__('Disabled'),
                    ),
                ),
            )
        );

			$fieldset->addField('comission', 'text', array(
				'label' => Mage::helper('fvets_salesrep')->__('Comission'),
				'name' => 'comission',
				'required' => false
			));

        $formValues = Mage::registry('fvets_salesrep')->getDefaultValues();
        if (!is_array($formValues)) {
            $formValues = array();
        }
        if (Mage::getSingleton('adminhtml/session')->getSalesrepData()) {
            $formValues = array_merge($formValues, Mage::getSingleton('adminhtml/session')->getSalesrepData());
            Mage::getSingleton('adminhtml/session')->setSalesrepData(null);
        } elseif (Mage::registry('fvets_salesrep')) {
            $formValues = array_merge($formValues, Mage::registry('fvets_salesrep')->getData());
        }
        $form->setValues($formValues);
        return parent::_prepareForm();
    }
}
