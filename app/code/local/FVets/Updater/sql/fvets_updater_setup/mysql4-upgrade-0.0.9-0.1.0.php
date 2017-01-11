<?php

/**
 * Inclui os clientes da doctorsvet nos grupos de acesso corretos
 */

$installer = $this;

$installer->startSetup();

$rewrite = Mage::getModel('core/url_rewrite');
// Attempt loading it first, to prevent duplicates:
$collection = $rewrite->getCollection()
	->addFieldToFilter('target_path', 'brandtype/index/index');

foreach ($collection as $rewrite)
{
	$rewrite->setTargetPath('attributemenu/index/index');
	$rewrite->save();

	$clone = clone $rewrite;

	$clone->setId(NULL);
	$clone->setIdPath(uniqid());
	$clone->setRequestPath('cat/'.$rewrite->getRequestPath());
	$clone->save();
}

$installer->endSetup();