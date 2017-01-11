<?php
/**
 * Created by IntelliJ IDEA.
 * Date: 22.02.14
 * Time: 11:30
 */

class FVets_DataImport_Test_Config_DefaultValueTest extends EcomDev_PHPUnit_Test_Case_Config
{

    public function testIfDefaultValuesAreProperlyDefined()
    {
        $this->assertDefaultConfigValue('fvetsdataimport/product/status', 1);
        $this->assertDefaultConfigValue('fvetsdataimport/product/visibility', 4);
        $this->assertDefaultConfigValue('fvetsdataimport/product/weight', 0);

        $this->assertDefaultConfigValue('fvetsdataimport/general/import_behavior', 'replace');
        $this->assertDefaultConfigValue('fvetsdataimport/general/partial_indexing', 0);
        $this->assertDefaultConfigValue('fvetsdataimport/general/continue_after_errors', 0);
        $this->assertDefaultConfigValue('fvetsdataimport/general/error_limit', 100);
        $this->assertDefaultConfigValue('fvetsdataimport/general/support_nested_arrays', 0);
    }
}