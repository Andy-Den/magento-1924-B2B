<?php

function getCommission($resource, $readConnection, $websiteId) {
    $comissions = [];

    $getCommissionErp = "SELECT `value` FROM ";
    $getCommissionErp .= $resource->getTableName(core_config_data);
    $getCommissionErp .= " WHERE path = 'comissions/general/erp_comission_value' AND scope_id = $websiteId;";

    $erp = $readConnection->fetchOne($getCommissionErp);

    $getCommissionSite = "SELECT `value` FROM ";
    $getCommissionSite .= $resource->getTableName(core_config_data) ;
    $getCommissionSite .= " WHERE path = 'comissions/general/site_comission_value' AND scope_id = $websiteId;";

    $site = $readConnection->fetchOne($getCommissionSite);

    $commissions = ['erp' => $erp, 'site' => $site];

    return $commissions;

}

function addCustomer($resource, $readConnection, $writeConnection, $websiteId, $storeId, $storeViewAll, $currentDateFormated, $atualizaEmail, $idGroup, $codeStore, $idErp, $name, $tipoPessoa, $emailErp, $password, $cnpj, $cpf, $ramoAtividadeIntegra, $inscricaoEstadual, $razaoSocial, $fantasia, $telefone, $state, $bairro, $city, $cep, $street, $telefone, $status, $lastOrder, $lastPurchasePrice, $commission) {
    // Define o código da distribuidora quando o o grupo não for definido
    if (!$idGroup):
        $idGroup = $codeStore;
    endif;

    /* Coloca o nome no padrão do Magento */
    if (!$name) {
        $name = $razaoSocial;
    }

    $name = explode(' ', $name, 2);
    $firstName = str_replace("'", "`", $name[0]);
    $lastName = str_replace("'", "`", $name[1]);

    /* define o tipo de negócio do cliente */
    if ($tipoPessoa == 1):
        $tipoPessoa = "PF";
    elseif ($tipoPessoa == 2):
        $tipoPessoa = 'PJ';
    elseif ($tipoPessoa == 3):
        $tipoPessoa = 'RC';
    endif;

    // Validação do email informado pelo ERP
    $er = "/^(([0-9a-zA-Z]+[-._+&])*[0-9a-zA-Z]+@([-0-9a-zA-Z]+[.])+[a-zA-Z]{2,6}){0,1}$/";

    // cadastro somente para customers com email válido

    if (preg_match($er, $emailErp) && $emailErp != '') {
        // Verifica se o email que está sendo informado pelo ERP pertence a outro cliente
        $searchEmail = "SELECT cev.`value` FROM ";
        $searchEmail .= $resource->getTableName(customer_entity) . " AS ce";
        $searchEmail .= " INNER JOIN customer_entity_varchar AS cev";
        $searchEmail .= " ON ce.entity_id = cev.entity_id";
        $searchEmail .= " WHERE ce.email = '$emailErp' AND cev.attribute_id = 183 AND ce.store_id in ($storeViewAll);";

        $returnIdErp = $readConnection->fetchOne($searchEmail);

        if (($returnIdErp == $idErp) || ($returnIdErp == false)) {
            // Pega o group_id setado para o cliente
            $getIdGroup = "SELECT customer_group_id FROM ";
            $getIdGroup .= $resource->getTableName(customer_group);
            $getIdGroup .= " WHERE id_tabela = '$idGroup';";

            $idGroup = $readConnection->fetchOne($getIdGroup);

//                // Faz o Dê Para dos ramos de atividade da distribuidora para o nosso
//                if ($ramoAtividade == 1): $ramoAtividadeIntegra = 4;
//                endif; // Agropecuária
//                if ($ramoAtividade == 6): $ramoAtividadeIntegra = 1;
//                endif; // Avicultura
//                if ($ramoAtividade == 3): $ramoAtividadeIntegra = NULL;
//                endif; // Funcionário
//                if ($ramoAtividade == 4): $ramoAtividadeIntegra = NULL;
//                endif; // Pessoa Física
//                if ($ramoAtividade == 5): $ramoAtividadeIntegra = 1;
//                endif; // Pet Shop
//                if ($ramoAtividade == 8): $ramoAtividadeIntegra = 2;
//                endif; // Pet Shop/Clinica Veterinária
//                if ($ramoAtividade == 9): $ramoAtividadeIntegra = 1;
//                endif; // Canil/Hoteis
//                if ($ramoAtividade == 7): $ramoAtividadeIntegra = 2;
//                endif; // Clinica Veterinária/Consultorio
//                if ($ramoAtividade == 10): $ramoAtividadeIntegra = 2;
//                endif; // Hospital Veterinario
//                if ($ramoAtividade == 11): $ramoAtividadeIntegra = 1;
//                endif; // Banho e tosa
//                if ($ramoAtividade == 12): $ramoAtividadeIntegra = 1;
//                endif; // Pet Shop/Banho e tosa
//                if ($ramoAtividade == 13): $ramoAtividadeIntegra = 1;
//                endif; // Pet Shop Completo

            /**
             * Procura pelo id_erp do cliente cliente para atualização das informações
             * Caso não encontre o id_erp pesquisa pelo email sempre validando o website_id
             */
            $getEntityId = "SELECT ce.entity_id FROM ";
            $getEntityId .= $resource->getTableName(customer_entity_varchar) . " as cev";
            $getEntityId .= " INNER JOIN " . $resource->getTableName(customer_entity) . " as ce ";
            $getEntityId .= "ON cev.entity_id = ce.entity_id ";
            $getEntityId .= "WHERE cev.attribute_id = 183 AND cev.`value` = '$idErp' AND ce.website_id = $websiteId";

            $entityId = $readConnection->fetchOne($getEntityId);

            /**
             * Se não encontra pelo id_erp procura pelo email do enviado pelo ERP tanto pelo email principal
             * como pelo email secundário
             */
            if (!$entityId) {
                $getEntityId = "SELECT entity_id FROM ";
                $getEntityId .= $resource->getTableName(customer_entity);
                $getEntityId .= " WHERE email = '$emailErp' AND website_id = $websiteId";

                $entityId = $readConnection->fetchOne($getEntityId);
            }

            if ($state){
                // Tratando o Estado do cliente
                $getIdState = "SELECT region_id, default_name FROM ";
                $getIdState .= $resource->getTableName(directory_country_region);
                $getIdState .= " WHERE code='$state';";

                $dataState = $readConnection->fetchAll($getIdState);
                $idState = $dataState[0]['region_id'];
                $nameState = $dataState[0]['default_name'];
            }

            if (!$entityId) {
                /**
                 * caso não encontre o usuário adiciona um entity_id para esse novo usuário
                 */
                if ($entityId == false) {
                    $addUser = "INSERT INTO ";
                    $addUser .= $resource->getTableName(customer_entity);
                    $addUser .= " (entity_type_id, attribute_set_id, website_id, email, increment_id, store_id, created_at, is_active, disable_auto_group_change, mp_cc_is_approved) ";
                    $addUser .= "VALUES(1, 0, $websiteId, '$emailErp', NULL, $storeId, STR_TO_DATE($currentDateFormated,'%Y-%m-%d %H:%i:%s'), $status, 0, 1);";

                    $writeConnection->query($addUser);

                    // pega o entity_id do usuário criado recentemente para cadastro do id Erp
                    $getEntityId = "SELECT entity_id FROM ";
                    $getEntityId .= $resource->getTableName(customer_entity);
                    $getEntityId .= " WHERE email = '$emailErp' AND store_id = $storeId;";

                    $entityId = $readConnection->fetchOne($getEntityId);

                    if (($entityId == true) && ($idErp == true)) {
                        // cadastra o id_erp
                        $addIdErp = "INSERT INTO ";
                        $addIdErp .= $resource->getTableName(customer_entity_varchar);
                        $addIdErp .= " (entity_type_id, attribute_id, entity_id, `value`) ";
                        $addIdErp .= "VALUES(1, 183, $entityId, '$idErp');";

                        $writeConnection->query($addIdErp);

                        // Cadastra a senha padrão para o novo cliente
                        $addPass = "INSERT INTO ";
                        $addPass .= $resource->getTableName(customer_entity_varchar);
                        $addPass .= " (entity_type_id, attribute_id, entity_id, `value`)";
                        $addPass .= "VALUES(1, 12, $entityId, '$password');";

                        $writeConnection->query($addPass);
                    } else {
                        echo "\n\nO cliente recentemente cadastrado não foi localizado para adicionar o id_erp.\n\n";
                    }

                    // alimenta o arquivo de relatório da integração clienteAdicionado.csv
                    echo "\n\n O $idErp com o $emailErp foi criado. \n\n";
                }
                // compara o email cadastrado com email enviado pelo ERP e atualiza se estiver habilitado conconfig
                if ($atualizaEmail == false) {

                    $getMainEmail = "SELECT email FROM ";
                    $getMainEmail .= $resource->getTableName(customer_entity);
                    $getMainEmail .= " WHERE entity_id = $entityId;";

                    $emailAtual = $readConnection->fetchOne($getMainEmail);

                    if ($emailAtual != $emailErp) {
                        // verifica se já existe um email secundário e atualiza ou cria
                        $getSecondEmail = "SELECT `value` FROM ";
                        $getSecondEmail .= $resource->getTableName(customer_entity_varchar);
                        $getSecondEmail .= " WHERE attribute_id = 193 AND entity_id = $entityId;";

                        $secondEmail = $readConnection->fetchOne($getSecondEmail);

                        if (($secondEmail == true) || (is_null($secondEmail))) {
                            $transfereEmail = "UPDATE ";
                            $transfereEmail .= $resource->getTableName(customer_entity_varchar);
                            $transfereEmail .= " SET `value` = '$emailAtual'";
                            $transfereEmail .= " WHERE attribute_id = 193 AND entity_id = $entityId";

                            if ($atualizaEmail == true):
                                $writeConnection->query($transfereEmail);
                                updateDateCustomer($resource, $writeConnection, $currentDateFormated, $websiteId, $entityId);
                            endif;
                        } else {
                            $addSecondEmail = "INSERT INTO ";
                            $addSecondEmail .= $resource->getTableName(customer_entity_varchar);
                            $addSecondEmail .= " (entity_type_id, attribute_id, entity_id, `value`)";
                            $addSecondEmail .= " VALUES(1, 193, $entityId, '$emailAtual');";

                            if ($atualizaEmail == true):
                                $writeConnection->query($addSecondEmail);
                                updateDateCustomer($resource, $writeConnection, $currentDateFormated, $websiteId, $entityId);
                            endif;
                        }
                    }
                }

                // Adiciona ou atualiza o CNPJ do cliente
                if ($cnpj) {
                    $getCnpj = "SELECT `value` FROM ";
                    $getCnpj .= $resource->getTableName(customer_entity_varchar);
                    $getCnpj .= " WHERE attribute_id = 136 AND entity_id = $entityId;";

                    $cnpjAtual = $readConnection->fetchOne($getCnpj);

                    if (($cnpjAtual == false) && (!is_null($cnpjAtual))) {
                        $addCnpj = "INSERT INTO ";
                        $addCnpj .= $resource->getTableName(customer_entity_varchar);
                        $addCnpj .= " (entity_type_id, attribute_id, entity_id, `value`) ";
                        $addCnpj .= "VALUES(1, 136, $entityId, '$cnpj');";

                        $writeConnection->query($addCnpj);
                    } elseif (($cnpjAtual != $cnpj) || (is_null($cnpjAtual))) {
                        $updateCnpj = "UPDATE ";
                        $updateCnpj .= $resource->getTableName(customer_entity_varchar);
                        $updateCnpj .= " SET `value` = '$cnpj'";
                        $updateCnpj .= " WHERE entity_id = $entityId AND attribute_id = 136;";

                        $writeConnection->query($updateCnpj);

                        updateDateCustomer($resource, $writeConnection, $currentDateFormated, $websiteId, $entityId);
                    }
                }
                // Adiciona ou atualiza o CPF do cliente
                if ($cpf) {
                    $getCpf = "SELECT `value` FROM ";
                    $getCpf .= $resource->getTableName(customer_entity_varchar);
                    $getCpf .= " WHERE attribute_id = 134 AND entity_id = $entityId;";

                    $cpfAtual = $readConnection->fetchOne($getCpf);

                    if ($cpfAtual == false) {
                        $addCpf = "INSERT INTO ";
                        $addCpf .= $resource->getTableName(customer_entity_varchar);
                        $addCpf .= " (entity_type_id, attribute_id, entity_id, `value`) ";
                        $addCpf .= "VALUES(1, 134, $entityId, '$cpf');";

                        $writeConnection->query($addCpf);
                    } elseif ($cpfAtual != $cpf) {
                        $updateCpf = "UPDATE ";
                        $updateCpf .= $resource->getTableName(customer_entity_varchar);
                        $updateCpf .= " SET `value` = '$cpf'";
                        $updateCpf .= " WHERE entity_id = $entityId AND attribute_id = 134;";

                        $writeConnection->query($updateCpf);

                        updateDateCustomer($resource, $writeConnection, $currentDateFormated, $websiteId, $entityId);
                    }
                } elseif ($cpf == false) {
                    $deleteCpf = "DELETE FROM ";
                    $deleteCpf .= $resource->getTableName(customer_entity_varchar);
                    $deleteCpf .= " WHERE entity_id = $entityId AND attribute_id = 134";

                    $writeConnection->query($deleteCpf);
                }
                // Adiciona ou atualiza firstname
                if ($firstName) {
                    $getFirstName = "SELECT `value` FROM ";
                    $getFirstName .= $resource->getTableName(customer_entity_varchar);
                    $getFirstName .= " WHERE attribute_id = 5 AND entity_id = $entityId;";

                    $firstNameAtual = $readConnection->fetchOne($getFirstName);

                    if ($firstNameAtual == false) {
                        $addFirstName = "INSERT INTO ";
                        $addFirstName .= $resource->getTableName(customer_entity_varchar);
                        $addFirstName .= " (entity_type_id, attribute_id, entity_id, `value`) ";
                        $addFirstName .= "VALUES(1, 5, $entityId, '$firstName');";

                        $writeConnection->query($addFirstName);
                    } elseif ($firstNameAtual != $firstName) {
                        $updateFirstNameAtual = "UPDATE ";
                        $updateFirstNameAtual .= $resource->getTableName(customer_entity_varchar);
                        $updateFirstNameAtual .= " SET `value` = '$firstName'";
                        $updateFirstNameAtual .= " WHERE entity_id = $entityId AND attribute_id = 5;";

                        $writeConnection->query($updateFirstNameAtual);

                        updateDateCustomer($resource, $writeConnection, $currentDateFormated, $websiteId, $entityId);
                    }
                }
                // Adiciona ou atualiza lastName
                if ($lastName) {
                    $getLastName = "SELECT `value` FROM ";
                    $getLastName .= $resource->getTableName(customer_entity_varchar);
                    $getLastName .= " WHERE attribute_id = 7 AND entity_id = $entityId;";

                    $lastNameAtual = $readConnection->fetchAll($getLastName)[0];

                    if ($lastNameAtual == false) {
                        $addLastName = "INSERT INTO ";
                        $addLastName .= $resource->getTableName(customer_entity_varchar);
                        $addLastName .= " (entity_type_id, attribute_id, entity_id, `value`) ";
                        $addLastName .= "VALUES(1, 7, $entityId, '$lastName');";

                        $writeConnection->query($addLastName);
                    } elseif ($lastNameAtual['value'] != $lastName) {
                        $updateLastNameAtual = "UPDATE ";
                        $updateLastNameAtual .= $resource->getTableName(customer_entity_varchar);
                        $updateLastNameAtual .= " SET `value` = '$lastName'";
                        $updateLastNameAtual .= " WHERE entity_id = $entityId AND attribute_id = 7;";

                        $writeConnection->query($updateLastNameAtual);

                        updateDateCustomer($resource, $writeConnection, $currentDateFormated, $websiteId, $entityId);
                    }
                }
                // Define o Ramo de atividade do cliente
                if ($ramoAtividadeIntegra) {
                    $getIdRamoAtividade = "SELECT `value` FROM ";
                    $getIdRamoAtividade .= $resource->getTableName(customer_entity_text);
                    $getIdRamoAtividade .= " WHERE attribute_id = 192 AND entity_id = $entityId;";

                    $idRamoAtividadeAtual = $readConnection->fetchOne($getIdRamoAtividade);

                    if (!$idRamoAtividadeAtual) {

                        $addRamoAtividade = "INSERT INTO ";
                        $addRamoAtividade .= $resource->getTableName(customer_entity_text);
                        $addRamoAtividade .= " (entity_type_id, attribute_id, entity_id, `value`) ";
                        $addRamoAtividade .= "VALUES(1, 192, $entityId, $ramoAtividadeIntegra);";

                        $writeConnection->query($addRamoAtividade);
                    } elseif ($idRamoAtividadeAtual != $ramoAtividadeIntegra) {
                        $updateRamoAtividade = "UPDATE ";
                        $updateRamoAtividade .= $resource->getTableName(customer_entity_text);
                        $updateRamoAtividade .= " SET `value` = $ramoAtividadeIntegra";
                        $updateRamoAtividade .= " WHERE entity_id = $entityId AND attribute_id = 192;";

                        $writeConnection->query($updateRamoAtividade);

                        updateDateCustomer($resource, $writeConnection, $currentDateFormated, $websiteId, $entityId);
                    }
                }
                // Adiciona ou altera o n. da inscrição estadual
                if ($inscricaoEstadual) {
                    $getInscricaoEstadual = "SELECT `value` FROM ";
                    $getInscricaoEstadual .= $resource->getTableName(customer_entity_varchar);
                    $getInscricaoEstadual .= " WHERE attribute_id = 138 AND entity_id = $entityId;";

                    $inscricaoEstadualAtual = $readConnection->fetchOne($getInscricaoEstadual);

                    if (($inscricaoEstadualAtual == false) && (!is_null($inscricaoEstadualAtual))) {
                        $addInscricaoEstadual = "INSERT INTO ";
                        $addInscricaoEstadual .= $resource->getTableName(customer_entity_varchar);
                        $addInscricaoEstadual .= " (entity_type_id, attribute_id, entity_id, `value`) ";
                        $addInscricaoEstadual .= "VALUES(1, 138, $entityId, '$inscricaoEstadual');";

                        $writeConnection->query($addInscricaoEstadual);
                    } elseif (($inscricaoEstadualAtual != $inscricaoEstadual)) {
                        $updateInscricaoEstadualAtual = "UPDATE ";
                        $updateInscricaoEstadualAtual .= $resource->getTableName(customer_entity_varchar);
                        $updateInscricaoEstadualAtual .= " SET `value` = '$inscricaoEstadual'";
                        $updateInscricaoEstadualAtual .= " WHERE entity_id = $entityId AND attribute_id = 138;";

                        $writeConnection->query($updateInscricaoEstadualAtual);

                        updateDateCustomer($resource, $writeConnection, $currentDateFormated, $websiteId, $entityId);
                    }
                }
                // Adiciona ou altera a razão social
                if ($razaoSocial) {
                    $getRazaoSocial = "SELECT `value` FROM ";
                    $getRazaoSocial .= $resource->getTableName(customer_entity_varchar);
                    $getRazaoSocial .= " WHERE attribute_id = 137 AND entity_id = $entityId;";

                    $razaoSocialAtual = $readConnection->fetchOne($getRazaoSocial);

                    if (($razaoSocialAtual == false) && (!is_null($razaoSocialAtual))) {
                        $addRazaoSocial = "INSERT INTO ";
                        $addRazaoSocial .= $resource->getTableName(customer_entity_varchar);
                        $addRazaoSocial .= " (entity_type_id, attribute_id, entity_id, `value`) ";
                        $addRazaoSocial .= "VALUES(1, 137, $entityId, '$razaoSocial');";

                        $writeConnection->query($addRazaoSocial);
                    } elseif ($razaoSocialAtual != $razaoSocial) {
                        $updateRazaoSocialAtual = "UPDATE ";
                        $updateRazaoSocialAtual .= $resource->getTableName(customer_entity_varchar);
                        $updateRazaoSocialAtual .= " SET `value` = '$razaoSocial'";
                        $updateRazaoSocialAtual .= " WHERE entity_id = $entityId AND attribute_id = 137;";

                        $writeConnection->query($updateRazaoSocialAtual);

                        updateDateCustomer($resource, $writeConnection, $currentDateFormated, $websiteId, $entityId);
                    }
                }
                // define nome fantasia para o cliente
                if ($fantasia) {
                    $getFantasia = "SELECT `value` FROM ";
                    $getFantasia .= $resource->getTableName(customer_entity_varchar);
                    $getFantasia .= " WHERE entity_id = $entityId AND attribute_id = 198;";

                    $fantasiaAtual = $readConnection->fetchOne($getFantasia);

                    if (($fantasiaAtual == false) && (!is_null($fantasiaAtual))) {
                        $addFantasia = "INSERT INTO ";
                        $addFantasia .= $resource->getTableName(customer_entity_varchar);
                        $addFantasia .= " (entity_type_id, attribute_id, entity_id, `value`) ";
                        $addFantasia .= "VALUES(1, 198, $entityId, '$fantasia');";

                        $writeConnection->query($addFantasia);
                    } elseif ($fantasiaAtual != $fantasia) {
                        $updateFantasia = "UPDATE ";
                        $updateFantasia .= $resource->getTableName(customer_entity_varchar);
                        $updateFantasia .= " SET `value` = '$fantasia'";
                        $updateFantasia .= " WHERE entity_id = $entityId AND attribute_id = 198;";

                        $writeConnection->query($updateFantasia);

                        updateDateCustomer($resource, $writeConnection, $currentDateFormated, $websiteId, $entityId);
                    }
                }
                // Adiciona ou altera o telefone
                if ($telefone) {
                    $getTelefone = "SELECT `value` FROM ";
                    $getTelefone .= $resource->getTableName(customer_entity_varchar);
                    $getTelefone .= " WHERE attribute_id = 135 AND entity_id = $entityId;";

                    $telefoneAtual = $readConnection->fetchOne($getTelefone);

                    if ($telefoneAtual == false) {
                        $addTelefone = "INSERT INTO ";
                        $addTelefone .= $resource->getTableName(customer_entity_varchar);
                        $addTelefone .= " (entity_type_id, attribute_id, entity_id, `value`) ";
                        $addTelefone .= "VALUES(1, 135, $entityId, '$telefone');";

                        $writeConnection->query($addTelefone);
                    } elseif ($telefoneAtual != $telefone) {
                        $updateTelefoneAtual = "UPDATE ";
                        $updateTelefoneAtual .= $resource->getTableName(customer_entity_varchar);
                        $updateTelefoneAtual .= " SET `value` = '$telefone'";
                        $updateTelefoneAtual .= " WHERE entity_id = $entityId AND attribute_id = 135;";

                        $writeConnection->query($updateTelefoneAtual);

                        updateDateCustomer($resource, $writeConnection, $currentDateFormated, $websiteId, $entityId);
                    }
                }

                // Adiciona ou altera o tipo pessoa
                if ($tipoPessoa) {
                    $getTipoPessoa = "SELECT `value` FROM ";
                    $getTipoPessoa .= $resource->getTableName(customer_entity_varchar);
                    $getTipoPessoa .= " WHERE attribute_id = 133 AND entity_id = $entityId;";

                    $tipoPessoaAtual = $readConnection->fetchOne($getTipoPessoa);

                    if ($tipoPessoaAtual == false) {
                        $addTipoPessoa = "INSERT INTO ";
                        $addTipoPessoa .= $resource->getTableName(customer_entity_varchar);
                        $addTipoPessoa .= " (entity_type_id, attribute_id, entity_id, `value`) ";
                        $addTipoPessoa .= "VALUES(1, 133, $entityId, '$tipoPessoa');";
                        $writeConnection->query($addTipoPessoa);

                    } elseif ($tipoPessoaAtual != $tipoPessoa) {
                        $updateTipoPessoaAtual = "UPDATE ";
                        $updateTipoPessoaAtual .= $resource->getTableName(customer_entity_varchar);
                        $updateTipoPessoaAtual .= " SET `value` = '$tipoPessoa'";
                        $updateTipoPessoaAtual .= " WHERE entity_id = $entityId AND attribute_id = 133;";

                        $writeConnection->query($updateTipoPessoaAtual);

                        updateDateCustomer($resource, $writeConnection, $currentDateFormated, $websiteId, $entityId);
                    }
                }

                // Adciona a comissão do cliente
                if ($commission) {
                    $setCommission = "INSERT INTO ";
                    $setCommission .= $resource->getTableName(customer_entity_decimal);
                    $setCommission .= " (entity_type_id, attribute_id, entity_id `value`) ";
                    $setCommission .= " VALUES (1, 216, $entityId, $commission);";

                    $writeConnection->query($setCommission);
                }

                // Pega o entity_id da tabela de endereços referente ao cliente
                $getEntityIdAddress = "SELECT entity_id FROM ";
                $getEntityIdAddress .= $resource->getTableName(customer_address_entity);
                $getEntityIdAddress .= " WHERE parent_id = $entityId;";

                $entityIdAddress = $readConnection->fetchOne($getEntityIdAddress);

                if ($entityIdAddress == false) {
                    $addEntityAddress = "INSERT INTO ";
                    $addEntityAddress .= $resource->getTableName(customer_address_entity);
                    $addEntityAddress .= " (entity_type_id, attribute_set_id, increment_id, parent_id, created_at, updated_at, is_active) ";
                    $addEntityAddress .= "VALUES(2, 0, NULL, $entityId, STR_TO_DATE($currentDateFormated,'%Y-%m-%d %H:%i:%s'), STR_TO_DATE($currentDateFormated,'%Y-%m-%d %H:%i:%s'), 1);";

                    $writeConnection->query($addEntityAddress);

                    $getEntityIdAddress = "SELECT entity_id FROM ";
                    $getEntityIdAddress .= $resource->getTableName(customer_address_entity);
                    $getEntityIdAddress .= " WHERE parent_id = $entityId;";

                    $entityIdAddress = $readConnection->fetchOne($getEntityIdAddress);

                    // define o pais do cliente padrão Brasil - BR
                    $setCountry = "INSERT IGNORE INTO ";
                    $setCountry .= $resource->getTableName(customer_address_entity_varchar);
                    $setCountry .= " (entity_type_id, attribute_id, entity_id, `value`)";
                    $setCountry .= " VALUES (2, 27, $entityIdAddress, 'BR');";

                    $writeConnection->query($setCountry);
                }

                // Adiciona o Estado do cliente
                if ($idState) {
                    $getIdState = "SELECT `value` FROM ";
                    $getIdState .= $resource->getTableName(customer_address_entity_int);
                    $getIdState .= " WHERE entity_id = $entityIdAddress AND attribute_id = 29;";

                    $idStateAtual = $readConnection->fetchOne($getIdState);

                    if ($idStateAtual == false) {
                        $addIdState = "INSERT INTO ";
                        $addIdState .= $resource->getTableName(customer_address_entity_int);
                        $addIdState .= " (entity_type_id, attribute_id, entity_id, `value`) ";
                        $addIdState .= "VALUES(2, 29, $entityIdAddress, $idState);";

                        $writeConnection->query($addIdState);
                    }
                }

                // Adiciona ou atualiza o Estado do cliente
                if ($state) {
                    $getState = "SELECT `value` FROM ";
                    $getState .= $resource->getTableName(customer_address_entity_varchar);
                    $getState .= " WHERE entity_id = $entityIdAddress AND attribute_id = 28;";

                    $stateAtual = $readConnection->fetchOne($getState);

                    if ($stateAtual == false) {
                        $addState = "INSERT INTO ";
                        $addState .= $resource->getTableName(customer_address_entity_varchar);
                        $addState .= " (entity_type_id, attribute_id, entity_id, `value`) ";
                        $addState .= "VALUES(2, 28, $entityIdAddress, '$nameState');";

                        $writeConnection->query($addState);
                    }
                }

                // Adiciona o Bairro do cliente
                if ($bairro) {
                    $getBairro = "SELECT `value` FROM ";
                    $getBairro .= $resource->getTableName(customer_address_entity_varchar);
                    $getBairro .= " WHERE entity_id = $entityIdAddress AND attribute_id = 142;";

                    $bairroAtual = $readConnection->fetchOne($getBairro);

                    if (!$bairroAtual) {
                        $addBairro = "INSERT INTO ";
                        $addBairro .= $resource->getTableName(customer_address_entity_varchar);
                        $addBairro .= " (entity_type_id, attribute_id, entity_id, `value`) ";
                        $addBairro .= "VALUES(2, 142, $entityIdAddress, '$bairro');";

                        $writeConnection->query($addBairro);
                    }
                }

                // Adiciona a Cidade do cliente
                if ($city) {
                    $getCity = "SELECT `value` FROM ";
                    $getCity .= $resource->getTableName(customer_address_entity_varchar);
                    $getCity .= " WHERE entity_id = $entityIdAddress AND attribute_id = 26;";

                    $cityAtual = $readConnection->fetchOne($getCity);

                    if ($cityAtual == false) {
                        $addCity = "INSERT INTO ";
                        $addCity .= $resource->getTableName(customer_address_entity_varchar);
                        $addCity .= " (entity_type_id, attribute_id, entity_id, `value`) ";
                        $addCity .= "VALUES(2, 26, $entityIdAddress, '$city');";

                        $writeConnection->query($addCity);
                    }
                }

                // Adiciona ou atualiza o CEP do cliente
                if ($cep) {
                    $getCep = "SELECT `value` FROM ";
                    $getCep .= $resource->getTableName(customer_address_entity_varchar);
                    $getCep .= " WHERE entity_id = $entityIdAddress AND attribute_id = 30;";

                    $cepAtual = $readConnection->fetchOne($getCep);

                    //Retira todos os caracteres nao numericos para que possa fazer a comparaçao mais precisa.
                    $cepAtual = preg_replace('/[^0-9,]|,[0-9]*$/', '', $cepAtual);
                    $cep = preg_replace('/[^0-9,]|,[0-9]*$/', '', $cep);
                    //Adiciona os zeros que estao faltando no começo da string
                    $cep = str_pad($cep, 8, "0", STR_PAD_LEFT);
                    $cep = substr($cep, 0, 5) . '-' . substr($cep, 5);

                    if ($cepAtual == false) {
                        $addCep = "INSERT INTO ";
                        $addCep .= $resource->getTableName(customer_address_entity_varchar);
                        $addCep .= " (entity_type_id, attribute_id, entity_id, `value`) ";
                        $addCep .= "VALUES(2, 30, $entityIdAddress, '$cep');";

                        $writeConnection->query($addCep);
                    }
                }

                // Adiciona o Logradouro do cliente
                if ($street) {
                    $getStreet = "SELECT `value` FROM ";
                    $getStreet .= $resource->getTableName(customer_address_entity_text);
                    $getStreet .= " WHERE entity_id = $entityIdAddress AND attribute_id = 25;";

                    $streetAtual = $readConnection->fetchOne($getStreet);

                    if (!$streetAtual) {
                        $addStreet = "INSERT INTO ";
                        $addStreet .= $resource->getTableName(customer_address_entity_text);
                        $addStreet .= " (entity_type_id, attribute_id, entity_id, `value`) ";
                        $addStreet .= "VALUES(2, 25, $entityIdAddress, '$street');";

                        $writeConnection->query($addStreet);
                    }
                }
                // Adiciona ou atualiza o Nome do cliente
                if ($firstName) {
                    $getName = "SELECT `value` FROM ";
                    $getName .= $resource->getTableName(customer_address_entity_varchar);
                    $getName .= " WHERE entity_id = $entityIdAddress AND attribute_id = 20;";

                    $nameAtual = $readConnection->fetchOne($getName);

                    if (!$nameAtual) {
                        $addName = "INSERT INTO ";
                        $addName .= $resource->getTableName(customer_address_entity_varchar);
                        $addName .= " (entity_type_id, attribute_id, entity_id, `value`) ";
                        $addName .= "VALUES(2, 20, $entityIdAddress, '$firstName');";

                        $writeConnection->query($addName);

                    } elseif ($nameAtual != $firstName) {
                        $updateNameAtual = "UPDATE ";
                        $updateNameAtual .= $resource->getTableName(customer_address_entity_varchar);
                        $updateNameAtual .= " SET `value` = '$firstName'";
                        $updateNameAtual .= " WHERE entity_id = $entityIdAddress AND attribute_id = 20;";

                        $writeConnection->query($updateNameAtual);

                        updateDateCustomer($resource, $writeConnection, $currentDateFormated, $websiteId, $entityId);
                    }
                }

                // Adiciona ou atualiza o Sobrenome do cliente
                if ($lastName) {
                    $getLastName = "SELECT `value` FROM ";
                    $getLastName .= $resource->getTableName(customer_address_entity_varchar);
                    $getLastName .= " WHERE entity_id = $entityIdAddress AND attribute_id = 22;";

                    $lastNameAtual = $readConnection->fetchOne($getLastName);

                    echo "\n\n\n $getLastName \n\n\n";

                    if (!$lastNameAtual) {
                        $addName = "INSERT INTO ";
                        $addName .= $resource->getTableName(customer_address_entity_varchar);
                        $addName .= " (entity_type_id, attribute_id, entity_id, `value`) ";
                        $addName .= "VALUES(2, 22, $entityIdAddress, '$lastName');";

                        $writeConnection->query($addName);

                    } elseif ($lastNameAtual != $lastName) {
                        $updateLastNameAtual = "UPDATE ";
                        $updateLastNameAtual .= $resource->getTableName(customer_address_entity_varchar);
                        $updateLastNameAtual .= " SET `value` = '$lastName'";
                        $updateLastNameAtual .= " WHERE entity_id = $entityIdAddress AND attribute_id = 22;";

                        $writeConnection->query($updateLastNameAtual);

                        updateDateCustomer($resource, $writeConnection, $currentDateFormated, $websiteId, $entityId);
                    }
                }

                // Adiciona o telefone do cliente
                if ($telefone) {
                    $getTelefone = "SELECT `value` FROM ";
                    $getTelefone .= $resource->getTableName(customer_address_entity_varchar);
                    $getTelefone .= " WHERE entity_id = $entityIdAddress AND attribute_id = 31;";

                    $telefoneAtual = $readConnection->fetchOne($getTelefone);

                    if ($telefoneAtual == false) {
                        $addTelefone = "INSERT INTO ";
                        $addTelefone .= $resource->getTableName(customer_address_entity_varchar);
                        $addTelefone .= " (entity_type_id, attribute_id, entity_id, `value`) ";
                        $addTelefone .= "VALUES(2, 31, $entityIdAddress, '$telefone');";

                        $writeConnection->query($addTelefone);
                    }

                    // define a data da ultima compra
                    if ($lastOrder) {
                        $getLastOrder = "SELECT `value` FROM ";
                        $getLastOrder .= $resource->getTableName(customer_entity_varchar);
                        $getLastOrder .= " WHERE entity_id = $entityId AND attribute_id = 197;";

                        $lastOrderAtual = $readConnection->fetchOne($getLastOrder);


                        if (($lastOrderAtual == false) && (!is_null($lastOrderAtual))) {
                            $addLastOrder = "INSERT INTO ";
                            $addLastOrder .= $resource->getTableName(customer_entity_varchar);
                            $addLastOrder .= " (entity_type_id, attribute_id, entity_id, `value`) ";
                            $addLastOrder .= "VALUES(1, 197, $entityId, '$lastOrder');";

                            $writeConnection->query($addLastOrder);
                        } elseif ($lastOrderAtual != $lastOrder) {
                            $updateLastOrder = "UPDATE ";
                            $updateLastOrder .= $resource->getTableName(customer_entity_varchar);
                            $updateLastOrder .= " SET `value` = '$lastOrder'";
                            $updateLastOrder .= " WHERE entity_id = $entityId AND attribute_id = 197;";

                            $writeConnection->query($updateLastOrder);

                            updateDateCustomer($resource, $writeConnection, $currentDateFormated, $websiteId, $entityId);
                        }
                    }

                    // define o valor da ultima compra
                    if ($lastPurchasePrice) {
                        $getLastPurchasePrice = "SELECT `value` FROM ";
                        $getLastPurchasePrice .= $resource->getTableName(customer_entity_varchar);
                        $getLastPurchasePrice .= " WHERE entity_id = $entityId AND attribute_id = 199;";

                        $lastPurchasePriceAtual = $readConnection->fetchOne($getLastPurchasePrice);

                        if (($lastPurchasePriceAtual == false) && (!is_null($lastPurchasePriceAtual))) {
                            $addLastPurchasePrice = "INSERT INTO ";
                            $addLastPurchasePrice .= $resource->getTableName(customer_entity_varchar);
                            $addLastPurchasePrice .= " (entity_type_id, attribute_id, entity_id, `value`) ";
                            $addLastPurchasePrice .= "VALUES(1, 199, $entityId, '$lastPurchasePrice');";

                            $writeConnection->query($addLastPurchasePrice);
                        } elseif ($lastPurchasePriceAtual != $lastPurchasePrice) {
                            $updateLastPurchasePrice = "UPDATE ";
                            $updateLastPurchasePrice .= $resource->getTableName(customer_entity_varchar);
                            $updateLastPurchasePrice .= " SET `value` = '$lastPurchasePrice'";
                            $updateLastPurchasePrice .= " WHERE entity_id = $entityId AND attribute_id = 199;";

                            $writeConnection->query($updateLastPurchasePrice);

                            updateDateCustomer($resource, $writeConnection, $currentDateFormated, $websiteId, $entityId);
                        }
                    }
                    // Define o endereço padrão de entrega do cliente
                    if ($street) {
                        $getAddress = "SELECT `value` FROM ";
                        $getAddress .= $resource->getTableName(customer_entity_int);
                        $getAddress .= " WHERE entity_id = $entityId AND attribute_id = 13;";

                        $address = $readConnection->fetchOne($getAddress);

                        if (!$address) {
                            $addAddress = "INSERT INTO ";
                            $addAddress .= $resource->getTableName(customer_entity_int);
                            $addAddress .= " (entity_type_id, attribute_id, entity_id, `value`) ";
                            $addAddress .= "VALUES(1, 13, $entityId, $entityIdAddress);";

                            $writeConnection->query($addAddress);
                        }
                    }

                    // define o status do cliente
//                    if ($status){
//                        // pega o status atual no Integravet
//                        $getStatus = "SELECT is_active FROM ";
//                        $getStatus .= $resource->getTableName(customer_entity);
//                        $getStatus .= "WHERE entity_id = $entityId";
//
//                        $statusAtual = $readConnection->fetchOne($getStatus);
//
//                        if($statusAtual != $status){
//                            $updateStatus = "UPDATE ";
//                            $updateStatus .= $resource->getTableName(customer_entity);
//                            $updateStatus .= " SET is_active = '$status'";
//                            $updateStatus .= " WHERE entity_id = $entityId;";
//
//                            $writeConnection->query($updateStatus);
//
//                            updateDateCustomer($resource, $writeConnection, $currentDateFormated, $websiteId, $entityId);
//                        }
//                    }

                    // seta o status Allin
                    $setStatusAllin = "INSERT IGNORE INTO ";
                    $setStatusAllin .= $resource->getTableName(customer_entity_varchar);
                    $setStatusAllin .= " (entity_type_id, attribute_id, entity_id, `value`)";
                    $setStatusAllin .= " VALUES(1, 195, $entityId, 'V');";

                    $writeConnection->query($setStatusAllin);

                    // define o status do second email
                    $setSecondEmail = "INSERT IGNORE INTO ";
                    $setSecondEmail .= $resource->getTableName(customer_entity_varchar);
                    $setSecondEmail .= " (entity_type_id, attribute_id, entity_id, `value`)";
                    $setSecondEmail .= " VALUES(1, 196, $entityId, 'I');";

                    $writeConnection->query($setSecondEmail);

                    $entityId = NULL;
                }
            } else {
                // define a data da ultima compra
                if ($lastOrder) {
                    $getLastOrder = "SELECT `value` FROM ";
                    $getLastOrder .= $resource->getTableName(customer_entity_varchar);
                    $getLastOrder .= " WHERE entity_id = $entityId AND attribute_id = 197;";

                    $lastOrderAtual = $readConnection->fetchOne($getLastOrder);


                    if (($lastOrderAtual == false) && (is_null($lastOrderAtual))) {
                        $addLastOrder = "INSERT INTO ";
                        $addLastOrder .= $resource->getTableName(customer_entity_varchar);
                        $addLastOrder .= " (entity_type_id, attribute_id, entity_id, `value`) ";
                        $addLastOrder .= "VALUES(1, 197, $entityId, '$lastOrder');";

                        $writeConnection->query($addLastOrder);
                    } elseif ($lastOrderAtual != $lastOrder) {
                        $updateLastOrder = "UPDATE ";
                        $updateLastOrder .= $resource->getTableName(customer_entity_varchar);
                        $updateLastOrder .= " SET `value` = '$lastOrder'";
                        $updateLastOrder .= " WHERE entity_id = $entityId AND attribute_id = 197;";

                        $writeConnection->query($updateLastOrder);

                        updateDateCustomer($resource, $writeConnection, $currentDateFormated, $websiteId, $entityId);
                    }
                }

                // define o valor da ultima compra
                if ($lastPurchasePrice) {
                    $getLastPurchasePrice = "SELECT `value` FROM ";
                    $getLastPurchasePrice .= $resource->getTableName(customer_entity_varchar);
                    $getLastPurchasePrice .= " WHERE entity_id = $entityId AND attribute_id = 199;";

                    $lastPurchasePriceAtual = $readConnection->fetchOne($getLastPurchasePrice);

                    if (($lastPurchasePriceAtual == false) && (!is_null($lastPurchasePriceAtual))) {
                        $addLastPurchasePrice = "INSERT INTO ";
                        $addLastPurchasePrice .= $resource->getTableName(customer_entity_varchar);
                        $addLastPurchasePrice .= " (entity_type_id, attribute_id, entity_id, `value`) ";
                        $addLastPurchasePrice .= "VALUES(1, 199, $entityId, '$lastPurchasePrice');";

                        $writeConnection->query($addLastPurchasePrice);
                    } elseif ($lastPurchasePriceAtual != $lastPurchasePrice) {
                        $updateLastPurchasePrice = "UPDATE ";
                        $updateLastPurchasePrice .= $resource->getTableName(customer_entity_varchar);
                        $updateLastPurchasePrice .= " SET `value` = '$lastPurchasePrice'";
                        $updateLastPurchasePrice .= " WHERE entity_id = $entityId AND attribute_id = 199;";

                        $writeConnection->query($updateLastPurchasePrice);

                        updateDateCustomer($resource, $writeConnection, $currentDateFormated, $websiteId, $entityId);
                    }
                }

                // Atualiza o Endereço do cliente
                if ($street) {
                    // Pega o entity_id da tabela de endereços referente ao cliente
                    $getEntityIdAddress = "SELECT entity_id FROM ";
                    $getEntityIdAddress .= $resource->getTableName(customer_address_entity);
                    $getEntityIdAddress .= " WHERE parent_id = $entityId;";

                    $entityIdAddress = $readConnection->fetchOne($getEntityIdAddress);

                    if ($entityIdAddress){

                        // Atualzia o Estado do cliente
                        if ($idState) {
                            $getIdState = "SELECT `value` FROM ";
                            $getIdState .= $resource->getTableName(customer_address_entity_int);
                            $getIdState .= " WHERE entity_id = $entityIdAddress AND attribute_id = 29;";

                            $idStateAtual = $readConnection->fetchOne($getIdState);

                            if ($idStateAtual != $idState) {
                                $updateIdStateAtual = "UPDATE ";
                                $updateIdStateAtual .= $resource->getTableName(customer_address_entity_int);
                                $updateIdStateAtual .= " SET `value` = $idState";
                                $updateIdStateAtual .= " WHERE entity_id = $entityIdAddress AND attribute_id = 29;";

                                $writeConnection->query($updateIdStateAtual);

                                updateDateCustomer($resource, $writeConnection, $currentDateFormated, $websiteId, $entityId);
                            }
                        }

                        // Atualiza o Estado do cliente
                        if ($state) {
                            $getState = "SELECT `value` FROM ";
                            $getState .= $resource->getTableName(customer_address_entity_varchar);
                            $getState .= " WHERE entity_id = $entityIdAddress AND attribute_id = 28;";

                            $stateAtual = $readConnection->fetchOne($getState);

                            if ($stateAtual != $state) {
                                $updateStateAtual = "UPDATE ";
                                $updateStateAtual .= $resource->getTableName(customer_address_entity_varchar);
                                $updateStateAtual .= " SET `value` = '$nameState'";
                                $updateStateAtual .= " WHERE entity_id = $entityIdAddress AND attribute_id = 28;";

                                $writeConnection->query($updateStateAtual);

                                updateDateCustomer($resource, $writeConnection, $currentDateFormated, $websiteId, $entityId);
                            }
                        }

                        // Atualiza a Cidade do cliente
                        if ($city) {
                            $getCity = "SELECT `value` FROM ";
                            $getCity .= $resource->getTableName(customer_address_entity_varchar);
                            $getCity .= " WHERE entity_id = $entityIdAddress AND attribute_id = 26;";

                            $cityAtual = $readConnection->fetchOne($getCity);

                            if ($cityAtual != $city) {
                                $updateCityAtual = "UPDATE ";
                                $updateCityAtual .= $resource->getTableName(customer_address_entity_varchar);
                                $updateCityAtual .= " SET `value` = '$city'";
                                $updateCityAtual .= " WHERE entity_id = $entityIdAddress AND attribute_id = 26;";

                                $writeConnection->query($updateCityAtual);

                                updateDateCustomer($resource, $writeConnection, $currentDateFormated, $websiteId, $entityId);
                            }
                        }

                        // Atualiza o Bairro do cliente
                        if ($bairro) {
                            $getBairro = "SELECT `value` FROM ";
                            $getBairro .= $resource->getTableName(customer_address_entity_varchar);
                            $getBairro .= " WHERE entity_id = $entityIdAddress AND attribute_id = 142;";

                            $bairroAtual = $readConnection->fetchOne($getBairro);

                            if ($bairroAtual != $bairro) {
                                $updateBairroAtual = "UPDATE ";
                                $updateBairroAtual .= $resource->getTableName(customer_address_entity_varchar);
                                $updateBairroAtual .= " SET `value` = '$bairro'";
                                $updateBairroAtual .= " WHERE entity_id = $entityIdAddress AND attribute_id = 142;";

                                $writeConnection->query($updateBairroAtual);

                                updateDateCustomer($resource, $writeConnection, $currentDateFormated, $websiteId, $entityId);
                            }
                        }

                        // Atualiza a Rua do cliente
                        if ($street) {
                            $getStreet = "SELECT `value` FROM ";
                            $getStreet .= $resource->getTableName(customer_address_entity_text);
                            $getStreet .= " WHERE entity_id = $entityIdAddress AND attribute_id = 25;";

                            $streetAtual = $readConnection->fetchOne($getStreet);

                            if (($streetAtual != $street) && ($streetAtual)) {
                                $updateStreetAtual = "UPDATE ";
                                $updateStreetAtual .= $resource->getTableName(customer_address_entity_text);
                                $updateStreetAtual .= " SET `value` = '$street'";
                                $updateStreetAtual .= " WHERE entity_id = $entityIdAddress AND attribute_id = 25;";

                                $writeConnection->query($updateStreetAtual);

                                updateDateCustomer($resource, $writeConnection, $currentDateFormated, $websiteId, $entityId);
                            }
                        }

                        // Atualiza o CEP do cliente
                        if ($cep) {
                            $getCep = "SELECT `value` FROM ";
                            $getCep .= $resource->getTableName(customer_address_entity_varchar);
                            $getCep .= " WHERE entity_id = $entityIdAddress AND attribute_id = 30;";

                            $cepAtual = $readConnection->fetchOne($getCep);

                            //Retira todos os caracteres nao numericos para que possa fazer a comparaçao mais precisa.
                            $cepAtual = preg_replace('/[^0-9,]|,[0-9]*$/', '', $cepAtual);
                            $cep = preg_replace('/[^0-9,]|,[0-9]*$/', '', $cep);
                            //Adiciona os zeros que estao faltando no começo da string
                            $cep = str_pad($cep, 8, "0", STR_PAD_LEFT);
                            $cep = substr($cep, 0, 5) . '-' . substr($cep, 5);

                            if ($cepAtual !== $cep) {
                                $updateCepAtual = "UPDATE ";
                                $updateCepAtual .= $resource->getTableName(customer_address_entity_varchar);
                                $updateCepAtual .= " SET `value` = '$cep'";
                                $updateCepAtual .= " WHERE entity_id = $entityIdAddress AND attribute_id = 30;";

                                $writeConnection->query($updateCepAtual);

                                updateDateCustomer($resource, $writeConnection, $currentDateFormated, $websiteId, $entityId);
                            }
                        }

                        // Atualiza o telefone do cliente
                        if ($telefone) {
                            $getTelefone = "SELECT `value` FROM ";
                            $getTelefone .= $resource->getTableName(customer_address_entity_varchar);
                            $getTelefone .= " WHERE entity_id = $entityIdAddress AND attribute_id = 31;";

                            $telefoneAtual = $readConnection->fetchOne($getTelefone);

                            if ($telefoneAtual != $telefone) {
                                $updateTelefoneAtual = "UPDATE ";
                                $updateTelefoneAtual .= $resource->getTableName(customer_address_entity_varchar);
                                $updateTelefoneAtual .= " SET `value` = '$telefone'";
                                $updateTelefoneAtual .= " WHERE entity_id = $entityIdAddress AND attribute_id = 31;";

                                $writeConnection->query($updateTelefoneAtual);

                                updateDateCustomer($resource, $writeConnection, $currentDateFormated, $websiteId, $entityId);
                            }
                        }
                    }
                }

            }
        } else {
            echo "\n\n O $idErp está querendo usar o $emailErp que já está cadastrado para o ID_ERP: $returnIdErp \n\n";
        }
    } else {
        echo "\n\n Email Invávido $emailErp \n\n";
    }
}

