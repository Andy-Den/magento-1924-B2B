<?php
/**
 * Visibility Source Model
 *
 * @category   FVets
 * @package    FVets_DataImport
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software Licence 3.0 (OSL-3.0)
 * @author     Carlos Eduardo Farah <carlosfarah@gmail.com>
 */
class FVets_DataImport_Model_System_Config_Source_Product_Visibility
{
    public static function toOptionArray()
    {
        $options = array(array('value' => '', 'label' => ''));
        foreach (Mage_Catalog_Model_Product_Visibility::getOptionArray() as $value => $label)
        {
            $options[] = array(
                'value' => $value,
                'label' => $label,
            );
        }
        return $options;
    }
}
