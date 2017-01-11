<?php
class FVets_Customer_Model_Observer
{

	private $attendantAllowedRoutes = array(
		'customer'
	);

	public function customerRegisterSuccess(Varien_Event_Observer $observer)
	{
		/*$emailTemplate  = Mage::getModel('core/email_template')
			->loadDefault('notify_new_customer');
		$emailTemplate
			->setSenderName(Mage::getStoreConfig('trans_email/ident_support/name'))
			->setSenderEmail(Mage::getStoreConfig('trans_email/ident_support/email'))
			->setTemplateSubject('New customer registered');
		$result = $emailTemplate->send(Mage::getStoreConfig('trans_email/ident_general/email'),Mage::getStoreConfig('trans_email/ident_general/name'), $observer->getCustomer()->getData());*/

		$customer = $observer->customer;

		$customer = $this->_handle_file_upload($customer);

		if (!$customer->getStoreView())
		{
			$customer->setStoreView($customer->getStoreId());
			$customer->save();
		}

	}

	public function beforeCustomerLogin(Varien_Event_Observer $observer)
	{

		if (Mage::getStoreConfig(FVets_Core_Helper_Data::XML_PATH_STORE_IS_BRAND))
		{ //Redirect from brand website to supplier website
			$coreHelper = Mage::helper('fvets_core');

			$views = $coreHelper->getBrandStoreviews(Mage::app()->getStore()->getCode());
			array_unshift($views, Mage::app()->getStore());

			$customer = Mage::getModel('customer/customer');

			//Set website of the first brand view that user are registered
			foreach ($views as $view)
			{
				$customer->setWebsiteId($view->getWebsiteId());
				$customer->loadByEmail($observer->username);

				if ($customer->getId())
				{
					$allowedStoreViews = $customer->getStoreView();
					if (!in_array($view->getId(), explode(',', $allowedStoreViews))) {
						continue;
					}
					if ($customer->validatePassword($observer->password))
					{
						if (Mage::app()->getStore()->getId() != $view->getId())
						{
							$url =$view->getBaseUrl().'customer/account/loginfrombrand/'.FVets_Customer_Helper_Data::CRYPT_QUERY_PARAM_NAME.'/'.rawurlencode(Mage::helper('core')->encrypt(base64_encode(json_encode(array('username' => $observer->username, 'password' => $observer->password)))));
							$session = Mage::getSingleton('customer/session');
							if (!is_null($session->getRedirectEnabled()) && !$session->getRedirectEnabled()) {
								$session->setAfterAuthUrl($url);
								throw Mage::exception('Mage_Core', null);
							} else {
								header('Location: ' . $url);
								exit();
							}
						}
						else
						{
							break;
						}
					}
				}
			}
		}
		else
		{
			$customer = Mage::getModel('customer/customer')
				->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
			$customer->loadByEmail($observer->username);

			if ($customer->getId() && $customer->validatePassword($observer->password))
			{
				$allowed = explode(',', $customer->getStoreView());
				//If user don't can access this storeview, search for another that can
				if (!in_array(Mage::app()->getStore()->getStoreId(), $allowed))
				{
					foreach($allowed as $view)
					{
						$view = Mage::getModel('core/store')->load($view);
						if (!Mage::getStoreConfig(FVets_Core_Helper_Data::XML_PATH_STORE_IS_BRAND, $view))
						{
							#biscoito
							//Gostaria de fazer ser redirecionado por storeview e com cookies
							header('Location: '.$view->getBaseUrl().'customer/account/loginfrombrand/'.FVets_Customer_Helper_Data::CRYPT_QUERY_PARAM_NAME.'/'.urlencode(Mage::helper('core')->encrypt(base64_encode(json_encode(array('username' => $observer->username, 'password' => $observer->password))))));
							exit();
						}
					}
				}
			}
		}
	}

	//Deslogar o usuário caso ele não tiver ativo. Não importa por onde o login tiver sido feito
	public function isCustomerActive(Varien_Event_Observer $observer)
	{
		if ($observer->getCustomer()->getIsActive() !== null && !$observer->getCustomer()->getIsActive())
		{
			if (Mage::getSingleton('customer/session')->getExceptionEnabled()) {
				Mage::getSingleton('customer/session')->unsetAll();
				throw Mage::exception('Mage_Core', Mage::helper('customer')->__('Sua conta encontra-se inativa, favor entrar em contato com a distribuidora.'),
					6
				);
			} else {
				Mage::getSingleton('customer/session')->unsetAll();
				Mage::getSingleton('customer/session')->addError(Mage::helper('customer')->__('Sua conta encontra-se inativa, favor entrar em contato com a distribuidora.'));
				$response = Mage::app()->getFrontController()->getResponse();
				$response->setRedirect('/');
				$response->sendResponse();
				die();
			}
		}
	}

	public function checkAttendantActionAllowed()
	{
		$customerSession = Mage::getSingleton('customer/session');
		if ($customerSession->isLoggedIn())
		{
			if (Mage::helper('fvets_customer')->isLoggedUserAttendant())
			{
				$route = strtolower(Mage::app()->getRequest()->getRouteName());
				if (!in_array($route, $this->attendantAllowedRoutes))
				{
					Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getUrl('customer/account/changeCustomerData'));
				}
			}
		}
	}

	private function _handle_file_upload($customer)
	{
		if (isset($_FILES['prooffile']['name']) && $_FILES['prooffile']['name'] != "")
		{
			$uploader = new Varien_File_Uploader("prooffile");
			$uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png', 'pdf'));
			$uploader->setAllowRenameFiles(false);
			$uploader->setFilesDispersion(false);
			$path = Mage::helper('fvets_customer')->getUploadCustomerImagePath();
			$logoName = $customer->getId() . "." . pathinfo($_FILES['prooffile']['name'], PATHINFO_EXTENSION);

			$uploader->save($path, $logoName);
			$customer->setProoffile($logoName);

			Mage::helper('fvets_customer')->resizeImage($customer->getId(), pathinfo($_FILES['prooffile']['name'], PATHINFO_EXTENSION), 100);
		}
		return $customer;
	}
}
?>