function addSalesRep($directoryRep,$currentDate, $resource, $readConnection, $writeConnection, $idErp, $storeViewReps, $firstName, $lastName, $razaoSocial, $emailErp, $telefone, $storeId) {
    // Verifica se o representante já está cadastrado
    $getSalesRep = "SELECT id, store_id, `name`, email, telephone, id_erp FROM ";
    $getSalesRep .= $resource->getTableName(fvets_salesrep);
    $getSalesRep .= " WHERE id_erp = '$idErp' AND store_id IN ($storeViewReps);";

    echo "\n\n\n $getSalesRep \n\n\n";

    $dataRep = $readConnection->fetchAll($getSalesRep);

    foreach ($dataRep as $rep) {
        $idRep = $rep['id'];
        $storeIdRepAtual = $rep['store_id'];
        $nameRepAtual = $rep['name'];
        $emailRepAtual = $rep['email'];
        $telefoneRepAtual = $rep['telephone'];
        $idErpAtual = $rep['id_erp'];
    }

    if (empty($dataRep)) {
        // adiciona representante
        $addSalesResp = " INSERT INTO ";
        $addSalesResp .= $resource->getTableName(fvets_salesrep);
        $addSalesResp .= " (store_id, `name`, email, telephone, id_erp, created_at)";
        $addSalesResp .= " VALUES('$storeViewReps', '$razaoSocial', '$emailErp', '$telefone', '$idErp', '$currentDate');";

        $writeConnection->query($addSalesResp);

    } else {
        if ($storeIdRepAtual != $storeId) {
            // Atualiza a store_id do representante
            $updateSotreIdRep = "UPDATE ";
            $updateSotreIdRep .= $resource->getTableName(fvets_salesrep);
            $updateSotreIdRep .= " SET store_id = '$storeId'";
            $updateSotreIdRep .= " WHERE id = '$idErpAtual'";

            $writeConnection->query($updateSotreIdRep);
        }

        if ($nameRepAtual != $razaoSocial) {
            // Atualiza o nome do representante
            $updateNameRep = "UPDATE ";
            $updateNameRep .= $resource->getTableName(fvets_salesrep);
            $updateNameRep .= " SET `name` = '$razaoSocial'";
            $updateNameRep .= " WHERE id = $idRep";

            $writeConnection->query($updateNameRep);
        }

        if (($emailRepAtual != $emailErp) && ($emailErp != '')) {
            // Atualiza o email do representante
            $updateEmailRep = "UPDATE ";
            $updateEmailRep .= $resource->getTableName(fvets_salesrep);
            $updateEmailRep .= " SET email = '$emailErp'";
            $updateEmailRep .= " WHERE id = $idRep";

            $writeConnection->query($updateEmailRep);
        }

        if (($telefoneRepAtual != $telefone) && ($telefone != ' ')) {
            // Atualiza o email do representante
            $updateTelefoneRep = "UPDATE ";
            $updateTelefoneRep .= $resource->getTableName(fvets_salesrep);
            $updateTelefoneRep .= " SET telephone = '$telefone'";
            $updateTelefoneRep .= " WHERE id = $idRep";

            $writeConnection->query($updateTelefoneRep);
        }

        if ($idErpAtual != $idErp) {
            // Atualiza o email do representante
            $updateIdErp = "UPDATE ";
            $updateIdErp .= $resource->getTableName(fvets_salesrep);
            $updateIdErp .= " SET id_erp = '$idErp'";
            $updateIdErp .= " WHERE id = $idRep";

            $writeConnection->query($updateIdErp);
        }
    }
}

