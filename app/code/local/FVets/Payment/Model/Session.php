<?php
	class FVets_Payment_Model_Session extends Mage_Core_Model_Session_Abstract
	{
		private $_condition;

		public function setCondition($condition)
		{
			$this->_condition = $condition;
		}

		public function getCondition()
		{
			return $this->_condition;
		}
	}