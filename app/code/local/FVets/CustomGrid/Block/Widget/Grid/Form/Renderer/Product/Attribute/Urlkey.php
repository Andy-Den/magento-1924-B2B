<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   FVets
 * @package    FVets_CustomGrid
 * @copyright  Copyright (c) 2014 Carlos Farah <carlos.farah@gmail.com>
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class FVets_CustomGrid_Block_Widget_Grid_Form_Renderer_Product_Attribute_Urlkey
    extends Mage_Adminhtml_Block_Catalog_Form_Renderer_Attribute_Urlkey
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('fvets/customgrid/widget/grid/form/renderer/product/fieldset/element.phtml');
    }
}