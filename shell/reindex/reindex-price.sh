#!/bin/bash

BASEDIR=$(dirname $0)
cd $BASEDIR

DIA=`date +%d`
MES=`date +%m`
ANO=`date +%Y`
HORA=`date +%T`

echo "Reindex price iniciado - $DIA/$MES/$ANO - $HORA" >> /var/log/integravet/reindex/price.log
php /wwwroot/whitelabel/current/shell/indexer.php -reindex catalog_product_price
echo "Reindex price finalizado - $DIA/$MES/$ANO - $HORA" >> /var/log/integravet/reindex/price.log
