<?php

$installer = $this;

$installer->startSetup();

Mage::register("isSecureArea", 1);

// Force the store to be admin
Mage::app()->setUpdateMode(false);
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
/* Install Omega Website Config */

$website = Mage::getResourceModel('core/website_collection')->addFieldToFilter('code', 'doctorsvet')->getData()[0];

if (isset($website['website_id']))
{
	$installer->run("
		DELETE FROM core_config_data
		WHERE scope = 'websites'
		AND scope_id = {$website['website_id']};
	;");

	$installer->run("
		INSERT INTO `core_config_data` (`scope`, `scope_id`, `path`, `value`) VALUES
		
		('websites', '{$website["website_id"]}', 'ajax_pro/catalogCategoryView/type', 'button'),
		('websites', '{$website["website_id"]}', 'ajax_pro/checkoutCart/messageHandle', 'tm_ajaxpro_checkout_cart_add_suggestpage'),
		('websites', '{$website["website_id"]}', 'ajax_pro/effect/enabled_overlay', '1'),
		('websites', '{$website["website_id"]}', 'ajax_pro/effect/overlay_opacity', '0.5'),
		('websites', '{$website["website_id"]}', 'ajax_pro/general/enabled', '1'),
		('websites', '{$website["website_id"]}', 'ajax_pro/general/useLoginFormBlock', '1'),
		('websites', '{$website["website_id"]}', 'carriers/freeshipping/active', '1'),
		('websites', '{$website["website_id"]}', 'carriers/freeshipping/name', 'Frete a combinar'),
		('websites', '{$website["website_id"]}', 'carriers/freeshipping/title', 'Envio Doctorsvet'),
		('websites', '{$website["website_id"]}', 'carriers/tablerate/import', '1409953796'),
		('websites', '{$website["website_id"]}', 'catalog/frontend/list_mode', 'list-grid'),
		('websites', '{$website["website_id"]}', 'catalog/placeholder/image_placeholder', 'websites/2/avatar_produto.jpg'),
		('websites', '{$website["website_id"]}', 'catalog/placeholder/small_image_placeholder', 'websites/2/small_produto.jpg'),
		('websites', '{$website["website_id"]}', 'catalog/price/group_as_normal', '1'),
		('websites', '{$website["website_id"]}', 'checkout/options/enable_agreements', '1'),
		('websites', '{$website["website_id"]}', 'confirmcustomer/admin_notification/enabled', '0'),
		('websites', '{$website["website_id"]}', 'confirmcustomer/admin_notification/recipients', ''),
		('websites', '{$website["website_id"]}', 'confirmcustomer/admin_notification/template', '34'),
		('websites', '{$website["website_id"]}', 'confirmcustomer/email/template', '33'),
		('websites', '{$website["website_id"]}', 'confirmcustomer/general/enabled', '1'),
		('websites', '{$website["website_id"]}', 'confirmcustomer/general/welcome_email', '1'),
		('websites', '{$website["website_id"]}', 'confirmcustomer/redirect/cms_page', 'account-awaiting-approval|33'),
		('websites', '{$website["website_id"]}', 'customer/create_account/email_confirmed_template', '31'),
		('websites', '{$website["website_id"]}', 'customer/create_account/email_template', '31'),
		('websites', '{$website["website_id"]}', 'customer/password/forgot_email_template', '30'),
		('websites', '{$website["website_id"]}', 'customer/password/remind_email_template', '29'),
		('websites', '{$website["website_id"]}', 'custom_menu/columns/count', '3'),
		('websites', '{$website["website_id"]}', 'custom_menu/columns/divided_horizontally', '1'),
		('websites', '{$website["website_id"]}', 'custom_menu/columns/integrate', '1'),
		('websites', '{$website["website_id"]}', 'custom_menu/general/ajax_load_content', '0'),
		('websites', '{$website["website_id"]}', 'custom_menu/general/display_empty_categories', '0'),
		('websites', '{$website["website_id"]}', 'custom_menu/general/enabled', '1'),
		('websites', '{$website["website_id"]}', 'custom_menu/general/mobile_menu', '1'),
		('websites', '{$website["website_id"]}', 'custom_menu/general/rtl', '0'),
		('websites', '{$website["website_id"]}', 'custom_menu/general/show_home_link', '0'),
		('websites', '{$website["website_id"]}', 'custom_menu/popup/delay_displaying', '10'),
		('websites', '{$website["website_id"]}', 'custom_menu/popup/delay_hiding', '10'),
		('websites', '{$website["website_id"]}', 'custom_menu/popup/top_offset', '0'),
		('websites', '{$website["website_id"]}', 'custom_menu/popup/width', '0'),
		('websites', '{$website["website_id"]}', 'design/email/logo', 'websites/7/logo_1.png'),
		('websites', '{$website["website_id"]}', 'design/email/logo_alt', 'DoctorsVet Online'),
		('websites', '{$website["website_id"]}', 'design/footer/copyright', '&copy; 2014 Doctorsvet Online. Todos os direitos reservados.'),
		('websites', '{$website["website_id"]}', 'design/head/default_description', 'DoctorsVet Online'),
		('websites', '{$website["website_id"]}', 'design/head/default_keywords', 'DoctorsVet, 4Vets'),
		('websites', '{$website["website_id"]}', 'design/head/default_title', 'DoctorsVet Online'),
		('websites', '{$website["website_id"]}', 'design/head/includes', ''),
		('websites', '{$website["website_id"]}', 'design/head/shortcut_icon', 'websites/7/doctorsvet-favicon_2.png'),
		('websites', '{$website["website_id"]}', 'design/head/title_prefix', 'DoctorsVet Online - '),
		('websites', '{$website["website_id"]}', 'design/header/logo_alt', 'DoctorsVet Online'),
		('websites', '{$website["website_id"]}', 'design/header/logo_src', 'websites/7/logo_1.png'),
		('websites', '{$website["website_id"]}', 'design/header/welcome', 'Bem-vindo(a)!'),
		('websites', '{$website["website_id"]}', 'design/package/name', 'argento'),
		('websites', '{$website["website_id"]}', 'design/theme/after_default', 'fvets_pure'),
		('websites', '{$website["website_id"]}', 'design/theme/layout', 'doctorsvet'),
		('websites', '{$website["website_id"]}', 'design/theme/locale', 'doctorsvet'),
		('websites', '{$website["website_id"]}', 'design/theme/skin', 'doctorsvet'),
		('websites', '{$website["website_id"]}', 'design/theme/template', 'doctorsvet'),
		('websites', '{$website["website_id"]}', 'dev/css/merge_css_files', '1'),
		('websites', '{$website["website_id"]}', 'dev/debug/template_hints', '0'),
		('websites', '{$website["website_id"]}', 'dev/debug/template_hints_blocks', '0'),
		('websites', '{$website["website_id"]}', 'dev/js/merge_files', '0'),
		('websites', '{$website["website_id"]}', 'dev/translate_inline/active', '0'),
		('websites', '{$website["website_id"]}', 'general/country/allow', 'BR'),
		('websites', '{$website["website_id"]}', 'general/country/default', 'BR'),
		('websites', '{$website["website_id"]}', 'general/store_information/address', 'Rua Emílio Colella, 311 - Pq São Domingos
São Paulo - SP
Cep: 05126-130
fone: 11 3645-4450
fax: 11 3641-6762
email: contato@doctorsvet.com.br
'),
		('websites', '{$website["website_id"]}', 'general/store_information/is_brand', '0'),
		('websites', '{$website["website_id"]}', 'general/store_information/name', 'DoctorsVet Online'),
		('websites', '{$website["website_id"]}', 'general/store_information/phone', '(11) 1111-1111'),
		('websites', '{$website["website_id"]}', 'lightboxpro/gallery/behaviour_enableLightboxEfect', '0'),
		('websites', '{$website["website_id"]}', 'magefm_customer/general/self_customer_addres_phone', '1'),
		('websites', '{$website["website_id"]}', 'newsletter/subscription/disablenewslettersuccesses', '1'),
		('websites', '{$website["website_id"]}', 'onestepcheckout/ajax_update/ajax_save_billing_fields', 'country'),
		('websites', '{$website["website_id"]}', 'onestepcheckout/exclude_fields/enable_address_fields', '1'),
		('websites', '{$website["website_id"]}', 'onestepcheckout/exclude_fields/enable_comments', '1'),
		('websites', '{$website["website_id"]}', 'onestepcheckout/exclude_fields/enable_comments_default', '1'),
		('websites', '{$website["website_id"]}', 'onestepcheckout/exclude_fields/enable_discount', '1'),
		('websites', '{$website["website_id"]}', 'onestepcheckout/exclude_fields/exclude_bairro', '0'),
		('websites', '{$website["website_id"]}', 'onestepcheckout/exclude_fields/exclude_cnpj', '0'),
		('websites', '{$website["website_id"]}', 'onestepcheckout/exclude_fields/exclude_company', '1'),
		('websites', '{$website["website_id"]}', 'onestepcheckout/exclude_fields/exclude_cpf', '0'),
		('websites', '{$website["website_id"]}', 'onestepcheckout/exclude_fields/exclude_fax', '1'),
		('websites', '{$website["website_id"]}', 'onestepcheckout/exclude_fields/exclude_inscricao_estadual', '0'),
		('websites', '{$website["website_id"]}', 'onestepcheckout/exclude_fields/exclude_mobilephone', '0'),
		('websites', '{$website["website_id"]}', 'onestepcheckout/exclude_fields/exclude_razao_social', '0'),
		('websites', '{$website["website_id"]}', 'onestepcheckout/exclude_fields/exclude_saveaddress', '1'),
		('websites', '{$website["website_id"]}', 'onestepcheckout/general/checkout_description', 'Por favor, insira  as informações abaixo para completar seu pedido'),
		('websites', '{$website["website_id"]}', 'onestepcheckout/general/checkout_title', 'Finalizar Pedido'),
		('websites', '{$website["website_id"]}', 'onestepcheckout/general/default_country', 'BR'),
		('websites', '{$website["website_id"]}', 'onestepcheckout/general/default_shipping_if_one', '1'),
		('websites', '{$website["website_id"]}', 'onestepcheckout/general/enable_different_shipping', '0'),
		('websites', '{$website["website_id"]}', 'onestepcheckout/general/hide_payment_method', '1'),
		('websites', '{$website["website_id"]}', 'onestepcheckout/general/hide_shipping_method', '1'),
		('websites', '{$website["website_id"]}', 'onestepcheckout/general/rewrite_checkout_links', '1'),
		('websites', '{$website["website_id"]}', 'onestepcheckout/general/single_address', '1'),
		('websites', '{$website["website_id"]}', 'onestepcheckout/general/update_address_and_customer', '1'),
		('websites', '{$website["website_id"]}', 'onestepcheckout/registration/registration_mode', 'disable_registration'),
		('websites', '{$website["website_id"]}', 'onestepcheckout/sortordering_fields/bairro', '8'),
		('websites', '{$website["website_id"]}', 'onestepcheckout/sortordering_fields/city', '7'),
		('websites', '{$website["website_id"]}', 'onestepcheckout/sortordering_fields/cnpj', '2'),
		('websites', '{$website["website_id"]}', 'onestepcheckout/sortordering_fields/company', '99'),
		('websites', '{$website["website_id"]}', 'onestepcheckout/sortordering_fields/confirm_password', '99'),
		('websites', '{$website["website_id"]}', 'onestepcheckout/sortordering_fields/country_id', '99'),
		('websites', '{$website["website_id"]}', 'onestepcheckout/sortordering_fields/cpf', '12'),
		('websites', '{$website["website_id"]}', 'onestepcheckout/sortordering_fields/create_account', '99'),
		('websites', '{$website["website_id"]}', 'onestepcheckout/sortordering_fields/dob', '99'),
		('websites', '{$website["website_id"]}', 'onestepcheckout/sortordering_fields/email', '14'),
		('websites', '{$website["website_id"]}', 'onestepcheckout/sortordering_fields/fax', '99'),
		('websites', '{$website["website_id"]}', 'onestepcheckout/sortordering_fields/firstname', '10'),
		('websites', '{$website["website_id"]}', 'onestepcheckout/sortordering_fields/gender', '99'),
		('websites', '{$website["website_id"]}', 'onestepcheckout/sortordering_fields/inscricao_estadual', '3'),
		('websites', '{$website["website_id"]}', 'onestepcheckout/sortordering_fields/lastname', '11'),
		('websites', '{$website["website_id"]}', 'onestepcheckout/sortordering_fields/mobilephone', '13'),
		('websites', '{$website["website_id"]}', 'onestepcheckout/sortordering_fields/password', '99'),
		('websites', '{$website["website_id"]}', 'onestepcheckout/sortordering_fields/postcode', '5'),
		('websites', '{$website["website_id"]}', 'onestepcheckout/sortordering_fields/razao_social', '1'),
		('websites', '{$website["website_id"]}', 'onestepcheckout/sortordering_fields/region_id', '6'),
		('websites', '{$website["website_id"]}', 'onestepcheckout/sortordering_fields/save_in_address_book', '99'),
		('websites', '{$website["website_id"]}', 'onestepcheckout/sortordering_fields/street', '9'),
		('websites', '{$website["website_id"]}', 'onestepcheckout/sortordering_fields/taxvat', '99'),
		('websites', '{$website["website_id"]}', 'onestepcheckout/sortordering_fields/telephone', '4'),
		('websites', '{$website["website_id"]}', 'onestepcheckout/sortordering_fields/use_for_shipping_yes', '99'),
		('websites', '{$website["website_id"]}', 'onestepcheckout/terms/enable_default_terms', '0'),
		('websites', '{$website["website_id"]}', 'onestepcheckout/terms/enable_terms', '1'),
		('websites', '{$website["website_id"]}', 'onestepcheckout/terms/enable_textarea', '1'),
		('websites', '{$website["website_id"]}', 'onestepcheckout/terms/terms_contents', '<strong>INFORMAÇÕES PRELIMINARES</strong><br />
<p>Estes Termos e Condições de Uso do site são aplicáveis ao uso dos serviços de compra online pelo site “<a href =\'http://doctorvetsonline.com.br\'>doctorvetsonline.com.br”</a>.</p>
<p> Para utilizar os serviços oferecidos pelo nosso site, você deverá necessariamente aceitar as regras estabelecidas nestes Termos e Condições de Uso.</p>
<p> Caso você não concorde com estes Termos e Condições de Uso, não poderá fazer uso dos serviços disponibilizados no site Comercial Doctorsvet.</p>
<p> Antes de efetuar seu cadastro e ter acesso aos produtos e serviços disponibilizados no site Comercial Doctorsvet, você deverá ler, entender e concordar com todas as disposições expostas nestes Termos e Condições de Uso.</p>
<p> Em caso de dúvidas acesse o Portal <a href=\'http://www.doctorvetsonline.com.br\'>www.doctorvetsonline.com.br</a> ou ligue para a central de atendimento 0800 70 70 512.</p>
<p><strong>1. DO SITE Comercial Doctorsvet</strong></p>
<p> A Comercial Doctorsvet é uma plataforma online destinada exclusivamente a atender petshops e clínicas veterinárias (“Usuário” ou “Usuários”). A Comercial Doctorsvet tem por objetivo ser um canal entre os Usuários e os fabricantes de produtos veterinários (“Fornecedor” ou “Fornecedores”). Os Usuários interessados em adquirir produtos veterinários podem encontrar e adquirir, através da plataforma Comercial Doctorsvet, produtos de Fornecedores que a Comercial Doctorsvet representa e distribui em sua região (“Produto” ou “Produtos”).</p>
<p>A compra e a venda dos Produtos são realizadas diretamente entre os Usuários e a Comercial Doctorsvet, no ambiente da plataforma Comercial Doctorsvet ou diretamente com seu representante de venda. A plataforma online Comercial Doctorsvet existe para facilitar a comercialização de produtos com seus Usuários.</p>
<p>A Comercial Doctorsvet é um canal facilitador para vender seus produtos para os Usuários interessados em adquirir os Produtos ofertados.</p>
<p><strong>2.CADASTRO DE USUÁRIO</strong></p>
<p>a)<u>Cadastro de Usuários – CNPJ / CRMV</u><br />
Para efetuar o cadastro na plataforma Comercial Doctorsvet, além das informações comuns, será necessário o número de Cadastro Nacional de Pessoa Jurídica (CNPJ) para Usuários pessoas jurídicas ou o número de registro no Conselho Regional de Medicina Veterinária (CRMV) para Usuários veterinários autônomos. Usuários que não possuam inscrição no CNPJ ou CRMV, conforme o caso, não poderão se cadastrar nem adquirir os Produtos disponibilizados na Comercial Doctorsvet. Caso você não tenha nenhuma das duas informações, por favor, entre em contato para podermos analisar seu caso.</p>
<p>b)<u>Capacidade:</u><br/>
Os Usuários deverão ser legalmente capazes para utilizar os serviços disponibilizados na plataforma Comercial Doctorsvet, bem como ter poderes para adquirir os Produtos em nome da pessoa jurídica cadastrada, sendo nulos os negócios jurídicos realizados com pessoas incapazes. Todas as informações prestadas pelos Usuários no cadastramento e/ou no procedimento de compra de Produtos na plataforma Comercial Doctorsvet são de inteira responsabilidade do Usuário.</p>
<p>c)<u>Unicidade do Cadastro:</u><br />
A cada Usuário será permitida a efetivação de somente 1 (um) cadastro. Na verificação de cadastros duplicados, o Usuário será notificado e um de seus cadastros será desabilitado.</p>
<p>d)<u>Acesso à Conta:</u></br>
Ao se cadastrar, o Usuário criará uma conta que poderá sempre ser acessada pela inscrição de um login (e-mail do Usuário) e senha. O Usuário deverá fornecer informações verdadeiras e se responsabilizará por atualizá-las sempre que necessário. A Comercial Doctorsvet não se responsabiliza pelas informações oferecidas nem pela veracidade ou correção das mesmas. Assim, o Usuário responderá de todas as formas legais pela veracidade, autenticidade e exatidão das informações cadastradas e se compromete a informar a Comercial Doctorsvet no caso de qualquer acesso não autorizado, à conta, por terceiros.</p>
<p>e)<u>Informações Adicionais:</u><br/>
Caso a Comercial Doctorsvet julgue por bem solicitar informações adicionais sobre o Usuário, ela poderá assim proceder e, na recusa do Usuário de prestar referidas informações, a Comercial Doctorsvet poderá suspender ou cancelar a sua conta.</p>
<p>f)<u>Recusa de Cadastro:</u><br/>
A Comercial Doctorsvet se reserva o direito de recusar qualquer pedido de cadastro e/ou de cancelar qualquer cadastro efetivado, se julgar, a seu critério, que está em desacordo com as políticas e regras destes Termos e Condições de Uso ou outra política interna da Comercial Doctorsvet.</p>
<p><strong>3.CADASTRO DE FORNECEDOR</strong></p>
<p>Os Fornecedores interessados em firmar parceria com a Comercial Doctorsvet para a disponibilização de seus produtos na plataforma Comercial Doctorsvet poderão contatar a equipe comercial da Comercial Doctorsvet por meio do link de cadastro. Após a formalização do referido contrato, o fornecedor passará a integrar a lista de Fornecedores da Comercial Doctorsvet e poderá ofertar na plataforma Comercial Doctorsvet os seus Produtos para os Usuários.</p>
<p><strong>4.         ALTERAÇÃO DOS TERMOS E CONDIÇÕES DE USO DO SITE Comercial Doctorsvet:</strong></p>
<p>A Comercial Doctorsvet se reserva o direito de alterar, a qualquer tempo, quando jugar necessário, o disposto nestes Termos e Condições de Uso do site, para fins de promover a melhoria dos serviços prestados.</p>
<p>As alterações passarão a ser válidas a partir da sua implementação e não prejudicarão as compras já efetuadas ou operações em andamento, que tenham sido iniciadas anteriormente às alterações.</p>
<p><strong>5.RESPONSABILIDADE</strong></p>
<p>a)<u>Informações sobre os Produtos Ofertados:</u>
A Comercial Doctorsvet não se responsabiliza pelas informações descritas para os Produtos. Todos os dados, especificações, informações quantitativas e qualitativas, descrições, preços, prazos de entrega, condições, imagens e demais informações referentes aos Produtos são de inteira responsabilidade da Comercial Doctorsvet.</p>
<p>b)<u>Falhas na Operacionalização do Site Comercial Doctorsvet:</u>
A Comercial Doctorsvet se compromete a envidar seus melhores esforços e empregar bons recursos técnicos para manter o bom funcionamento do site Comercial Doctorsvet. Contudo, a Comercial Doctorsvet não se responsabiliza por qualquer falha técnica ou operacional no site Comercial Doctorsvet decorrente de defeitos advindos do sistema do Usuário ou de qualquer outra razão externa, fora de seu controle, tais como, sem se limitar a, vírus, ação de hackers, quedas de sistema, interrupções de servidores ou fornecedores de serviços de hospedagem, operadoras de telecomunicações e/ou energia elétrica, dentre outras hipótese que possam ocasionar dificuldade na operacionalização do site Comercial Doctorsvet.</p>
<p>c)<u>Vícios e/ou Defeitos dos Produtos ou Falhas na Entrega dos Produtos:</u><br/>
A compra e a venda dos Produtos são realizadas diretamente entre Usuários e a Comercial Doctorsvet.</p>
<p>  A Comercial Doctorsvet será responsável por qualquer falha no cumprimento das obrigações dela para com o Usuário, por qualquer divergência qualitativa ou quantitativa dos Produtos adquiridos e entregues ou por qualquer atraso na entrega dos Produtos.</p>
<p><strong>7.OBRIGAÇÕES DO USUÁRIO</strong></p>
<p> O Usuário, para adquirir os Produtos da Comercial Doctorsvet obriga-se a seguir todas as regras destes Termos e Condições de Uso.</p>
<p> Na aquisição de Produtos, o Usuário obriga-se a respeitar as políticas e condições de vendas descritas no anúncio pela Comercial Doctorsvet para o respectivo Produto de interesse.</p>
<p>  O Usuário se compromete a indenizar a Comercial Doctorsvet por qualquer ação ajuizada por outros Usuários ou terceiros, em razão de qualquer ato praticado pelo Usuário em descumprimento destes Termos e Condições de Uso e/ou violação de lei ou direitos de terceiros.</p>
<p><strong>8.PRÁTICAS PROIBIDAS NO USO DO SITE</strong></p>
<p> Qualquer prática criminosa, fraudulenta, ilícita ou que expresse má-fé, dolosa ou culposamente, contrária aos bons costumes, que posa gerar danos à Comercial Doctorsvet, ou a outros Usuários, ou, ainda, ao sistema de funcionamento e operacionalização do site Comercial Doctorsvet, seja pelo descumprimento destes Termos e Condições de Uso, seja por qualquer outra prática lesiva, implicará a exclusão do cadastro do Usuário responsável do site Comercial Doctorsvet, sem prejuízo do direito da Comercial Doctorsvet de tomar as medidas legais cabíveis para a responsabilização civil e/ou criminal do Usuário e reparação dos danos suportados.</p>
<p><strong>9.PEDIDO E PAGAMENTO</strong></p>
<p> Ao efetuar um pedido, a Comercial Doctorsvet entrará em contato com o Usuário em no máximo 48 horas para que as condições de pagamento sejam discutidas.</p>
<p> Apenas após a finalização do pagamento que os produtos serão enviados ao Usuário.</p>
<p> Em razão de a transação de compra e venda ocorrer diretamente entre os Usuários e os Fornecedores, em nenhuma hipótese a Comercial Doctorsvet será responsável por restituir valores aos Usuários, sob qualquer fundamento.</p>
<p><strong> 10.TROCAS E DEVOLUÇÕES</strong></p>
<p> As trocas e devoluções dos Produtos deverão ser solicitadas diretamente a Comercial Doctorsvet. As políticas referentes à garantia, troca e devolução dos Produtos deverão ser consultadas nas Políticas da Comercial Doctorsvet. Para saber mais, acesse <a href = \'http://www.doctorvetsonline.com.br\'>www.doctorvetsonline.com.br</a></p>
<p><strong>11.PROPRIEDADE INTELECTUAL</strong></p>
<p> O conteúdo disponibilizado no site Comercial Doctorsvet, tais como, sem se limitar a, textos, gráficos, logotipos, símbolos, compilação de dados, layout, imagens, informações, dados, figuras, bem como o uso comercial da expressão “Comercial Doctorsvet”, como marca, nome de domínio ou denominação social, são de propriedade da Comercial Doctorsvet (ressalvados aqueles que pertencem aos Fornecedores, conforme o caso). Qualquer forma de violação, uso indevido, reprodução, apropriação desses direitos de propriedade intelectual pelo Usuário ou qualquer terceiro, será passível de medidas legais para preservação e manutenção dos direitos a seu titular bem como para reparação de danos, sem prejuízo da eventual responsabilização civil e/ou criminal aplicável.</p>
<p><strong> 12.PROTEÇÃO E USO DE DADOS</strong></p>
<p> A Comercial Doctorsvet manterá sigiloe confidencialidade de todas as informações pessoais dos Usuários que efetivarem cadastro no site. A Comercial Doctorsvet envidará seus melhores esforços para garantir a segurança das informações dos Usuários, utilizando adequados sistemas de segurança, mas não se responsabilizará por ações externas de terceiros que violem as medidas de segurança aplicadas pela Comercial Doctorsvet e que tenham ocorrido de forma inevitável e imprevisível à Comercial Doctorsvet.</p>
<p> Ao se cadastrar no site, o Usuário concorda que a Comercial Doctorsvet poderá armazenar em seu banco de dados, por tempo indeterminado, as informações por ele fornecidas. O banco de dados bem como o seu conteúdo são de propriedade da Comercial Doctorsvet, podendo esta dispor e usar livremente as informações que integram esse banco de dados, contanto que não viole a privacidade das informações estritamente pessoais dos Usuários, assim entendidas aquelas que permitem a identificação individualizada de uma pessoa física ou jurídica.</p>
<p> As informações dos Usuários poderão ser utilizadas para auxiliar a Comercial Doctorsvet a desenvolver estudos e analisar o comportamento, perfil, bem como as áreas e assuntos de interesse desses Usuários na utilização do site Comercial Doctorsvet, com o objetivo de melhorar e aprimorar os serviços oferecidos.</p>
<p> Ao se cadastrar no site, o Usuário concorda também que a Comercial Doctorsvet poderá disponibilizar as informações do seu banco de dados, que não sejam estritamente pessoais, de forma genérica, sem identificação específica e pessoal, para outras finalidades, a seu critério, tais como, sem se limitar a, realização de pesquisas, análises, estudos e levantamentos, operacionalização e aprimoramento dos serviços disponibilizados pelo site, facilitação do uso de sites, serviços ou funcionalidades, comunicação com o Usuário, envio de informativos, anúncios, questionários ou e-mails promocionais, dentre outros.</p>
<p> Em caso de ordem judicial ou exigência de autoridades governamentais competentes, a Comercial Doctorsvet poderá divulgar as informações pessoais do Usuário. Em nenhuma hipótese, a Comercial Doctorsvet compactuará com qualquer ato fraudulento e ou de qualquer outra natureza ilícita que tente violar ou que viole direitos de propriedade intelectual ou direitos de qualquer outra natureza do site ou de seus Usuários.</p>
<p> A qualquer momento, o Usuário poderá solicitar a exclusão da sua conta e, consequentemente, das informações cadastradas. Para certos casos, a Comercial Doctorsvet se reserva o direito de manter, em seus arquivos, os dados pessoais do Usuário da conta excluída para o exclusivo fim de utilizá-los em eventuais litígios ou solução de problemas decorrentes do uso do site Comercial Doctorsvet.</p>
<p><strong> 13.COOKIES</strong></p>
 <p>Ao se cadastrar, o Usuário concorda que a Comercial Doctorsvet poderá utilizar um sistema de monitoramento – <i>cookies<i> – com a finalidade de personalizar o acesso do Usuário no site.</p>
