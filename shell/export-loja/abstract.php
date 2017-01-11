<?php

require_once '../../app/Mage.php';
umask(0);
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

ini_set('display_errors', 1);
ini_set('max_execution_time', 600);

$resource = Mage::getSingleton('core/resource');
$connection = $resource->getConnection('core_read');







$website_code = 'doctorsvet';

$stores_codes = array('doctorsvet');


$config_replace = array(
	array("'", "\\'"),
	array('http://http://www.comercialomega.com.br', 'http://www.doctorvetsonline.com.br'),
	array('comercialomega.com.br', 'doctorvetsonline.com.br'),
	array('www.comercialomega.com.br', 'www.doctorvetsonline.com.br'),
	array('http://betadoctorsvet.4vets.com.br', 'http://doctorsvet.4vets.com.br'),
	array('julio@4vets.com.br', 'ti+doctorsvet@4vets.com.br'),
	array('Envio peclam', 'Envio Doctorsvet'),
	array('Omega', 'Doctorsvet')
);


//php export.php > ../../app/code/local/FVets/Updater/sql/fvets_updater_setup/mysql4-upgrade-0.0.3-0.0.4.php

