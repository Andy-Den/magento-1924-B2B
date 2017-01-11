<?php

/**
 * Inclui os clientes da doctorsvet nos grupos de acesso corretos
 */

$installer = $this;

$installer->startSetup();

//Atualiza as variáveis dos emails transacionais de representantes
$installer->run('
	UPDATE `core_email_template` SET `template_text` = "<div style=\"font:11px/1.35em Verdana, Arial, Helvetica, sans-serif;\">
<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"98%\" style=\"margin-top:10px; font:11px/1.35em Verdana, Arial, Helvetica, sans-serif; margin-bottom:10px;\">
<tr>
    <td align=\"center\" valign=\"top\">
        <!-- [ header starts here] -->
        <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"650\">
            <tr>
                <td valign=\"top\"><a href=\"{{store url=\"\"}}\" target=\"_blank\"><img src=\"{{var logo_url}}\" alt=\"{{var logo_alt}}\"/></a></td>
            </tr>
        </table>
        <!-- [ middle starts here] -->
        <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"650\">
            <tr>
                <td valign=\"top\">
                    <p style=\"font: 20px/2em Verdana, Arial, Helvetica, sans-serif;\">
                        <strong>{{htmlescape var=$rep.getName()}}</strong>, Parabéns pela sua nova venda!<br/>
                        <strong>{{var order.getCustomCustomerName()}}</strong> acaba de realizar uma compra online.<br/>
                        {{var order.getComissionString()}}

                    <h3 style=\"border-bottom:1.5px solid #eee; font-size:1.05em; padding-bottom:1px; \">Pedido número {{var order.increment_id}} <small>(comprado no dia {{var order.getCreatedAtFormated(\'long\')}})</small></h3>
                    <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">
                        <thead>
                        <tr>
                            <th align=\"left\" width=\"48.5%\" bgcolor=\"#d9e5ee\" style=\"padding:5px 9px 6px 9px; border:1px solid #bebcb7; border-bottom:none; line-height:1em;\">Informação de Cobrança:</th>
                            <th width=\"3%\"></th>
                            <th align=\"left\" width=\"48.5%\" bgcolor=\"#d9e5ee\" style=\"padding:5px 9px 6px 9px; border:1px solid #bebcb7; border-bottom:none; line-height:1em;\">Método de Pagamento:</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td valign=\"top\" style=\"padding:7px 9px 9px 9px; border:1px solid #bebcb7; border-top:0; background:#f8f7f5;\">
                                {{var order.getBillingAddress().format(\'html\')}}
                            </td>
                            <td>&nbsp;</td>
                            <td valign=\"top\" style=\"padding:7px 9px 9px 9px; border:1px solid #bebcb7; border-top:0; background:#f8f7f5;\">
                                {{var payment_html}}
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <br/>
                    {{layout handle=\"sales_email_order_items\" order=$order}}
                    <br/>
<div style=\"float:left; margin-top:3%; width:20%;\">
<img style=\"width: 100px;\" src=\"{{skin url=\"images/email/interrogation.png\" _area=\'frontend\'}}\" />
</div>
<div style=\"float:left; width:80%; font: 20px/1.5em Verdana, Arial, Helvetica, sans-serif;\">
<p><h2>Sabia que:</h2>
Você será informado de todos os pedidos que os seus clientes realizarem na plataforma online! <strong>Cada cliente está associado ao seu respectivo Representante</strong> de Vendas.
</p>
</div>
<br><br><br>
                    {{var order.getEmailCustomerNote()}}
                    <p>Obrigado,<br/><strong>{{var store.getFrontendName()}}</strong></p>
                </td>
            </tr>
        </table>
    </td>
</tr>
</table>
</div>" WHERE `template_id` = 40;
	UPDATE `core_email_template` SET `template_subject` = "{{var store.getFrontendName()}}: Seu cliente: \"{{var order.getCustomCustomerName()}}\" realizou uma compra pelo site. Pedido número #{{var order.increment_id}}" WHERE `template_id` = 40;
	UPDATE `core_email_template` SET `template_text` = "<div style=\"font:11px/1.35em Verdana, Arial, Helvetica, sans-serif;\">
<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"98%\" style=\"margin-top:10px; font:11px/1.35em Verdana, Arial, Helvetica, sans-serif; margin-bottom:10px;\">
<tr>
    <td align=\"center\" valign=\"top\">
        <!-- [ header starts here] -->
        <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"650\">
            <tr>
                <td valign=\"top\"><a href=\"{{store url=\"\"}}\" target=\"_blank\"><img src=\"{{var logo_url}}\" alt=\"{{var logo_alt}}\"/></a></td>
            </tr>
        </table>
        <!-- [ middle starts here] -->
        <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"650\">
            <tr>
                <td valign=\"top\">
                    <p style=\"font: 20px/2em Verdana, Arial, Helvetica, sans-serif;\">
                        <strong>{{htmlescape var=$rep.getName()}}</strong>, Parabéns pela sua nova venda!<br/>
                        <strong>{{var order.getCustomCustomerName()}}</strong> acaba de realizar uma compra online.<br/>
                        {{var order.getComissionString()}}

                    <h3 style=\"border-bottom:1.5px solid #eee; font-size:1.05em; padding-bottom:1px; \">Pedido número {{var order.increment_id}} <small>(comprado no dia {{var order.getCreatedAtFormated(\'long\')}})</small></h3>
                    <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">
                        <thead>
                        <tr>
                            <th align=\"left\" width=\"48.5%\" bgcolor=\"#d9e5ee\" style=\"padding:5px 9px 6px 9px; border:1px solid #bebcb7; border-bottom:none; line-height:1em;\">Informação de Cobrança:</th>
                            <th width=\"3%\"></th>
                            <th align=\"left\" width=\"48.5%\" bgcolor=\"#d9e5ee\" style=\"padding:5px 9px 6px 9px; border:1px solid #bebcb7; border-bottom:none; line-height:1em;\">Método de Pagamento:</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td valign=\"top\" style=\"padding:7px 9px 9px 9px; border:1px solid #bebcb7; border-top:0; background:#f8f7f5;\">
                                {{var order.getBillingAddress().format(\'html\')}}
                            </td>
                            <td>&nbsp;</td>
                            <td valign=\"top\" style=\"padding:7px 9px 9px 9px; border:1px solid #bebcb7; border-top:0; background:#f8f7f5;\">
                                {{var payment_html}}
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <br/>
                    {{layout handle=\"sales_email_order_items\" order=$order}}
                    <br/>
                    {{var order.getEmailCustomerNote()}}
                    <p>Obrigado,<br/><strong>{{var store.getFrontendName()}}</strong></p>
                </td>
            </tr>
        </table>
    </td>
</tr>
</table>
</div>" WHERE `template_id` = 41;
	UPDATE `core_email_template` SET `template_subject` = "{{var store.getFrontendName()}}: Seu cliente: \"{{var order.getCustomCustomerName()}}\" realizou uma compra pelo site. Pedido número #{{var order.increment_id}}" WHERE `template_id` = 41;
');

$installer->endSetup();

