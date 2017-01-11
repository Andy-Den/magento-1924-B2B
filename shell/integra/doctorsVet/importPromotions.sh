#!/bin/bash

BASEDIR=$(dirname $0)
cd $BASEDIR

scp -r doctorsvet@52.1.2.47:importer/promotions/promotions.csv /wwwroot/integracao/doctorsvet/importer/promotions/

php importPromotions.php

PATH_FILES_CSV="/wwwroot/integracao/doctorsvet/"

DIA=`date +%d`
MES=`date +%m`
ANO=`date +%Y`
HORA=`date +%T`
cp "$PATH_FILES_CSV"importer/promotions/promotions.csv "$PATH_FILES_CSV"imported/promotions/promotions_"ANO"_"MES"_"DIA"-"HORA".csv

DIA=`date +%d`
MES=`date +%m`
ANO=`date +%Y`
HORA=`date +%T`
cd /usr/local/bin
echo "Integração de campanha executado em - $DIA/$MES/$ANO - $HORA" >> /var/log/script_doctorsvet.log
