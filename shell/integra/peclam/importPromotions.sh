#!/bin/bash

BASEDIR=$(dirname $0)
cd $BASEDIR

php importPromotions.php

PATH_FILES_CSV="/usr/local/chroot/peclamr/"

DIA=`date +%d`
MES=`date +%m`
ANO=`date +%Y`
HORA=`date +%T`
cd /usr/local/bin
echo "Importação de campanhas executado em - $DIA/$MES/$ANO - $HORA" >> /var/log/script_peclam.log