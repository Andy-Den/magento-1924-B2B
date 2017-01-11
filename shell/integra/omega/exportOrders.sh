#!/bin/bash

BASEDIR=$(dirname $0)
cd $BASEDIR

php exportOrders.php

PATH_FILES_CSV="/wwwroot/integracao/omega/"

cp "$PATH_FILES_CSV""exporter/orders/*" "$PATH_FILES_CSV""exported/orders/"

scp -r -i /wwwroot/integracao/integracao /wwwroot/integracao/omega/exporter/orders/* omega@52.1.2.47:exporter/orders/

rm /wwwroot/integracao/omega/exporter/orders/*

DIA=`date +%d`
MES=`date +%m`
ANO=`date +%Y`
HORA=`date +%T`
cd /usr/local/bin
echo "Exportação de pedidos executado em - $DIA/$MES/$ANO - $HORA" >> /wwwroot/integracao/omega/report/logs/script_orders.log

scp -r -i /wwwroot/integracao/integracao /wwwroot/integracao/omega/report/logs/script_orders.log omega@52.1.2.47:report/logs/