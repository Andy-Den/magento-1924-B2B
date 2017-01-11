<?php
require_once 'Mage/Customer/controllers/AccountController.php';

class FVets_Customer_AccountController extends Mage_Customer_AccountController
{

  /**
   * Action predispatch
   *
   * Check customer authentication for some actions
   */
  public function preDispatch()
  {
    // a brute-force protection here would be nice

    //parent::preDispatch();

    //Ignore parent class and use only the grandparent class
    call_user_func(array(get_parent_class(get_parent_class($this)), 'preDispatch'));

    if (!$this->getRequest()->isDispatched()) {
      return;
    }

    $action = $this->getRequest()->getActionName();
    $openActions = array(
        'create',
        'login',
        'logoutsuccess',
        'forgotpassword',
        'forgotpasswordpost',
        'resetpassword',
        'resetpasswordpost',
        'confirm',
        'confirmation',
        'sendresetpassword',
        'newsletter',
        'activate',
        'validateCustomer',
        'confirmorrequestconfirmregister',
        'loginboxpost',
        'changeforgotten',
        'validatecnpj',
        'validatecpf',
        'getcitybyregion',
        'getdistrictbycity',
        'getCustomersByFilters'
    );
    $pattern = '/^(' . implode('|', $openActions) . ')/i';

    if (!preg_match($pattern, $action)) {
      if (!$this->_getSession()->authenticate($this)) {
        $this->setFlag('', 'no-dispatch', true);
      }
    } else {
      $this->_getSession()->setNoReferer(true);
    }
  }

  public function loginAction()
  {
    if (!Mage::getStoreConfig('magefm_customer/pages/show_login_page', Mage::app()->getStore())) {
      header('Location: ' . Mage::app()->getStore()->getBaseUrl());
    }
    parent::loginAction();
  }


  /**
   * Create customer account action
   */
  public function createPostAction()
  {
    if ($this->getRequest()->isPost()) {
      if (Mage::getStoreConfig('magefm_customer/general/self_customer_addres_phone')) {
        $this->getRequest()->setPost('telephone', $this->getRequest()->getPost('telefone'));
      }
    }

    /** @var $session Mage_Customer_Model_Session */
    $session = $this->_getSession();
    if ($session->isLoggedIn()) {
      $this->_redirect('*/*/');
      return;
    }
    $session->setEscapeMessages(true); // prevent XSS injection in user input
    if (!$this->getRequest()->isPost()) {
      $errUrl = $this->_getUrl('*/*/create', array('_secure' => true));
      $this->_redirectError($errUrl);
      return;
    }

    $customer = $this->_getCustomer();
    $customer->setData('area_of_interest',$this->getRequest()->getPost('area_of_interest'));
    $customer->setData('origem', 'SITE');
    $customer->setData('fvets_allin_status', 'V');
    $customer->setData('status_secondary_email', 'I');

    try {
      $errors = $this->_getCustomerErrors($customer);

      if (empty($errors)) {
        $customer->cleanPasswordsValidationData();
        $customer->save();
        $this->_dispatchRegisterSuccess($customer);
        $this->_successProcessRegistration($customer);
        return;
      } else {
        $this->_addSessionError($errors);
      }
    } catch (Mage_Core_Exception $e) {
      $session->setCustomerFormData($this->getRequest()->getPost());
      if ($e->getCode() === Mage_Customer_Model_Customer::EXCEPTION_EMAIL_EXISTS) {
        $url = $this->_getUrl('customer/account/forgotpassword');
        $message = $this->__('There is already an account with this email address. If you are sure that it is your email address, <a href="%s">click here</a> to get your password and access your account.', $url);
        $session->setEscapeMessages(false);
      } else {
        $message = $e->getMessage();
      }
      $session->addError($message);
    } catch (Exception $e) {
      $session->setCustomerFormData($this->getRequest()->getPost())
          ->addException($e, $this->__('Cannot save the customer.'));
    }
    $errUrl = $this->_getUrl('*/*/create', array('_secure' => true));
    $this->_redirectError($errUrl);
  }

  /**
   *  Activate customer
   */
  public function activateAction()
  {
    $resetPasswordLinkToken = (string)$this->getRequest()->getQuery('token');
    $customerId = (int)$this->getRequest()->getQuery('id');

    //script para teste do envio de email de ativação
//		$app = $this->_getApp();
//		$store = $app->getStore();
//		$customer = $this->_getModel('customer/customer')->load($customerId);
//		$customer->sendAccountActivateEmail($store);
//		die();

    try {
      $this->_validateResetPasswordLinkToken($customerId, $resetPasswordLinkToken);
      $this->loadLayout();
      // Pass received parameters to the activate form
      $this->getLayout()->getBlock('activate.customer')
          ->setCustomerId($customerId)
          ->setResetPasswordLinkToken($resetPasswordLinkToken);

      $this->renderLayout();
    } catch (Exception $exception) {
      $this->_getSession()->addError($this->_getHelper('customer')->__('Your password reset link has expired.'));
      $this->_redirect('*/*/forgotpassword');
    }
  }

  public function activatePostAction()
  {
    $resetPasswordLinkToken = (string)$this->getRequest()->getQuery('token');
    $customerId = (int)$this->getRequest()->getQuery('id');
    $password = (string)$this->getRequest()->getPost('password');
    $passwordConfirmation = (string)$this->getRequest()->getPost('confirmation');
    $receiveContactByCompany = (string)$this->getRequest()->getPost('receive-contact-by-company');

    try {
      $this->_validateResetPasswordLinkToken($customerId, $resetPasswordLinkToken);

      //lógica para setar o usuario como logado apos a ativação da conta;
      $customer = Mage::getModel('customer/customer')->load($customerId);
      $this->_getSession()->setCustomerAsLoggedIn($customer);
      //fim

    } catch (Exception $exception) {
      $this->_getSession()->addError($this->_getHelper('customer')->__('Your password create link has expired.'));
      $this->_redirect('*/*/');
      return;
    }

    $errorMessages = array();
    if (iconv_strlen($password) <= 0) {
      array_push($errorMessages, $this->_getHelper('customer')->__('Password field cannot be empty.'));
    }
    /** @var $customer Mage_Customer_Model_Customer */
    $customer = $this->_getModel('customer/customer')->load($customerId);

    $customer->setPassword($password);
    //defino nessas duas variáveis a confirmação do password pois na atualização para a versão 1.9, ele deixou de ser setConfirmation para setPasswordConfirmation; Deixamos os dois métodos
    //pois pode ser que influencie algum outro código que utilize o formato antigo;
    $customer->setConfirmation($passwordConfirmation);
    $customer->setPasswordConfirmation($passwordConfirmation);
    //fim
    $validationErrorMessages = $customer->activateValidate();
    if (is_array($validationErrorMessages)) {
      $errorMessages = array_merge($errorMessages, $validationErrorMessages);
    }

    if (!empty($errorMessages)) {
      $this->_getSession()->setCustomerFormData($this->getRequest()->getPost());
      foreach ($errorMessages as $errorMessage) {
        $this->_getSession()->addError($errorMessage);
      }
      $this->_redirect('*/*/resetpassword', array(
          'id' => $customerId,
          'token' => $resetPasswordLinkToken
      ));
      return;
    }

    try {
      // Empty current reset password token i.e. invalidate it
      $customer->setRpToken(null);
      $customer->setRpTokenCreatedAt(null);
      $customer->setConfirmation(null);
      $customer->save();

      if (isset($receiveContactByCompany) && $receiveContactByCompany == 'on') {
        //envia email pra loja
        $this->sendCustomerRequiresContactEmail($customerId);
      }

      $this->_getSession()->addSuccess($this->_getHelper('customer')->__('Sua conta foi ativada.'));

      //envia email de notificação informando o usuário sobre a ativação da sua conta
      $app = $this->_getApp();
      $store = $app->getStore();
      try {
        $customer->sendAccountActivateEmail($store);
      } catch (Exception $ex) {
        //template de email não foi criado
      }
      //fim

      if ($customer->getEmailPasswordRecovery()) {
        $session = $this->_getSession();
        $app = $this->_getApp();
        $store = $app->getStore();
        $customer->sendNewRegisteredAccountEmail(
            'registered',
            $session->getBeforeAuthUrl(),
            $store->getId()
        );
        $customer->setEmailPasswordRecovery(false)->save();
      }

      $this->_redirect('/');
    } catch (Exception $exception) {
      $this->_getSession()->addException($exception, $this->__('Cannot create a new password.'));
      $this->_redirect('*/*/resetpassword', array(
          'id' => $customerId,
          'token' => $resetPasswordLinkToken
      ));
      return;
    }
  }

