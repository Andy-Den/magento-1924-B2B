#!/bin/bash

BASEDIR=$(dirname $0)
cd $BASEDIR

php exportCustomers.php

PATH_FILES_CSV="/wwwroot/integracao/petvale/"

cp "$PATH_FILES_CSV""exporter/customers/*" "$PATH_FILES_CSV""exported/customers/"

scp -r /wwwroot/integracao/petvale/exporter/customers/* petvale@52.1.2.47:exporter/customers/

DIA=`date +%d`
MES=`date +%m`
ANO=`date +%Y`
HORA=`date +%T`
cd /usr/local/bin
echo "Exportação de customers executado em - $DIA/$MES/$ANO - $HORA" >> /wwwroot/integracao/petvale/report/logs/script_customers.log

scp -r /wwwroot/integracao/doctorsvet/report/logs/script_customers.log doctorsvet@52.1.2.47:report/logs/