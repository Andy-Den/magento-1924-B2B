<?php

require_once './configScript.php';

/**
 * Created by PhpStorm.
 * User: julhets
 * Date: 08/05/15
 * Time: 22:03
 */

$sql = "delete from customer_entity_int where attribute_id = 195";
$writeConnection->query($sql);
