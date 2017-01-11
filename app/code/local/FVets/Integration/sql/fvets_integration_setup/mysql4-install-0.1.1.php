<?php
$installer = $this;

$installer->startSetup();

/**
 * Essa alteração adiciona a coluna id_tabela para associar as tabelas de valores diferenciados por cliente na tabela
 * customer_group
 */
$installer->run("
ALTER TABLE {$this->getTable('customer_group')} ADD id_tabela VARCHAR(60) COMMENT 'id_tabela armazena o ID da tabela de
preços enviado pelo metodo de integraçao' ;
");

$installer->endSetup();