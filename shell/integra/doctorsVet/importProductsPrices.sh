#!/bin/bash

BASEDIR=$(dirname $0)
cd $BASEDIR

scp -r doctorsvet@52.1.2.47:importer/table_prices/* /wwwroot/whitelabel/integracao/doctorsvet/importer/table_prices/
scp -r doctorsvet@52.1.2.47:importer/products/* /wwwroot/whitelabel/integracao/doctorsvet/importer/products/

php importProducts.php
php importPrices.php

PATH_FILES_CSV="/wwwroot/whitelabel/integracao/doctorsvet/"

DIA=`date +%d`

MES=`date +%m`
ANO=`date +%Y`
HORA=`date +%T`

cp "$PATH_FILES_CSV"importer/products/products.csv "$PATH_FILES_CSV"imported/products/products_"$ANO"_"$MES"_"$DIA"-"$HORA".csv
cp "$PATH_FILES_CSV"importer/table_prices/table_prices.csv "$PATH_FILES_CSV"imported/table_prices/tablePrices_"$ANO"_"$MES"_"$DIA"-"$HORA".csv

cd /usr/local/bin
echo "Importação de produtos executado em - $DIA/$MES/$ANO - $HORA">> /wwwroot/whitelabel/integracao/doctorsvet/report/logs/script_importProducts.log
echo "Importação da tabela de preços executado em - $DIA/$MES/$ANO - $HORA" >> /wwwroot/whitelabel/integracao/doctorsvet/report/logs/script_importPrices.log

scp -r /wwwroot/whitelabel/integracao/doctorsvet/report/logs/script_doctorsvet.log doctorsvet@52.1.2.47:report/logs/
scp -r /wwwroot/whitelabel/integracao/doctorsvet/imported/products/products_"$ANO"_"$MES"_"$DIA"* doctorsvet@52.1.2.47:imported/products/
scp -r /wwwroot/whitelabel/integracao/doctorsvet/imported/table_prices/tablePrices_"$ANO"_"$MES"_"$DIA"* doctorsvet@52.1.2.47:imported/table_prices/
