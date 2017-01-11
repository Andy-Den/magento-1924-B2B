#!/bin/bash

BASEDIR=$(dirname $0)
cd $BASEDIR
scp -r -i /wwwroot/whitelabel/integracao/integracao petvale@52.1.2.47:importer/products/* /wwwroot/whitelabel/integracao/petvale/importer/products/
php importProducts.php

DIA=`date +%d`
MES=`date +%m`
ANO=`date +%Y`
HORA=`date +%T`
cp /wwwroot/whitelabel/integracao/petvale/importer/products/products.csv /wwwroot/whitelabel/integracao/petvale/imported/products/products_"$ANO"_"$MES"_"$DIA"-"$HORA".csv

BASEDIR=$(dirname $0)
cd $BASEDIR
scp -r -i /wwwroot/whitelabel/integracao/integracao petvale@52.1.2.47:importer/table_prices/* /wwwroot/whitelabel/integracao/petvale/importer/table_prices/
php importPrices.php

DIA=`date +%d`
MES=`date +%m`
ANO=`date +%Y`
HORA=`date +%T`

cp /wwwroot/whitelabel/integracao/petvale/importer/table_prices/table_prices.csv /wwwroot/whitelabel/integracao/petvale/imported/table_prices/tablePrices_"$ANO"_"$MES"_"$DIA"-"$HORA".csv

DIA=`date +%d`
MES=`date +%m`
ANO=`date +%Y`
HORA=`date +%T`
cd /usr/local/bin
echo "Importação de produtos executado em - $DIA/$MES/$ANO - $HORA" >> /wwwroot/whitelabel/integracao/petvale/report/logs/script_importProducts.log
echo "Importação de tabelas de preços executada em - $DIA/$MES/$ANO - $HORA" >> /wwwroot/whitelabel/integracao/petvale/report/logs/script_importPrices.log

scp -r -i /wwwroot/whitelabel/integracao/integracao /wwwroot/whitelabel/integracao/petvale/report/logs/script_importProducts.log petvale@52.1.2.47:report/logs/
scp -r -i /wwwroot/whitelabel/integracao/integracao /wwwroot/whitelabel/integracao/petvale/imported/table_prices/tablePrices_"$ANO"_"$MES"_"$DIA"* petvale@52.1.2.47:imported/table_prices/
scp -r -i /wwwroot/whitelabel/integracao/integracao /wwwroot/whitelabel/integracao/petvale/imported/products/products_"$ANO"_"$MES"_"$DIA"* petvale@52.1.2.47:imported/products/
scp -r -i /wwwroot/whitelabel/integracao/integracao /wwwroot/whitelabel/integracao/petvale/imported/products/products_"$ANO"_"$MES"_"$DIA"* petvale@52.1.2.47:imported/products/
