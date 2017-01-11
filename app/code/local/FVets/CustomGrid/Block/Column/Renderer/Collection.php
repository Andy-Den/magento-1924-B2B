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

class FVets_CustomGrid_Block_Column_Renderer_Collection
    extends FVets_CustomGrid_Block_Column_Renderer_Abstract
{
    protected function _getController()
    {
        return 'column_renderer_collection';
    }
    
    protected function _getFormId()
    {
        return 'column_renderer_collection_options_form';
    }
    
    public function getFormHtml()
    {
        $html = '<div class="fvetscg-collection-renderer-help">' . $this->getRenderer()->getHelp() . '</div>';
        $html .= parent::getFormHtml();
        return $html;
    }
    
    public function getRenderer()
    {
        return Mage::registry('current_collection_column_renderer');
    }
}