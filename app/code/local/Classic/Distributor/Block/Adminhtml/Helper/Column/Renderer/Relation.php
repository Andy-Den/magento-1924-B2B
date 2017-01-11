<?php
/**
 * Classic_Distributor extension
 * 
 * @category       Classic
 * @package        Classic_Distributor
 * @copyright      Copyright (c) 2015
 */
/**
 * related entities column renderer
 * @category   Classic
 * @package    Classic_Distributor
 * @author      Douglas Borella Ianitsky
 */
class Classic_Distributor_Block_Adminhtml_Helper_Column_Renderer_Relation extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Text
{
    /**
     * render the column
     *
     * @access public
     * @param Varien_Object $row
     * @return string
     * @author Douglas Borella Ianitsky
     */
    public function render(Varien_Object $row)
    {
        $base = $this->getColumn()->getBaseLink();
        if (!$base) {
            return parent::render($row);
        }
        $paramsData = $this->getColumn()->getData('params');
        $params = array();
        if (is_array($paramsData)) {
            foreach ($paramsData as $name=>$getter) {
                if (is_callable(array($row, $getter))) {
                    $params[$name] = call_user_func(array($row, $getter));
                }
            }
        }
        $staticParamsData = $this->getColumn()->getData('static');
        if (is_array($staticParamsData)) {
            foreach ($staticParamsData as $key=>$value) {
                $params[$key] = $value;
            }
        }
        return '<a href="'.$this->getUrl($base, $params).'" target="_blank">'.$this->_getValue($row).'</a>';
    }
}
