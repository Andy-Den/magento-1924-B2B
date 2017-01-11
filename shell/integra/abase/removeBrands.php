<?php
require_once './configIntegra.php';
require_once '../_functions/importCustomers.php';

$lines = file("$directoryImp/customers/removeBrands.csv", FILE_IGNORE_NEW_LINES);

if (empty($lines)) :
    echo "\n\n" . "arquivo csv vazio ou não existe" . "\n\n";
else:

    foreach ($lines as $key => $value) {
        $i++;
        if  ( $key == 0 ) { }
        else {
            $temp = str_getcsv($value, '|', "'");
            $remoteId = $temp[0];
            $bioctal = $temp[1];
            $bayer = $temp[2];
            $zoetis = $temp[3];
            $hills = $temp[4];

            $setRemoveBrands = array($bioctal, $bayer, $zoetis, $hills);

            removeBrandsFromCustomers($resource, $writeConnection, $readConnection, $remoteId, $websiteId, $setRemoveBrands, $removeBrands);
        }
    }

endif;