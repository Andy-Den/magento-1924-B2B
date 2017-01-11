#!/bin/bash

BASEDIR=$(dirname $0)
cd $BASEDIR

DIA=`date +%d`
MES=`date +%m`
ANO=`date +%Y`
HORA=`date +%T`

echo "Reindex catalogsearch iniciado - $DIA/$MES/$ANO - $HORA" >> /var/log/integravet/reindex/catalogsearch.log
php /wwwroot/whitelabel/current/shell/indexer.php -reindex catalogsearch_fulltext
echo "Reindex catalogsearch finalizado - $DIA/$MES/$ANO - $HORA" >> /var/log/integravet/reindex/catalogsearch.log
