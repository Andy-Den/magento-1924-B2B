<?php

class FVets_Sales_Model_Sales_Quote_Observer
{
    public function setQuoteItemOptionPackingIdErp($observer)
    {
        $item = $observer->getQuoteItem();
        $product = $item->getProduct();
        if ($optionIds = $product->getCustomOption('option_ids')) {
            foreach (explode(',', $optionIds->getValue()) as $optionId) {
                if ($option = $product->getOptionById($optionId)) {
                    $confItemOption = $product->getCustomOption('option_' . $option->getId());
                    $optionValues = $option->getValues();
                    //verifica se o option_type_id do produto que está na sessão do usuário é o mesmo option_type_id do produto no banco
                    if(isset($optionValues[$confItemOption->getValue()])) {
                        $item->setOptionIdErp($option->getValues()[$confItemOption->getValue()]->getIdErp());
                    } else {
                        //caso não seja (significa que o option_type_id foi removido do banco de dados) seta o id_erp atual do banco de dados
                        $item->setOptionIdErp(array_shift($optionValues)->getIdErp());
                    }
                    break;
                }
            }
        }
    }

    /**
     * Adds the product salesrep when an item was added to the order
     * @param $observer | Varien_Event_Observer
     */
    public function setOrderItemPackingIdErp($observer)
    {
        $item = $observer->getOrderItem();
        $product = $item->getProduct();
        if ($optionIds = $product->getCustomOption('option_ids')) {
            foreach (explode(',', $optionIds->getValue()) as $optionId) {
                if ($option = $product->getOptionById($optionId)) {
                    $confItemOption = $product->getCustomOption('option_' . $option->getId());
                    $item->setOptionIdErp($option->getValues()[$confItemOption->getValue()]->getIdErp());
                    break;
                }
            }
        }
    }
}