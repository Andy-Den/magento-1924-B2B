#!/bin/bash

BASEDIR=$(dirname $0)
cd $BASEDIR

scp -r -i /wwwroot/integracao/integracao omega@52.1.2.47:importer/customers/customers.csv /wwwroot/integracao/omega/importer/customers/
php importCustomers.php
php updateOrigemCustomer.php

PATH_FILES_CSV="/wwwroot/integracao/omega/"

DIA=`date +%d`
MES=`date +%m`
ANO=`date +%Y`
HORA=`date +%T`
cp "$PATH_FILES_CSV""importer/customers/customers.csv" "$PATH_FILES_CSV""imported/customers_"$now".csv"

DIA=`date +%d`
MES=`date +%m`
ANO=`date +%Y`
HORA=`date +%T`
cd /usr/local/bin
echo "Integração de customers e atualização da origem executado em - $DIA/$MES/$ANO - $HORA" >> /wwwroot/integracao/omega/report/logs/script_importCustomers.log
scp -r /wwwroot/integracao/omega/report/logs/script_importCustomers.log omega@52.1.2.47:report/logs/
scp -r /wwwroot/integracao/omega/imported/customers/customers_"$ANO"_"$MES"_"$DIA"* omega@52.1.2.47:imported/customers/