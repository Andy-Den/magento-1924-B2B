<?php
/**
 * Created by PhpStorm.
 * User: julhets
 * Date: 2/17/16
 * Time: 10:37 PM
 */

require_once '/wwwroot/whitelabel/current/shell/services/configService.php';

array_shift($argv);
list($data) = $argv;
$data = json_decode($data);

$usuario = $data->usuario;
$senha = $data->senha;

if (empty($usuario) || empty($senha)) {
  die('O usuário e senha não podem estar em branco.');
}

$resultAutenthicate = Mage::getModel('admin/user')->authenticate($usuario, $senha);

if ($resultAutenthicate) {
  $user = Mage::getModel('admin/user')->loadByUsername($usuario);
  $role = $user->getRole();
  $rule = Mage::getModel('amrolepermissions/rule')->load($role->getId(), 'role_id');

  $data = array();

  $data['websites'] = $rule->getScopeWebsites();
  $data['role_name'] = $role->getRoleName();

  die(json_encode(array('status' => 200, 'data' => $data)));
} else {
  die(json_encode(array('status' => 401, 'msg' => 'Usuário e senha não autenticados.')));
}
