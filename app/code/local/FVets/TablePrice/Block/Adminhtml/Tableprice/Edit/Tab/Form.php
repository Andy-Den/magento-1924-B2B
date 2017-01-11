<?php
/**
 * FVets_TablePrice extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       FVets
 * @package        FVets_TablePrice
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Table Price edit form tab
 *
 * @category    FVets
 * @package     FVets_TablePrice
 * @author      Douglas Ianitsky
 */
class FVets_TablePrice_Block_Adminhtml_Tableprice_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare the form
     *
     * @access protected
     * @return FVets_TablePrice_Block_Adminhtml_Tableprice_Edit_Tab_Form
     * @author Douglas Ianitsky
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('tableprice_');
        $form->setFieldNameSuffix('tableprice');
        $this->setForm($form);
        $fieldset = $form->addFieldset(
            'tableprice_form',
            array('legend' => Mage::helper('fvets_tableprice')->__('Table Price'))
        );

        $fieldset->addField(
            'name',
            'text',
            array(
                'label' => Mage::helper('fvets_tableprice')->__('Name'),
                'name'  => 'name',
            'required'  => true,
            'class' => 'required-entry',

           )
        );

        $fieldset->addField(
            'customer_group_id',
            'select',
            array(
                'label' => Mage::helper('fvets_tableprice')->__('Customer Group ID'),
                'name'  => 'customer_group_id',
            'required'  => true,
            'class' => 'required-entry',
            'values'=> Mage::getModel('customer/group')->getCollection()
				->addFieldToFilter('multiple_table', true) //Filtrar para mostrar somente as tabelas que terao multiplas tabelas
				->toOptionArray(),
			'after_element_html' => '<p class="nm"><small>' .  Mage::helper('fvets_tableprice')->__("Show only groups with checked option 'Has multiple table'") . '</small></p>',
           )
        );

        $fieldset->addField(
            'id_erp',
            'text',
            array(
                'label' => Mage::helper('fvets_tableprice')->__('ID ERP'),
                'name'  => 'id_erp',
            'required'  => true,
            'class' => 'required-entry',

           )
        );

		$fieldset->addField(
			'discount',
			'text',
			array(
				'label' => Mage::helper('fvets_tableprice')->__('Discount (%)'),
				'name'  => 'discount',
				'required'  => false,
				'class' => 'required-entry',

			)
		);

        $fieldset->addField(
            'status',
            'select',
            array(
                'label'  => Mage::helper('fvets_tableprice')->__('Status'),
                'name'   => 'status',
                'values' => array(
                    array(
                        'value' => 1,
                        'label' => Mage::helper('fvets_tableprice')->__('Enabled'),
                    ),
                    array(
                        'value' => 0,
                        'label' => Mage::helper('fvets_tableprice')->__('Disabled'),
                    ),
                ),
            )
        );
        $formValues = Mage::registry('current_tableprice')->getDefaultValues();
        if (!is_array($formValues)) {
            $formValues = array();
        }
        if (Mage::getSingleton('adminhtml/session')->getTablepriceData()) {
            $formValues = array_merge($formValues, Mage::getSingleton('adminhtml/session')->getTablepriceData());
            Mage::getSingleton('adminhtml/session')->setTablepriceData(null);
        } elseif (Mage::registry('current_tableprice')) {
            $formValues = array_merge($formValues, Mage::registry('current_tableprice')->getData());
        }
        $form->setValues($formValues);
        return parent::_prepareForm();
    }
}
