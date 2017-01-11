<?php
/**
 * FVets_CatalogRestrictionGroup extension
 * 
 * @category       FVets
 * @package        FVets_CatalogRestrictionGroup
 * @copyright      Copyright (c) 2016
 */
/**
 * Restriction Group list block
 *
 * @category    FVets
 * @package     FVets_CatalogRestrictionGroup
 * @author Douglas Ianitsky
 */
class FVets_CatalogRestrictionGroup_Block_Catalogrestrictiongroup_List extends Mage_Core_Block_Template
{
    /**
     * initialize
     *
     * @access public
     * @author Douglas Ianitsky
     */
    public function __construct()
    {
        parent::__construct();
        $catalogrestrictiongroups = Mage::getResourceModel('fvets_catalogrestrictiongroup/catalogrestrictiongroup_collection')
                         ->addFieldToFilter('status', 1);
        $catalogrestrictiongroups->setOrder('name', 'asc');
        $this->setCatalogrestrictiongroups($catalogrestrictiongroups);
    }

    /**
     * prepare the layout
     *
     * @access protected
     * @return FVets_CatalogRestrictionGroup_Block_Catalogrestrictiongroup_List
     * @author Douglas Ianitsky
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $pager = $this->getLayout()->createBlock(
            'page/html_pager',
            'fvets_catalogrestrictiongroup.catalogrestrictiongroup.html.pager'
        )
        ->setCollection($this->getCatalogrestrictiongroups());
        $this->setChild('pager', $pager);
        $this->getCatalogrestrictiongroups()->load();
        return $this;
    }

    /**
     * get the pager html
     *
     * @access public
     * @return string
     * @author Douglas Ianitsky
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
}
