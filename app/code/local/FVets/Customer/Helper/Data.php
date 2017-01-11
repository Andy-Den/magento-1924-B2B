<?php

/**
 * MageParts
 *
 * NOTICE OF LICENSE
 *
 * This code is copyrighted by MageParts and may not be reproduced
 * and/or redistributed without a written permission by the copyright
 * owners. If you wish to modify and/or redistribute this file please
 * contact us at info@mageparts.com for confirmation before doing
 * so. Please note that you are free to modify this file for personal
 * use only.
 *
 * If you wish to make modifications to this file we advice you to use
 * the "local" file scope in order to aviod conflicts with future updates.
 * For information regarding modifications see http://www.magentocommerce.com.
 *
 * DISCLAIMER
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE
 * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF
 * USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY
 * OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE
 * OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED
 * OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   MageParts
 * @package    MageParts_ConfirmCustomer
 * @copyright  Copyright (c) 2009 MageParts (http://www.mageparts.com/)
 * @author       MageParts Crew
 */
class FVets_Customer_Helper_Data extends Mage_Core_Helper_Abstract
{

	/**
	 * Query param name for last url visited
	 */
	const CRYPT_QUERY_PARAM_NAME = 'hash';

	/**
	 * system config options
	 */
	const REDIRECT_CMS_PAGE = 'magefm_customer/redirect/cms_page';
	const REDIRECT_USE_CUSTOM = 'magefm_customer/redirect/use_custom_url';
	const REDIRECT_CUSTOM_URL = 'magefm_customer/redirect/custom_url';

	/**
	 * Redirect URL for unapproved customers attempting to sign in
	 *
	 * @var string
	 */
	protected $_redirectURL;

	/**
	 * Store id
	 *
	 * @var int
	 */
	protected $_storeId;

	/**
	 * Get current store id
	 *
	 * @return int
	 */
	public function getStoreId()
	{
		if (is_null($this->_storeId)) {
			$this->_storeId = intval(Mage::app()->getStore()->getId());
		}

		return $this->_storeId;
	}

	/**
	 * Retrieve redirection URL for unapproved customers
	 *
	 * @return boolean
	 */
	public function getRedirectUrl()
	{
		if (is_null($this->_redirectURL)) {
			// get store id
			$storeId = $this->getStoreId();

			// check if we should use a custom URL or CMS page
			$useCustomUrl = intval(Mage::getStoreConfig(self::REDIRECT_USE_CUSTOM, $storeId)) == 1 ? true : false;

			if ($useCustomUrl) {
				$this->_redirectURL = Mage::getStoreConfig(self::REDIRECT_CUSTOM_URL, $storeId);
			} else {
				// get CMS page identifier
				$pageId = Mage::getStoreConfig(self::REDIRECT_CMS_PAGE, $storeId);

				if (!empty($pageId)) {
					// check if id includes a delimiter
					$delPos = strrpos($pageId, '|');

					// get page id by delimiter position
					if ($delPos) {
						$pageId = substr($pageId, 0, $delPos);
					}

					// retrieve redirect URL
					$this->_redirectURL = Mage::helper('cms/page')->getPageUrl($pageId);
				}
			}

		}

		return $this->_redirectURL;
	}

