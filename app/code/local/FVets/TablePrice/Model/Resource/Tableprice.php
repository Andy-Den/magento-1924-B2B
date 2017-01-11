<?php
/**
 * FVets_TablePrice extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       FVets
 * @package        FVets_TablePrice
 * @copyright      Copyright (c) 2015
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Table Price resource model
 *
 * @category    FVets
 * @package     FVets_TablePrice
 * @author      Douglas Ianitsky
 */
class FVets_TablePrice_Model_Resource_Tableprice extends Mage_Core_Model_Resource_Db_Abstract
{

    /**
     * constructor
     *
     * @access public
     * @author Douglas Ianitsky
     */
    public function _construct()
    {
        $this->_init('fvets_tableprice/tableprice', 'entity_id');
    }
}
