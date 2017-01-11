<?php
class Idev_OneStepCheckout_Helper_Data extends Mage_Core_Helper_Abstract
{

	const XML_GENERAL_VALIDATE_CHECKOUT_CUSTOMER = 'onestepcheckout/general/validate_checkout_customer';

    public function checkValid($observer)
    {
        $layout = Mage::app()->getLayout();
        $content = $layout->getBlock('content');
        $block = $layout->createBlock('onestepcheckout/valid');
        $content->insert($block);
    }

    public function setCustomerComment($observer)
    {
		$orderComment = '';
        $enableComments = Mage::getStoreConfig('onestepcheckout/exclude_fields/enable_comments');
        $enableCommentsDefault = Mage::getStoreConfig('onestepcheckout/exclude_fields/enable_comments_default');
        $enableFeedback = Mage::getStoreConfig('onestepcheckout/feedback/enable_feedback');

		if (strlen(trim($this->_getRequest()->getPost('onestepcheckout_comments'))))
		{
			$orderComment .= $this->__('Observações do cliente:') . ' ' . $this->_getRequest()->getPost('onestepcheckout_comments') . '      ';
		}

		if (strlen(trim($observer->getQuote()->getCustomer()->getNotes())))
		{
			$orderComment .= $this->__('*Nota padrão do cliente:') . ' ' . $observer->getQuote()->getCustomer()->getNotes();
		}

		$orderComment = str_replace('%', '%%', trim($orderComment));
		$orderComment = str_replace('|', ';)', trim($orderComment));



        if($enableComments && !$enableCommentsDefault) {
            if ($orderComment != ""){
                $observer->getEvent()->getOrder()->setOnestepcheckoutCustomercomment(Mage::helper('core')->escapeHtml($orderComment));
            }
        }

		if ($enableComments && $enableCommentsDefault) {
			$order = $observer->getEvent()->getOrder();
			if (Mage::getStoreConfig('fvets_salesrule/checkout/concatenate_on_comments')) {
				if($orderComment) {
					$orderComment .= "\n";
				}
				//adicionando/concatenando informações de campanhas no comentário do cliente
				$orderComment .= $this->getCampaignsDescription($order);
			}
			if ($orderComment != "") {
				$observer->getEvent()->getOrder()->setState($order->getStatus(), true, Mage::helper('core')->escapeHtml($orderComment), false);
			}
		}

        if($enableFeedback){

            $feedbackValues = unserialize(Mage::getStoreConfig('onestepcheckout/feedback/feedback_values'));
            $feedbackValue = $this->_getRequest()->getPost('onestepcheckout-feedback');
            $feedbackValueFreetext = $this->_getRequest()->getPost('onestepcheckout-feedback-freetext');

            if(!empty($feedbackValue)){
                if($feedbackValue!='freetext'){
                    $feedbackValue = $feedbackValues[$feedbackValue]['value'];
                } else {
                    $feedbackValue = $feedbackValueFreetext;
                }

                $observer->getEvent()->getOrder()->setOnestepcheckoutCustomerfeedback(Mage::helper('core')->escapeHtml($feedbackValue));
            }

        }
    }

    public function isRewriteCheckoutLinksEnabled()
    {
        return Mage::getStoreConfig('onestepcheckout/general/rewrite_checkout_links');
    }

    /**
     * If we are using enterprise wersion or not
     * @return int
     */
    public function isEnterprise(){
        return (int)is_object(Mage::getConfig()->getNode('global/models/enterprise_enterprise'));
    }

    /**
     * If we have ee_rewards enabled or not
     * @return int
     */
    public function hasEeRewards(){
        return (int)is_object(Mage::getConfig()->getNode('global/models/enterprise_reward'));
    }

    /**
     * If we have ee_customerbalance enabled or not
     * @return int
     */
    public function hasEeCustomerbalanace(){
        return (int)is_object(Mage::getConfig()->getNode('global/models/enterprise_customerbalance'));
    }

    /**
     * If we have ee_giftcard enabled or not
     * @return int
     */
    public function hasEeGiftcards(){
        return (int)is_object(Mage::getConfig()->getNode('global/models/enterprise_giftcard'));
    }

    /**
     * Encode the mixed $valueToEncode into the JSON format
     *
     * @param mixed $valueToEncode
     * @param  boolean $cycleCheck Optional; whether or not to check for object recursion; off by default
     * @param  array $options Additional options used during encoding
     * @return string
     */
    public function jsonEncode($valueToEncode, $cycleCheck = false, $options = array())
    {
        $json = Zend_Json::encode($valueToEncode, $cycleCheck, $options);
        /* @var $inline Mage_Core_Model_Translate_Inline */
        $inline = Mage::getSingleton('core/translate_inline');
        if ($inline->isAllowed()) {
            $inline->setIsJson(true);
            $inline->processResponseBody($json);
            $inline->setIsJson(false);
        }

        return $json;
    }

    /**
     * Check if value is only -
     * @param mixed $value
     */
    public function clearDash($value = null){
		$value = trim($value);
        if($value == '-'){
            return '';
        }
        if(method_exists(Mage::helper('core'), 'escapeHtml')){
            return Mage::helper('core')->escapeHtml($value);
        } else {
            //backwards compatibility with < 1.4.1.*
            return Mage::helper('core')->htmlEscape($value);
        }
    }

	public function getCampaignsDescription($order)
	{
		$result = "";
		$items = $order->getAllVisibleItems();

		$atLeastOneItemHasCampaign = false;
		foreach ($items as $item) {
			if ($item->getAppliedRuleIds()) {
				$atLeastOneItemHasCampaign = true;
				foreach (explode(',', $item->getAppliedRuleIds()) as $campaignId) {
					$result .= "Item " . '"' . $item->getName() . '" => "' . $this->getCampaignDescription($campaignId) . '"' . "\n";
				}
			}
		}
		if ($atLeastOneItemHasCampaign) {
			$result = "Descrição das campanhas aplicadas:\n" . $result;
		}
		return $result;
	}

	private function getCampaignDescription($campaignId) {
		$campaign = Mage::getModel('salesrule/rule')->load($campaignId);
		if ($campaign && $campaign->getId()) {
			return $campaign->getDescription();
		}
		return '';
	}
}
