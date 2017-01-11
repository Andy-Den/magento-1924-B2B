<?php

require_once './configScript.php';

/**
 * Created by PhpStorm.
 * User: julhets
 * Date: 08/05/15
 * Time: 22:03
 */

$removeLabels = "DELETE FROM " . $resource->getTableName(tm_prolabels_rules) . " where label_name like '%doctorsVet%'";

$writeConnection->query($removeLabels);