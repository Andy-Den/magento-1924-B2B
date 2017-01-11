<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */


class Amasty_Rolepermissions_Block_Adminhtml_Tab_Products_Grid_Massaction extends Mage_Adminhtml_Block_Widget_Grid_Massaction
{
    public function getJavaScript()
    {
        return " window.{$this->getJsObjectName()} = new varienGridMassaction('{$this->getHtmlId()}', "
        . "{$this->getGridJsObjectName()}, '{$this->getSelectedJson()}'"
        . ", '{$this->getFormFieldNameInternal()}', '{$this->getFormFieldName()}');"
        . "{$this->getJsObjectName()}.setItems({$this->getItemsJson()}); "
        . "{$this->getJsObjectName()}.setGridIds('{$this->getGridIdsJson()}');"
        . ($this->getUseAjax() ? "{$this->getJsObjectName()}.setUseAjax(true);" : '')
        . ($this->getUseSelectAll() ? "{$this->getJsObjectName()}.setUseSelectAll(true);" : '')
        . "{$this->getJsObjectName()}.errorText = '{$this->getErrorText()}';";
    }

    public function getSelectedJson()
    {
        if($selected = Mage::registry('current_rule')->getProducts()) {
            $selected = explode(',', $selected);
            return join(',', $selected);
        } else {
            return '';
        }
    }
}