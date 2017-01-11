<?php
/**
 * Innoexts
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the InnoExts Commercial License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://innoexts.com/commercial-license-agreement
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@innoexts.com so we can send you a copy immediately.
 * 
 * @category    Innoexts
 * @package     Innoexts_Warehouse
 * @copyright   Copyright (c) 2014 Innoexts (http://www.innoexts.com)
 * @license     http://innoexts.com/commercial-license-agreement  InnoExts Commercial License
 */

/**
 * One page checkout shipping available method
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Block_Checkout_Onepage_Shipping_Method_Available 
    extends Mage_Checkout_Block_Onepage_Shipping_Method_Available
{
    /**
     * Single mode renderer
     * 
     * @var array
     */
    protected $_singleModeRenderer;
    /**
     * Multiple mode renderer
     * 
     * @var array
     */
    protected $_multipleModeRenderer;
    /**
     * Get warehouse helper
     * 
     * @return Innoexts_Warehouse_Helper_Data
     */
    protected function getWarehouseHelper()
    {
        return Mage::helper('warehouse');
    }
    /**
     * Set single mode renderer
     * 
     * @param string $block
     * @param string $template
     * 
     * @return Innoexts_Warehouse_Block_Checkout_Onepage_Shipping_Method_Available
     */
    public function setSingleModeRenderer($block, $template)
    {
        $this->_singleModeRenderer = array(
            'block'     => $block, 
            'template'  => $template, 
            'renderer'  => null
        );
        return $this;
    }
    /**
     * Get single mode renderer
     *
     * @return Mage_Core_Block_Abstract
     */
    public function getSingleModeRenderer()
    {
        if (!is_null($this->_singleModeRenderer)) {
            if (is_null($this->_singleModeRenderer['renderer'])) {
                $block = $this->_singleModeRenderer['block'];
                $template = $this->_singleModeRenderer['template'];
                $this->_singleModeRenderer['renderer'] = $this->getLayout()
                    ->createBlock($block)
                    ->setTemplate($template)
                    ->setRenderedBlock($this);
            }
            return $this->_singleModeRenderer['renderer'];
        } else {
            return null;
        }
    }
    /**
     * Get single mode html
     * 
     * @return string
     */
    public function getSingleModeHtml()
    {
        $renderer = $this->getSingleModeRenderer();
        if ($renderer) {
            return $renderer->toHtml();
        } else {
            return null;
        }
    }
    /**
     * Set multiple mode renderer
     * 
     * @param string $block
     * @param string $template
     * 
     * @return Innoexts_Warehouse_Block_Checkout_Onepage_Shipping_Method_Available
     */
    public function setMultipleModeRenderer($block, $template)
    {
        $this->_multipleModeRenderer = array(
            'block'     => $block, 
            'template'  => $template, 
            'renderer'  => null
        );
        return $this;
    }
    /**
     * Get multiple mode renderer
     *
     * @return Mage_Core_Block_Abstract
     */
    public function getMultipleModeRenderer()
    {
        if (!is_null($this->_multipleModeRenderer)) {
            if (is_null($this->_multipleModeRenderer['renderer'])) {
                $block = $this->_multipleModeRenderer['block'];
                $template = $this->_multipleModeRenderer['template'];
                $renderer = $this->getLayout()->createBlock($block)
                    ->setTemplate($template)->setRenderedBlock($this);
                $this->_multipleModeRenderer['renderer'] = $renderer;
            }
            return $this->_multipleModeRenderer['renderer'];
        } else {
            return null;
        }
    }
    /**
     * Get multiple mode html
     * 
     * @return string
     */
    public function getMultipleModeHtml()
    {
        $renderer = $this->getMultipleModeRenderer();
        if ($renderer) {
            return $renderer->toHtml();
        } else {
            return null;
        }
    }
}