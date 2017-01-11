#!/bin/bash

BASEDIR=$(dirname $0)
cd $BASEDIR

scp -r -i /wwwroot/integracao/integracao mixpet@52.1.2.47:importer/promotions/* /wwwroot/integracao/mixpet/importer/promotions/

php importPromotions.php

PATH_FILES_CSV="/wwwroot/integracao/mixpet/"

cp "$PATH_FILES_CSV""importer/promotions/promotions.csv" "$PATH_FILES_CSV""imported/promotions_"$now".csv"

DIA=`date +%d`
MES=`date +%m`
ANO=`date +%Y`
HORA=`date +%T`
cd /usr/local/bin
echo "Integração de campanha executado em - $DIA/$MES/$ANO - $HORA" >> /var/log/script_mixpet.log
