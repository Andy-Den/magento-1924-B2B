#!/bin/bash

BASEDIR=$(dirname $0)

cd $BASEDIR

php exportOrders.php


cp /wwwroot/whitelabel/integracao/peclam/exporter/orders/* /wwwroot/whitelabel/integracao/peclam/exported/orders/

scp -r -i /wwwroot/whitelabel/integracao/integracao /wwwroot/whitelabel/integracao/peclam/exporter/orders/* peclam@52.1.2.47:exporter/orders/

rm /wwwroot/whitelabel/integracao/peclam/exporter/orders/*

DIA=`date +%d`
MES=`date +%m`
ANO=`date +%Y`
HORA=`date +%T`
cd /usr/local/bin
echo "Exportacao de pedidos executado em - $DIA/$MES/$ANO - $HORA" >> /wwwroot/whitelabel/integracao/peclam/report/logs/script_order.log   
 
scp -r -i /wwwroot/whitelabel/integracao/integracao /wwwroot/whitelabel/integracao/peclam/report/logs/script_orders.log peclam@52.1.2.47:report/logs/
