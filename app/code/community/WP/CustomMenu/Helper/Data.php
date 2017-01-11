<?php

class WP_CustomMenu_Helper_Data extends Mage_Core_Helper_Abstract
{
    private $_menuData = null;

    private $_categoryCollection = null;
    public function getCategoryCollection()
    {
        if (!$this->_categoryCollection) {
            $this->_categoryCollection = Mage::getModel('catalog/category')->getCollection();
            $storeId = Mage::app()->getStore()->getId();
            /* @var $collection Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Collection */
            $this->_categoryCollection->addAttributeToSelect('name')
                ->addAttributeToSelect('is_active')
                ->setProductStoreId($storeId)
                ->setLoadProductCount(true)
                ->setStoreId($storeId);
        }
        return $this->_categoryCollection;
    }

    public function saveCurrentCategoryIdToSession()
    {
        $currentCategory = Mage::registry('current_category');
        $currentCategoryId = 0;
        if (is_object($currentCategory)) {
            $currentCategoryId = $currentCategory->getId();
        }
        Mage::getSingleton('catalog/session')
            ->setCustomMenuCurrentCategoryId($currentCategoryId);
    }

    public function initCurrentCategory()
    {
        $currentCategoryId = Mage::getSingleton('catalog/session')->getCustomMenuCurrentCategoryId();
        $currentCategory = null;
        if ($currentCategoryId) {
            $currentCategory = Mage::getModel('catalog/category')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($currentCategoryId);
        }
        Mage::unregister('current_category');
        Mage::register('current_category', $currentCategory);
    }

    public function getMenuData()
    {
        if (!is_null($this->_menuData)) return $this->_menuData;
        $blockClassName = Mage::getConfig()->getBlockClassName('custommenu/navigation');
        $block = new $blockClassName();
        $categories = $block->getStoreCategories();
        if (is_object($categories)) $categories = $block->getStoreCategories()->getNodes();
        if (Mage::getStoreConfig('custom_menu/general/ajax_load_content')) {
            $_moblieMenuAjaxUrl = str_replace('http:', '', Mage::getUrl('custommenu/ajaxmobilemenucontent'));
            $_menuAjaxUrl = str_replace('http:', '', Mage::getUrl('custommenu/ajaxmenucontent'));
        } else {
            $_moblieMenuAjaxUrl = '';
            $_menuAjaxUrl = '';
        }
        $this->_menuData = array(
            '_block'                        => $block,
            '_categories'                   => $categories,
            '_moblieMenuAjaxUrl'            => $_moblieMenuAjaxUrl,
            '_menuAjaxUrl'                  => $_menuAjaxUrl,
            '_showHomeLink'                 => Mage::getStoreConfig('custom_menu/general/show_home_link'),
            '_popupWidth'                   => Mage::getStoreConfig('custom_menu/popup/width') + 0,
            '_popupTopOffset'               => Mage::getStoreConfig('custom_menu/popup/top_offset') + 0,
            '_popupDelayBeforeDisplaying'   => Mage::getStoreConfig('custom_menu/popup/delay_displaying') + 0,
            '_popupDelayBeforeHiding'       => Mage::getStoreConfig('custom_menu/popup/delay_hiding') + 0,
            '_rtl'                          => Mage::getStoreConfig('custom_menu/general/rtl') + 0,
            '_mobileMenuEnabled'            => Mage::getStoreConfig('custom_menu/general/mobile_menu') + 0,
        );
        return $this->_menuData;
    }

	public function getClassicMenuData()
	{
		if (!is_null($this->_menuData)) return $this->_menuData;

		$blockClassName = Mage::getConfig()->getBlockClassName('custommenu/navigationclassic');
		$block = new $blockClassName();

		$categories = $block->getStoreCategories();
		if (is_object($categories)) $categories = $block->getStoreCategories()->getNodes();

		if (Mage::getStoreConfig('custom_menu/general/ajax_load_content')) {
			$_moblieMenuAjaxUrl = Mage::getUrl('custommenu/ajaxmobilemenucontent');
			$_menuAjaxUrl = Mage::getUrl('custommenu/ajaxmenucontent');
		} else {
			$_moblieMenuAjaxUrl = '';
			$_menuAjaxUrl = '';
		}

		$this->_menuData = array(
			'_block'                        => $block,
			'_categories'                   => $categories,
			'_moblieMenuAjaxUrl'            => $_moblieMenuAjaxUrl,
			'_menuAjaxUrl'                  => $_menuAjaxUrl,
			'_showHomeLink'                 => Mage::getStoreConfig('custom_menu/general/show_home_link'),
			'_popupWidth'                   => Mage::getStoreConfig('custom_menu/popup/width') + 0,
			'_popupTopOffset'               => Mage::getStoreConfig('custom_menu/popup/top_offset') + 0,
			'_popupDelayBeforeDisplaying'   => Mage::getStoreConfig('custom_menu/popup/delay_displaying') + 0,
			'_popupDelayBeforeHiding'       => Mage::getStoreConfig('custom_menu/popup/delay_hiding') + 0,
			'_rtl'                          => Mage::getStoreConfig('custom_menu/general/rtl') + 0,
			'_mobileMenuEnabled'            => Mage::getStoreConfig('custom_menu/general/mobile_menu') + 0,
		);

		return $this->_menuData;
	}

