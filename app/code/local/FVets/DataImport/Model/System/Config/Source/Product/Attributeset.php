<?php
/**
 * Attribute Set Source Model
 *
 * @category   FVets
 * @package    FVets_DataImport
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software Licence 3.0 (OSL-3.0)
 * @author     Carlos Eduardo Farah <carlosfarah@gmail.com>
 */
class FVets_DataImport_Model_System_Config_Source_Product_Attributeset
{
    public static function toOptionArray()
    {
        $options = array(array('value' => '', 'label' => ''));

        $entityTypeId = Mage::getModel('eav/entity')
            ->setType('catalog_product')
            ->getTypeId();
        $sets = Mage::getModel('eav/entity_attribute_set')
            ->getCollection()
            ->setEntityTypeFilter($entityTypeId);

        foreach ($sets as $set)
        {
            $options[] = array(
                'value' => $set['attribute_set_id'],
                'label' => $set['attribute_set_name'],
            );
        }
        return $options;
    }
}
