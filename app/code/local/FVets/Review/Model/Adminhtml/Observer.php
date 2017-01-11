<?php

/**
 * Created by PhpStorm.
 * User: julio
 * Date: 9/1/15
 * Time: 9:23 AM
 */
class FVets_Review_Model_Adminhtml_Observer
{

	public function onReviewSave($observer)
	{
		$_origData = $observer->getData()['data_object']->getOrigData();
		$_data = $observer->getData()['data_object']->getData();

		if ($_data['status_id'] == 1 && ($_data['status_id'] != $_origData['status_id']))
		{

			//if feature activated, send email
			$sendEmailFeatureActivated = Mage::getStoreConfig("review/general/approvemailsendenabled", $_data['store_id']);
			if (!$sendEmailFeatureActivated)
			{
				return;
			}
			$this->sendReviewAcceptedEmail($_data);
			//end

		}
	}

	private function sendReviewAcceptedEmail($review, $storeId = '0')
	{
		$review = Mage::getModel('review/review')->load($review['review_id']);

		if ($review->getStatusId() != 1)
		{
			return;
		}

		if (!$review->getCustomerId())
		{
			return;
		}

		$customer = Mage::getModel('customer/customer')->load($review->getCustomerId());

		$translate = Mage::getSingleton('core/translate');
		$translate->setTranslateInline(false);

		if (!$storeId)
		{
			$storeId = $review->getStoreId();
		}

		$template = Mage::getStoreConfig("review/general/approvemailsendtemplate", $storeId);
		$sender = Mage::getStoreConfig("review/general/approvemailsendidentity", $storeId);

		if (!$template || !$sender)
		{
			return;
		}

		$review->setCustomerReviewUrl(Mage::getBaseUrl() . 'review/customer/view/id/' . $review->getId());

		Mage::getModel('core/email_template')
			->setDesignConfig(array('area' => 'frontend', 'store' => $storeId))
			->sendTransactional(
				$template,
				$sender,
				$customer->getEmail(),
				$customer->getName(),
				array('customer' => $customer, 'review' => $review));

		$translate->setTranslateInline(true);

		return $this;
	}

	public function setProductHasReview($observer)
	{
		/**
		 * save stores
		 */
		$object = $observer->getObject();
		$stores = $object->getStores();
		if (!empty($stores)) {

			$insertedStoreIds = array();
			foreach ($stores as $storeId)
			{
				if (in_array($storeId, $insertedStoreIds))
				{
					continue;
				}

				$insertedStoreIds[] = $storeId;

				$has_review  = ((bool)Mage::getModel('review/review')->getTotalReviews($object->getEntityPkValue(), true, $storeId)) ? '1' : '0';

				$product = Mage::getModel('catalog/product');
				$product->setStoreId($storeId);
				$product->load($object->getEntityPkValue());
				$product->setHasReview($has_review);
				$product->getResource()->saveAttribute($product, 'has_review');
			}
		}
	}

}