    public function getMobileMenuContent()
    {
        $menuData = Mage::helper('custommenu')->getMenuData();
        extract($menuData);
        if (!$_mobileMenuEnabled) return '';
        // --- Home Link ---
        $homeLinkUrl        = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
        $homeLinkText       = $this->__('Home');
        $homeLink           = '';
        if ($_showHomeLink) {
            $homeLink = <<<HTML
<div id="menu-mobile-0" class="menu-mobile level0">
    <div class="parentMenu">
        <a href="$homeLinkUrl">
            <span>$homeLinkText</span>
        </a>
    </div>
</div>
HTML;
        }
        // --- Menu Content ---
        $mobileMenuContent = '';
        $mobileMenuContentArray = array();
        foreach ($_categories as $_category) {
            $mobileMenuContentArray[] = $_block->drawCustomMenuMobileItem($_category);
        }
        if (count($mobileMenuContentArray)) {
            $mobileMenuContent = implode("\n", $mobileMenuContentArray);
        }
        // --- Result ---
        $menu = <<<HTML
$homeLink
$mobileMenuContent
<div class="clearBoth"></div>
HTML;
        return $menu;
    }

    public function getMenuContent()
    {
        $menuData = Mage::helper('custommenu')->getMenuData();
        extract($menuData);
        // --- Home Link ---
        $homeLinkUrl        = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
        $homeLinkText       = $this->__('Home');
        $homeLink           = '';
        if ($_showHomeLink) {
            $homeLink = <<<HTML
<div class="menu">
    <div class="parentMenu menu0">
        <a href="$homeLinkUrl">
            <span>$homeLinkText</span>
        </a>
    </div>
</div>
HTML;
    }
        // --- Menu Content ---
        $menuContent = '';
        $menuContentArray = array();

		//#biscoito
		if (count($_categories) < 15) {
			foreach ($_categories as $_category) {
				$_block->drawCustomMenuItem($_category);
			}
			$topMenuArray = $_block->getTopMenuArray();
			$topMenuContent = null;
			if (count($topMenuArray)) {
				$topMenuContent = implode("\n", $topMenuArray);
			}
			$popupMenuArray = $_block->getPopupMenuArray();
			$popupMenuContent = null;
			if (count($popupMenuArray)) {
				$popupMenuContent = implode("\n", $popupMenuArray);
			}
			// --- Result ---
			$topMenu = <<<HTML
$homeLink
$topMenuContent
<div class="clearBoth"></div>
HTML;
			return array('topMenu' => $topMenu, 'popupMenu' => $popupMenuContent);
		} else {
			foreach ($_categories as $_category) {
				$_block->drawCustomMenuItem($_category, 0, false, false);
			}
			$topMenuArray = $_block->getTopMenuArray();
			$topMenuContent = null;
			if (count($topMenuArray)) {
				$topMenuContent = implode("\n", $topMenuArray);
			}
			// --- Result ---
			$topMenu = <<<HTML
			<div class="menu-headers">
$homeLink
$topMenuContent
<div class="clearBoth"></div>
</div>

<style>

#custommenu {
	width: 500px;
}
.menu-headers div.menu {
	width: auto;
}

.menu-headers div.menu {
    box-sizing: border-box;
    padding: 0 5px;
    width: 125px;
    height: 30px;
    overflow: hidden;
}

#menu-button::after, .parentMenu::after {
	content: "";
	position: relative;
}

</style>
HTML;
			return array('topMenu' => $topMenu, 'popupMenu' => '');
		}
    }

	public function getClassicMobileMenuContent()
	{
		$menuData = Mage::helper('custommenu')->getClassicMenuData();
		extract($menuData);
		// --- Home Link ---
		$homeLinkUrl        = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
		$homeLinkText       = $this->__('Home');
		$homeLink           = '';
		if ($_showHomeLink) {
			$homeLink = <<<HTML
<div id="menu-mobile-0" class="menu-mobile level0">
    <div class="parentMenu">
        <a href="$homeLinkUrl">
            <span>$homeLinkText</span>
        </a>
    </div>
</div>
HTML;
		}
		// --- Menu Content ---
		$mobileMenuContent = '';
		$mobileMenuContentArray = array();
		foreach ($_categories as $_category) {
			$mobileMenuContentArray[] = $_block->drawCustomMenuMobileItem($_category);
		}
		if (count($mobileMenuContentArray)) {
			$mobileMenuContent = implode("\n", $mobileMenuContentArray);
		}
		// --- Result ---
		$menu = <<<HTML
$homeLink
$mobileMenuContent
<div class="clearBoth"></div>
HTML;
		return $menu;
	}

	public function getClassicMenuContent()
	{
		$menuData = Mage::helper('custommenu')->getClassicMenuData();
		extract($menuData);
		// --- Home Link ---
		$homeLinkUrl        = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
		$homeLinkText       = $this->__('Home');
		$homeLink           = '';
		if ($_showHomeLink) {
			$homeLink = <<<HTML
<div class="menu">
    <div class="parentMenu menu0">
        <a href="$homeLinkUrl">
            <span>$homeLinkText</span>
        </a>
    </div>
</div>
HTML;
		}
		// --- Menu Content ---
		$menuContent = '';
		$menuContentArray = array();
//		$menuContentArray[] = '<div class="menu-see-all-site">
//																<a class="level-top" href="/procurepormarca"><i class="fa fa-chevron-down"></i> &nbsp; <span>procure por marca</span></a>
//															</div>';
		foreach ($_categories as $_category)
		{
			$menuContentArray[] = $_block->drawCustomMenuItem($_category);
		}

		$menuContentArray[] ='<div class="clearBoth"></div>
				</div>
			</div></div>';

		if (count($menuContentArray)) {
			$menuContent = implode("\n", $menuContentArray);
		}
		// --- Result ---
		$menu = <<<HTML
$homeLink
$menuContent
<div class="clearBoth"></div>
HTML;
		return $menu;
	}
}
