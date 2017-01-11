<?php
class FVets_Review_Helper_Data extends Mage_Review_Helper_Data
{

	public function getOptionsStars($rating_id)
	{
		$votes = Mage::getModel('rating/rating_option_vote')
			->getResourceCollection()
			->addFieldToFilter('rating_id', $rating_id)
			->setStoreFilter(Mage::app()->getStore()->getId())
			->load();

		$stars = array(
			1 => array('count' => 0, 'percent' => 0),
			2 => array('count' => 0, 'percent' => 0),
			3 => array('count' => 0, 'percent' => 0),
			4 => array('count' => 0, 'percent' => 0),
			5 => array('count' => 0, 'percent' => 0),
		);

		foreach ($votes as $vote)
		{
			$stars[$vote->getValue()]['count']++;
			$stars[$vote->getValue()]['percent'] = $stars[$vote->getValue()]['count'] * 100 / count($votes);
		}

		return $stars;
	}

	public function getBestReview($product) {
		$reviews = Mage::getModel('review/review')
			->getResourceCollection()
			->addStoreFilter(Mage::app()->getStore()->getId())
			->addEntityFilter('product', $product->getId())
			->addStatusFilter(Mage_Review_Model_Review::STATUS_APPROVED)
			->setDateOrder()
			->addRateVotes();

		$arrayReviews = array();

		foreach($reviews as $review) {
			$ratingVotes = $review->getRatingVotes();
			$items = $ratingVotes->getItems();
			if (!$items) {
				continue;
			}
			$item = $items[key($items)];
			$arrayReviews[$item->getValue()] = $review;
		}

		if (!$arrayReviews) {
			return null;
		}

		arsort($arrayReviews);

		$bestReview = $arrayReviews[key($arrayReviews)];

		$ratings = $bestReview->getRatingVotes()->getItems();
		$rating = $ratings[key($ratings)];
		$ratingValue = $rating->getValue();

		if ($ratingValue >= 3) {
			$bestReview->setIsRecommended(true);
		} else {
			$bestReview->setIsRecommended(false);
		}

		return $bestReview;
	}

	public function getLatestReview($product) {
		$reviews = Mage::getModel('review/review')
			->getResourceCollection()
			->addStoreFilter(Mage::app()->getStore()->getId())
			->addEntityFilter('product', $product->getId())
			->addStatusFilter(Mage_Review_Model_Review::STATUS_APPROVED)
			->setDateOrder()
			->addRateVotes()
			->getFirstItem();

		return $reviews;
	}

	public function getReviews($product) {
		$reviews = Mage::getModel('review/review')
			->getResourceCollection()
			->addStoreFilter(Mage::app()->getStore()->getId())
			->addEntityFilter('product', $product->getId())
			->addStatusFilter(Mage_Review_Model_Review::STATUS_APPROVED)
			->setDateOrder()
			->addRateVotes();

		return $reviews;
	}

	public function getReviewsVotesArray($_product)
	{
		$_max = 0;
		$_votes = array(5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0);
		$_count = 0;
		foreach ( $this->getReviews($_product) as $rating)
		{
			foreach ($rating->getRatingVotes() as $vote)
			{
				$_votes[$vote->getValue()]++;

				if ($_max < $_votes[$vote->getValue()])
				{
					$_max = $_votes[$vote->getValue()];
				}

				$_count++;
			}
		}

		return array('max' => $_max, 'votes' => $_votes, 'count' => $_count);
	}

	public function getVoteString($key)
	{
		if ($key == 1)
		{
			return $this->__('Horrível');
		}
		elseif ($key == 2)
		{
			return $this->__('Ruim');
		}
		elseif ($key == 3)
		{
			return $this->__('Razoável');
		}
		elseif ($key == 4)
		{
			return $this->__('Muito bom');
		}
		elseif ($key == 5)
		{
			return $this->__('Recomendado');
		}

		return '';
	}

	public function getProductSummaryEvaluation($product) {
		$connection = Mage::getSingleton('core/resource')->getConnection('core_read');
		$sql = "select * from rating_option_vote_aggregated rova where rova.entity_pk_value = " . $product->getId() . " and rating_id = 1 and store_id = 1";
		return $connection->fetchRow($sql);
	}

	public function getCustomerLevel($customerId)
	{
		$valueStatus1 = (int)Mage::getStoreConfig('review/raters/statusvalue1');
		$nameStatus1 = Mage::getStoreConfig('review/raters/statusname1');
		$valueStatus2 = (int)Mage::getStoreConfig('review/raters/statusvalue2');
		$nameStatus2 = Mage::getStoreConfig('review/raters/statusname2');
		$valueStatus3 = (int)Mage::getStoreConfig('review/raters/statusvalue3');
		$nameStatus3 = Mage::getStoreConfig('review/raters/statusname3');
		$valueStatus4 = (int)Mage::getStoreConfig('review/raters/statusvalue4');
		$nameStatus4 = Mage::getStoreConfig('review/raters/statusname4');

		$reviews = Mage::getModel('review/review')->getCollection()
			->addFieldToFilter('customer_id', $customerId);

		$customerReviewsQty = count($reviews);


		if (!$customerReviewsQty || $customerReviewsQty <= $valueStatus1)
		{
			return '<div class="status-container"><div class="nivel-classificado-label"><span class>Avaliador nível:</span></div><div class="status fit"></div><div class="status"></div><div class="status"></div><div class="status"></div><span class="status-avaliador">(' . $nameStatus1 . ')</span></div>';
		} elseif ($customerReviewsQty > $valueStatus1 && $customerReviewsQty <= $valueStatus2)
		{
			return '<div class="status-container"><div class="nivel-classificado-label"><span class>Avaliador nível:</span></div><div class="status fit"></div><div class="status fit"></div><div class="status"></div><div class="status"></div><span class="status-avaliador">(' . $nameStatus2 . ')</span></div>';
		} elseif ($customerReviewsQty > $valueStatus2 && $customerReviewsQty <= $valueStatus3)
		{
			return '<div class="status-container"><div class="nivel-classificado-label"><span class>Avaliador nível:</span></div><div class="status fit"></div><div class="status fit"></div><div class="status fit"></div><div class="status"></div><span class="status-avaliador">(' . $nameStatus3 . ')</span></div>';
		} elseif ($customerReviewsQty > $valueStatus3)
		{
			return '<div class="status-container"><div class="nivel-classificado-label"><span class>Avaliador nível:</span></div><div class="status fit"></div><div class="status fit"></div><div class="status fit"></div><div class="status fit"></div><span class="status-avaliador">(' . $nameStatus4 . ')</span></div>';
		}
	}

	public function sendMail($fromEmail, $fromName, $toEmail, $toName, $subject, $message)
	{
		try {
			$emailTemplate = Mage::getModel('core/email_template');
			$emailTemplate->setSenderEmail($fromEmail);
			$emailTemplate->setSenderName($fromName);
			$emailTemplate->setTemplateSubject($subject);
			$emailTemplate->setTemplateText($message);

			$emailTemplate->send($toEmail, $toName, array());

			return true;
		} catch (Exception $ex) {
			return $ex->__toString();
		}
	}

	public function log($data, $filename = 'FVets_Review.log')
	{
		return Mage::getModel('core/log_adapter', $filename)->log($data);
	}
}