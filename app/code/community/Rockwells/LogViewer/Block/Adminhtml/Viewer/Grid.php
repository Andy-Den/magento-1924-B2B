<?php

class Rockwells_LogViewer_Block_Adminhtml_Viewer_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    /* @var $_helper Rockwells_LogViewer_Helper_Data */
    protected $_helper;
    
    public function __construct() {
        parent::__construct();
        $this->_helper = Mage::helper('logviewer');
        
        $this->setId('logviewer_grid');
        $this->_filterVisibility = false;
        $this->_pagerVisibility = false;
        $this->_defaultSort = 'filesize';
        $this->_defaultDir = 'desc';
    }

    protected function _prepareCollection() {
        $sort = $this->getParam('sort', $this->_defaultSort);
        $dir = $this->getParam('dir', $this->_defaultDir);
        $files = $this->_helper->getLogFiles();
        if (isset($sort) && !empty($files) && isset($files[0][$sort])) {
            usort($files, function($a, $b) use ($sort, $dir) {
                $a = $a[$sort];
                $b = $b[$sort];
                if (is_numeric($a)) {
                    return ($dir == 'asc' ? $a - $b : $b - $a);
                } else {
                    return ($dir == 'asc' ? strcmp($a, $b) : -strcmp($a, $b));
                }
            });
        }
        $collection = new Varien_Data_Collection();
        foreach ($files as $file) {
            $item = new Varien_Object();
            $item->setIdFieldName('filename');
            $item->setFilename($file['filename']);
            $item->setFilesize($this->_helper->humanFilesize($file['filesize']));
            $item->setLines($file['lines']);
            $collection->addItem($item);
        }
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareMassaction() {
        $this->setMassactionIdField('filename');
        $this->getMassactionBlock()->setFormFieldName('files');
        $this->getMassactionBlock()->addItem('files', array(
            'label' => $this->_helper->__('Empty'),
            'url' => $this->getUrl('*/*/massEmpty'),
            'confirm' => $this->_helper->__('Are you sure you want to do this?')
        ));
        return $this;
    }

    protected function _prepareColumns() {
        $this->addColumn('filename', array(
            'header' => $this->_helper->__('File Name'),
            'index' => 'filename',
            'sortable' => true,
        ));
        $this->addColumn('filesize', array(
            'header' => $this->_helper->__('File Size'),
            'index' => 'filesize',
            'sortable' => true,
        ));
        $this->addColumn('lines', array(
            'header' => $this->_helper->__('Lines'),
            'index' => 'lines',
            'sortable' => true,
        ));
        $this->addColumn('action',
            array(
                'header'    => $this->_helper->__('Action'),
                'width'     => '50px',
                'type'      => 'action',
                'getter'    => 'getFilename',
                'actions'   => array(
                    array(
                        'caption' => $this->_helper->__('View'),
                        'url'     => array(
                            'base' => '*/*/view',
                        ),
                        'field'   => 'file'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
        ));
        return parent::_prepareColumns();
    }

    public function getRowUrl($row) {
        return $this->getUrl('*/*/view', array(
            'file' => $row->getFilename(),
        ));
    }
}
