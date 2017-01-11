<?php

require_once 'abstract.php';



$output = null;


$output .= '<?php

$installer = $this;

$installer->startSetup();

Mage::register("isSecureArea", 1);

// Force the store to be admin
Mage::app()->setUpdateMode(false);
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);';


/* Website config */

$website = Mage::getResourceModel('core/website_collection')->addFieldToFilter('code', $website_code)->getData()[0];


$query = 'SELECT
			scope,
			\'{$website["website_id"]}\' as website_id,
			path,
			value
			FROM core_config_data
			WHERE scope = \'websites\'
			AND scope_id = '.$website['website_id'].';';

$results = $connection->fetchAll($query);

$select_output = null;
foreach($results as $result)
{
	//Faz um replace das string nas configurações
	foreach($config_replace as $replace)
	{
		$result['value'] = str_replace($replace[0], $replace[1], $result['value']);
	}
	$select_output .= "\n\t\t('{$result['scope']}', '{$result['website_id']}', '{$result['path']}', '{$result['value']}'),";
}
$select_output = substr($select_output, 0, strlen($select_output) - 1);

$output .= '
/* Install Omega Website Config */

$website = Mage::getResourceModel(\'core/website_collection\')->addFieldToFilter(\'code\', \''.$website_code.'\')->getData()[0];

if (isset($website[\'website_id\']))
{
	$installer->run("
		DELETE FROM core_config_data
		WHERE scope = \'websites\'
		AND scope_id = {$website[\'website_id\']};
	;");

	$installer->run("
		INSERT INTO `core_config_data` (`scope`, `scope_id`, `path`, `value`) VALUES
		'.$select_output.'
	;");
}';

/* Store config */

foreach ($stores_codes as $store_code)
{
	$store = Mage::getResourceModel('core/store_collection')->addFieldToFilter('code', $store_code)->getData()[0];

	$query = 'SELECT
				scope,
				\'{$store["store_id"]}\' as store_id,
				path,
				value
				FROM core_config_data
				WHERE scope = \'stores\'
				AND scope_id = '.$store['store_id'].';';

	$results = $connection->fetchAll($query);

	$select_output = null;
	foreach($results as $result)
	{
		//Faz um replace das string nas configurações
		foreach($config_replace as $replace)
		{
			$result['value'] = str_replace($replace[0], $replace[1], $result['value']);
		}
		$select_output .= "\n\t\t('{$result['scope']}', '{$result['store_id']}', '{$result['path']}', '{$result['value']}'),";
	}
	$select_output = substr($select_output, 0, strlen($select_output) - 1);

	$output .= '
	/* Install Omega Website Config */

	$store = Mage::getResourceModel(\'core/store_collection\')->addFieldToFilter(\'code\', \''.$store_code.'\')->getData()[0];

	if (isset($store[\'store_id\']))
	{
		$installer->run("
			DELETE FROM core_config_data
			WHERE scope = \'stores\'
			AND scope_id = {$store[\'store_id\']};
		;");

		$installer->run("
			INSERT INTO `core_config_data` (`scope`, `scope_id`, `path`, `value`) VALUES
			'.$select_output.'
		;");
	}';

}


$output .= '

$installer->endSetup();

';

echo $output;