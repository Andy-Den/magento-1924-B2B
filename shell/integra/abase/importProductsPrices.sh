#!/bin/bash

BASEDIR=$(dirname $0)
cd $BASEDIR
scp -r -i /wwwroot/whitelabel/integracao/integracao abase@52.1.2.47:importer/products/* /wwwroot/whitelabel/integracao/abase/importer/products/
php importProducts.php

DIA=`date +%d`
MES=`date +%m`
ANO=`date +%Y`
HORA=`date +%T`
cp /wwwroot/whitelabel/integracao/abase/importer/products/products.csv /wwwroot/whitelabel/integracao/abase/imported/products/products_"$ANO"_"$MES"_"$DIA"-"$HORA".csv
#cp /wwwroot/whitelabel/integracao/abase/importer/groups/groups.csv /wwwroot/whitelabel/integracao/abase/imported/groups/groups_"$ANO"_"$MES"_"$DIA"-"$HORA".csv

BASEDIR=$(dirname $0)
cd $BASEDIR
scp -r -i /wwwroot/whitelabel/integracao/integracao abase@52.1.2.47:importer/table_prices/* /wwwroot/whitelabel/integracao/abase/importer/table_prices/
php importPrices.php

DIA=`date +%d`
MES=`date +%m`
ANO=`date +%Y`
HORA=`date +%T`

cp /wwwroot/whitelabel/integracao/abase/importer/table_prices/table_prices.csv /wwwroot/whitelabel/integracao/abase/imported/table_prices/tablePrices_"$ANO"_"$MES"_"$DIA"-"$HORA".csv

DIA=`date +%d`
MES=`date +%m`
ANO=`date +%Y`
HORA=`date +%T`
cd /usr/local/bin
echo "Importação de produtos executado em - $DIA/$MES/$ANO - $HORA" >> /wwwroowhitelabel/t/integracao/abase/report/logs/script_importProducts.log
echo "Importação de tabelas de preços executada em - $DIA/$MES/$ANO - $HORA" >> /wwot/whitelabel/integracao/abase/report/logs/script_importPrices.log

scp -r -i /wwwroot/whitelabel/integracao/integracao /wwwroot/whitelabel/integracao/abase/report/logs/script_importProducts.log abase@52.1.2.47:report/logs/
scp -r -i /wwwroot/whitelabel/integracao/integracao /wwwroot/whitelabel/integracao/abase/report/logs/script_importPrices.log abase@52.1.2.47:report/logs/
scp -r -i /wwwroot/whitelabel/integracao/integracao /wwwroot/whitelabel/integracao/abase/imported/table_prices/tablePrices_"$ANO"_"$MES"_"$DIA"* abase@52.1.2.47:imported/table_prices/
scp -r -i /wwwroot/whitelabel/integracao/integracao /wwwroot/whitelabel/integracao/abase/imported/products/products_"$ANO"_"$MES"_"$DIA"* abase@52.1.2.47:imported/products/
scp -r -i /wwwroot/whitelabel/integracao/integracao /wwwroot/whitelabel/integracao/abase/imported/products/products_"$ANO"_"$MES"_"$DIA"* abase@52.1.2.47:imported/products/
