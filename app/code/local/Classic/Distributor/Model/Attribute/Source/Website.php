<?php
/**
 * Classic_Distributor extension
 * 
 * @category       Classic
 * @package        Classic_Distributor
 * @copyright      Copyright (c) 2015
 */
/**
 * Admin source model for Website
 *
 * @category    Classic
 * @package     Classic_Distributor
 * @author      Douglas Borella Ianitsky
 */
class Classic_Distributor_Model_Attribute_Source_Website extends Mage_Eav_Model_Entity_Attribute_Source_Table
{
    /**
     * get possible values
     *
     * @access public
     * @param bool $withEmpty
     * @param bool $defaultValues
     * @return array
     * @author Douglas Borella Ianitsky
     */
    public function getAllOptions($withEmpty = true, $defaultValues = false)
    {
		$options = array();
		$websites = Mage::app()->getWebsites();

		foreach ($websites as $website)
		{
			$options[] = array(
				'label' => $website->getName(),
				'value'	=> $website->getId()
			);
		}

		if ($withEmpty) {
			array_unshift($options, array('label'=>'', 'value'=>''));
		}

		return $options;

    }

    /**
     * get options as array
     *
     * @access public
     * @param bool $withEmpty
     * @return string
     * @author Douglas Borella Ianitsky
     */
    public function getOptionsArray($withEmpty = true)
    {
        $options = array();
        foreach ($this->getAllOptions($withEmpty) as $option) {
            $options[$option['value']] = $option['label'];
        }
        return $options;
    }

    /**
     * get option text
     *
     * @access public
     * @param mixed $value
     * @return string
     * @author Douglas Borella Ianitsky
     */
    public function getOptionText($value)
    {
        $options = $this->getOptionsArray();
        if (!is_array($value)) {
            $value = explode(',', $value);
        }
        $texts = array();
        foreach ($value as $v) {
            if (isset($options[$v])) {
                $texts[] = $options[$v];
            }
        }
        return implode(', ', $texts);
    }
}
