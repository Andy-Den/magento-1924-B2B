<?php

class FVets_Salesrep_Model_Source_Brands extends Mage_Eav_Model_Entity_Attribute_Source_Table
{

    public function getAllOptions()
    {
        if ($this->_options === null)
		{
			$param = (Mage::app()->getRequest()->getParam('id')) ? Mage::app()->getRequest()->getParam('id') : Mage::app()->getRequest()->getParam('customer_id') ;
			$customer =  Mage::getModel("customer/customer")->load($param);

            $reps = Mage::getResourceModel('fvets_salesrep/salesrep_collection')
				->addFieldToFilter('id', array('in' => explode(',',$customer->getFvetsSalesrep())))
			;

			$reps->getSelect()->__toString();
			$this->_options = array();
			foreach($reps as $rep)
			{
				foreach ($rep->getSelectedCategoriesCollection(false) as $category)
				{
					$this->_options[$category->getId()] = [
						'value' => $category->getId(),
						'label' => $category->getName()
					];
				}
			}
        }

		usort($this->_options, 'sortByLabel');

        return $this->_options;
    }

}

function sortByLabel($a, $b) {
	return strcasecmp ( $a['label'] , $b['label'] );
}
