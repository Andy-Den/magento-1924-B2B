<?php
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 5/6/15
 * Time: 11:34 AM
 */
require_once './configScript.php';

$users = array(
	"gattopet@hotmail.com" => "gattopet@hotmail.com",
	"122388.174@cofrenfe.com.br" => "karoamigo@yahoo.com.br",
	"SAUAIA@SUPERIG.COM.BR" => "sac@caopestre.com.br",
	"dogwalkerreal@gmail.com" => "eduardonishida@uol.com.br",
	"luh.tkm@gmail.com" => "buenovetrx@gmail.com",
	"LUCIANA@ESPACOPRACACHORRO.COM.BR" => "adm@espacopracachorro.com.br",
	"RMJVET@GMAIL.COM" => "magelavet@gmail.com",
	"CANILELSHADAY@GLOBO.COM" => "canilmenorah@globo.com",
	"LOJAANIMALPLACE@HOTMAIL.COM" => "animalplace@hotmail.com",
	"melazzo.sud@gmail.com.br" => "melazzo.sud@gmail.com",
	"ANAEPATO70@GMAIL.COM.BR" => "vetguainazes@yahoo.com.br",
	"LIDOBEM@HOTMAIL.COM" => "contato@petdobem.com",
	"PARAISO_DOS_ANIMAIS@HOTMAIL.COM" => "paraiso_dosanimais@hotmail.com",
	"JOVAIAFREITAS@IG.COM.BR" => "jeovafreitas@ig.com.br",
	"VAGNERWILLIAN_@HOTMAIL.COM" => "vagnerwillian@hotmail.com",
	"SERGIOMELO769@HOTMAIL.COM" => "SERGIOMELO769@HOTMAIL.COM",
	"pshopcantodouro@gmail.com" => "petshop.cantodouro@gmail.com",
	"Fernanda.omega.msd@gmail.com" => "fcoelhomiranda@bol.com.br",
	"contato@clinicoveerinaria.com.br" => "contato@clinicoveterinaria.com.br",
	"PETSHOPTREMENBE@HOTMAIL.COM" => "petshoptremembe@hotmail.com",
	"FLAVIO.MACHARELLI@BOL.COM.BR" => "flavio.macharelli@yahoo.com.br",
	"GRAZISOFIA80@GMAIL.COM" => "sac@caopestre.com.br",
	"FERNANDAMONTEIRO.VENDAS@HOTMAIL.COM" => "caopescador@hotmail.com",
	"FERNANDA.ABM@HOTMAIL.COM" => "joaquimjorgedacosta@yahoo.com.br",
	"secretaria@clubinhodopet.com" => "cvalphaville@bol.com.br"
);
$users = array_change_key_case($users, CASE_LOWER);
$users = array_map('strtolower', $users);
$customersAlterados = 0;

$customers = Mage::getModel('customer/customer')
	->getCollection()
	->addAttributeToFilter('website_id', $websiteId)
	->addAttributeToFilter('email', array('in' => array_keys($users)));

foreach ($customers as $customer) {
	//echo $customer->getId() . ' <> ' . $customer->getEmail() . ' <> ' . $users[strtolower($customer->getEmail())] . "\n";
	$customer->load();
	$customer->setEmail($users[strtolower($customer->getEmail())]);
	try {
		$customer->save();
		$customersAlterados ++;
	} catch (Exception $ex) {
		//echo $ex->getMessage() . "\n";
	}
}

echo "WebsiteId: " . $websiteId . " <> Customers Alterados:" . $customersAlterados . "\n";