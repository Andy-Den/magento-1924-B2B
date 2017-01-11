jQuery(document).ready(function(){

    var dataForm = new VarienForm('form-validate', true);

    var tipopessoaChange = function(event, element) {
        if (element.value === 'PJ') {
            $$('.tipopessoa-pf').each(function(e){
                e.hide();
            });
            $$('.tipopessoa-pj').each(function(e){
                e.show();
            });
        } else {
            $$('.tipopessoa-pj').each(function(e){
                e.hide();
            });
            $$('.tipopessoa-pf').each(function(e){
                e.show();
            });
        }
    };

    $('inscricao_estadual_isento').on('change', function(event, element){
        if(element.checked === true) {
            $('inscricao_estadual').disable();
            $('inscricao_estadual').removeClassName('required-entry');
        } else {
            $('inscricao_estadual').enable();
            $('inscricao_estadual').addClassName('required-entry');
        }
    });

    $('tipopessoapf').on('change', tipopessoaChange);
    $('tipopessoapj').on('change', tipopessoaChange);

    var dataForm = new VarienForm('form-validate', true);

    //var regionJson inside phtml
    var regionUpdater = new RegionUpdater('country', 'region', 'region_id', regionJson, undefined, 'zip');

    //var cepUrl inside .phtml
    jQuery('#buscar-cep').on('click', function(e){
        jQuery('#cep-please-wait').show();
        jQuery.ajax({
            url: cepUrl,
            data: { cep: jQuery('#zip').val() },
            dataType: 'json',
            success: function (data) {
                jQuery('#cep-please-wait').hide();

                if (data.resultado == '0') {
                    alert(data.message);
                    //jQuery('#country').val('');
                    regionUpdater.update();
                    jQuery('#logradouro').val('');
                    jQuery('#bairro').val('');
                    jQuery('#city').val('');
                    jQuery('#numero').val('');
                    jQuery('#complemento').val('');
                    jQuery('#referencia').val('');
                    jQuery('#region_id').val('');
                } else {
                    jQuery('#country').val('BR');
                    regionUpdater.update();
                    jQuery('#logradouro').val(data.tipo_logradouro + ' ' + data.logradouro);
                    jQuery('#bairro').val(data.bairro);
                    jQuery('#city').val(data.cidade);
                    jQuery('#numero').val('');
                    jQuery('#complemento').val('');
                    jQuery('#referencia').val('');

                    for (var regionId in regionUpdater.regions['BR'])
                    {
                        if (regionUpdater.regions['BR'][regionId].code == data.uf) {
                            jQuery('#region_id').val(regionId);
                        }
                    }
                }

                jQuery('li.hidden-no-cep').show();
            }
        });
    });

});