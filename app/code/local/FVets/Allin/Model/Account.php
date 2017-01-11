<?php

class FVets_Allin_Model_Account extends Mage_Core_Model_Abstract
{

	public function _construct()
	{
		$this->_init('fvets_allin/account');
	}

	public function createRemoteList() {
		if (!listExists($this->getListName())) {
			
		}
	}

	protected function listExists($listName) {
		return false;
	}

	protected function validateConnectionData() {
		return true;
	}
}
