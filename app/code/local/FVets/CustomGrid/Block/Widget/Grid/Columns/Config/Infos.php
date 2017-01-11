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

class FVets_CustomGrid_Block_Widget_Grid_Columns_Config_Infos
    extends FVets_CustomGrid_Block_Widget_Grid_Columns_Config_Abstract
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('fvets/customgrid/widget/grid/columns/config/infos.phtml');
    }
    
    public function getGridInformations()
    {
        $block  = $this->getGridBlock();
        $model  = $this->getGridModel();
        $helper = Mage::helper('customgrid');
        
        return array(
            array('label' => $helper->__('Block Type'),      'value' => $model->getBlockType()),
            array('label' => $helper->__('Grid Type'),       'value' => $model->getTypeModelName($helper->__('none'))),
            array('label' => $helper->__('Rewriting Class'), 'value' => $model->getRewritingClassName()),
            array('label' => $helper->__('Module Name'),     'value' => $model->getModuleName()),
            array('label' => $helper->__('Controller Name'), 'value' => $model->getControllerName()),
            array('label' => $helper->__('Block ID'),        'value' => ($block ? $block->getId() : $model->getBlockId())),
        );
    }
}