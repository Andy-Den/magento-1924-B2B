#!/bin/bash

BASEDIR=$(dirname $0)
cd $BASEDIR

scp -r -i /wwwroot/whitelabel/integracao/integracao abase@repo.integravet.com.br:importer/customers/customers.csv /wwwroot/whitelabel/integracao/abase/importer/customers/
php importCustomers.php
#php updateOrigemCustomer.php

PATH_FILES_CSV="/wwwroot/whitelabel/integracao/abase/"

DIA=`date +%d`
MES=`date +%m`
ANO=`date +%Y`
HORA=`date +%T`
cp /wwwroot/whitelabel/integracao/abase/importer/customers/customers.csv /wwwroot/whitelabel/integracao/abase/imported/customers/customers_"$ANO"_"$MES"_"$DIA"-"$HORA".csv

DIA=`date +%d`
MES=`date +%m`
ANO=`date +%Y`
HORA=`date +%T`
cd /usr/local/bin
echo "Integração de customers e atualização da origem executado em - $DIA/$MES/$ANO - $HORA" >> /wwwroot/whitelabel/integracao/abase/report/logs/script_importCustomers.log
scp -r -i /wwwroot/whitelabel/integracao/integracao /wwwroot/whitelabel/integracao/abase/report/logs/script_importCustomers.log abase@52.1.2.47:report/logs/
scp -r -i /wwwroot/whitelabel/integracao/integracao /wwwroot/whitelabel/integracao/abase/imported/customers/customers_"$ANO"_"$MES"_"$DIA"* abase@52.1.2.47:imported/customers/
