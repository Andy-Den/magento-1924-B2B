<?php
/**
 * Website Source Model
 *
 * @category   FVets
 * @package    FVets_DataImport
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software Licence 3.0 (OSL-3.0)
 * @author     Carlos Eduardo Farah <carlosfarah@gmail.com>
 */
class FVets_DataImport_Model_System_Config_Source_Product_Website
{
    public static function toOptionArray()
    {
        $options = array(array('value' => '', 'label' => ''));

        $websites = Mage::getModel('core/website')
            ->getCollection();

        foreach ($websites as $website)
        {
            $options[] = array(
                'value' => $website['website_id'],
                'label' => $website['name'],
            );
        }
        return $options;
    }
}
