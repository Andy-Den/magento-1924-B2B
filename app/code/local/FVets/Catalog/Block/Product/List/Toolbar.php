<?php

class FVets_Catalog_Block_Product_List_Toolbar extends Mage_Catalog_Block_Product_List_Toolbar
{
	/**
	 * #biscoito
	 *
	 * Somente no classic, precisamos que a order da collection seja primeiro pelo review e depois por nome.
	 * Mas no magento, quando vc põe uma ordem, ele só utiliza um,  que no caso é o has_review.
	 * Estou incluíndo também o nome aí em baixo, saco a ordem seja has_review
	 */
	public function setCollection($collection)
	{
		parent::setCollection($collection);

		if ($this->getCurrentOrder() == 'has_review')
		{
			$this->getCollection()->setOrder('name', 'asc');
		}

		return $this;
	}
}