function setSalesRep($resource, $readConnection, $writeConnection, $websiteId, $idRep, $idErp, $storeViewReps, $idRepsIntegra, $tipoPessoa) {
    // Define o Representante do cliente
    echo "\n Definindo o representante do cliente\n";

    if ($tipoPessoa == 3):
        $idRep = $idErp;
    endif;

    /**
     * Procura pelo id_erp do cliente cliente para atualização das informações
     * Caso não encontre o id_erp pesquisa pelo email sempre validando o website_id
     */
    $getEntityId = "SELECT ce.entity_id FROM ";
    $getEntityId .= $resource->getTableName(customer_entity_varchar) . " as cev";
    $getEntityId .= " INNER JOIN " . $resource->getTableName(customer_entity) . " as ce ";
    $getEntityId .= "ON cev.entity_id = ce.entity_id ";
    $getEntityId .= "WHERE cev.attribute_id = 183 AND cev.`value` = '$idErp' AND ce.website_id = $websiteId";

    $entityId = $readConnection->fetchOne($getEntityId);

    if ($entityId){

        $idReps = explode(',',$idRep);

        foreach ($idReps as $idRep):
            // Pega o ID do representante referente ao IntegraVet
            $getRepIntegra = "SELECT id FROM ";
            $getRepIntegra .= $resource->getTableName(fvets_salesrep);
            $getRepIntegra .= " WHERE id_erp = '$idRep' AND store_id in ($storeViewReps);";
            $idRepIntegra = $readConnection->fetchOne($getRepIntegra);
            $idRepsIntegra .= "$idRepIntegra, ";

        endforeach;

        $idRepsIntegra = substr($idRepsIntegra, 0,-2);

        if ($idRepIntegra) {
            $getIdRep = "SELECT `value` FROM ";
            $getIdRep .= $resource->getTableName(customer_entity_varchar);
            $getIdRep .= " WHERE attribute_id = 148 AND entity_id = $entityId;";

            $idRepAtual = $readConnection->fetchOne($getIdRep);

            if (($idRepAtual == false) && (!is_null($idRepAtual))) {

                $addIdRep = "INSERT INTO ";
                $addIdRep .= $resource->getTableName(customer_entity_varchar);
                $addIdRep .= " (entity_type_id, attribute_id, entity_id, `value`) ";
                $addIdRep .= "VALUES(1, 148, $entityId, '$idRepsIntegra');";

                $writeConnection->query($addIdRep);
            } elseif ($idRepAtual != $idRep) {

                $updateIdRepAtual = "UPDATE ";
                $updateIdRepAtual .= $resource->getTableName(customer_entity_varchar);
                $updateIdRepAtual .= " SET `value` = '$idRepsIntegra'";
                $updateIdRepAtual .= " WHERE entity_id = $entityId AND attribute_id = 148;";

                $writeConnection->query($updateIdRepAtual);
            }
        }
    }
}

