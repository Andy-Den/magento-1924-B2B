<?php
require_once  Mage::getModuleDir('controllers', 'Mage_Checkout').DS.'CartController.php';
class Idev_OneStepCheckout_CartController extends Mage_Checkout_CartController
{
    /**
     * Set back redirect url to response
     *
     * @return Mage_Checkout_CartController
     */
    protected function _goBack()
    {

        if ($returnUrl = $this->getRequest()->getParam('return_url')) {
            // clear layout messages in case of external url redirect
            if ($this->_isUrlInternal($returnUrl)) {
                $this->_getSession()->getMessages(true);
            }
            $this->getResponse()->setRedirect($returnUrl);
        } elseif (!Mage::getStoreConfig('checkout/cart/redirect_to_cart')
            && !$this->getRequest()->getParam('in_cart')
            && $backUrl = $this->_getRefererUrl()) {
            $this->getResponse()->setRedirect($backUrl);
        } else {
            if (($this->getRequest()->getActionName() == 'add') && !$this->getRequest()->getParam('in_cart')) {
                $this->_getSession()->setContinueShoppingUrl($this->_getRefererUrl());
            }
            //if config enabled, clear messages and redirect to checkout
            if(Mage::getStoreConfig('onestepcheckout/direct_checkout/redirect_to_cart')){

                $customerGroupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
                $allowedGroups = Mage::getStoreConfig('onestepcheckout/direct_checkout/group_ids');

                if(!empty($allowedGroups)){
                    $allowedGroups = explode(',',$allowedGroups);
                } else {
                    $allowedGroups = array();
                }

                if(!in_array($customerGroupId, $allowedGroups)){

                    $this->_getSession()->getMessages(true);
                    $this->_redirect('onestepcheckout', array('_secure'=>true));
                } else {
                    $this->_redirect('checkout/cart');
                }

            } else {
                $this->_redirect('checkout/cart');
            }


        }
        return $this;
    }

	public function addAction()
	{
		if (!$this->_validateFormKey()) {
			$this->_goBack();
			return;
		}
		$cart   = $this->_getCart();
		$params = $this->getRequest()->getParams();
		try {
			if (isset($params['qty'])) {
				$filter = new Zend_Filter_LocalizedToNormalized(
					array('locale' => Mage::app()->getLocale()->getLocaleCode())
				);
				$params['qty'] = $filter->filter($params['qty']);
			}

			$product = $this->_initProduct();
			$related = $this->getRequest()->getParam('related_product');

			/**
			 * Check product availability
			 */
			if (!$product) {
				$this->_goBack();
				return;
			}

			//fvets -> check and format product options parameters - BUG do ajax pro
			if (isset($params['custom_option']) && isset($params['custom_option_value_id'])) {
				if(!isset($params['options'])) {
					$params['options'] = array();
				}
				$params['options'][$params['custom_option']] = $params['custom_option_value_id'];
			}
			//

			if ((empty($related) && !$product->isSaleable()) || $product->isSaleable()) {
				$cart->addProduct($product, $params);
			}
			if (!empty($related)) {
				$cart->addProductsByIds(explode(',', $related));
			}

			$cart->save();

			$this->_getSession()->setCartWasUpdated(true);

			/**
			 * @todo remove wishlist observer processAddToCart
			 */
			Mage::dispatchEvent('checkout_cart_add_product_complete',
				array('product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse())
			);

			if (!$this->_getSession()->getNoCartRedirect(true)) {
				if (!$cart->getQuote()->getHasError()) {
					$message = $this->__('%s was added to your shopping cart.', Mage::helper('core')->escapeHtml($product->getName()));
					$this->_getSession()->addSuccess($message);
				}
				$this->_goBack();
			}
		} catch (Mage_Core_Exception $e) {
			if ($this->_getSession()->getUseNotice(true)) {
				$this->_getSession()->addNotice(Mage::helper('core')->escapeHtml($e->getMessage()));
			} else {
				$messages = array_unique(explode("\n", $e->getMessage()));
				foreach ($messages as $message) {
					$this->_getSession()->addError(Mage::helper('core')->escapeHtml($message));
				}
			}

			$url = $this->_getSession()->getRedirectUrl(true);
			if ($url) {
				$this->getResponse()->setRedirect($url);
			} else {
				$this->_redirectReferer(Mage::helper('checkout/cart')->getCartUrl());
			}
		} catch (Exception $e) {
			$this->_getSession()->addException($e, $this->__('Cannot add the item to shopping cart.'));
			Mage::logException($e);
			$this->_goBack();
		}
	}
}
