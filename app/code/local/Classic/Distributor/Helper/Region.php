<?php
/**
 * Classic_Distributor extension
 * 
 * @category       Classic
 * @package        Classic_Distributor
 * @copyright      Copyright (c) 2015
 */
/**
 * Region helper
 *
 * @category    Classic
 * @package     Classic_Distributor
 * @author      Douglas Borella Ianitsky
 */
class Classic_Distributor_Helper_Region extends Classic_Distributor_Helper_Data
{

    /**
     * get the selected distributors for a region
     *
     * @access public
     * @param Mage_Catalog_Model_Region $region
     * @return array()
     * @author Douglas Borella Ianitsky
     */
    public function getSelectedDistributors(Mage_Directory_Model_Country $region)
    {
        if (!$region->hasSelectedDistributors()) {
            $distributors = array();
            foreach ($this->getSelectedDistributorsCollection($region) as $distributor) {
                $distributors[] = $distributor;
            }
            $region->setSelectedDistributors($distributors);
        }
        return $region->getData('selected_distributors');
    }

    /**
     * get distributor collection for a region
     *
     * @access public
     * @param Mage_Catalog_Model_Region $region
     * @return Classic_Distributor_Model_Resource_Distributor_Collection
     * @author Douglas Borella Ianitsky
     */
    public function getSelectedDistributorsCollection(Mage_Directory_Model_Country $region)
    {
        $collection = Mage::getResourceSingleton('classic_distributor/collection')
            ->addRegionFilter($region);
        return $collection;
    }
}
