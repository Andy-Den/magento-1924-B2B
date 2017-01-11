<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2014 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Button widget
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class FVets_Widgets_Block_Adminhtml_Select extends Mage_Adminhtml_Block_Widget
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getOnChange()
    {
        if (!$this->getData('on_change')) {
            return $this->getData('onchange');
        }
        return $this->getData('on_change');
    }

    protected function _toHtml()
    {
        $html = $this->getBeforeHtml().'<select '
            . ($this->getId()?' id="'.$this->getId() . '"':'')
            . ($this->getElementName()?' name="'.$this->getElementName() . '"':'')
            . ' title="'
            . Mage::helper('core')->quoteEscape($this->getTitle() ? $this->getTitle() : $this->getLabel())
            . '"'
            . ' class="scalable ' . $this->getClass() . ($this->getDisabled() ? ' disabled' : '') . '"'
            . ' onchange="'.$this->getOnChange().'"'
            . ' style="'.$this->getStyle() .'"'
            . ($this->getDisabled() ? ' disabled="disabled"' : '')
						. ($this->getMultiple() ? ' multiple="multiple"' : '')
            . '>';

		foreach ($this->getOptions() as $key => $value)
		{
			$html .= '<option value="'.$key.'"';
			$html .= ' {'.$key.'} ';
			$html .= '>'.$value.'</option>';
		}


		$html .='</select>';

        return $html;
    }
}
