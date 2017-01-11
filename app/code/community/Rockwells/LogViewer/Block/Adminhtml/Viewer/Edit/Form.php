<?php

class Rockwells_LogViewer_Block_Adminhtml_Viewer_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareForm() {
        $form = new Varien_Data_Form(array(
            'id'        => 'view_form',
        ));
        
        $fieldset = $form->addFieldset('view_file', array());
        $fieldset->addField('enable-linenumbers', 'checkbox', array(
            'label'     => Mage::helper('logviewer')->__('Enable Line Numbers'),
            'name'      => 'enable-linenumbers',
            'checked'   => 'checked',
            'after_element_html'    => '&nbsp;&nbsp;&nbsp;<label for="enable-linenumbers"><i>' . Mage::helper('logviewer')->__("Uncheck this if you're experiencing bad performance while scrolling") . '</i></label>',
        ));
        $textarea = $fieldset->addField('log-contents', 'textarea', array(
            'label'     => Mage::helper('logviewer')->__('File Contents'),
            'name'      => 'log-contents',
            'readonly'  => 'readonly',
            'style'     => 'width:98%;height:52em;font-family:courier new;',
        ));
        $textarea->setCols(10000);
        
        $path = Mage::getBaseDir('log') . DS . Mage::registry('log_file');
        restore_error_handler();
        $contents = file_get_contents($path);
        if ($contents === false) {
            $err = error_get_last();
            if (!empty($err)) {
                $contents = Mage::helper('logviewer')->__('Error reading file: %s', $err['message']);
            } else {
                $contents = Mage::helper('logviewer')->__('Error reading file.');
            }
        }
        
        $form->setUseContainer(true);
        $form->setValues(array('log-contents' => $contents));
        $this->setForm($form);
        return parent::_prepareForm();
    }
    
}
