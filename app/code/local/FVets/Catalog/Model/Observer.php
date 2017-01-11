<?php

class FVets_Catalog_Model_Observer
{
    public function validateProductCustomerAccess(Varien_Event_Observer $observer)
    {
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $customerRestrictedBrands = explode(',', $customer->getRestrictedBrands());
        $salesrepIds = $customer->getFvetsSalesrep();
        $product = Mage::getModel('catalog/product')->load((int)Mage::app()->getRequest()->getParam('id'));
        $cats = $product->getCategoryIds();

        $canShowProduct = false;

        //filtrando pelas marcas que o(s) rep(s) vende(m)
        if ($salesrepIds) {
            foreach (explode(',', $salesrepIds) as $salesrepId) {
                $salesrep = Mage::getModel('fvets_salesrep/salesrep')->load($salesrepId);
                $categories = $salesrep->getSelectedCategoriesCollection();
                foreach ($categories as $category) {
                    foreach ($cats as $cat) {
                        if ($cat == $category->getId()) {
                            $canShowProduct = true;
                            break;
                        }
                    }
                    if ($canShowProduct) {
                        break;
                    }
                }
                if ($canShowProduct) {
                    break;
                }
            }
        }

        //verificando se cliente possui restrição com a marca do produto
        if ($canShowProduct) {
            foreach ($customerRestrictedBrands as $customerRestrictedBrand) {
                if (in_array($customerRestrictedBrand, $cats)) {
                    $canShowProduct = false;
                }
            }
        }

        //verificando se o cliente possui grupos de restrição e se o grupo contém o produto
        if ($canShowProduct) {
            $customerHelper = Mage::helper('fvets_catalogrestrictiongroup/customer');
            $productHelper = Mage::helper('fvets_catalogrestrictiongroup/product');
            $customerRestrictionGroups = $customerHelper->getSelectedCatalogrestrictiongroups($customer);
            $productRestrictionGroups = $productHelper->getSelectedCatalogrestrictiongroups($product);

            foreach ($customerRestrictionGroups as $customerRestrictionGroup) {
                foreach ($productRestrictionGroups as $productRestrictionGroup) {
                    if ($productRestrictionGroup->getId() == $customerRestrictionGroup->getId()) {
                        $canShowProduct = false;
                        break;
                    }
                }
                if (!$canShowProduct) {
                    break;
                }
            }
        }

        if (!$canShowProduct) {
            header("Status: 301");
            header('Location: ' . Mage::getBaseUrl());
            Mage::getSingleton('customer/session')->addError("Você não pode acessar esse produto.");
            exit;
        }
    }
}