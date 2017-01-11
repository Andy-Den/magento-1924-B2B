<?php
require_once 'Idev/OneStepCheckout/controllers/AjaxController.php';
class FVets_CheckoutSplit_AjaxController extends Idev_OneStepCheckout_AjaxController
{
	public function successAction()
	{
		$html = null;

		$this->loadLayout('checkoutsplit_ajax_success');
		$layout = $this->getLayout();
		$block = $layout->getBlock('checkout.split.success');
		$html = $block->toHtml();

		if (count(Mage::getSingleton('checkout/session')->getQuote()->getAllItems()) <= 0)
		{
			$html .= '<script>';
			$html .= 'window.location = "'.Mage::getUrl('checkout/onepage/success', array('_secure'=>true)).'";';
			$html .= 'jQuery(".loading-overlay").show();';
			$html .= '</script>';

		}

		$this->getResponse()->setHeader('Content-type', 'application/json');
		$this->getResponse()->setBody($html);

	}

	public function set_methods_separateAction()
	{
		$helper = Mage::helper('onestepcheckout/checkout');

		$shipping_method = $this->getRequest()->get('shipping_method', false);
		$old_shipping_method = $this->_getOnepage()->getQuote()->getShippingAddress()->getShippingMethod();

		if($shipping_method != '' && $shipping_method != $old_shipping_method)  {
			//$result = $this->_getOnepage()->saveShippingMethod($shipping_method);
			// Use our helper instead
			$helper->saveShippingMethod($shipping_method);
		}
		//$this->_getOnepage()->getQuote()->getShippingAddress()->collectTotals();

		$paymentMethod = explode('-', $this->getRequest()->get('payment_method', false));
		$paymentMethod = array_shift($paymentMethod);
		$selectedMethod = $this->_getOnepage()->getQuote()->getPayment()->getMethod();

		$store = $this->_getOnepage()->getQuote() ? $this->_getOnepage()->getQuote()->getStoreId() : null;
		$methods = $helper->getActiveStoreMethods($store, $this->_getOnepage()->getQuote());

		if($paymentMethod && !empty($methods) && !in_array($paymentMethod, $methods)){
			$paymentMethod = false;
		}

		if(!$paymentMethod && $selectedMethod && in_array($selectedMethod, $methods)){
			$paymentMethod = $selectedMethod;
		}

		try {
			$payment = array($this->getRequest()->get('salesrep', false) => $paymentMethod);
			//$payment = $this->getRequest()->getPost('payment', array());
			//$payment = $payment[$this->getRequest()->getPost('salesrep', false)];
			//$payment = array();
			//if(!empty($paymentMethod)){
				$payment['method'] = $paymentMethod;
			//}
			//$payment_result = $this->_getOnepage()->savePayment($payment);
			$helper->savePayment($payment);
		}
		catch(Exception $e) {
			die('Error: ' . $e->getMessage());
			// Silently fail for now
		}

		$this->_getOnepage()->getQuote()->setSalesrep(Mage::getModel('fvets_salesrep/salesrep')->load($this->getRequest()->get('salesrep', false)));
		$this->_getOnepage()->getQuote()->setSplitBySalesrep(true);
		$this->_getOnepage()->getQuote()->collectTotals()->save();

		//$this->loadLayout(false);
		//$this->renderLayout();
	}

	public function paymentMethodsRefreshAction() {
		$this->set_methods_separateAction();

		$this->loadLayout(false);
		$this->renderLayout();
	}

	public function allowCheckoutWithoutOrderAction()
	{
		$salesrep = $this->getRequest()->getPost('salesrep');
		$handles = $this->getRequest()->getPost('handles');

		if ($this->getRequest()->getPost('status') === "true")
			$status = true;
		else
			$status = false;

		//Adiciona a verificação para continuar no checkout mesmo que o pedido desse representante não tenha atingido o mínimo.
		Mage::getSingleton('checkout/session')->{"setSalesrep".$salesrep.'AllowCheckout'}($status);


		//Carrega o layout do carrinho e pega somente o bloco necessário "checkout.cart", ignorando o restante. (head, header e etc)
		$this->loadLayout($handles);
		$layout = $this->getLayout();
		$block = $layout->getBlock('checkout.cart');

		//$jsonData = json_encode(array('data' => $block->toHtml()));
		$this->getResponse()->setHeader('Content-type', 'application/json');
		$this->getResponse()->setBody($block->toHtml());
	}

	public function get_checkout_first_stepAction()
	{
		$this->loadLayout('onestepcheckout_index_index');

		$layout = $this->getLayout();
		$block = $layout->getBlock('checkout.split');

		$salesrep = Mage::getModel('fvets_salesrep/salesrep')->load($this->getRequest()->getParam('salesrep'));
		$block->getQuote()->setSalesrep($salesrep);

		$block->formErrors = unserialize($this->getRequest()->getParam('error'));

		//$block->setSplitSalesrep($this->getRequest()->getPost('salesrep'));
		//$block->collectTotals();

		//$jsonData = json_encode(array('data' => $block->toHtml()));
		$this->getResponse()->setHeader('Content-type', 'application/json');
		$this->getResponse()->setBody($block->toHtml());
		$block->getQuote()->setSplitBySalesrep(false);
	}

