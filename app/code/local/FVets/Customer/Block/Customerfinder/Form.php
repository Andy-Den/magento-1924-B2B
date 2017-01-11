<?php

/**
 * Created by PhpStorm.
 * User: julhets
 * Date: 8/10/16
 * Time: 9:24 PM
 */
class FVets_Customer_Block_Customerfinder_Form extends Mage_Page_Block_Html
{

  protected $regions = array();

  public function getRegions()
  {
    if (!$this->regions) {
      $resource = Mage::getSingleton('core/resource');
      $readConnection = $resource->getConnection('core_read');
      $websiteId = Mage::app()->getWebsite()->getId();

      $query = "SELECT distinct(caev.value) AS region FROM customer_entity AS e
                JOIN customer_address_entity cae on cae.parent_id = e.entity_id
                JOIN customer_address_entity_varchar caev on caev.entity_id = cae.entity_id
                WHERE e.website_id = " . $websiteId . "
                and caev.attribute_id = (select attribute_id from eav_attribute where entity_type_id = 2 and attribute_code = 'region')
                and caev.value is not null
                order by caev.value asc";
      $this->regions = $readConnection->fetchAll($query);

    }
    return $this->regions;
  }

  public function getCitySelectUrl()
  {
    return Mage::getUrl('customer/account/getCityByRegion', array('_secure' => true));;
  }

  public function getDistrictSelectUrl() {
    return Mage::getUrl('customer/account/getDistrictByCity', array('_secure' => true));;
  }

  public function findCustomersUrl() {
    return Mage::getUrl('customer/account/getCustomersByFilters', array('_secure' => true));;
  }
}