<p> Quando o navegador do Usuário acessar a Comercial Doctorsvet, os cookies permitirão monitorar e identificar assuntos de interesse, perfil e comportamento do Usuário, para que a Comercial Doctorsvet possa entender melhor as necessidades e interesses do Usuário e possibilitar acesso facilitado a informações a eles relacionadas.</p>
<p><strong> 14.LEI APLICÁVEL</strong></p>
<p> Estes Termos e Condições de Uso serão regidos e interpretados de acordo com as leis da República Federativa do Brasil.</p>
<p><strong>15.ACEITE E CONCORDÂNCIA</strong></p>
<p> O Usuário declara que leu e entendeu todo o teor dos presentes Termos e Condições de Uso. O Usuário aceita e concorda com todos os termos aqui dispostos e, ao efetuar o seu cadastro, ratifica e confirma o seu aceite e concordância.</p>'),
		('websites', '{$website["website_id"]}', 'onestepcheckout/terms/terms_title', 'Bem vindo à Comercial Doctorsvet.'),
		('websites', '{$website["website_id"]}', 'privatesales/access/authonly', '0'),
		('websites', '{$website["website_id"]}', 'privatesales/access/catalog', '1'),
		('websites', '{$website["website_id"]}', 'privatesales/access/navigation', '1'),
		('websites', '{$website["website_id"]}', 'privatesales/general/enable', '1'),
		('websites', '{$website["website_id"]}', 'privatesales/registration/disable', '0'),
		('websites', '{$website["website_id"]}', 'privatesales/registration/login_panel', '0'),
		('websites', '{$website["website_id"]}', 'sales_email/order/copy_to', ''),
		('websites', '{$website["website_id"]}', 'sales_email/order/copy_to_rep', '1'),
		('websites', '{$website["website_id"]}', 'sales_email/order/template', '35'),
		('websites', '{$website["website_id"]}', 'tm_ajaxsearch/general/attributes', 'name,sku'),
		('websites', '{$website["website_id"]}', 'tm_ajaxsearch/general/enabled', '1'),
		('websites', '{$website["website_id"]}', 'tm_ajaxsearch/general/show_category_filter', '0'),
		('websites', '{$website["website_id"]}', 'trans_email/ident_custom1/email', 'ti+doctorsvet@4vets.com.br'),
		('websites', '{$website["website_id"]}', 'trans_email/ident_custom1/name', 'DoctorsVet'),
		('websites', '{$website["website_id"]}', 'trans_email/ident_custom2/email', 'ti+doctorsvet@4vets.com.br'),
		('websites', '{$website["website_id"]}', 'trans_email/ident_custom2/name', 'DoctorsVet'),
		('websites', '{$website["website_id"]}', 'trans_email/ident_general/email', 'ti+doctorsvet@4vets.com.br'),
		('websites', '{$website["website_id"]}', 'trans_email/ident_general/name', 'DoctorsVet'),
		('websites', '{$website["website_id"]}', 'trans_email/ident_sales/email', 'ti+doctorsvet@4vets.com.br'),
		('websites', '{$website["website_id"]}', 'trans_email/ident_sales/name', 'DoctorsVet Vendas'),
		('websites', '{$website["website_id"]}', 'trans_email/ident_support/email', 'ti+doctorsvet@4vets.com.br'),
		('websites', '{$website["website_id"]}', 'trans_email/ident_support/name', 'DoctorsVet Suporte ao cliente'),
		('websites', '{$website["website_id"]}', 'web/cookie/cookie_domain', '.4vets.com.br'),
		('websites', '{$website["website_id"]}', 'web/secure/base_url', 'http://doctorsvet.4vets.com.br/'),
		('websites', '{$website["website_id"]}', 'web/unsecure/base_url', 'http://doctorsvet.4vets.com.br/'),
		('websites', '{$website["website_id"]}', 'web/url/use_store', '0')
	;");
}
	/* Install Omega Website Config */

	$store = Mage::getResourceModel('core/store_collection')->addFieldToFilter('code', 'doctorsvet')->getData()[0];

	if (isset($store['store_id']))
	{
		$installer->run("
			DELETE FROM core_config_data
			WHERE scope = 'stores'
			AND scope_id = {$store['store_id']};
		;");

		$installer->run("
			INSERT INTO `core_config_data` (`scope`, `scope_id`, `path`, `value`) VALUES
			
		('stores', '{$store["store_id"]}', 'askit/general/enabled', '1'),
		('stores', '{$store["store_id"]}', 'custom_menu/general/headline1', 'Compre por'),
		('stores', '{$store["store_id"]}', 'custom_menu/general/headline2', 'Marcas'),
		('stores', '{$store["store_id"]}', 'design/categories/menu_root_category', '3'),
		('stores', '{$store["store_id"]}', 'design/theme/layout', 'doctorsvet'),
		('stores', '{$store["store_id"]}', 'design/theme/locale', 'doctorsvet'),
		('stores', '{$store["store_id"]}', 'design/theme/skin', 'doctorsvet'),
		('stores', '{$store["store_id"]}', 'design/theme/template', 'doctorsvet'),
		('stores', '{$store["store_id"]}', 'dev/debug/template_hints', '0'),
		('stores', '{$store["store_id"]}', 'dev/debug/template_hints_blocks', '0'),
		('stores', '{$store["store_id"]}', 'easycatalogimg/category/enabled_for_anchor', '0'),
		('stores', '{$store["store_id"]}', 'easycatalogimg/category/enabled_for_default', '0'),
		('stores', '{$store["store_id"]}', 'easycatalogimg/general/enabled', '1'),
		('stores', '{$store["store_id"]}', 'easyslide/general/load', '1'),
		('stores', '{$store["store_id"]}', 'facebooklb/category_products/color', 'light'),
		('stores', '{$store["store_id"]}', 'facebooklb/category_products/enabled', '0'),
		('stores', '{$store["store_id"]}', 'facebooklb/category_products/layout', 'button_count'),
		('stores', '{$store["store_id"]}', 'facebooklb/category_products/send', '0'),
		('stores', '{$store["store_id"]}', 'facebooklb/category_products/showfaces', '0'),
		('stores', '{$store["store_id"]}', 'facebooklb/category_products/width', '350'),
		('stores', '{$store["store_id"]}', 'facebooklb/productlike/color', 'light'),
		('stores', '{$store["store_id"]}', 'facebooklb/productlike/enabled', '1'),
		('stores', '{$store["store_id"]}', 'facebooklb/productlike/layout', 'button_count'),
		('stores', '{$store["store_id"]}', 'facebooklb/productlike/send', '1'),
		('stores', '{$store["store_id"]}', 'facebooklb/productlike/showfaces', '0'),
		('stores', '{$store["store_id"]}', 'facebooklb/productlike/width', '350'),
		('stores', '{$store["store_id"]}', 'general/categories/menu_root_category', '365'),
		('stores', '{$store["store_id"]}', 'general/country/allow', 'BR'),
		('stores', '{$store["store_id"]}', 'general/country/default', 'BR'),
		('stores', '{$store["store_id"]}', 'general/store_information/alias', ''),
		('stores', '{$store["store_id"]}', 'lightboxpro/general/enabled', '1'),
		('stores', '{$store["store_id"]}', 'lightboxpro/size/main', '512x512'),
		('stores', '{$store["store_id"]}', 'lightboxpro/size/maxWindow', '800x600'),
		('stores', '{$store["store_id"]}', 'lightboxpro/size/thumbnail', '112x112'),
		('stores', '{$store["store_id"]}', 'navigationpro/top/enabled', '1'),
		('stores', '{$store["store_id"]}', 'richsnippets/general/enabled', '1'),
		('stores', '{$store["store_id"]}', 'richsnippets/general/manufacturer', 'manufacturer'),
		('stores', '{$store["store_id"]}', 'soldtogether/customer/enabled', '1'),
		('stores', '{$store["store_id"]}', 'soldtogether/general/enabled', '1'),
		('stores', '{$store["store_id"]}', 'soldtogether/general/random', '1'),
		('stores', '{$store["store_id"]}', 'soldtogether/order/addtocartcheckbox', '0'),
		('stores', '{$store["store_id"]}', 'soldtogether/order/amazonestyle', '1'),
		('stores', '{$store["store_id"]}', 'soldtogether/order/enabled', '1'),
		('stores', '{$store["store_id"]}', 'suggestpage/general/show_after_addtocart', '1'),
		('stores', '{$store["store_id"]}', 'tm_easytabs/general/enabled', '1'),
		('stores', '{$store["store_id"]}', 'web/secure/base_url', 'http://doctorsvet.4vets.com.br/'),
		('stores', '{$store["store_id"]}', 'web/unsecure/base_url', 'http://doctorsvet.4vets.com.br/'),
		('stores', '{$store["store_id"]}', 'web/url/use_store', '0')
		;");
	}

$installer->endSetup();

