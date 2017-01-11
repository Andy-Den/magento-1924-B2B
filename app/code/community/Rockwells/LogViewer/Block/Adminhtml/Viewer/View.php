<?php

class Rockwells_LogViewer_Block_Adminhtml_Viewer_View extends Mage_Adminhtml_Block_Widget_Form_Container {

    /* @var $_helper Rockwells_LogViewer_Helper_Data */
    protected $_helper;

    public function __construct() {
        parent::__construct();
        $this->_helper = Mage::helper('logviewer');

        $this->_objectId = 'file';
        $this->_blockGroup = 'logviewer';
        $this->_controller = 'adminhtml_viewer';
        $this->_removeButton('save');
        $this->_removeButton('reset');
        $this->_addButton('empty', array(
            'label'     => $this->_helper->__('Empty File'),
            'class'     => 'delete',
            'onclick'   => 'deleteConfirm(\''. Mage::helper('adminhtml')->__('Are you sure you want to do this?')
                .'\', \'' . $this->getDeleteUrl() . '\')',
        ));
        
        $file = $this->getRequest()->getParam($this->_objectId);
        Mage::register('log_file', $file);
    }

    public function getDeleteUrl() {
        return $this->getUrl('*/*/empty', array($this->_objectId => $this->getRequest()->getParam($this->_objectId)));
    }

    public function getHeaderText(){
        $file = $this->getRequest()->getParam($this->_objectId);
        return sprintf($this->_helper->__('Viewing file: %s'), $file);
    }
    
    /**
     * Returns the CSS class for the header
     *
     * Usually 'icon-head' and a more precise class is returned. We return
     * only an empty string to avoid spacing on the left of the header as we
     * don't have an icon.
     *
     * @return string
     */
    public function getHeaderCssClass() {
        return '';
    }

}