	public function getWebsitesByBrand($brand)
	{
		$result = array();
		try {
			$collection = Mage::app()->getStores();
			foreach ($collection as $store) {
				if (!$store->getIsActive()) {
					continue;
				}
				if (preg_match('/_' . $brand . '$/', $store->getCode())) {
					$result[$store->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK, true)] = $store->getName();
				}
			}
			return $result;
		} catch (Exception $ex) {
			echo $ex->__toString();
		}
	}

	function isLoggedUserAttendant()
	{
		$tp = Mage::getSingleton('customer/session')->getCustomer()->getTipopessoa();
		if ($tp == 'AC' || $tp == 'AA') {
			return true;
		} else {
			return false;
		}
	}

	function isLoggedUserAdminAttendant()
	{
		$tp = Mage::getSingleton('customer/session')->getCustomer()->getTipopessoa();
		if ($tp == 'AA') {
			return true;
		} else {
			return false;
		}
	}

	function checkEmailAddress($email)
	{
		// First, we check that there's one @ symbol, and that the lengths are right
		if (!preg_match("/^[^@]{1,64}@[^@]{1,255}$/", $email)) {
			// Email invalid because wrong number of characters in one section, or wrong number of @ symbols.
			return false;
		}
		// Split it into sections to make life easier
		$email_array = explode("@", $email);
		$local_array = explode(".", $email_array[0]);
		for ($i = 0; $i < sizeof($local_array); $i++) {
			if (!preg_match("/^(([A-Za-z0-9!#$%&'*+\/=?^_`{|}~-][A-Za-z0-9!#$%&'*+\/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$/", $local_array[$i])) {
				return false;
			}
		}
		if (!preg_match("/^\[?[0-9\.]+\]?$/", $email_array[1])) { // Check if domain is IP. If not, it should be valid domain name
			$domain_array = explode(".", $email_array[1]);
			if (sizeof($domain_array) < 2) {
				return false; // Not enough parts to domain
			}
			for ($i = 0; $i < sizeof($domain_array); $i++) {
				if (!preg_match("/^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$/", $domain_array[$i])) {
					return false;
				}
			}
		}

		return true;
	}

	function getAccountUrl()
	{
		return '/minhaconta';
	}

	function getHelpUrl()
	{
		return '/contato';
	}

	function getAllowedFieldsToEdit($admin = false)
	{
		if ($admin) {
			return array("id_erp", "name", "razao_social", "inscricao_estadual", "cnpj", "cpf", "address", "is_active", "allin_active", "new_email_address", "telefone", "store_view", "fvets_salesrep", "notes");
		} else {
			return array("address", "allin_active", "new_email_address", "telefone", "fvets_salesrep", "notes");
		}
	}

	public function getCustomerAvailableName()
	{
		$dataHelper = Mage::helper('onestepcheckout');
		$temp = $dataHelper->clearDash(Mage::getSingleton('customer/session')->getCustomer()->getFirstname());
		if (!empty($temp)) :
			return Mage::getSingleton('customer/session')->getCustomer()->getName();
		else :
			return Mage::getSingleton('customer/session')->getCustomer()->getRazaoSocial();
		endif;
		return $this->__('Visitor');
	}

	public function getUploadCustomerImagePath()
	{
		$path = Mage::getBaseDir("media") . DS . "fvets" . DS . "customer" . DS;

		if (!file_exists($path)) {
			mkdir($path, 0777, true);
		}

		return $path;
	}

	public function getUploadCustomerImageResizedPath()
	{
		$path = Mage::getBaseDir("media") . DS . "fvets" . DS . "customer" . DS . "resized" . DS;

		if (!file_exists($path)) {
			mkdir($path, 0777, true);
		}

		return $path;
	}

	public function getUploadCustomerImagePathUrl()
	{
		$path = Mage::getBaseUrl("media") . "fvets" . DS . "customer" . DS;
		return $path;
	}

	public function getUploadCustomerImageResizedPathUrl()
	{
		$path = Mage::getBaseUrl("media") . "fvets" . DS . "customer" . DS . "resized" . DS;
		return $path;
	}

	private function _getAllowedImageExtensions()
	{
		return array('jpg', 'jpeg', 'gif', 'png');
	}

	public function getCustomerImageUrl($customerId)
	{
		$allowedExtensions = $this->_getAllowedImageExtensions();
		$customerImageUrl = null;
		foreach ($allowedExtensions as $allowedExtension) {
			$filePath = $this->getUploadCustomerImageResizedPath() . $customerId . "." . $allowedExtension;
			if (file_exists($filePath)) {
				$customerImageUrl = $this->getUploadCustomerImageResizedPathUrl() . $customerId . "." . $allowedExtension;
				break;
			}
		}

		if (!$customerImageUrl) {
			$customerImageUrl = Mage::helper('inchoo_socialconnect/facebook')
				->getPictureUrl($customerId);
		}

		if (!$customerImageUrl) {
			$customerImageUrl = Mage::getDesign()->getSkinUrl('images/avatar.jpg');
		}

		return $customerImageUrl;
	}

	public function getImagePostActionUrl()
	{
		return Mage::getUrl('customer/account/imageposturl', null);
	}

	public function removeCustomerImage($customer)
	{
		$allowedExtensions = $this->_getAllowedImageExtensions();

		foreach ($allowedExtensions as $allowedExtension) {
			$filePath = $this->getUploadCustomerImagePath() . $customer->getId() . "." . $allowedExtension;
			if (file_exists($filePath)) {
				unlink($filePath);
			}

			$filePath = $this->getUploadCustomerImageResizedPath() . $customer->getId() . "." . $allowedExtension;
			if (file_exists($filePath)) {
				unlink($filePath);
			}
		}
	}


	function resizeImage($image, $extension, $width = 150, $height = 150, $quality = 100)
	{
		$imageUrl = $this->getUploadCustomerImagePath() . $image . "." . $extension;
		if (!is_file($imageUrl)) {
			return false;
		}

		$imageResized = $this->getUploadCustomerImageResizedPath() . $image . "." . $extension;
		if (!file_exists($imageResized) && file_exists($imageUrl)) :
			$imageObj = new Varien_Image ($imageUrl);
			$imageObj->constrainOnly(true);
			$imageObj->keepAspectRatio(true);
			$imageObj->keepFrame(false);
			$imageObj->quality($quality);
			$imageObj->resize($width, $height);
			$imageObj->save($imageResized);
		endif;

		if (file_exists($imageResized)) {
			return $this->getUploadCustomerImageResizedPathUrl() . $image . "." . $extension;
		} else {
			return null;
		}
	}

	function removeAcentos($str)
	{
		$from = "áàãâéêíóôõúüçÁÀÃÂÉÊÍÓÔÕÚÜÇ";
		$to = "aaaaeeiooouucAAAAEEIOOOUUC";
		$keys = array();
		$values = array();
		preg_match_all('/./u', $from, $keys);
		preg_match_all('/./u', $to, $values);
		$mapping = array_combine($keys[0], $values[0]);
		return strtr($str, $mapping);
	}
}