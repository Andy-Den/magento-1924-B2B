<?php

/**
 * Inclui os clientes da doctorsvet nos grupos de acesso corretos
 */

$installer = $this;

$installer->startSetup();

//Exclude not used groups
//Removido pq deu pau em produção, fazer na mão.

/*$groups = array(2, 3, 34, 35, 36, 37, 38, 40, 41, 42, 43, 44);


foreach ($groups as $group)
{
	$group = Mage::getModel('customer/group')->load($group);
	if ($group->getId())
	{
		$group->delete();
	}
}*/


$installer->endSetup();

