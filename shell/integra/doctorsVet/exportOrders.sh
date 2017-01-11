#!/bin/bash

BASEDIR=$(dirname $0)
cd $BASEDIR

php exportOrders.php

PATH_FILES_CSV="/wwwroot/whitelabel/integracao/doctorsvet/"

cp "$PATH_FILES_CSV""exporter/orders/*" "$PATH_FILES_CSV""exported/orders/"

scp -r /wwwroot/whitelabel/integracao/doctorsvet/exporter/orders/* doctorsvet@52.1.2.47:exporter/orders/

rm /wwwroot/whitelabel/integracao/doctorsvet/exporter/orders/*

DIA=`date +%d`
MES=`date +%m`
ANO=`date +%Y`
HORA=`date +%T`
cd /usr/local/bin
echo "Exportação de pedidos executado em - $DIA/$MES/$ANO - $HORA" >> /wwwroot/whitelabel/integracao/doctorsvet/report/logs/script_orders.log

scp -r /wwwroot/whitelabel/integracao/doctorsvet/report/logs/script_orders.log doctorsvet@52.1.2.47:report/logs/
