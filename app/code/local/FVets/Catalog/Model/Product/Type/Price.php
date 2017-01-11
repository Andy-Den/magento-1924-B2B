<?php
class FVets_Catalog_Model_Product_Type_Price extends Mage_Catalog_Model_Product_Type_Price
{
    /**
     * Apply options price
     *
     * @param Mage_Catalog_Model_Product $product
     * @param int $qty
     * @param float $finalPrice
     * @return float
     */
    protected function _applyOptionsPrice($product, $qty, $finalPrice)
    {
        if ($optionIds = $product->getCustomOption('option_ids')) {
            $basePrice = $finalPrice;
            foreach (explode(',', $optionIds->getValue()) as $optionId) {
                if ($option = $product->getOptionById($optionId)) {
                    $confItemOption = $product->getCustomOption('option_'.$option->getId());

                    $group = $option->groupFactory($option->getType())
                        ->setOption($option)
                        ->setConfigurationItemOption($confItemOption);
                    $optionPrice = $group->getOptionPrice($confItemOption->getValue(), $basePrice);

                    $optionValues = $option->getValues();
                    //verifica se o option_type_id do produto que está na sessão do usuário é o mesmo option_type_id do produto no banco
                    if(isset($optionValues[$confItemOption->getValue()])) {
                        $action = $optionValues[$confItemOption->getValue()]->getAction();
                    } else {
                        //caso não seja (significa que o option_type_id foi removido do banco de dados) seta o id_erp atual do banco de dados
                        $action = array_shift($optionValues)->getAction();
                    }

                    if ($action == 'decrease'){
                        $finalPrice -= $optionPrice;
                    } else {
                        $finalPrice += $optionPrice;
                    }
                }
            }
        }

        return $finalPrice;
    }
}