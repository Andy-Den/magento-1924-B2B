#!/bin/bash

BASEDIR=$(dirname $0)
cd $BASEDIR
scp -r -i /wwwroot/integracao/integracao omega@52.1.2.47:importer/products/* /wwwroot/integracao/omega/importer/products/
php importProducts.php
php updateGroups.php

DIA=`date +%d`
MES=`date +%m`
ANO=`date +%Y`
HORA=`date +%T`
cp /wwwroot/integracao/omega/importer/products/products.csv /wwwroot/integracao/omega/imported/products/products_"$ANO"_"$MES"_"$DIA"-"$HORA".csv
cp /wwwroot/integracao/omega/importer/groups/groups.csv /wwwroot/integracao/omega/imported/groups/groups_"$ANO"_"$MES"_"$DIA"-"$HORA".csv

BASEDIR=$(dirname $0)
cd $BASEDIR
scp -r -i /wwwroot/integracao/integracao omega@52.1.2.47:importer/table_prices/* /wwwroot/integracao/omega/importer/table_prices/
php importPrices.php

DIA=`date +%d`
MES=`date +%m`
ANO=`date +%Y`
HORA=`date +%T`

cp /wwwroot/integracao/omega/importer/table_prices/table_prices.csv /wwwroot/integracao/omega/imported/table_prices/tablePrices_"$ANO"_"$MES"_"$DIA"-"$HORA".csv

BASEDIR=$(dirname $0)
cd $BASEDIR
php createTablePrice.php

rm /wwwroot/integracao/omega/importer/table_prices/table_prices.csv
cp /wwwroot/integracao/omega/importer/table_prices/table_prices_virtual.csv /wwwroot/integracao/omega/imported/table_prices/tablePricesVirtual_"$ANO"_"$MES"_"$DIA"-"$HORA".csv

php importPrices.php

rm /wwwroot/integracao/omega/importer/table_prices/table_prices_virtual.csv

DIA=`date +%d`
MES=`date +%m`
ANO=`date +%Y`
HORA=`date +%T`
cd /usr/local/bin
echo "Importação de produtos executado em - $DIA/$MES/$ANO - $HORA" >> /wwwroot/integracao/omega/report/logs/script_importProducts.log
echo "Importação de tabelas de preços executada em - $DIA/$MES/$ANO - $HORA" >> /wwwroot/integracao/omega/report/logs/script_importPrices.log

scp -r -i /wwwroot/integracao/integracao /wwwroot/integracao/omega/report/logs/script_importProducts.log omega@52.1.2.47:report/logs/
scp -r -i /wwwroot/integracao/integracao /wwwroot/integracao/omega/report/logs/script_importPrices.log omega@52.1.2.47:report/logs/
scp -r -i /wwwroot/integracao/integracao /wwwroot/integracao/omega/imported/table_prices/tablePrices_"$ANO"_"$MES"_"$DIA"* omega@52.1.2.47:imported/table_prices/
scp -r -i /wwwroot/integracao/integracao /wwwroot/integracao/omega/imported/products/products_"$ANO"_"$MES"_"$DIA"* omega@52.1.2.47:imported/products/
scp -r -i /wwwroot/integracao/integracao /wwwroot/integracao/omega/imported/products/products_"$ANO"_"$MES"_"$DIA"* omega@52.1.2.47:imported/products/