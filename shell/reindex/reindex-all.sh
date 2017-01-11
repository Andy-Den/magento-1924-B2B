#!/bin/bash

BASEDIR=$(dirname $0)
cd $BASEDIR

DIA=`date +%d`
MES=`date +%m`
ANO=`date +%Y`
HORA=`date +%T`
cd /usr/local/bin
echo "Reindex all iniciado - $DIA/$MES/$ANO - $HORA" >> /var/log/integravet/reindex/all.log
php /wwwroot/whitelabel/current/shell/indexer.php -reindexall
echo "Reindex all finalizado - $DIA/$MES/$ANO - $HORA" >> /var/log/integravet/reindex/all.log