  private function sendCustomerRequiresContactEmail($customerId)
  {
    $translate = Mage::getSingleton('core/translate');
    $translate->setTranslateInline(false);
    $customer = Mage::getModel('customer/customer')->load($customerId);
    $storeId = $customer->getStore()->getId();

    Mage::getModel('core/email_template')
        ->setDesignConfig(array('area' => 'frontend', 'store' => $storeId))
        ->sendTransactional(
            'notify_company_customer_requires_contact',
            'general',
            Mage::getStoreConfig('trans_email/ident_support/email'),
            null,
            array('customer' => $customer));

    $translate->setTranslateInline(true);
    return;
  }

  /**
   * Customer login form page
   */
  public function sendResetPasswordAction()
  {

    if ($this->getRequest()->getPost()) {
      $emails = explode("\n", $this->getRequest()->getPost('emails'));
      foreach ($emails as $email) {
        if ($email) {
          $email = trim($email);
          if (!Zend_Validate::is($email, 'EmailAddress')) {
            $this->_getSession()->addError($this->__('Invalid email address %s ', $email));
            continue;
          }

          /** @var $customer Mage_Customer_Model_Customer */
          $customer = $this->_getModel('customer/customer')
              ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
              ->loadByEmail($email);

          if ($customer->getId()) {
            try {
              $newResetPasswordLinkToken = $this->_getHelper('customer')->generateResetPasswordLinkToken();
              $customer->changeResetPasswordLinkToken($newResetPasswordLinkToken);
              $customer->sendPasswordResetConfirmationEmail();
            } catch (Exception $exception) {
              $this->_getSession()->addError($exception->getMessage() . $email);
              continue;
            }
          }
          $this->_getSession()
              ->addSuccess($this->_getHelper('customer')
                  ->__('%s send.',
                      $this->_getHelper('customer')->escapeHtml($email)));
          continue;
        } else {
          $this->_getSession()->addError($this->__('Please enter your email.'));
          continue;
        }
      }
    }

    $this->loadLayout();
    $this->renderLayout();
  }

  /**
   * Reset forgotten password
   * Used to handle data recieved from reset forgotten password form
   */
  public function resetPasswordPostAction()
  {
    list($customerId, $resetPasswordLinkToken) = $this->_getRestorePasswordParameters($this->_getSession());
    $password = (string)$this->getRequest()->getPost('password');
    $passwordConfirmation = (string)$this->getRequest()->getPost('confirmation');

    try {
      $this->_validateResetPasswordLinkToken($customerId, $resetPasswordLinkToken);
    } catch (Exception $exception) {
      $this->_getSession()->addError($this->_getHelper('customer')->__('Your password reset link has expired.'));
      $this->_redirect('*/*/');
      return;
    }

    $errorMessages = array();
    if (iconv_strlen($password) <= 0) {
      array_push($errorMessages, $this->_getHelper('customer')->__('New password field cannot be empty.'));
    }
    /** @var $customer Mage_Customer_Model_Customer */
    $customer = $this->_getModel('customer/customer')->load($customerId);

    $customer->setPassword($password);
    $customer->setPasswordConfirmation($passwordConfirmation);
    $validationErrorMessages = $customer->validate();
    if (is_array($validationErrorMessages)) {
      $errorMessages = array_merge($errorMessages, $validationErrorMessages);
    }

    if (!empty($errorMessages)) {
      $this->_getSession()->setCustomerFormData($this->getRequest()->getPost());
      foreach ($errorMessages as $errorMessage) {
        $this->_getSession()->addError($errorMessage);
      }
      $this->_redirect('*/*/resetpassword', array(
          'id' => $customerId,
          'token' => $resetPasswordLinkToken
      ));
      return;
    }

    try {
      // Empty current reset password token i.e. invalidate it
      $customer->setRpToken(null);
      $customer->setRpTokenCreatedAt(null);
      $customer->setConfirmation(null);
      $customer->save();
      $this->_getSession()->addSuccess($this->_getHelper('customer')->__('Your password has been updated.'));

      if ($customer->getEmailPasswordRecovery()) {
        $session = $this->_getSession();
        $app = $this->_getApp();
        $store = $app->getStore();
        $customer->sendNewRegisteredAccountEmail(
            'registered',
            $session->getBeforeAuthUrl(),
            $store->getId()
        );
        $customer->setEmailPasswordRecovery(false)->save();
      }

      $this->_redirect('/');
    } catch (Exception $exception) {
      $this->_getSession()->addException($exception, $this->__('Cannot save a new password.'));
      $this->_redirect('*/*/resetpassword', array(
          'id' => $customerId,
          'token' => $resetPasswordLinkToken
      ));
      return;
    }
  }

  public function loginPostAction()
  {
    //setamos aqui a HOME como página de redirecionamento pós login
    $session = $this->_getSession();
    $session->setBeforeAuthUrl(Mage::getBaseUrl());
    parent::loginPostAction();

    $customerIsActive = $session->getCustomer()->getIsActive();
    if (isset($customerIsActive) && !$customerIsActive) {
      $session->getMessages(true);
      $session->addError($this->__('Sua conta encontra-se inativa, favor entrar em contato com a distribuidora.'));
      $session->logout()->renewSession();
      $this->_redirect("/");
    }
  }

