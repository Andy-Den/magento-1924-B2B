<?php
 
class Rockwells_LogViewer_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action {

    /* @var $_helper Rockwells_LogViewer_Helper_Data */
    protected $_helper;
 
    public function _construct() {
        parent::_construct();
        $this->_helper = Mage::helper('logviewer');
    }
    
    public function indexAction() {
        $this->_title($this->_helper->__('System'))->_title($this->_helper->__('Log Viewer'));
        $this->loadLayout()->_setActiveMenu('system');
        $this->renderLayout();
    }
    
    public function viewAction() {
        $this->_title($this->_helper->__('System'))->_title($this->_helper->__('Log Viewer'))->_title($this->_helper->__('View Log'));
        $this->loadLayout()->_setActiveMenu('system');
        $this->renderLayout();
    }
    
    protected function _emptyFile($file) {
        $path = Mage::getBaseDir('log') . DS . $file;
        $fh = fopen($path, 'r+');
        if ($fh === false) {
            $err = error_get_last();
            if (!empty($err)) {
                throw new Exception($this->_helper->__('Error emptying file: %s', $err['message']));
            }
            throw new Exception($this->_helper->__('Error emptying file.'));
        }
        ftruncate($fh, 0);
        fclose($fh);
        return $this;
    }
    
    public function emptyAction() {
        restore_error_handler();
        $file = $this->getRequest()->getParam('file');
        try {
            $this->_emptyFile($file);
            Mage::getSingleton('adminhtml/session')->addSuccess($this->_helper->__('The file has been emptied.'));
            $this->_redirect('*/*/');
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            $this->_redirect('*/*/view', array('file' => $file));
        }
    }
    
    public function massEmptyAction() {
        $files = $this->getRequest()->getParam('files');
        if (empty($files) || empty($files[0])) {
            Mage::getSingleton('adminhtml/session')->addNotice($this->_helper->__('No files selected.'));
        } else {
            $errors = array();
            for ($i = 0; $i < count($files); $i++) {
                $file = $files[$i];
                try {
                    $this->_emptyFile($file);
                } catch (Exception $e) {
                    unset($files[$i]);
                    $errors[] = $file;
                }
            }
            $success = sprintf($this->_helper->__('Emptied files:<br /> * %s', implode($this->_helper->__('<br /> * '), $files)));
            Mage::getSingleton('adminhtml/session')->addSuccess($success);
            if (!empty($errors)) {
                $error = sprintf($this->_helper->__('Could not empty files:<br /> * %s', implode($this->_helper->__('<br /> * '), $errors)));
                Mage::getSingleton('adminhtml/session')->addError($error);
            }
        }
        $this->_redirect('*/*/');
    }
    
//    public function loremAction() {
//        $lorem = explode(' ', 'lorem ipsum dolor sit amet consectetur adipiscing elit donec non porta quam in placerat nulla nam malesuada massa sed eros vestibulum pellentesque consequat vitae orci nunc sollicitudin enim accumsan metus congue volutpat ut malesuada viverra diam non scelerisque sed eros ligula dignissim et iaculis vel eleifend pellentesque elit aenean at enim porttitor sodales odio eu tempus augue duis bibendum ligula ut purus auctor condimentum aliquam erat volutpat fusce et blandit neque non imperdiet nulla aliquam eros enim pellentesque vitae ultricies quis tempus ac augue suspendisse elit mi elementum sit amet nisl et rutrum pharetra sapien');
//        $logs = array('exception.log', 'system.log', 'custom.log');
//        foreach ($logs as $log) {
//            for ($i = 0, $c = rand(50, 5000); $i < $c; $i++) {
//                $len = rand(2, count($lorem));
//                $text = implode(' ', array_slice($lorem, 0, $len));
//                Mage::log($text, rand(0, 7), $log);
//            }
//        }
//    }
  
}