function setGroupAccess($resource, $readConnection, $writeConnection, $currentDateFormated, $websiteId, $storeviewId, $idErp) {
    /**
     * Procura pelo id_erp do cliente cliente para atualização das informações
     * Caso não encontre o id_erp pesquisa pelo email sempre validando o website_id
     */
    $getEntityId = "SELECT ce.entity_id FROM ";
    $getEntityId .= $resource->getTableName(customer_entity_varchar) . " as cev";
    $getEntityId .= " INNER JOIN " . $resource->getTableName(customer_entity) . " as ce ";
    $getEntityId .= "ON cev.entity_id = ce.entity_id ";
    $getEntityId .= "WHERE cev.attribute_id = 183 AND cev.`value` = '$idErp' AND ce.website_id = $websiteId";

    $entityId = $readConnection->fetchOne($getEntityId);

    // Adiciona ou altera a store_view do cliente
    // Valores das store IDS
    if ($entityId):
        if ($storeviewId) {
            $getStoreViewId = "SELECT `value` FROM ";
            $getStoreViewId .= $resource->getTableName(customer_entity_text);
            $getStoreViewId .= " WHERE attribute_id = 191 AND entity_id = $entityId;";

            $storeviewIdAtual = $readConnection->fetchOne($getStoreViewId);

            if (!$storeviewIdAtual) {
                $addRazaoSocial = "INSERT INTO ";
                $addRazaoSocial .= $resource->getTableName(customer_entity_text);
                $addRazaoSocial .= " (entity_type_id, attribute_id, entity_id, `value`) ";
                $addRazaoSocial .= "VALUES(1, 191, $entityId, '$storeviewId');";

                $writeConnection->query($addRazaoSocial);
            }
            updateDateCustomer($resource, $writeConnection, $currentDateFormated, $websiteId, $entityId);
            // alimenta o arquivo de relatório da integração grupoDeAcessoAlterado.csv
        }
    endif;
}

