<?php
/**
 * FVets_CatalogRestrictionGroup extension
 * 
 * @category       FVets
 * @package        FVets_CatalogRestrictionGroup
 * @copyright      Copyright (c) 2016
 */
/**
 * website attribute source model
 *
 * @category    FVets
 * @package     FVets_CatalogRestrictionGroup
 * @author      Douglas Ianitsky
 */
class FVets_CatalogRestrictionGroup_Model_Attribute_Source_Website extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    /**
     * Get list of all available websites
     *
     * @access public
     * @return mixed
     * @author Douglas Ianitsky
     */
    public function getAllOptions()
    {
        $cacheKey = 'CORE_WEBSITE_SELECT';
        if (Mage::app()->useCache('config') && $cache = Mage::app()->loadCache($cacheKey)) {
            $options = unserialize($cache);
        } else {
            $collection = Mage::getModel('core/website')->getResourceCollection();
            $options = $collection->toOptionArray();
            if (Mage::app()->useCache('config')) {
                Mage::app()->saveCache(serialize($options), $cacheKey, array('config'));
            }
        }
        return $options;
    }
}