	public function get_checkout_next_stepAction()
	{
		$this->loadLayout('checkoutsplit_ajax_get_checkout_next_step');

		$layout = $this->getLayout();
		$block = $layout->getBlock('checkout.split.step2');
		$block->setSplitSalesrep($this->getRequest()->getPost('salesrep'));
		$block->collectTotals();

		//$jsonData = json_encode(array('data' => $block->toHtml()));
		$this->getResponse()->setHeader('Content-type', 'application/json');
		$this->getResponse()->setBody($block->toHtml());
		$block->getQuote()->setSplitBySalesrep(false);
	}

	public function disable_checkout_orderAction()
	{
		Mage::getSingleton('checkout/session')->{"setSalesrep".$this->getRequest()->getPost('salesrep').'DisableCheckout'}(false);

		$this->loadLayout('checkoutsplit_ajax_disable_checkout_order');

		$layout = $this->getLayout();
		$block = $layout->getBlock('checkout.split.step1');
		$block->setSplitSalesrep($this->getRequest()->getPost('salesrep'));

		$this->getResponse()->setHeader('Content-type', 'application/json');
		$this->getResponse()->setBody($block->toHtml());
	}

	public function add_couponAction()
	{
		$quote = $this->_getOnepage()->getQuote();
		$couponCode = (string)$this->getRequest()->getParam('code');

		if ($this->getRequest()->getParam('remove') == 1) {
			$couponCode = '';
		}

		$response = array(
			'success' => false,
			'error'=> false,
			'message' => false,
		);



		try {

			$quote->getShippingAddress()->setCollectShippingRates(true);
			$quote->setCouponCode(strlen($couponCode) ? $couponCode : '')
				->collectTotals()
				->save();

			if ($couponCode) {
				if ($couponCode == $quote->getCouponCode()) {
					$response['success'] = true;
					$response['message'] = $this->__('Coupon code "%s" was applied successfully.', Mage::helper('core')->escapeHtml($couponCode));
				}
				else {
					$response['success'] = false;
					$response['error'] = true;
					$response['message'] = $this->__('Coupon code "%s" is not valid.', Mage::helper('core')->escapeHtml($couponCode));
				}
			} else {
				$response['success'] = true;
				$response['message'] = $this->__('Coupon code was canceled successfully.');
			}


		}
		catch (Mage_Core_Exception $e) {
			$response['success'] = false;
			$response['error'] = true;
			$response['message'] = $e->getMessage();
		}
		catch (Exception $e) {
			$response['success'] = false;
			$response['error'] = true;
			$response['message'] = $this->__('Can not apply coupon code.');
		}




		/*$html = $this->getLayout()
			->createBlock('checkout/onepage_shipping_method_available')
			->setTemplate('onestepcheckout/shipping_method.phtml')
			->toHtml();

		$response['shipping_method'] = $html;*/


		/*$html = $this->getLayout()
		->createBlock('checkout/onepage_payment_methods','choose-payment-method')
		->setTemplate('onestepcheckout/payment_method.phtml');*/

		/*$html = $this->loadLayout()->getLayout()->getBlock('choose-payment-method');

		if(Mage::helper('onestepcheckout')->isEnterprise() && Mage::helper('customer')->isLoggedIn()){

			if (Mage::helper('onestepcheckout')->hasEeCustomerbalanace()) {
				$customerBalanceBlock = $this->getLayout()->createBlock('enterprise_customerbalance/checkout_onepage_payment_additional', 'customerbalance', array(
					'template' => 'onestepcheckout/customerbalance/payment/additional.phtml'
				));
				$customerBalanceBlockScripts = $this->getLayout()->createBlock('enterprise_customerbalance/checkout_onepage_payment_additional', 'customerbalance_scripts', array(
					'template' => 'onestepcheckout/customerbalance/payment/scripts.phtml'
				));
				$this->getLayout()
					->getBlock('choose-payment-method')
					->append($customerBalanceBlock)
					->append($customerBalanceBlockScripts);
			}

			if (Mage::helper('onestepcheckout')->hasEeRewards()) {
				$rewardPointsBlock = $this->getLayout()->createBlock('enterprise_reward/checkout_payment_additional', 'reward.points', array(
					'template' => 'onestepcheckout/reward/payment/additional.phtml',
					'before' => '-'
				));
				$rewardPointsBlockScripts = $this->getLayout()->createBlock('enterprise_reward/checkout_payment_additional', 'reward.scripts', array(
					'template' => 'onestepcheckout/reward/payment/scripts.phtml',
					'after' => '-'
				));
				$this->getLayout()
					->getBlock('choose-payment-method')
					->append($rewardPointsBlock)
					->append($rewardPointsBlockScripts);
			}

		}

		if (Mage::helper('onestepcheckout')->isEnterprise() && Mage::helper('onestepcheckout')->hasEeGiftcards()) {
			$giftcardScripts = $this->getLayout()->createBlock('enterprise_giftcardaccount/checkout_onepage_payment_additional', 'giftcardaccount_scripts', array(
				'template' => 'onestepcheckout/giftcardaccount/onepage/payment/scripts.phtml'
			));
			$html->append($giftcardScripts);
		}

		$response['payment_method'] = $html->toHtml();*/

		// Add updated totals HTML to the output
		/*$html = $this->getLayout()
		->createBlock('onestepcheckout/summary')
		->setTemplate('onestepcheckout/summary.phtml')
		->toHtml();*/

		$html = $this->loadLayout()->getLayout()->getBlock('coupon')->toHtml();

		$response['coupon'] = $html;

		$this->getResponse()->setBody(Zend_Json::encode($response));
	}

}