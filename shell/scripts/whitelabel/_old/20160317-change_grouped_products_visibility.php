<?php
require_once './configScript.php';

$start = time();

Mage::app()->setCurrentStore(1);

$products = Mage::getModel('catalog/product')->getCollection()
    /*->addAttributeToSelect('name')
    ->addAttributeToSelect('sku')
    ->addAttributeToSelect('brand')
    ->addAttributeToSelect('description')
    ->addAttributeToSelect('short_description')
    ->addAttributeToSelect('image')
    ->addAttributeToSelect('small_image')
    ->addAttributeToSelect('thumbnail')
    ->addAttributeToSelect('manufacturer')
    ->addAttributeToSelect('status')
    ->addAttributeToSelect('visibility')*/
    //->addAttributeToFilter('sku', 'ZOE0007G')
    ->addAttributeToFilter('type_id', array('eq' => 'grouped'));

echo date('i:s', time() - $start) . ' Encontrados ' . $products->count() . ' produtos!' . "\n\n";

foreach ($products as $product) {
    $product = Mage::getModel('catalog/product')->load($product->getId());

    echo date('i:s', time() - $start) . ' Product ' . $product->getName() . '(' . $product->getSku() . ')' . "\n";
    $childs = $product->getTypeInstance(true)->getAssociatedProducts($product);

    if (count($childs) > 0) {

        $save_product = false;

        if ($product->getVisibility() != '4' || $product->getStatus() != '1') {
            echo date('i:s', time() - $start) . ' ' . '--- Alterando status do produto' . "\n";
            $product->setStatus('1');
            $product->setVisibility('4');
            $save_product = true;
        }

        $igore_images = false;
        foreach ($childs as $child) {
            $child = Mage::getModel('catalog/product')->load($child->getId());
            echo date('i:s', time() - $start) . ' ' . '--' . $child->getName() . '(' . $child->getSku() . ')' . "\n";

            if ($child->getVisibility() != '1') {
                $child->setVisibility('1');
                echo date('i:s', time() - $start) . ' ' . '--- Salvando produto' . "\n";
                $child->save();
                echo date('i:s', time() - $start) . " OK\n";
            }

            //Reviews
            $reviews = Mage::getModel('review/review')->getCollection()
                ->addEntityFilter('product', $child->getId());

            if ($reviews->count() > 0) {
                echo date('i:s', time() - $start) . ' ' . $reviews->count() . ' reviews' . "\n";
                foreach ($reviews as $review) {
                    $review->setEntityPkValue($product->getId());
                    $review->save();
                }
            }

            //Brands
            /*if ($child->getBrand() > 0 && $child->getBrand() != $product->getBrand()) {
                echo date('i:s', time() - $start) . ' ' . '--- Alterando marca do produto' . "\n";
                $product->setBrand($child->getBrand());
                $save_product = true;
            };*/

            //Categories
            if (count(array_diff($child->getCategoryIds(), $product->getCategoryIds())) > 0) {
                $categories = array_merge($product->getCategoryIds(), array_diff($child->getCategoryIds(), $product->getCategoryIds()));
                echo date('i:s', time() - $start) . ' ' . '--- Colocando produto nas categorias corretas [' . implode(',', $categories) . ']' . "\n";
                $product->setCategoryIds($categories);
                $save_product = true;
            }

            //Description
            if (trim($product->getDescription()) == '' && trim($child->getDescription()) != '') {
                echo date('i:s', time() - $start) . ' ' . '--- Alterando a descrição produto' . "\n";
                $product->setDescription($child->getDescription());
                $save_product = true;
            }

            //Gallery
            if (!$igore_images && count($product->getMediaGalleryImages()) < 1 && count($child->getMediaGalleryImages()) > 0) {
                echo date('i:s', time() - $start) . ' ' . '--- Adicionando imagens no produto' . "\n";
                $images = array();
                $baseDir = Mage::getSingleton('catalog/product_media_config')->getBaseMediaPath();
                foreach ($product->getMediaAttributes() as $imageAttribute) {
                    /** @var Mage_Catalog_Model_Resource_Eav_Attribute $imageAttribute */
                    $imageAttributeCode = $imageAttribute->getAttributeCode();
                    $file = $baseDir . $child->getData($imageAttributeCode);
                    if (file_exists($file)) {
                        if (!isset($images[$file])) {
                            $images[$file] = array();
                        }
                        $images[$file][] = $imageAttributeCode;
                    } else {
                        echo 'error : ' . $file . "\n";
                    }
                }
                foreach ($images as $file => $imageAttributeList) {
                    try {
                        $product->addImageToMediaGallery($file, $imageAttributeList, false, false);
                        $save_product = true;
                        $igore_images = true;
                    } catch (Exception $e) {
                        Mage::logException($e);

                    }
                }
            }

            //Short Description
            if (trim($product->getShortDescription()) == '' && trim($child->getShortDescription()) != '') {
                echo date('i:s', time() - $start) . ' ' . '--- Alterando a descrição curta do produto' . "\n";
                $product->setShortDescription($child->getShortDescription());
                $save_product = true;
            }

            //Image
            if (trim($product->getImage()) == '' && trim($child->getImage()) != '') {
                echo date('i:s', time() - $start) . ' ' . '--- Alterando a imagem do produto' . "\n";
                $product->setImage($child->getImage());
                $save_product = true;
            }

            //Small Image
            if (trim($product->getSmallImage()) == '' && trim($child->getSmallImage()) != '') {
                echo date('i:s', time() - $start) . ' ' . '--- Alterando a imagem pequena do produto' . "\n";
                $product->setSmallImage($child->getSmallImage());
                $save_product = true;
            }

            //Thumbnail
            if (trim($product->getThumbnail()) == '' && trim($child->getThumbnail()) != '') {
                echo date('i:s', time() - $start) . ' ' . '--- Alterando o "thumb" do produto' . "\n";
                $product->setThumbnail($child->getThumbnail());
                $save_product = true;
            }

            //Manufacturer
            /*if ($child->getManufacturer() > 0 && $child->getManufacturer() != $product->getManufacturer()) {
                echo date('i:s', time() - $start) . ' ' . '--- Alterando o fabricante do produto' . "\n";
                $product->setManufacturer($child->getManufacturer());
                $save_product = true;
            };*/

            /*$ignore = array('msrp_enabled', 'width', 'height', 'length', 'msrp_display_actual_price_type', 'initial_special_price', 'special_price', 'news_from_date', 'meta_title', 'category_ids', 'link_id', 'price', 'box_qty', 'initial_price');
            foreach ($child->getData() as $key => $data) {
                if (($product->getData($key) == '' || $product->getData($key) == null) && $data != $product->getData($key) && !in_array($key, $ignore))
                {
                    echo date('i:s', time() - $start) . ' ' . '--- Alterando ' . $key . '['.$data.']' .  "\n";
                    $product->setData($key, $data);
                    $save_product = true;
                }
            }*/
        }

        if ($save_product) {
            echo date('i:s', time() - $start) . ' ' . '--- Salvando produto' . "\n";
            try {
            $product->save();
            } catch (Exception $e) {
                Mage::logException($e);
            } catch (Mage_Exception $e) {
                Mage::logException($e);
            }
            echo date('i:s', time() - $start) . " OK\n";
        }
    }
    echo "\n";

}

echo date('i:s', time() - $start) . ' Bye';