function setCustomerGroup($resource, $readConnection, $writeConnection, $currentDateFormated, $websiteId, $storeviewId, $idErp, $idGroup){
    /**
     * Procura pelo id_erp do cliente cliente para atualização das informações
     * Caso não encontre o id_erp pesquisa pelo email sempre validando o website_id
     */
    $getEntityId = "SELECT ce.entity_id FROM ";
    $getEntityId .= $resource->getTableName(customer_entity_varchar) . " as cev";
    $getEntityId .= " INNER JOIN " . $resource->getTableName(customer_entity) . " as ce ";
    $getEntityId .= "ON cev.entity_id = ce.entity_id ";
    $getEntityId .= "WHERE cev.attribute_id = 183 AND cev.`value` = '$idErp' AND ce.website_id = $websiteId";

    $entityId = $readConnection->fetchOne($getEntityId);

    $getWebsite = "SELECT code, name FROM core_store WHERE website_id = $websiteId ORDER BY store_id LIMIT 1;";

    $website = $readConnection->fetchAll($getWebsite);

    $websiteCode = $website[0]['code'];
    $websiteName = $website[0]['name'];

    if (!$idGroup) {
        /**
         * Para as distribuidoras que possuem clientes sem grupos de clientes define o grupo padrão
         * no caso os grupos de clientes padrão são o próprio código do website da distribuidora.
         */

            if (!$idGroup){
                // Procura o grupo padrão
                $getCustomerGroupDefault = "SELECT customer_group_id FROM customer_group WHERE website_id = $websiteId AND id_tabela = '$websiteCode';";
                $idCustomerGroupDefault = $readConnection->fetchOne($getCustomerGroupDefault);

                // Caso não encontre adiciona um e pega o id do grupo recém adicionado
                if (!$idCustomerGroupDefault):
                    $addGroupCustomerDefault = "INSERT INTO customer_group
                                          (customer_group_code, tax_class_id, id_tabela, website_id, multiple_table)
                                        VALUES
                                          ('$websiteName', 3, '$websiteCode', $websiteId, 0);";
                    $writeConnection->query($addGroupCustomerDefault);

                    $getCustomerGroupDefault = "SELECT customer_group_id FROM customer_group WHERE website_id = $websiteId AND id_tabela = '$websiteCode';";
                    $idCustomerGroupDefault = $readConnection->fetchOne($getCustomerGroupDefault);
                endif;
            }
        } else {
        if (($idGroup) && ($entityId)):
                $getGroupOfCustomer = "SELECT group_id FROM customer_entity WHERE entity_id = $entityId;";
                 $idGroupOfCustomer = $readConnection->fetchOne($getGroupOfCustomer);

                // Procura o grupo padrão
                $getCustomerGroupDefault = "SELECT customer_group_id FROM customer_group WHERE website_id = $websiteId AND id_tabela = '$idGroup';";
                $idCustomerGroupDefault = $readConnection->fetchOne($getCustomerGroupDefault);

                if (!$idGroupOfCustomer):
                    $setIdCustomerGroupDefault = "UPDATE customer_entity SET group_id = $idCustomerGroupDefault WHERE entity_id = $entityId;";
                    $writeConnection->query($setIdCustomerGroupDefault);
                endif;
            else:
                echo "\n\n Cliente com ID-Erp: $idErp não foi localizado para associar ele a um grupo";
            endif;
        }
}

