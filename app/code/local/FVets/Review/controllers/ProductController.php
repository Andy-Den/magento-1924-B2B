<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Review
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Review controller
 *
 * @category   Mage
 * @package    Mage_Review
 * @author     Magento Core Team <core@magentocommerce.com>
 */
require_once 'Mage/Review/controllers/ProductController.php';

class FVets_Review_ProductController extends Mage_Review_ProductController
{
	/**
	 * Submit new review action
	 *
	 */
	public function postAction()
	{
		if (!$this->_validateFormKey()) {
			// returns to the product item page
			$this->_redirectReferer();
			return;
		}

		if ($data = Mage::getSingleton('review/session')->getFormData(true)) {
			$rating = array();
			if (isset($data['ratings']) && is_array($data['ratings'])) {
				$rating = $data['ratings'];
			}
		} else {
			$data = $this->getRequest()->getPost();
			$rating = $this->getRequest()->getParam('ratings', array());
		}

		if (($product = $this->_initProduct()) && !empty($data)) {
			$session = Mage::getSingleton('core/session');
			/* @var $session Mage_Core_Model_Session */
			$review = Mage::getModel('review/review')->setData($this->_cropReviewData($data));
			/* @var $review Mage_Review_Model_Review */

			$validate = $review->validate();
			if ($validate === true) {
				try {
					$review->setEntityId($review->getEntityIdByCode(Mage_Review_Model_Review::ENTITY_PRODUCT_CODE))
						->setEntityPkValue($product->getId())
						->setStatusId(Mage_Review_Model_Review::STATUS_PENDING)
						->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId())
						->setStoreId(Mage::app()->getStore()->getId())
						->setStores(array(Mage::app()->getStore()->getId()))
						->save();

					foreach ($rating as $ratingId => $optionId) {
						Mage::getModel('rating/rating')
							->setRatingId($ratingId)
							->setReviewId($review->getId())
							->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId())
							->addOptionVote($optionId, $product->getId());
					}

					$review->aggregate();
					$session->addSuccess($this->__('Your review has been accepted for moderation.'));

					//send transactional
					try {
						$this->sendTransactionalWarning($review);
					} catch(Exception $e) {
						Mage::helper('review')->log($e->getMessage());
					}

				} catch (Exception $e) {
					$session->setFormData($data);
					$session->addError($this->__('Unable to post the review.'));
				}
			} else {
				$session->setFormData($data);
				if (is_array($validate)) {
					foreach ($validate as $errorMessage) {
						$session->addError($errorMessage);
					}
				} else {
					$session->addError($this->__('Unable to post the review.'));
				}
			}
		}

		if ($redirectUrl = Mage::getSingleton('review/session')->getRedirectUrl(true)) {
			$this->_redirectUrl($redirectUrl);
			return;
		}
		$this->_redirectReferer();
	}

	public function sendTransactionalWarning($review)
	{
		$emails = Mage::getStoreConfig('review/general/emailtosendtransactionalwarning');

		if (!$emails) {
			return;
		}

		$emails = explode(',', $emails);
		$helper = Mage::helper('review');

		$fromEmail = Mage::getStoreConfig('trans_email/ident_general/email');
		$fromName = Mage::getStoreConfig('trans_email/ident_general/name');
		$toName = Mage::getStoreConfig('trans_email/ident_general/name');

		if ($review->getCustomerId() && $review->getEntityPkValue()) {
			$customer = Mage::getModel('customer/customer')->load($review->getCustomerId());

			$subject = 'Uma avaliação foi realizada por ' . $customer->getName() . ' (' . $customer->getEmail() . ')';

			$product = Mage::getModel('catalog/product')
				->setStoreId(Mage::app()->getStore()->getStoreId())
				->load($review->getEntityPkValue());
			$message = $subject . '<br><br>' . 'Produto avaliado: ' . $product->getName();
			foreach ($emails as $email) {
				$helper->sendMail($fromEmail, $fromName, $email, $toName, $subject, $message);
			}
		}
		return true;
	}
}


