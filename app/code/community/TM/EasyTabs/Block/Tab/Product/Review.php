<?php

class TM_EasyTabs_Block_Tab_Product_Review extends Mage_Review_Block_Product_View_List
{

	protected function _prepareLayout()
	{
			$reviewForm = $this->getLayout()->createBlock('review/form', 'product.review.form');
			if ($reviewForm) {
					$wrapper = $this->getLayout()
							->createBlock('page/html_wrapper', 'product.review.form.fields.before');
					if ($wrapper) {
							$wrapper->setMayBeInvisible(1);
							$reviewForm->setChild('form_fields_before', $wrapper);
					}
					$this->setChild('review_form', $reviewForm);

					//#biscoito -> Nesse bloco, a função getAlias() não retorna nada a não ser que setemos um valor pro atributo;
					$this->setAlias('review_tabbed_local');
			}
			return parent::_prepareLayout();
	}

	/**
	 * Get product reviews summary
	 *
	 * @param Mage_Catalog_Model_Product $product
	 * @param bool $templateType
	 * @param bool $displayIfNoReviews
	 * @return string
	 */
	public function getReviewsSummaryHtml(Mage_Catalog_Model_Product $product, $templateType = false,
																				$displayIfNoReviews = false)
	{
		if ($templateType == 'stars')
		{
			return $this->getLayout()->createBlock('rating/entity_detailed')
				->setEntityId($this->getProduct()->getId())
				->toHtml();
		} else {
			if ($this->_initReviewsHelperBlock()) {
				return $this->_reviewsHelperBlock->getSummaryHtml($product, $templateType, $displayIfNoReviews);
			}
		}

		return '';
	}
}
