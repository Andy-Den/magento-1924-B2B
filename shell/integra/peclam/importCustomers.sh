#!/bin/bash

BASEDIR=$(dirname $0)
cd $BASEDIR

scp -r -i /wwwroot/whitelabel/integracao/integracao peclam@52.1.2.47:importer/customers.csv /wwwroot/whitelabel/integracao/peclam/importer/
php importCustomers.php

PATH_FILES_CSV="/wwwroot/whitelabel/integracao/peclam/"

DIA=`date +%d`
MES=`date +%m`
ANO=`date +%Y`
HORA=`date +%T`
cp "$PATH_FILES_CSV"importer/customers.csv "$PATH_FILES_CSV"imported/customers_"$ANO"_"$MES"_"$DIA"-"$HORA".csv

DIA=`date +%d`
MES=`date +%m`
ANO=`date +%Y`
HORA=`date +%T`
cd /usr/local/bin
echo "Integração de customers e atualização da origem executado em - $DIA/$MES/$ANO - $HORA" >> /wwwroot/whitelabel/integracao/peclam/report/logs/script_importCustomers.log
scp -r -i /wwwroot/whitelabel/integracao/integracao /wwwroot/whitelabel/integracao/peclam/report/logs/script_importCustomers.log peclam@52.1.2.47:report/logs/
scp -r -i /wwwroot/whitelabel/integracao/integracao /wwwroot/whitelabel/integracao/peclam/imported/customers_"$ANO"_"$MES"_"$DIA"* peclam@52.1.2.47:/home/peclam/imported/customers/

