<?php
/**
 * Created by IntelliJ IDEA.
 * Date: 22.02.14
 * Time: 11:30
 */

class FVets_DataImport_Test_Config_MainTest extends EcomDev_PHPUnit_Test_Case_Config
{

    /**
     * @test
     */
    public function testIfCategoryEntityIsLoadedProperly()
    {
        $this->assertModelAlias('fvetsdataimport/import_entity_category', 'FVets_DataImport_Model_Import_Entity_Category');
    }

    /**
     * @test
     */
    public function testIfSimpleProductEntityIsLoadedProperly()
    {
        $this->assertModelAlias('fvetsdataimport/import_entity_product_type_simple', 'FVets_DataImport_Model_Import_Entity_Product_Type_Simple');
    }

    /**
     * @test
     */
    public function testIfConfigurableProductEntityIsLoadedProperly()
    {
        $this->assertModelAlias('fvetsdataimport/import_entity_product_type_configurable', 'FVets_DataImport_Model_Import_Entity_Product_Type_Configurable');
    }

    /**
     * @test
     */
    public function testIfVirtualProductEntityIsLoadedProperly()
    {
        $this->assertModelAlias('fvetsdataimport/import_entity_product_type_virtual', 'FVets_DataImport_Model_Import_Entity_Product_Type_Virtual');
    }

    /**
     * @test
     */
    public function testIfGroupedProductEntityIsLoadedProperly()
    {
        $this->assertModelAlias('fvetsdataimport/import_entity_product_type_Grouped', 'FVets_DataImport_Model_Import_Entity_Product_Type_Grouped');
    }

    /**
     * @test
     */
    public function testIfBundleProductEntityIsLoadedProperly()
    {
        $this->assertModelAlias('fvetsdataimport/import_entity_product_type_Bundle', 'FVets_DataImport_Model_Import_Entity_Product_Type_Bundle');
    }

}