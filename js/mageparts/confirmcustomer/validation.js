if(Validation)
{
    Validation.addAllThese([

        ['validate-approval-status', 'Por favor, escolha um motivo para não aprovar este cliente.', function(v) {
            return v == 0 ? false : true;
        }],

        ['validate-group-table-price', 'Por favor, escolha uma tabela de preço para este cliente.', function(v) {
            return v == 1 ? false : true;
        }],

    ]);
}