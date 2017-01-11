<?php
/**
 * FVets_SalesRule extension
 * 
 * @category       FVets
 * @package        FVets_SalesRule
 */
/**
 * SalesRule default helper
 *
 * @category    FVets
 * @package     FVets_SalesRule
 * @author      Júlio Reis
 * @author      Douglas Borella Ianitsky
 */
class FVets_SalesRule_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * convert array to options
     *
     * @access public
     * @param $options
     * @return array
     * @author Douglas Borella Ianitsky
     */
    public function convertOptions($options)
    {
        $converted = array();
        foreach ($options as $option) {
            if (isset($option['value']) && !is_array($option['value']) &&
                isset($option['label']) && !is_array($option['label'])) {
                $converted[$option['value']] = $option['label'];
            }
        }
        return $converted;
    }

    /**
     * Get Label Html
     *
     * @access public
     * @param $product
     * @return string
     * @author Júlio Reis
     */
	public function getLabelHtml($product)
	{
		$_block = Mage::app()->getLayout()->createBlock('fvets_salesrule/label');
		return $_block->setProduct($product)->_toHtml();
	}
}
