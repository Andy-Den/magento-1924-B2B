<?php
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 4/20/16
 * Time: 9:52 AM
 */

if (Mage::helper('core')->isModuleEnabled('Ebizmarts_Mandrill')) {
	class FVets_Email_Model_Email_Template_Tmp extends Ebizmarts_Mandrill_Model_Email_Template
	{
	}
} else {
	class FVets_Email_Model_Email_Template_Tmp extends Mage_Core_Model_Email_Template
	{
	}
}

class FVets_Email_Model_Email_Template extends FVets_Email_Model_Email_Template_Tmp
{

	public function send($email, $name = null, array $variables = array())
	{
		//verify if an universal email is set
		if (isset($variables['store'])) {
			$customerStore = $variables['store'];
		} else {
			if (isset($variables['customer'])) {
				$customerStore = $variables['customer']->getStore();
			}
		}
		if ($customerStore) {
			$universalSenderEmail = Mage::getStoreConfig('trans_email/ident_universal_sender/email', $customerStore->getId());
			if ($universalSenderEmail) {
				$this->setSenderEmail($universalSenderEmail);
			}
			$universalSenderName = Mage::getStoreConfig('trans_email/ident_universal_sender/name', $customerStore->getId());
			if ($universalSenderName) {
				$this->setSenderName($universalSenderName);
			}
		}
		//end

		return parent::send($email, $name, $variables);
	}
}