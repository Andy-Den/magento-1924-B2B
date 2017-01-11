#!/bin/bash

BASEDIR=$(dirname $0)
cd $BASEDIR

DIA=`date +%d`
MES=`date +%m`
ANO=`date +%Y`
HORA=`date +%T`

echo "Reindex stock iniciado - $DIA/$MES/$ANO - $HORA" >> /var/log/integravet/reindex/stock.log
php /wwwroot/whitelabel/current/shell/indexer.php -reindex cataloginventory_stock
echo "Reindex stock finalizado - $DIA/$MES/$ANO - $HORA" >> /var/log/integravet/reindex/stock.log
