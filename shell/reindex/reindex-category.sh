#!/bin/bash

BASEDIR=$(dirname $0)
cd $BASEDIR

DIA=`date +%d`
MES=`date +%m`
ANO=`date +%Y`
HORA=`date +%T`

echo "Reindex category iniciado - $DIA/$MES/$ANO - $HORA" >> /var/log/integravet/reindex/category.log
php /wwwroot/whitelabel/current/shell/indexer.php -reindex catalog_category_flat
echo "Reindex category finalizado - $DIA/$MES/$ANO - $HORA" >> /var/log/integravet/reindex/category.log
