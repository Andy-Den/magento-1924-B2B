<?php

class FVets_Catalog_Model_Category extends Mage_Catalog_Model_Category
{
	public function getBaseImage($image = null)
	{
		if (!isset($image))
			$image = $this->getImage();

		return Mage::getBaseUrl ( 'media' ) .DS . "catalog" . DS . "category" . DS . $image;
	}

	public function getBaseThumbnail($image = null)
	{
		if (!isset($image))
			$image = $this->getThumbnail ();

		return Mage::getBaseUrl ( 'media' ) .DS . "catalog" . DS . "category" . DS . $image;
	}

	public function getResizedImage($width, $height = null, $quality = 100) {
		if (! $this->getImage ())
			return false;

		return $this->resizeImage($this->getImage (), $width, $height, $quality);

	}

	public function getResizedThumbnail($width, $height = null, $quality = 100) {
		if (! $this->getThumbnail ())
			return false;

		return $this->resizeImage($this->getThumbnail (), $width, $height, $quality);

	}

	function resizeImage($image, $width, $height = null, $quality = 100)
	{
		$imageUrl = Mage::getBaseDir ( 'media' ) . DS . "catalog" . DS . "category" . DS . $image;
		if (! is_file ( $imageUrl ))
			return false;

		$imageResized = Mage::getBaseDir ( 'media' ) . DS . "catalog" . DS . "product" . DS . "cache" . DS . "cat_resized" . DS . $image;// Because clean Image cache function works in this folder only
		if (! file_exists ( $imageResized ) && file_exists ( $imageUrl ) || file_exists($imageUrl) && filemtime($imageUrl) > filemtime($imageResized)) :
			$imageObj = new Varien_Image ( $imageUrl );
			$imageObj->constrainOnly ( true );
			$imageObj->keepAspectRatio ( true );
			$imageObj->keepFrame ( false );
			$imageObj->quality ( $quality );
			$imageObj->resize ( $width, $height );
			$imageObj->save( $imageResized );
		endif;

		if(file_exists($imageResized)){
			return Mage::getBaseUrl ( 'media' ) ."/catalog/product/cache/cat_resized/" . $image;
		}else{
			return $this->getImageUrl();
		}
	}

	/**
	 * Get category url
	 *
	 * @return string
	 */
	public function getUrl()
	{
		if ($this->getStaticUrl() !== null && trim($this->getStaticUrl()) != '') {
			return $this->getStaticUrl();
		}
		return $this->getUrlModel()->getCategoryUrl($this);
	}
}

?>