function removeBrandsFromCustomers($resource, $writeConnection, $readConnection, $remoteId, $websiteId, $setRemoveBrands, $removeBrands) {
    $getEntityId = "SELECT ce.entity_id FROM ";
    $getEntityId .= $resource->getTableName(customer_entity_varchar) . " as cev";
    $getEntityId .= " INNER JOIN " . $resource->getTableName(customer_entity) . " as ce ";
    $getEntityId .= "ON cev.entity_id = ce.entity_id ";
    $getEntityId .= "WHERE cev.attribute_id = 183 AND cev.`value` = $remoteId AND ce.website_id = $websiteId";

    $entityId = $readConnection->fetchOne($getEntityId);

    if ($entityId){
        $i = 0;
        while ($brand = current($brands)) {
            $removeBrand = $setRemoveBrands[$i];
            if ($removeBrand == 1):
                $removeBrands .= "$brand, ";
            endif;
            $i++;
            next($brands);
        }
        $removeBrands = mb_substr($removeBrands, 0,-2);

        $setRemoveBrands = "INSERT IGNORE INTO customer_entity_varchar (entity_type_id, attribute_id, entity_id, `value`)VALUES(1,215, $entityId, '$removeBrands');";

        $writeConnection->query($setRemoveBrands);
    }
}
