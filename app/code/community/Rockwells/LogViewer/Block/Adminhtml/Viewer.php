<?php

class Rockwells_LogViewer_Block_Adminhtml_Viewer extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {
        $this->_blockGroup = 'logviewer';
        $this->_controller = 'adminhtml_viewer';
        $this->_headerText = Mage::helper('logviewer')->__('Log Files');
        parent::__construct();
    }

    protected function _prepareLayout() {
        $this->removeButton('add');
        return parent::_prepareLayout();
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
