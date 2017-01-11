#!/bin/bash

BASEDIR=$(dirname $0)
cd $BASEDIR

scp -r -i /wwwroot/whitelabel/integracao/integracao peclam@52.1.2.47:importer/products.csv /wwwroot/whitelabel/integracao/peclam/importer/

php importProducts.php
php importPrices.php

PATH_FILES_CSV="/wwwroot/whitelabel/integracao/peclam/"

DIA=`date +%d`
MES=`date +%m`
ANO=`date +%Y`
HORA=`date +%T`

cp "$PATH_FILES_CSV""importer/products.csv" "$PATH_FILES_CSV""imported/products_"$ANO"_"$MES"_"$DIA"_"$HORA".csv"

DIA=`date +%d`
MES=`date +%m`
ANO=`date +%Y`
HORA=`date +%T`
cd /usr/local/bin
echo "Importação e de produtos executado em - $DIA/$MES/$ANO - $HORA" >> /wwwroot/whitelabel/integracao/peclam/report/logs/script_products.log

scp -r -i /wwwroot/whitelabel/integracao/integracao /wwwroot/whitelabel/integracao/peclam/report/logs/script_products.log peclam@52.1.2.47:report/logs/
scp -r -i /wwwroot/whitelabel/integracao/integracao /wwwroot/whitelabel/integracao/peclam/imported/products_"$ANO"_"$MES"_"$DIA"* peclam@52.1.2.47:imported/
