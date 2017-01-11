<?php
	class FVets_Page_Block_Html_Header extends Mage_Page_Block_Html_Header
	{
		private $_customerCoupon = null;

		public function getUseBrandLogo()
		{
			if (empty($this->_data['use_brand_logo'])) {
				$this->_data['use_brand_logo'] = Mage::getStoreConfig('design/header/use_brand_logo');
			}
			return $this->_data['use_brand_logo'];
		}

		public function getBrandLogo($usemediaurl = false)
		{
			if (empty($this->_data['brand_logo'])) {
				$this->_data['brand_logo'] = Mage::getStoreConfig('design/header/brand_logo');
				if ($usemediaurl)
					$this->_data['brand_logo'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'logos/' . $this->_data['brand_logo'];
			}
			return $this->_data['brand_logo'];
		}

		public function getBrandLogoAlt()
		{
			if (empty($this->_data['brand_logo_alt'])) {
				$this->_data['brand_logo_alt'] = Mage::getStoreConfig('design/header/brand_logo_alt');
			}
			return $this->_data['brand_logo_alt'];
		}

		public function getVendorLogo($usemediaurl = false)
		{
			if (empty($this->_data['logo_src'])) {
				$this->_data['logo_src'] = Mage::getStoreConfig('design/header/logo_src');
				if ($usemediaurl)
					$this->_data['logo_src'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'logos/' . $this->_data['logo_src'];
			}
			return $this->_data['logo_src'];
		}

		public function customerCoupon()
		{
			if (!isset($this->_customerCoupon)) {
				$coupon_code = Mage::getModel('customer/customer')->load(Mage::getSingleton('customer/session')->getId())->getCoupon();
				if (trim($coupon_code) != '') {
					$oCoupon = Mage::getModel('salesrule/coupon')->load($coupon_code, 'code');
					$this->_customerCoupon = Mage::helper('page/rule')->getCouponRule($oCoupon, Mage::getSingleton('customer/session')->getId());
				} else {
					return false;
				}
			}
			return $this->_customerCoupon;
		}

		public function getWelcome()
		{
			if (empty($this->_data['welcome']))
			{
				if (Mage::isInstalled() && Mage::getSingleton('customer/session')->isLoggedIn())
				{
					$frontName = $this->escapeHtml(Mage::getSingleton('customer/session')->getCustomer()->getRazaoSocial());
					if (!trim($frontName))
					{
						$frontName = $this->escapeHtml(Mage::getSingleton('customer/session')->getCustomer()->getFantasyName());
						if (!trim($frontName))
						{
							$frontName = Mage::getSingleton('customer/session')->getCustomer()->getName();
						}
					}
					$this->_data['welcome'] = $this->__('Welcome, %s!', $frontName);
				} else
				{
					$this->_data['welcome'] = Mage::getStoreConfig('design/header/welcome');
				}
			}
			return $this->_data['welcome'];
		}

		public function getTopHeaderMessage()
		{
			if (strlen(Mage::getStoreConfig('design/header/header_customers_message')))
			{
				return Mage::getStoreConfig('design/header/header_customers_message');
			}
			return false;
		}
	}
?>