  /**
   * Login box post action
   */
  public function loginBoxPostAction()
  {
//		if (!$this->_validateFormKey()) {
//			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array('urlRedirect' => Mage::getUrl('*/*/'))));
//			return;
//		}

    if ($this->_getSession()->isLoggedIn()) {
      $this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array('urlRedirect' => Mage::getUrl('*/*/'))));
      return;
    }
    $session = $this->_getSession();

    $result = array();

    if ($this->getRequest()->isPost()) {
      $login = $this->getRequest()->getPost('login');
      if (!empty($login['username']) && !empty($login['password'])) {
        try {
          $session->setRedirectEnabled(false);
          $session->setExceptionEnabled(true);
          $session->login($login['username'], $login['password']);
          $result['logined'] = 1;
          $result['user'] = $session->getCustomer()->getName();
          if ($session->getAfterAuthUrl()) {
            $result['urlRedirect'] = $session->getAfterAuthUrl();
          } else {
            $result['urlRedirect'] = Mage::getBaseUrl();
          }
        } catch (Mage_Core_Exception $e) {
          switch ($e->getCode()) {
            case Mage_Customer_Model_Customer::EXCEPTION_EMAIL_NOT_CONFIRMED:
              $result['logined'] = 0;
              $result['urlRedirect'] = $this->_getHelper('customer')->getEmailConfirmationUrl($login['username']);
              $result['error'] = $this->_getHelper('customer')->__('This account is not confirmed. <a href="%s">Click here</a> to resend confirmation email.', $value);
              break;
            case Mage_Customer_Model_Customer::EXCEPTION_INVALID_EMAIL_OR_PASSWORD:
              $result['error'] = $e->getMessage();
              break;
            /*case 5 :
              $result['logined'] = 1;
              $result['user'] = $session->getCustomer()->getName();
              $session->setRedirectEnabled(null);
              $result['urlRedirect'] = Mage::helper('confirmcustomer')->getRedirectUrl();
              break;*/
            default:
              if ($e->getMessage() != '') {
                $result['error'] = $e->getMessage();
              }
              if ($session->getAfterAuthUrl()) {
                $result['urlRedirect'] = $session->getAfterAuthUrl();
              }
          }
          $session->setUsername($login['username']);
        } catch (Exception $e) {
          // Mage::logException($e); // PA DSS violation: this exception log can disclose customer password
        }
      } else {
        $session->addError($this->__('Login and password are required.'));
      }
    }

    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
  }

  public function loginBoxSendAfterUrlPostAction()
  {
    $url = Mage::app()->getRequest()->getParam('url');
    $session = $this->_getSession();
    $session->setAfterAuthUrl($url);
  }

  protected function _loginPostRedirect()
  {
    $session = $this->_getSession();

    if (!$session->getBeforeAuthUrl() || $session->getBeforeAuthUrl() == Mage::getBaseUrl()) {
      //caso seja um novo usuário ou uma nova sessão e o beforeURL esteja setada como null, definimos a HOME como página para redirect;
      if (!$session->getBeforeAuthUrl()) {
        $session->setBeforeAuthUrl(Mage::getBaseUrl());
      }

      if ($session->isLoggedIn()) {
        if (!Mage::getStoreConfigFlag(
            Mage_Customer_Helper_Data::XML_PATH_CUSTOMER_STARTUP_REDIRECT_TO_DASHBOARD
        )
        ) {
          $referer = $this->getRequest()->getParam(Mage_Customer_Helper_Data::REFERER_QUERY_PARAM_NAME);
          if ($referer) {
            // Rebuild referer URL to handle the case when SID was changed
            $referer = $this->_getModel('core/url')
                ->getRebuiltUrl($this->_getHelper('core')->urlDecode($referer));
            if ($this->_isUrlInternal($referer)) {
              $session->setBeforeAuthUrl($referer);
            }
          }
        } else if ($session->getAfterAuthUrl()) {
          $session->setBeforeAuthUrl($session->getAfterAuthUrl(true));
        }
      } else {
        $url = Mage::helper('core/http')->getHttpReferer() ? Mage::helper('core/http')->getHttpReferer() : $this->_getHelper('customer')->getLoginUrl();
        $session->setBeforeAuthUrl($url);
      }
    } else if ($session->getBeforeAuthUrl() == $this->_getHelper('customer')->getLogoutUrl()) {
      $session->setBeforeAuthUrl($this->_getHelper('customer')->getDashboardUrl());
    } else {
      if (!$session->getAfterAuthUrl()) {
        $session->setAfterAuthUrl($session->getBeforeAuthUrl());
      }
      if ($session->isLoggedIn()) {
        $session->setBeforeAuthUrl($session->getAfterAuthUrl(true));
      }
    }
    $this->_redirectUrl($session->getBeforeAuthUrl(true));
  }

  public function logoutAction()
  {
    //setamos aqui a HOME como página de redirecionamento pós login
    $this->_getSession()->logout()
        ->renewSession()
        ->setBeforeAuthUrl(Mage::getBaseUrl());
    $this->_redirect('*/*/logoutSuccess');
  }

  public function newsletterAction()
  {
    //captura valor do parâmetro urloferta que vêm no link da news;
    $urlFoward = $this->getRequest()->getParam('urloferta');
    $session = $this->_getSession();
    if (isset($urlFoward)) {
      //se o link veio no parâmetro, setar na variável beforeAuthUrl para que após o login, seja direcionado para ela;
      $session->setBeforeAuthUrl($urlFoward);
    } else {
      //senão, segue o fluxo default;
      $this->_loginPostRedirect();
      return;
    }

    //se o usuário já está logado, redireciona pro link da news (que veio no parâmetro);
    if ($session->isLoggedIn()) {
      $this->_loginPostRedirect();
    } else {
      $this->loadLayout();
      $this->renderLayout();
    }
  }

  function loginByEmailAction()
  {
    $email = $this->getRequest()->getParams('email');
    if (isset($email['login']['username'])) {
      $email = $email['login']['username'];
    } else {
      $email = '';
    }

    $websiteId = Mage::app()->getWebsite()->getId();

    //Mage::init($websiteId, 'website');
    // ensure that we are on the correct website
    $customer = Mage::getModel('customer/customer');
    $customer->setWebsiteId($websiteId);
    // the website must be set here again!!!
    $customer->loadByEmail($email);
    $session = Mage::getSingleton('customer/session');
    if ($customer->getId()) {
      $session->setCustomerAsLoggedIn($customer);
      //return $session;
      $this->_loginPostRedirect();
    } else {
      $websiteName = Mage::app()->getWebsite()->getName();
      $this->_getSession()->addError($this->__('Você ainda não tem um cadastro na %s. Preencha o formulário abaixo para se cadastrar.', $websiteName));
      $this->_redirect('*/*/create');
    }
    //throw new Exception('Login failed');
  }

  function loginFromBrandAction()
  {
    if (!$this->getRequest()->getParam(FVets_Customer_Helper_Data::CRYPT_QUERY_PARAM_NAME)) {
      $this->_redirect('*/*/');
      return;
    }

    if ($this->_getSession()->isLoggedIn()) {
      $this->_redirect('*/*/');
      return;
    }
    $session = $this->_getSession();

    if ($this->getRequest()->getParam(FVets_Customer_Helper_Data::CRYPT_QUERY_PARAM_NAME)) {
      $login = (array)json_decode(base64_decode(Mage::helper('core')->decrypt(rawurldecode($this->getRequest()->getParam(FVets_Customer_Helper_Data::CRYPT_QUERY_PARAM_NAME)))));
      if (!empty($login['username']) && !empty($login['password'])) {
        try {
          $customer = Mage::getModel('customer/customer')
              ->setWebsiteId(Mage::app()->getStore()->getWebsiteId());
          if ($customer->authenticate($login['username'], $login['password'])) {
            $session->setCustomerAsLoggedIn($customer);
          }
          if ($session->getCustomer()->getIsJustConfirmed()) {
            $this->_welcomeCustomer($session->getCustomer(), true);
          }
        } catch (Mage_Core_Exception $e) {
          switch ($e->getCode()) {
            case Mage_Customer_Model_Customer::EXCEPTION_EMAIL_NOT_CONFIRMED:
              $value = $this->_getHelper('customer')->getEmailConfirmationUrl($login['username']);
              $message = $this->_getHelper('customer')->__('This account is not confirmed. <a href="%s">Click here</a> to resend confirmation email.', $value);
              break;
            case Mage_Customer_Model_Customer::EXCEPTION_INVALID_EMAIL_OR_PASSWORD:
              $message = $e->getMessage();
              break;
            default:
              $message = $e->getMessage();
          }
          $session->addError($message);
          $session->setUsername($login['username']);
        } catch (Exception $e) {
          // Mage::logException($e); // PA DSS violation: this exception log can disclose customer password
        }
      } else {
        $session->addError($this->__('Login and password are required.'));
      }
    }

    $this->_loginPostRedirect();
  }


  /**
   * Forgot customer password action
   */
  public function forgotPasswordPostAction()
  {
    $email = (string)$this->getRequest()->getPost('email');
    if ($email) {
      if (!Zend_Validate::is($email, 'EmailAddress')) {
        $this->_getSession()->setForgottenEmail($email);
        $this->_getSession()->addError($this->__('Invalid email address.'));
        $this->_redirect('*/*/forgotpassword');
        return;
      }

      $customer = Mage::getModel('customer/customer');

      if (Mage::getStoreConfig(FVets_Core_Helper_Data::XML_PATH_STORE_IS_BRAND)) {
        $coreHelper = Mage::helper('fvets_core');
        $views = $coreHelper->getBrandStoreviews(Mage::app()->getStore()->getCode());
        array_unshift($views, Mage::app()->getStore());

        //Set website of the first brand view that user are registered
        foreach ($views as $view) {
          $customer->setWebsiteId($view->getWebsiteId());
          $customer->loadByEmail($email);
          if ($customer->getId()) {
            $allowed = explode(',', $customer->getStoreView());
            if (in_array($view->getId(), $allowed)) {
              $customer->setStoreId($view->getId());
              break;
            }
          }

          $customer = Mage::getModel('customer/customer');
        }


      } else {
        /** @var $customer Mage_Customer_Model_Customer */
        $customer->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
            ->loadByEmail($email);
      }

      if ($customer->getId()) {
        try {
          $newResetPasswordLinkToken = $this->_getHelper('customer')->generateResetPasswordLinkToken();
          $customer->changeResetPasswordLinkToken($newResetPasswordLinkToken);
          $customer->sendPasswordResetConfirmationEmail();
        } catch (Exception $exception) {
          $this->_getSession()->addError($exception->getMessage());
          $this->_redirect('*/*/forgotpassword');
          return;
        }
      }

      $this->_getSession()
          ->addSuccess($this->_getHelper('customer')
              ->__('If there is an account associated with %s you will receive an email with a link to reset your password.',
                  $this->_getHelper('customer')->escapeHtml($email)));
      $this->_redirect('/');
      return;
    } else {
      $this->_getSession()->addError($this->__('Please enter your email.'));
      $this->_redirect('*/*/forgotpassword');
      return;
    }
  }

  private function requestNewPassByEmail($emailAux)
  {
    $email = (string)$this->getRequest()->getPost('email');
    if ($emailAux) {
      $email = $emailAux;
    }
    if ($email) {
      if (!Zend_Validate::is($email, 'EmailAddress')) {
        $this->_getSession()->setForgottenEmail($email);
        $this->_getSession()->addError($this->__('Invalid email address.'));
        $this->_redirect('*/*/forgotpassword');
        return;
      }

      $customer = Mage::getModel('customer/customer');

      if (Mage::getStoreConfig(FVets_Core_Helper_Data::XML_PATH_STORE_IS_BRAND)) {
        $coreHelper = Mage::helper('fvets_core');
        $views = $coreHelper->getBrandStoreviews(Mage::app()->getStore()->getCode());
        array_unshift($views, Mage::app()->getStore());

        //Set website of the first brand view that user are registered
        foreach ($views as $view) {
          $customer->setWebsiteId($view->getWebsiteId());
          $customer->loadByEmail($email);
          if ($customer->getId()) {
            $allowed = explode(',', $customer->getStoreView());
            if (in_array($view->getId(), $allowed)) {
              $customer->setStoreId($view->getId());
              break;
            }
          }

          $customer = Mage::getModel('customer/customer');
        }


      } else {
        /** @var $customer Mage_Customer_Model_Customer */
        $customer->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
            ->loadByEmail($email);
      }

      if ($customer->getId()) {
        try {
          $newResetPasswordLinkToken = $this->_getHelper('customer')->generateResetPasswordLinkToken();
          $customer->changeResetPasswordLinkToken($newResetPasswordLinkToken);
          $customer->sendPasswordResetConfirmationEmail();
          if ($emailAux) {
            return;
          }
        } catch (Exception $exception) {
          $this->_getSession()->addError($exception->getMessage());
          $this->_redirect('*/*/forgotpassword');
          return;
        }
      }

      $this->_getSession()
          ->addSuccess($this->_getHelper('customer')
              ->__('If there is an account associated with %s you will receive an email with a link to reset your password.',
                  $this->_getHelper('customer')->escapeHtml($email)));
      $this->_redirect('/');
      return;
    }
    return;
  }

  public function resetPasswordWithoutCurrentPassPostAction()
  {
    $customerId = (int)$this->getRequest()->getQuery('id');
    $password = (string)$this->getRequest()->getPost('password');
    $passwordConfirmation = (string)$this->getRequest()->getPost('confirmation');

    $helper = Mage::helper('customer');

    $errorMessages = array();
    if (iconv_strlen($password) <= 0) {
      array_push($errorMessages, $helper->__('New password field cannot be empty.'));
    }
    /** @var $customer Mage_Customer_Model_Customer */
    $customer = Mage::getModel('customer/customer')->load($customerId);

    $errors = array();
    $errorMessages = array();

    if (!Zend_Validate::is($password, 'NotEmpty')) {
      $errors[] = $helper->__('The password cannot be empty.');
    }
    if (strlen($password) && !Zend_Validate::is($password, 'StringLength', array(6))) {
      $errors[] = $helper->__('The minimum password length is %s', 6);
    }
    if ($password != $passwordConfirmation) {
      $errors[] = $helper->__('Please make sure your passwords match.');
    }

    $customer->setPassword($password);
    $customer->setPasswordConfirmation($passwordConfirmation);
    //$validationErrorMessages = $customer->validate();
    if (is_array($errors)) {
      $errorMessages = array_merge($errorMessages, $errors);
    }

    if (!empty($errorMessages)) {
      $this->_getSession()->setCustomerFormData($this->getRequest()->getPost());
      foreach ($errorMessages as $errorMessage) {
        $this->_getSession()->addError($errorMessage);
      }
      $this->_redirect('*/*/edit');
      return;
    }

    try {
      // Empty current reset password token i.e. invalidate it
      $customer->setConfirmation(null);
      $customer->save();
      $this->_getSession()->addSuccess($helper->__('Your password has been updated.'));

      if ($customer->getEmailPasswordRecovery()) {
        $session = $this->_getSession();
        $app = getMage::app();
        $store = $app->getStore();
        $customer->sendNewRegisteredAccountEmail(
            'registered',
            $session->getBeforeAuthUrl(),
            $store->getId()
        );
        $customer->setEmailPasswordRecovery(false)->save();
      }

      $this->_redirect('*/*/');
    } catch (Exception $exception) {
      $this->_getSession()->addException($exception, $helper->__('Cannot save a new password.'));
      $this->_redirect('*/*/edit');
      return;
    }
  }

  public function validateCustomerAction()
  {
    $type = $this->getRequest()->getParam('type');
    if ($type && $type == 'cnpj') {
      $cnpj = $this->getRequest()->getParam('value');

      $resource = Mage::getSingleton('core/resource');
      $readConnection = $resource->getConnection('core_read');

      $query = "SELECT `e`.*, `at_cnpj`.`value` AS `cnpj` FROM `customer_entity` AS `e`
 INNER JOIN `customer_entity_varchar` AS `at_cnpj` ON (`at_cnpj`.`entity_id` = `e`.`entity_id`) AND (`at_cnpj`.`attribute_id` = '136') WHERE (`e`.`entity_type_id` = '1') AND (`e`.`website_id` = '" . Mage::app()->getWebsite()->getId() . "') AND (replace(replace(replace(at_cnpj.value, '-', ''), '.', ''), '/', '') = '" . $cnpj . "')";

      $collection = $readConnection->fetchAll($query);

      if (count($collection) > 0) {
        echo 'valid';
      } else {
        echo 'invalid';
      }
      return;
    } elseif ($type && $type == 'cpf') {
      $cpf = $this->getRequest()->getParam('value');

      $resource = Mage::getSingleton('core/resource');
      $readConnection = $resource->getConnection('core_read');

      $query = "SELECT `e`.*, `at_cpf`.`value` AS `cpf` FROM `customer_entity` AS `e`
 INNER JOIN `customer_entity_varchar` AS `at_cpf` ON (`at_cpf`.`entity_id` = `e`.`entity_id`) AND (`at_cpf`.`attribute_id` = '134') WHERE (`e`.`entity_type_id` = '1') AND (`e`.`website_id` = '" . Mage::app()->getWebsite()->getId() . "') AND (replace(replace(at_cpf.value, '-', ''), '.', '') = '" . $cpf . "')";

      $collection = $readConnection->fetchAll($query);

      if (count($collection) > 0) {
        echo 'valid';
      } else {
        echo 'invalid';
      }
      return;
    } elseif ($type && $type == 'email') {
      $email = $this->getRequest()->getParam('value');

      $collection = Mage::getModel('customer/customer')->getCollection()
          ->addAttributeToFilter('website_id', Mage::app()->getWebsite()->getId())
          ->addAttributeToFilter('email', $email);

      if (count($collection) > 0) {
        echo 'valid';
      } else {
        echo 'invalid';
      }
      return;
    } elseif ($type && $type == 'codigo') {
      $codigo = $this->getRequest()->getParam('value');

      $collection = Mage::getModel('customer/customer')->getCollection()
          ->addAttributeToFilter('website_id', Mage::app()->getWebsite()->getId())
          ->addAttributeToFilter('id_erp', $codigo);

      if (count($collection) > 0) {
        echo 'valid';
      } else {
        echo 'invalid';
      }
      return;
    } else {
      echo 'error';
      return;
    }
  }

  public function confirmOrRequestConfirmRegisterAction()
  {
    $registerType = $this->getRequest()->getParam('registerType');

    $cnpj = $this->getRequest()->getParam('cnpj-check');
    $cpf = $this->getRequest()->getParam('cpf-check');
    $email = $this->getRequest()->getParam('email-check');
    $codigo = $this->getRequest()->getParam('codigo-check');

    $storePhone = Mage::getStoreConfig('general/store_information/phone');
    $storeEmail = Mage::getStoreConfig('trans_email/ident_general/email');

    $validateCount = 0;
    if ($cnpj || $cpf) {
      $validateCount++;
    }
    if ($email) {
      $validateCount++;
    }
    if ($codigo) {
      $validateCount++;
    }

    if ($validateCount < 2) {
      echo "<p>Todos os campos devem ser preenchidos!</p>
			<br><br>
							<p>Está com dúvidas? Por favor entre em contato conosco.</p>
              <p><strong>$storePhone</strong></p>
              <p><strong>$storeEmail</strong></p>";
      return;
    }

    if ($registerType == 'confirmregister') {
      if ($cnpj) {
        $email = $this->getEmailByCnpjOrCpf('cnpj', str_replace('/', '', str_replace('.', '', str_replace('-', '', $cnpj))));
      } elseif ($cpf) {
        $email = $this->getEmailByCnpjOrCpf('cpf', str_replace('.', '', str_replace('-', '', $cpf)));
      }

      //enviar email
      $this->sendConfirmRegisterEmail($email);
      //...

      $html = '<p>Um e-mail com as informações foi enviado para a distribuidora. Por favor aguarde nosso contato ou entre em contato conosco:<br></p>
                 <p><strong>' . $storePhone . '</strong></p>
                 <p><strong>' . $storeEmail . '</strong></p>';

      echo $html;
      return;
    } else {
      $this->sendRequestRegisterEmail($email, $cnpj, $cpf, $codigo);
      $html = '<p>A solicitaçao de cadastro foi enviada para nosso sistema com sucesso, <strong>entraremos em contato dentro de 1 dia útil.</strong></p><br>
<p>Ou entre em contato com a distribuidora através de um dos canais <strong>(' . Mage::getStoreConfig('general/store_information/phone') . ' ou ' . Mage::getStoreConfig('trans_email/ident_general/email') . ')</strong>.</p>
<br>
<p>Em breve você poderá comprar da <strong>' . Mage::getStoreConfig('general/store_information/name') . '</strong> a qualquer hora e lugar!</p>';
      echo $html;
      return;
    }
  }

  public function getEmailByCnpjOrCpf($type, $value)
  {
    $resource = Mage::getSingleton('core/resource');
    $readConnection = $resource->getConnection('core_read');
    if ($type == 'cpf') {
      $query = "SELECT `e`.email AS `cpf` FROM `customer_entity` AS `e`
 INNER JOIN `customer_entity_varchar` AS `at_cpf` ON (`at_cpf`.`entity_id` = `e`.`entity_id`) AND (`at_cpf`.`attribute_id` = '134') WHERE (`e`.`entity_type_id` = '1') AND (`e`.`website_id` = '" . Mage::app()->getWebsite()->getId() . "') AND (replace(replace(at_cpf.value, '-', ''), '.', '') = '" . $value . "')";
      $email = $readConnection->fetchOne($query);
    } else {
      $query = "SELECT `e`.email FROM `customer_entity` AS `e`
 INNER JOIN `customer_entity_varchar` AS `at_cnpj` ON (`at_cnpj`.`entity_id` = `e`.`entity_id`) AND (`at_cnpj`.`attribute_id` = '136') WHERE (`e`.`entity_type_id` = '1') AND (`e`.`website_id` = '" . Mage::app()->getWebsite()->getId() . "') AND (replace(replace(replace(at_cnpj.value, '-', ''), '.', ''), '/', '') = '" . $value . "')";

      $email = $readConnection->fetchOne($query);
    }
    return $email;
  }

  public function getIdByCnpjOrCpfOrIdErpRsEmailSe($type, $value)
  {
    $resource = Mage::getSingleton('core/resource');
    $readConnection = $resource->getConnection('core_read');
    $email = null;

    if ($type == 'email') {
      $query = "SELECT `e`.entity_id AS `cpf` FROM `customer_entity` AS `e`
 WHERE (`e`.`entity_type_id` = '1') AND (`e`.`website_id` = '" . Mage::app()->getWebsite()->getId() . "') AND e.mp_cc_is_approved = " . MageParts_ConfirmCustomer_Helper_Data::STATE_APPROVED . " AND `e`.`email` = '" . $value . "'";
      $email = $readConnection->fetchOne($query);
    } elseif ($type == 'second_email') {
      $query = "SELECT `e`.entity_id AS `cpf` FROM `customer_entity` AS `e`
 INNER JOIN `customer_entity_varchar` AS `at_cpf` ON (`at_cpf`.`entity_id` = `e`.`entity_id`) AND (`at_cpf`.`attribute_id` = '193')
 WHERE (`e`.`entity_type_id` = '1') AND (`e`.`website_id` = '" . Mage::app()->getWebsite()->getId() . "') AND e.mp_cc_is_approved = " . MageParts_ConfirmCustomer_Helper_Data::STATE_APPROVED . " AND at_cpf.value = '" . $value . "'";
      $email = $readConnection->fetchOne($query);
    } elseif ($type == 'cpf') {
      $value = preg_replace('/[^A-Za-z0-9\-\']/', '', $value);
      $query = "SELECT `e`.entity_id AS `cpf` FROM `customer_entity` AS `e`
 INNER JOIN `customer_entity_varchar` AS `at_cpf` ON (`at_cpf`.`entity_id` = `e`.`entity_id`) AND (`at_cpf`.`attribute_id` = '134')
 WHERE (`e`.`entity_type_id` = '1') AND (`e`.`website_id` = '" . Mage::app()->getWebsite()->getId() . "') AND e.mp_cc_is_approved = " . MageParts_ConfirmCustomer_Helper_Data::STATE_APPROVED . " AND (replace(replace(at_cpf.value, '-', ''), '.', '') = '" . $value . "')";
      $email = $readConnection->fetchOne($query);
    } elseif ($type == 'cnpj') {
      $value = preg_replace('/[^A-Za-z0-9\-\']/', '', $value);
      $query = "SELECT `e`.entity_id FROM `customer_entity` AS `e`
 INNER JOIN `customer_entity_varchar` AS `at_cnpj` ON (`at_cnpj`.`entity_id` = `e`.`entity_id`) AND (`at_cnpj`.`attribute_id` = '136')
 WHERE (`e`.`entity_type_id` = '1') AND (`e`.`website_id` = '" . Mage::app()->getWebsite()->getId() . "') AND e.mp_cc_is_approved = " . MageParts_ConfirmCustomer_Helper_Data::STATE_APPROVED . " AND (replace(replace(replace(at_cnpj.value, '-', ''), '.', ''), '/', '') = '" . $value . "')";

      $email = $readConnection->fetchOne($query);
    } elseif ($type == 'id_erp') {
      $query = "SELECT `e`.entity_id FROM `customer_entity` AS `e`
 INNER JOIN `customer_entity_varchar` AS `at_id_erp` ON (`at_id_erp`.`entity_id` = `e`.`entity_id`) AND (`at_id_erp`.`attribute_id` = '183')
 WHERE (`e`.`entity_type_id` = '1') AND (`e`.`website_id` = '" . Mage::app()->getWebsite()->getId() . "') AND e.mp_cc_is_approved = " . MageParts_ConfirmCustomer_Helper_Data::STATE_APPROVED . " AND at_id_erp.value = '" . $value . "'";

      $email = $readConnection->fetchOne($query);
    } elseif ($type == 'razao_social') {
      $query = "SELECT `e`.entity_id FROM `customer_entity` AS `e`
 INNER JOIN `customer_entity_varchar` AS `at_id_erp` ON (`at_id_erp`.`entity_id` = `e`.`entity_id`) AND (`at_id_erp`.`attribute_id` = '137')
 WHERE (`e`.`entity_type_id` = '1') AND (`e`.`website_id` = '" . Mage::app()->getWebsite()->getId() . "') AND e.mp_cc_is_approved = " . MageParts_ConfirmCustomer_Helper_Data::STATE_APPROVED . " AND at_id_erp.value like '%" . $value . "%'";

      $email = $readConnection->fetchOne($query);
    }
    return $email;
  }

  private function sendConfirmRegisterEmail($email)
  {
    $this->requestNewPassByEmail($email);
    return;
  }

  private function sendRequestRegisterEmail($email, $cnpj, $cpf, $codigo)
  {
    $translate = Mage::getSingleton('core/translate');
    $translate->setTranslateInline(false);
    $storeId = Mage::app()->getStore()->getStoreId();

    $data = new Varien_Object();
    $data->setEmail($email);
    $data->setCnpj($cnpj);
    $data->setCpf($cpf);
    $data->setCodigo($codigo);

    try {

      Mage::getModel('core/email_template')
          ->setDesignConfig(array('area' => 'frontend', 'store' => $storeId))
          ->sendTransactional(
              Mage::getStoreConfig('validate_customer/general/template_customer_requires_register'),
              'general',
              Mage::getStoreConfig('validate_customer/general/repository_customer_requires_register'),
              null,
              array('data' => $data));

      $translate->setTranslateInline(true);
    } catch (Exception $ex) {
      //template não criado
      $ex->toString();
    }
    return;
  }

  public function changeCustomerDataAction()
  {
    $this->loadLayout();

    $helper = $this->_getHelper('fvets_customer');
    if (!$helper->isLoggedUserAttendant() && !$helper->isLoggedUserAdminAttendant()) {
      Mage::getSingleton('core/session')->addError('Você não têm permissão para acessar essa área.');
      $this->_redirect('/');
      return;
    }

    $paramIdCustomerToChange = $this->getRequest()->getParam('customerIdToChange');
    if ($paramIdCustomerToChange) {
      $this->getLayout()->getBlock('customer_account_changecustomerdata')->setCustomer(Mage::getModel('customer/customer')->load($paramIdCustomerToChange));
    }
    $this->renderLayout();
  }

  public function changeCustomerDataPostAction()
  {
    //guarda os dados do formulario enviado na sessao para caso haja erro, nao deixar o usuario ter que digitar tudo denovo
    $session = $this->_getSession();
    $session->setEscapeMessages(true);
    $session->setCustomerFormData($this->getRequest()->getPost());

    $customerId = $this->getRequest()->getParam('customer_id');
    if ($customerId) {
      $isUserAdmin = Mage::helper('fvets_customer')->isLoggedUserAdminAttendant();
      $allowedFieldsToEdit = Mage::helper('fvets_customer')->getAllowedFieldsToEdit($isUserAdmin);

      $customer = Mage::getModel('customer/customer')->load($customerId);
      $address = '';

      foreach ($allowedFieldsToEdit as $field) {
        if ($field == 'name') {
          $nomeCompletoArray = explode(' ', $this->getRequest()->getParam($field));
          $firstName = '';
          $aux = '';
          foreach ($nomeCompletoArray as $key => $nome) {
            if ($key == 0) {
              $firstName = $nome;
              continue;
            } else {
              $aux .= $nome . ' ';
            }
          }
          $lastName = rtrim($aux, ' ');
          $customer->setFirstname($firstName);
          $customer->setLastname($lastName);
        }
        if ($field == 'allin_active') {
          $customer->setFvetsAllinStatus($this->getRequest()->getParam($field) == null ? 'I' : 'V');
          continue;
        }
        if ($field == 'is_active') {
          $customer->setIsActive($this->getRequest()->getParam($field)[0]);
        }
        if ($field == 'store_view') {
          $customer->setStoreView(implode(',', $this->getRequest()->getParam($field)));
        }
        if ($field == 'fvets_salesrep') {
          $customer->setFvetsSalesrep($this->getRequest()->getParam($field));
        }
        if ($field == 'new_email_address') {
          continue;
        }
        if ($field == 'address') {
          $address .= $this->getRequest()->getParam('street') . "\n";
          $address .= $this->getRequest()->getParam('numero') . "\n\n";
          $address .= $this->getRequest()->getParam('complemento');

          $customerAddresses = $customer->getAddresses();
          if ($customerAddresses && count($customerAddresses)) {
            $customerAddress = reset($customerAddresses);

            $customerAddress->setStreet($address);
            $customerAddress->setBairro($this->getRequest()->getParam('bairro'));
            $customerAddress->setCity($this->getRequest()->getParam('city'));
            $customerAddress->setRegionId($this->getRequest()->getParam('region_id'));
            $customerAddress->setPostcode($this->getRequest()->getParam('postcode'));
            $customerAddress->setTelephone($this->getRequest()->getParam('telefone'));

            $customerAddress->save();
          }
          continue;
        }
        $customer->setData($field, $this->getRequest()->getParam($field));
      }

      $novoEmail = $this->getRequest()->getParam('new_email_address');
      if ($novoEmail) {
        if ($this->_getHelper('fvets_customer')->checkEmailAddress($novoEmail)) {
          $oldEmail = $customer->getEmail();
          $customer->setEmail($novoEmail);
          $customer->setSecondEmail($oldEmail);
        } else {
          Mage::getSingleton('core/session')->addError('Novo email deve possuir um formato válido.');
          $this->_redirect('*/*/changecustomerdata', array('customerIdToChange' => $customerId));
          return;
        }
      }

      try {
        $customer->save();

        //se foi informado um novo email, seleciona o antigo email para ser flageado como "inválido"para o all lin;
        if ($novoEmail) {
          $allinHelper = Mage::helper('fvets_allin');
          $allinHelper->saveTrash($oldEmail, $customer->getWebsiteId());
        }
        //limpando os dados do form que estavam na sessão
        $session->setCustomerFormData(null);
        Mage::getSingleton('core/session')->addSuccess('Cliente alterado com sucesso.');

        try {
          //registrando a alteração na auditoria
          $currentLoggedUser = Mage::getSingleton('customer/session')->getCustomer();
          Mage::helper('amaudit')->addFrontCustomerAuditToAmastyLog($currentLoggedUser->getEmail(), $customer->getId(), $customer->getOrigData(), $customer->getData(), Mage::app()->getRequest()->getActionName(), $customer->getResourceName());
          //fim
        } catch (Exception $ex) {
          Mage::logException($ex);
          Mage::log($ex->getMessage());
        }

        $this->_redirect('*/*/changecustomerdata', array('customerIdToChange' => $customerId));
        return;
      } catch (Exception $ex) {
        Mage::getSingleton('core/session')->addError('Ocorreu um problema ao tentar alterar o usuário. (' . $ex->__toString() . ')');
        $this->_redirect('*/*/changecustomerdata', array('customerIdToChange' => $customerId));
        return;
      }

    } else {
      Mage::getSingleton('core/session')->addError('Você precisa pesquisar um cliente primeito.');
      $this->_redirect('*/*/changecustomerdata', array('customerIdToChange' => $customerId));
      return;
    }
  }

  public function searchCustomerAction()
  {
    $minDigitsValidation = 1;

    $param = $this->getRequest()->getParam('search-customer');
    if ($param && strlen($param) >= $minDigitsValidation) {

      $customerId = $this->loadUserBySearchParam($param);

      $this->_redirect('*/*/changecustomerdata', array('customerIdToChange' => $customerId));
      return;
    } else {
      Mage::getSingleton('core/session')->addError('Você deve informar pelo menos ' . $minDigitsValidation . ' dígitos para realizar a pesquisa.');
      $this->_redirect('*/*/changecustomerdata');
      return;
    }
  }

  private function loadUserBySearchParam($param)
  {
//		$website = Mage::app()->getWebsite();
//		$customer = Mage::getModel('customer/customer');
//		$customer->setWebsiteId($website->getId());
//		$customer->loadByEmail($param);
//		if ($customer->getId()) {
//			return $customer->getId();
//		}

    $customerId = $this->getIdByCnpjOrCpfOrIdErpRsEmailSe('email', $param);
    if ($customerId) {
      return $customerId;
    }

    $customerId = $this->getIdByCnpjOrCpfOrIdErpRsEmailSe('second_email', $param);
    if ($customerId) {
      return $customerId;
    }

    $customerId = $this->getIdByCnpjOrCpfOrIdErpRsEmailSe('cnpj', str_replace('/', '', str_replace('.', '', str_replace('-', '', $param))));
    if ($customerId) {
      return $customerId;
    }

    $customerId = $this->getIdByCnpjOrCpfOrIdErpRsEmailSe('cpf', str_replace('/', '', str_replace('.', '', str_replace('-', '', $param))));
    if ($customerId) {
      return $customerId;
    }

    $customerId = $this->getIdByCnpjOrCpfOrIdErpRsEmailSe('id_erp', $param);
    if ($customerId) {
      return $customerId;
    }

    $customerId = $this->getIdByCnpjOrCpfOrIdErpRsEmailSe('razao_social', $param);
    if ($customerId) {
      return $customerId;
    }

    Mage::getSingleton('core/session')->addError('Cliente não encontrado.');
    $this->_redirect('*/*/changecustomerdata');
    return;
  }

  public function imagePostUrlAction()
  {
    if (isset($_FILES['prooffile']['name']) && $_FILES['prooffile']['name'] != "") {
      try {
        $customerId = Mage::app()->getRequest()->getParam('customer_id');
        $customer = Mage::getModel('customer/customer')->load($customerId);

        Mage::helper('fvets_customer')->removeCustomerImage($customer);

        $uploader = new Varien_File_Uploader("prooffile");
        $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png', 'pdf'));
        $uploader->setAllowRenameFiles(false);
        $uploader->setFilesDispersion(false);
        $path = Mage::helper('fvets_customer')->getUploadCustomerImagePath();
        $logoName = $customer->getId() . "." . pathinfo($_FILES['prooffile']['name'], PATHINFO_EXTENSION);

        $uploader->save($path, $logoName);
        $customer->setProoffile($logoName);
        $customer->save();

        Mage::helper('fvets_customer')->resizeImage($customer->getId(), pathinfo($_FILES['prooffile']['name'], PATHINFO_EXTENSION));

        Mage::getSingleton('core/session')->addSuccess('Imagem alterada com sucesso.');
      } catch (Exception $ex) {
        Mage::getSingleton('core/session')->addError('Ocorreram problemas ao alterar a imagem.');
      }
    }
    $this->_redirect('customer/account/index');
  }

  public function validateCnpjAction()
  {
    try {
      $cnpj = Mage::app()->getRequest()->getParam('cnpj');

      //remove formatação
      $cnpj = (str_replace('.', '', str_replace('/', '', str_replace('-', '', $cnpj))));

      if (!$cnpj) {
        return 'not allowed';
      }

      $collection = Mage::getModel('customer/customer')->getCollection()
          ->addFieldToFilter('website_id', Mage::app()->getWebsite()->getId())
          ->addAttributeToFilter('cnpj', array('neq' => ''));
      $select = $collection->getSelect()
          ->where('replace(replace(replace(value, "-", ""), "/", ""), ".","") = ' . $cnpj);

      $customer = $collection->getFirstItem();

      if ($customer && $customer->getId()) {
        echo "not allowed";

      } else {
        echo "allowed";
      }
    } catch (Exception $ex) {
      exit;
    }
    exit;
  }

  public function validateCpfAction()
  {
    try {
      $cpf = Mage::app()->getRequest()->getParam('cpf');

      //remove formatação
      $cpf = (str_replace('.', '', str_replace('/', '', str_replace('-', '', $cpf))));

      if (!$cpf) {
        return 'not allowed';
      }

      $collection = Mage::getModel('customer/customer')->getCollection()
          ->addFieldToFilter('website_id', Mage::app()->getWebsite()->getId())
          ->addAttributeToFilter('cpf', array('neq' => ''))
          ->addAttributeToFilter('tipopessoa', array('eq' => 'PF'));
      $select = $collection->getSelect()
          ->where('replace(replace(at_cpf.value, "-", ""), ".","") = ' . $cpf);

      $customer = $collection->getFirstItem();

      if ($customer && $customer->getId()) {
        echo "not allowed";

      } else {
        echo "allowed";
      }
    } catch (Exception $ex) {
      exit;
    }
    exit;
  }

  public function getCityByRegionAction()
  {
    $region = Mage::app()->getRequest()->getParam('region');

    try {
      $resource = Mage::getSingleton('core/resource');
      $readConnection = $resource->getConnection('core_read');
      $websiteId = Mage::app()->getWebsite()->getId();

      $query = "SELECT distinct(caev2.value) AS city FROM customer_entity AS e
                JOIN customer_address_entity cae on cae.parent_id = e.entity_id
                JOIN customer_address_entity_varchar caev on caev.entity_id = cae.entity_id
                JOIN customer_address_entity_varchar caev2 on caev2.entity_id = cae.entity_id
                WHERE e.website_id = " . $websiteId . "
                and caev.attribute_id = (select attribute_id from eav_attribute where entity_type_id = 2 and attribute_code = 'region')
                and caev2.attribute_id = (select attribute_id from eav_attribute where entity_type_id = 2 and attribute_code = 'city')
                and caev2.value is not null
                and caev.value like '%" . $region . "%'
                order by caev2.value asc";
      $cities = $readConnection->fetchAll($query);

      $html = "<option value='0'>CIDADE</option>";

      foreach ($cities as $city) {
        $html .= "<option value='" . $city['city'] . "'>" . $city['city'] . "</option>";
      }

      echo $html;
    } catch (Exception $ex) {
      exit;
    }
    exit;
  }

  public function getDistrictByCityAction()
  {
    $city = Mage::app()->getRequest()->getParam('city');

    try {
      $resource = Mage::getSingleton('core/resource');
      $readConnection = $resource->getConnection('core_read');
      $websiteId = Mage::app()->getWebsite()->getId();

      $query = "SELECT distinct(caev2.value) AS district FROM customer_entity AS e
                JOIN customer_address_entity cae on cae.parent_id = e.entity_id
                JOIN customer_address_entity_varchar caev on caev.entity_id = cae.entity_id
                JOIN customer_address_entity_varchar caev2 on caev2.entity_id = cae.entity_id
                WHERE e.website_id = " . $websiteId . "
                and caev.attribute_id = (select attribute_id from eav_attribute where entity_type_id = 2 and attribute_code = 'city')
                and caev2.attribute_id = (select attribute_id from eav_attribute where entity_type_id = 2 and attribute_code = 'bairro')
                and caev2.value is not null
                and caev.value like '%" . $city . "%'
                order by caev2.value asc";
      $districts = $readConnection->fetchAll($query);

      $html = "<option value='0'>BAIRRO</option>";

      foreach ($districts as $district) {
        $html .= "<option value='" . $district['district'] . "'>" . $district['district'] . "</option>";
      }

      echo $html;
    } catch (Exception $ex) {
      exit;
    }
    exit;
  }

  public function getCustomersByFiltersAction()
  {
    $city = Mage::app()->getRequest()->getParam('city');
    $district = Mage::app()->getRequest()->getParam('district');

    try {
      $resource = Mage::getSingleton('core/resource');
      $readConnection = $resource->getConnection('core_read');
      $websiteId = Mage::app()->getWebsite()->getId();

      if($district != "0") {

      $query = "
      SELECT distinct cev.value as razao_social, caev4.value as street, caev3.value AS telephone, e.email FROM customer_entity AS e
                left JOIN customer_entity_varchar cev on cev.entity_id = e.entity_id and cev.attribute_id = (select attribute_id from eav_attribute where entity_type_id = 1 and attribute_code = 'razao_social')
                left JOIN customer_address_entity cae on cae.parent_id = e.entity_id
                left JOIN customer_address_entity_varchar caev on caev.entity_id = cae.entity_id and caev.attribute_id = (select attribute_id from eav_attribute where entity_type_id = 2 and attribute_code = 'city')
                left JOIN customer_address_entity_varchar caev2 on caev2.entity_id = cae.entity_id and caev2.attribute_id = (select attribute_id from eav_attribute where entity_type_id = 2 and attribute_code = 'bairro')
                left JOIN customer_address_entity_varchar caev3 on caev3.entity_id = cae.entity_id and caev3.attribute_id = (select attribute_id from eav_attribute where entity_type_id = 2 and attribute_code = 'telephone')
                left JOIN customer_address_entity_text caev4 on caev4.entity_id = cae.entity_id and caev4.attribute_id = (select attribute_id from eav_attribute where entity_type_id = 2 and attribute_code = 'street')
                WHERE e.website_id = " . $websiteId . "
                and caev.value like '%" . $city . "%'
                and caev2.value like '%" . $district . "%'
                order by cev.value asc";
      } else {
        $query = "
      SELECT distinct cev.value as razao_social, caev4.value as street, caev3.value AS telephone, e.email FROM customer_entity AS e
                left JOIN customer_entity_varchar cev on cev.entity_id = e.entity_id and cev.attribute_id = (select attribute_id from eav_attribute where entity_type_id = 1 and attribute_code = 'razao_social')
                left JOIN customer_address_entity cae on cae.parent_id = e.entity_id
                left JOIN customer_address_entity_varchar caev on caev.entity_id = cae.entity_id and caev.attribute_id = (select attribute_id from eav_attribute where entity_type_id = 2 and attribute_code = 'city')
                left JOIN customer_address_entity_varchar caev3 on caev3.entity_id = cae.entity_id and caev3.attribute_id = (select attribute_id from eav_attribute where entity_type_id = 2 and attribute_code = 'telephone')
                left JOIN customer_address_entity_text caev4 on caev4.entity_id = cae.entity_id and caev4.attribute_id = (select attribute_id from eav_attribute where entity_type_id = 2 and attribute_code = 'street')
                WHERE e.website_id = " . $websiteId . "
                and caev.value like '%" . $city . "%'
                order by cev.value asc";
      }

      $customers = $readConnection->fetchAll($query);

      $html = "";

      foreach ($customers as $customer) {
        $html .= "<ul class='customer'>";
        $html .= "<li>" . $customer['razao_social'] . "</li>";
        $html .= "<li>" . $customer['street'] . "</li>";
        $html .= "<li>" . $customer['telephone'] . "</li>";
        $html .= "</ul>";
      }

      echo $html;
    } catch (Exception $ex) {
      exit;
    }
    exit;
  }
}
