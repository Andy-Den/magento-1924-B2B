<?php
/**
 * Created by PhpStorm.
 * User: julio
 * Date: 9/8/15
 * Time: 11:17 AM
 */

require_once './configScript.php';

$websiteId = '9';
$storeviewId = '17';

//limpando tabela de customers
$sql = "delete from customer_entity";
$writeConnection->query($sql);

//removendo todos usuários admin
$sql = "delete from admin_user";
$writeConnection->query($sql);

//removendo todas orders realizadas
$sql = "delete from sales_flat_order";
$writeConnection->query($sql);

////definindo o preço de todos os produtos para o valor 10
//$sql = "update catalog_product_index_price set price = 10, final_price = 10, min_price = 10, max_price = 10, tier_price = 10, group_price = 10";
//$writeConnection->query($sql);
//
////definindo o preço do index de todos os produtos para o valor 10
//$sql = "update catalog_product_index_price_idx set price = 10, final_price = 10, min_price = 10, max_price = 10, tier_price = 10, group_price = 10";
//$writeConnection->query($sql);

//criando um usuário de front para teste
$customer = Mage::getModel('customer/customer');

$customer->setNome('Customer Teste');
$customer->setEmail('admin@4vets.com.br');
$customer->setPassword('abc1234');
$customer->setCpf('55632322475');
$customer->setIdErp(0);
$customer->setData('mp_cc_is_approved', 1);
$customer->setWebsiteId($websiteId);
$customer->setStoreView($storeviewId);
$customer->save();

//criando um usuário de back para teste
$user = Mage::getModel("admin/user")
	->setUsername('admin')
	->setPassword('abc1234')
	->setFirstname('Customer')
	->setLastname('Teste')
	->setEmail('customerteste@4vets.com.br')
	->save();
$role = Mage::getModel("admin/role");
$role->setParentId(1);
$role->setTreeLevel(2);
$role->setRoleType('U');
$role->setRoleName('Admin');
$role->setUserId($user->getId());
$role->save();
$role = Mage::getModel("admin/role");
$role->setParentId(1);
$role->setTreeLevel(2);
$role->setRoleType('G');
$role->setRoleName('Admin');
$role->setUserId($user->getId());
$role->save();
