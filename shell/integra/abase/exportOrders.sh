#!/bin/bash

BASEDIR=$(dirname $0)
cd $BASEDIR

php exportOrders.php

scp -r -i /wwwroot/whitelabel/integracao/integracao /wwwroot/whitelabel/integracao/abase/exporter/orders/* abase@52.1.2.47:exporter/orders/
scp -r -i /wwwroot/whitelabel/integracao/integracao /wwwroot/whitelabel/integracao/abase/exporter/orders/* abase@52.1.2.47:exported/orders/

rm /wwwroot/whitelabel/integracao/abase/exporter/orders/*

DIA=`date +%d`
MES=`date +%m`
ANO=`date +%Y`
HORA=`date +%T`
cd /usr/local/bin
echo "Exportação de pedidos executado em - $DIA/$MES/$ANO - $HORA" >> /wwwroot/whitelabel/integracao/abase/report/logs/script_orders.log

scp -r -i /wwwroot/whitelabel/integracao/integracao /wwwroot/whitelabel/integracao/abase/report/logs/script_orders.log abase@52.1.2.47:report/logs/
