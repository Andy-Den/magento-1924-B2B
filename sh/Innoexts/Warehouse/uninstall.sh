#!/bin/sh
CWD=$(pwd)

rm -fr $CWD/app/code/local/Innoexts/Core/
rm -fr $CWD/skin/adminhtml/default/default/template/innoexts/core/
rm -f $CWD/app/etc/modules/Innoexts_Core.xml
rm -f $CWD/app/locale/en_US/Innoexts_Core.csv
rm -fr $CWD/js/innoexts/core/
rm -fr $CWD/shell/Innoexts/Core/

rm -fr $CWD/app/code/local/Innoexts/GeoCoder/
rm -f $CWD/app/etc/modules/Innoexts_GeoCoder.xml
rm -f $CWD/app/locale/en_US/Innoexts_GeoCoder.csv

rm -fr $CWD/app/code/local/Innoexts/GeoIp/
rm -f $CWD/app/etc/modules/Innoexts_GeoIp.xml

rm -fr $CWD/app/code/local/Innoexts/CustomerLocator/
rm -f $CWD/app/design/frontend/base/default/layout/customerlocator.xml
rm -fr $CWD/app/design/frontend/base/default/template/customerlocator/
rm -f $CWD/app/design/frontend/default/iphone/layout/customerlocator.xml
rm -f $CWD/app/design/frontend/default/modern/layout/customerlocator.xml
rm -f $CWD/app/design/frontend/rwd/default/layout/customerlocator.xml
rm -f $CWD/app/etc/modules/Innoexts_CustomerLocator.xml
rm -f $CWD/app/locale/en_US/Innoexts_CustomerLocator.csv
rm -fr $CWD/skin/frontend/base/default/css/customerlocator/
rm -fr $CWD/skin/frontend/default/blank/css/customerlocator/
rm -fr $CWD/skin/frontend/default/blue/css/customerlocator/
rm -fr $CWD/skin/frontend/default/blue/images/customerlocator/
rm -fr $CWD/skin/frontend/default/default/css/customerlocator/
rm -fr $CWD/skin/frontend/default/default/images/customerlocator/
rm -fr $CWD/skin/frontend/default/iphone/css/customerlocator/
rm -fr $CWD/skin/frontend/default/modern/css/customerlocator/
rm -fr $CWD/skin/frontend/rwd/default/css/customerlocator/
rm -fr $CWD/sh/Innoexts/CustomerLocator/
rm -f $CWD/var/connect/Innoexts_CustomerLocator.xml

rm -fr $CWD/app/code/local/Innoexts/ShippingTablerate/
rm -f $CWD/app/design/adminhtml/default/default/layout/shippingtablerate.xml
rm -fr $CWD/app/design/adminhtml/default/default/template/shippingtablerate/
rm -f $CWD/app/etc/modules/Innoexts_ShippingTablerate.xml
rm -f $CWD/app/locale/en_US/Innoexts_ShippingTablerate.csv
rm -fr $CWD/skin/adminhtml/default/default/shippingtablerate/
rm -fr $CWD/skin/adminhtml/default/default/images/shippingtablerate/

rm -fr $CWD/app/code/local/Innoexts/Warehouse/
rm -f $CWD/app/design/adminhtml/default/default/layout/warehouse.xml
rm -fr $CWD/app/design/adminhtml/default/default/template/warehouse/
rm -f $CWD/app/design/frontend/base/default/layout/warehouse.xml
rm -fr $CWD/app/design/frontend/base/default/template/warehouse/
rm -f $CWD/app/design/frontend/default/iphone/layout/warehouse.xml
rm -fr $CWD/app/design/frontend/default/iphone/template/warehouse/
rm -f $CWD/app/design/frontend/default/modern/layout/warehouse.xml
rm -fr $CWD/app/design/frontend/default/modern/template/warehouse/
rm -f $CWD/app/design/frontend/rwd/default/layout/warehouse.xml
rm -fr $CWD/app/design/frontend/rwd/default/template/warehouse/
rm -f $CWD/app/etc/modules/Innoexts_Warehouse.xml
rm -f $CWD/app/locale/en_US/Innoexts_Warehouse.csv
rm -fr $CWD/app/locale/en_US/template/email/sales/warehouse/
rm -fr $CWD/js/innoexts/warehouse/
rm -fr $CWD/shell/Innoexts/Warehouse/
rm -fr $CWD/skin/adminhtml/default/default/warehouse/
rm -fr $CWD/skin/adminhtml/default/default/images/warehouse/
rm -fr $CWD/skin/frontend/base/default/js/warehouse/
rm -fr $CWD/skin/frontend/base/default/css/warehouse/
rm -fr $CWD/skin/frontend/default/blank/css/warehouse/
rm -fr $CWD/skin/frontend/default/blue/css/warehouse/
rm -fr $CWD/skin/frontend/default/blue/images/warehouse/
rm -fr $CWD/skin/frontend/default/default/css/warehouse/
rm -fr $CWD/skin/frontend/default/default/images/warehouse/
rm -fr $CWD/skin/frontend/default/iphone/css/warehouse/
rm -fr $CWD/skin/frontend/default/modern/css/warehouse/
rm -fr $CWD/skin/frontend/rwd/default/css/warehouse/
rm -fr $CWD/sql/Innoexts/Warehouse/
rm -fr $CWD/var/import/Innoexts/Warehouse/
rm -f $CWD/var/connect/Innoexts_Warehouse.xml