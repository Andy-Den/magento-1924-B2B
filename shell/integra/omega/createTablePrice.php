<?php
require_once './configIntegra.php';

/**
 * Em função da dependência de clientes com suas respectivas tabelas de preços roda-se primeiro
 * a integração de tabela de preços
 */

// Primeiro pegamos todos os Ids dos Grupos enviados pelo ERP
// Montamos um arquivo dentro do diretório padrão de grupos e
// Montamos um array com os Ids dos grupos para fazer a comparação e na sequência a atualização
// Dos grupos que devem ser mantidos e ou criados
$lines = file("$directoryImp/table_prices/table_prices.csv", FILE_IGNORE_NEW_LINES);

if (empty($lines)){
    echo "\n\n" . "arquivo csv vazio ou não existe" . "\n\n";
} else {

    foreach ($lines as $key => $value) {
        $temp = str_getcsv($value, '|', "'");
        $idGroupErp = $temp[0];
        $nameGroupErp = $temp[1];
        $idErpProduct = $temp[2];
        $price = $temp[3];

        if (($price != '0,0000') && ($idGroupErp == 112 || $idGroupErp == 97)){
            $out .= "112+97|Purina A + MSD|$idErpProduct|$price\n";
        }

        if (($price != '0,0000') && ($idGroupErp == 112 || $idGroupErp == 98)){
            $out .= "112+98|Purina A + KA MSD|$idErpProduct|$price\n";
        }

        if (($price != '0,0000') && ($idGroupErp == 113 || $idGroupErp == 97)){
            $out .= "113+97|Purina B + MSD|$idErpProduct|$price\n";
        }

        if (($price != '0,0000') && ($idGroupErp == 113 || $idGroupErp == 98)){
            $out .= "113+98|Purina B + KA MSD|$idErpProduct|$price\n";
        }

        if (($price != '0,0000') && ($idGroupErp == 114 || $idGroupErp == 97)){
            $out .= "114+97|Purina C + MSD|$idErpProduct|$price\n";
        }

        if (($price != '0,0000') && ($idGroupErp == 114 || $idGroupErp == 98)){
            $out .= "114+98|Purina C + KA MSD|$idErpProduct|$price\n";
        }

        if (($price != '0,0000') && ($idGroupErp == 93 || $idGroupErp == 97)){
            $out .= "93+97|Purina D + MSD|$idErpProduct|$price\n";
        }

        if (($price != '0,0000') && ($idGroupErp == 93 || $idGroupErp == 98)){
            $out .= "93+98|Purina D + KA MSD|$idErpProduct|$price\n";
        }

    }
}

// criando o arquivo com os grupos enviados pelo ERP no diretório correspondente aos grupos
file_put_contents("$directoryImp/table_prices/table_prices_virtual.csv", $out);