<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   FVets
 * @package    FVets_CustomGrid
 * @copyright  Copyright (c) 2014 Carlos Farah <carlos.farah@gmail.com>
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class FVets_CustomGrid_Block_Widget_Grid_Form_Attribute_Default
    extends FVets_CustomGrid_Block_Widget_Grid_Form
{
    public function getIsRequiredValueEdit()
    {
        if ($attribute = $this->getEditedAttribute()) {
            return $attribute->getIsRequired();
        }
        return false;
    }
    
    protected function _prepareForm()
    {
        $form = $this->_createForm();
        $form->setDataObject($this->getEditedEntity());
        
        $config    = $this->getEditedConfig();
        $entity    = $this->getEditedEntity();
        $attribute = $this->getEditedAttribute();
        
        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => $this->__('%s : %s', $this->getEditedEntityName(), $attribute->getFrontendLabel())
                . ($attribute->getIsRequired() ? ' (<span class="fvetscg-editor-required-marker">'.$this->__('Required').'</span>)' : ''),
            'class'  => 'fieldset-wide fvetscg-editor-fieldset',
        ));
        $this->_setFieldset(array($attribute), $fieldset);
        
        $form->addValues($entity->getData());
        $form->setFieldNameSuffix($config['values_key']);
        $this->setForm($form);
        return $this;
    }
    
    protected function _beforeToHtml()
    {
        if (is_object($this->getEditedAttribute())) {
            return parent::_beforeToHtml();
        } else {
            $this->_canDisplay = false;
            return $this;
        }
    }
}