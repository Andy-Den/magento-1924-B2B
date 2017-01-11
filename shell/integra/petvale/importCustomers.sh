#!/bin/bash

BASEDIR=$(dirname $0)
cd $BASEDIR

scp -r -i /wwwroot/whitelabel/integracao/integracao petvale@52.1.2.47:importer/customers/customers.csv /wwwroot/whitelabel/integracao/petvale/importer/customers/
php importCustomers.php
php updateOrigemCustomer.php

PATH_FILES_CSV="/wwwroot/whitelabel/integracao/petvale/"

DIA=`date +%d`
MES=`date +%m`
ANO=`date +%Y`
HORA=`date +%T`
cp /wwwroot/whitelabel/integracao/petvale/importer/customers/customers.csv /wwwroot/whitelabel/integracao/petvale/imported/customers/customers_"$ANO"_"$MES"_"$DIA"-"$HORA".csv

DIA=`date +%d`
MES=`date +%m`
ANO=`date +%Y`
HORA=`date +%T`
cd /usr/local/bin
echo "Integração de customers e atualização da origem executado em - $DIA/$MES/$ANO - $HORA" >> /wwwroot/whitelabel/integracao/petvale/report/logs/script_importCustomers.log
scp -r -i /wwwroot/whitelabel/integracao/integracao /wwwroot/whitelabel/integracao/petvale/report/logs/script_importCustomers.log petvale@52.1.2.47:report/logs/
scp -r -i /wwwroot/whitelabel/integracao/integracao /wwwroot/whitelabel/integracao/petvale/imported/customers/customers_"$ANO"_"$MES"_"$DIA"* petvale@52.1.2.47:imported/customers/
