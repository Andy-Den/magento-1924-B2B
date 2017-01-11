#!/bin/bash

 BASEDIR=$(dirname $0)
 cd $BASEDIR

 php exportCustomers.php

 cp /wwwroot/integracao/peclam/exporter/customers/* /wwwroot/integracao/peclam/exported/customers/

 scp -r -i /wwwroot/integracao/integracao /wwwroot/integracao/peclam/exporter/customers/* peclam@52.1.2.47:exporter/customers/

 DIA=`date +%d`
 MES=`date +%m`
 ANO=`date +%Y`
 HORA=`date +%T`
 cd /usr/local/bin
 echo "Exportação de customers executado em - $DIA/$MES/$ANO - $HORA" >> /wwwroot/integracao/peclam/report/logs/script_customers.log

 scp -r -i /wwwroot/integracao/integracao /wwwroot/integracao/peclam/report/logs/script_customers.log peclam@52.1.2.47:report/logs/