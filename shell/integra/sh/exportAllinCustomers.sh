#!/bin/bash

BASEDIR=$(dirname $0)
cd $BASEDIR
php ../peclam/exportAllinCustomers.php
DIA=`date +%d`
MES=`date +%m`
ANO=`date +%Y`
HORA=`date +%T`
cd /usr/local/bin
echo "Exportação de dados de customers para o Allin via WebService - $DIA/$MES/$ANO - $HORA" >> /wwwroot/integracao/peclam/report/logs/script_allin.log
 scp -r -i /wwwroot/integracao/integracao /wwwroot/integracao/peclam/report/logs/script_allin.log peclam@52.1.2.47:report/logs/

BASEDIR=$(dirname $0)
cd $BASEDIR
php ../doctorsVet/exportAllinCustomers.php
DIA=`date +%d`
MES=`date +%m`
ANO=`date +%Y`
HORA=`date +%T`
cd /usr/local/bin
echo "Exportação de dados de customers para o Allin via WebService - $DIA/$MES/$ANO - $HORA" >> /wwwroot/integracao/doctorsvet/report/logs/script_allin.log
scp -r /wwwroot/integracao/doctorsvet/report/logs/script_allin.log doctorsvet@52.1.2.47:report/logs/

#BASEDIR=$(dirname $0)
#cd $BASEDIR
#php ../omega/exportAllinCustomers.php
#DIA=`date +%d`
#MES=`date +%m`
#ANO=`date +%Y`
#HORA=`date +%T`
#cd /usr/local/bin
#echo "Exportação de dados de customers para o Allin via WebService - $DIA/$MES/$ANO - $HORA" >> /wwwroot/integracao/omega/report/logs/script_allin.log
#scp -r -i /wwwroot/integracao/integracao /wwwroot/integracao/omega/report/logs/script_allin.log omega@52.1.2.47:report/logs/

BASEDIR=$(dirname $0)
cd $BASEDIR
php ../petvale/exportAllinCustomers.php
DIA=`date +%d`
MES=`date +%m`
ANO=`date +%Y`
HORA=`date +%T`
cd /usr/local/bin
echo "Exportação de dados de customers para o Allin via WebService - $DIA/$MES/$ANO - $HORA" >> /wwwroot/integracao/petvale/report/logs/script_allin.log
scp -r -i /wwwroot/integracao/integracao /wwwroot/integracao/petvale/report/logs/script_allin.log petvale@52.1.2.47:report/logs/

BASEDIR=$(dirname $0)
cd $BASEDIR
php ../abase/exportAllinCustomers.php
DIA=`date +%d`
MES=`date +%m`
ANO=`date +%Y`
HORA=`date +%T`
cd /usr/local/bin
echo "Exportação de dados de customers para o Allin via WebService - $DIA/$MES/$ANO - $HORA" >> /wwwroot/integracao/abase/report/logs/script_allin.log
scp -r -i /wwwroot/integracao/integracao /wwwroot/integracao/abase/report/logs/script_allin.log abase@52.1.2.47:report/logs/

BASEDIR=$(dirname $0)
cd $BASEDIR
php ../mixpet/exportAllinCustomers.php
DIA=`date +%d`
MES=`date +%m`
ANO=`date +%Y`
HORA=`date +%T`
cd /usr/local/bin
echo "Exportação de dados de customers para o Allin via WebService - $DIA/$MES/$ANO - $HORA" >> /wwwroot/integracao/mixpet/report/logs/script_allin.log
scp -r -i /wwwroot/integracao/integracao /wwwroot/integracao/mixpet/report/logs/script_allin.log mixpet@52.1.2.47:report/logs/

BASEDIR=$(dirname $0)
cd $BASEDIR
php ../disprolpet/exportAllinCustomers.php
DIA=`date +%d`
MES=`date +%m`
ANO=`date +%Y`
HORA=`date +%T`
cd /usr/local/bin
echo "Exportação de dados de customers para o Allin via WebService - $DIA/$MES/$ANO - $HORA" >> /wwwroot/integracao/disprolpet/report/logs/script_allin.log
scp -r -i /wwwroot/integracao/integracao /wwwroot/integracao/disprolpet/report/logs/script_allin.log disprolpet@52.1.2.47:report/logs/

BASEDIR=$(dirname $0)
cd $BASEDIR
php ../granda/exportAllinCustomers.php
DIA=`date +%d`
MES=`date +%m`
ANO=`date +%Y`
HORA=`date +%T`
cd /usr/local/bin
echo "Exportação de dados de customers para o Allin via WebService - $DIA/$MES/$ANO - $HORA" >> /wwwroot/integracao/granda/report/logs/script_allin.log
scp -r -i /wwwroot/integracao/integracao /wwwroot/integracao/granda/report/logs/script_allin.log granda@52.1.2.47:report/logs/

BASEDIR=$(dirname $0)
cd $BASEDIR
php ../vetcenter/exportAllinCustomers.php
DIA=`date +%d`
MES=`date +%m`
ANO=`date +%Y`
HORA=`date +%T`
cd /usr/local/bin
echo "Exportação de dados de customers para o Allin via WebService - $DIA/$MES/$ANO - $HORA" >> /wwwroot/integracao/vetcenter/report/logs/script_allin.log
scp -r -i /wwwroot/integracao/integracao /wwwroot/integracao/vetcenter/report/logs/script_allin.log vetcenter@52.1.2.47:report/logs/

BASEDIR=$(dirname $0)
cd $BASEDIR
php ../petvet/exportAllinCustomers.php
DIA=`date +%d`
MES=`date +%m`
ANO=`date +%Y`
HORA=`date +%T`
cd /usr/local/bin
echo "Exportação de dados de customers para o Allin via WebService - $DIA/$MES/$ANO - $HORA" >> /wwwroot/integracao/petvet/report/logs/script_allin.log
scp -r -i /wwwroot/integracao/integracao /wwwroot/integracao/petvet/report/logs/script_allin.log petvet@52.1.2.47:report/logs/
<<<<<<< HEAD
=======

BASEDIR=$(dirname $0)
cd $BASEDIR
php ../vetfaro/exportAllinCustomers.php
DIA=`date +%d`
MES=`date +%m`
ANO=`date +%Y`
HORA=`date +%T`
cd /usr/local/bin
echo "Exportação de dados de customers para o Allin via WebService - $DIA/$MES/$ANO - $HORA" >> /wwwroot/integracao/vetfaro/report/logs/script_allin.log
scp -r -i /wwwroot/integracao/integracao /wwwroot/integracao/vetfaro/report/logs/script_allin.log vetfaro@52.1.2.47:report/logs/
>>>>>>> 20cbd0c07756526b40c29a26840b0c1bd22c6fe9

BASEDIR=$(dirname $0)
cd $BASEDIR
php ../centralpet/exportAllinCustomers.php

BASEDIR=$(dirname $0)
cd $BASEDIR
php ../compet/exportAllinCustomers.php

BASEDIR=$(dirname $0)
cd $BASEDIR
php ../suprimed/exportAllinCustomers.php

BASEDIR=$(dirname $0)
cd $BASEDIR
php ../classic/exportAllinCustomers.php

BASEDIR=$(dirname $0)
cd $BASEDIR
php ../whitelabel/setaEmailsInvalidosAllin.php
php ../../scripts/datavalidate/log_allin_data.php
php ../../scripts/datavalidate/log_customers_data.php
php ../../scripts/datavalidate/log_orders.php