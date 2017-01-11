<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */

class Amasty_Rolepermissions_Block_Adminhtml_Tab_Products extends Mage_Adminhtml_Block_Widget_Form
{
    const MODE_ANY = 0;
    const MODE_SELECTED = 1;
    const MODE_MY = 2;

    public function _beforeToHtml()
    {
        $this->_initForm();
        return parent::_beforeToHtml();
    }

    protected function _initForm()
    {
        $form = new Varien_Data_Form();

        $model = Mage::registry('current_rule');

        if ($model->getProducts() == '')
            $modeValue = self::MODE_ANY;
        else if ($model->getProducts() == 0)
            $modeValue = self::MODE_MY;
        else
            $modeValue = self::MODE_SELECTED;

        $fieldset = $form->addFieldset('products_mode_fieldset', array('legend'=>$this->__('Product Access'), 'class' => 'fieldset-wide'));

        $grid = $this->getLayout()->createBlock('amrolepermissions/adminhtml_tab_products_grid')
            ->setProductIds($this->getRequest()->getPost('amrolepermissions[products]', null));


        $serializer = $this->getLayout()->createBlock('adminhtml/widget_grid_serializer');
        $serializer->initSerializerBlock($grid, 'getSavedProducts', 'amrolepermissions[products]', 'product_ids');

        $mode = $fieldset->addField('amrolepermissions[products_access_mode]', 'select',
            array(
                'label' => $this->__('Allow Access To'),
                'id'    => 'amrolepermissions[products_access_mode]',
                'name'  => 'amrolepermissions[products_access_mode]',
                'values'=> array(
                    self::MODE_ANY => $this->__('All Products'),
                    self::MODE_SELECTED => $this->__('Selected Products'),
                    self::MODE_MY => $this->__('Own Created Products'),
                ),
                'value' => $modeValue
            )
        );

        $fieldset->addField('products_list', 'hidden',
            array(
                'after_element_html' => $grid->toHtml() . $serializer->toHtml(),
            )
        );

        $this->setForm($form);

        $this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
            ->addFieldMap($mode->getHtmlId(), $mode->getName())
            ->addFieldMap('product_grid', 'product_grid')
            ->addFieldDependence(
                'product_grid',
                $mode->getName(),
                self::MODE_SELECTED
            )
        );
    }
}
