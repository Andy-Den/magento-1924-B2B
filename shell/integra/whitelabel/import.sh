#!/bin/bash

Principal() {
    echo "Selecione a opção"
    echo "1 para importar produtos"
    echo "2 para disponibilizar produtos para um website"
    echo
    echo -n "Qual opção?"
    echo
    read opcao
    case $opcao in
        1) Import ;;
        2) Connect ;;
        *) "Opção inválida" ; echo ; Principal ;;
    esac
}

Connect(){
    scp -r -i /wwwroot/integracao/integracao whitelabel@52.1.2.47:importer/products.csv /wwwroot/integracao/whitelabel/importer/
    php connectProducts.php

    PATH_FILES_CSV="/wwwroot/integracao/whitelabel/"

    DIA=`date +%d`
    MES=`date +%m`
    ANO=`date +%Y`
    HORA=`date +%T`
    cp "$PATH_FILES_CSV""importer/products.csv" "$PATH_FILES_CSV""imported/productsConnected_"$DIA"_"$MES"_"$ANO".csv"

    DIA=`date +%d`
    MES=`date +%m`
    ANO=`date +%Y`
    HORA=`date +%T`
    cd /usr/local/bin

    echo "Os produdutos foram disponibilizados em - $DIA/$MES/$ANO - $HORA" >> /wwwroot/integracao/whitelabel/report/logs/script_connectProducts.log
    scp -r -i /wwwroot/integracao/integracao /wwwroot/integracao/whitelabel/report/logs/script_connectProducts.log whitelabel@52.1.2.47:report/logs
    scp -r -i /wwwroot/integracao/integracao /wwwroot/integracao/whitelabel/imported/productsConnected_"$DIA"_"$MES"_"$ANO".csv whitelabel@52.1.2.47:imported/
}

Import(){
    scp -r -i /wwwroot/integracao/integracao whitelabel@52.1.2.47:importer/newProducts.csv /wwwroot/integracao/whitelabel/importer/
    php importProducts.php

    PATH_FILES_CSV="/wwwroot/integracao/whitelabel/"

    DIA=`date +%d`
    MES=`date +%m`
    ANO=`date +%Y`
    HORA=`date +%T`
    cp "$PATH_FILES_CSV""importer/newProducts.csv" "$PATH_FILES_CSV""imported/productsImported_"$DIA"_"$MES"_"$ANO".csv"

    DIA=`date +%d`
    MES=`date +%m`
    ANO=`date +%Y`
    HORA=`date +%T`
    cd /usr/local/bin

    echo "Os produdutos foram importados em - $DIA/$MES/$ANO - $HORA" >> /wwwroot/integracao/whitelabel/report/logs/script_importProducts.log
    scp -r -i /wwwroot/integracao/integracao /wwwroot/integracao/whitelabel/report/logs/script_importProducts.log whitelabel@52.1.2.47:report/logs
    scp -r -i /wwwroot/integracao/integracao /wwwroot/integracao/whitelabel/imported/productsImported_"$DIA"_"$MES"_"$ANO".csv whitelabel@52.1.2.47:imported/
}
Principal