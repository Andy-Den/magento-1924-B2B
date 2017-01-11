#!/bin/bash

BASEDIR=$(dirname $0)
cd $BASEDIR

scp -r doctorsvet@52.1.2.47:importer/customers/customers.csv /wwwroot/whitelabel/integracao/doctorsvet/importer/customers/
php importCustomers.php

PATH_FILES_CSV="/wwwroot/whitelabel/integracao/doctorsvet/"

DIA=`date +%d`
MES=`date +%m`
ANO=`date +%Y`
HORA=`date +%T`
cp "$PATH_FILES_CSV"importer/customers/customers.csv "$PATH_FILES_CSV"imported/customers/customers_"$ANO"_"$MES"_"$DIA"-"$HORA".csv

DIA=`date +%d`
MES=`date +%m`
ANO=`date +%Y`
HORA=`date +%T`
cd /usr/local/bin
echo "Integração de customers e atualização da origem executado em - $DIA/$MES/$ANO - $HORA" >> /wwwroot/whitelabel/integracao/doctorsvet/report/logs/script_importCustomers.log
scp -r /wwwroot/whitelabel/integracao/doctorsvet/report/logs/script_importCustomers.log doctorsvet@52.1.2.47:report/logs/
scp -r /wwwroot/whitelabel/integracao/doctorsvet/imported/customers/customers_"$ANO"_"$MES"_"$DIA"* doctorsvet@52.1.2.47:imported/customers/
