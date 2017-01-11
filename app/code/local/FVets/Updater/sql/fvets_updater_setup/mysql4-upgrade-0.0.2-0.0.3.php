<?php

$installer = $this;

$installer->startSetup();

Mage::register('isSecureArea', 1);

// Force the store to be admin
Mage::app()->setUpdateMode(false);
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);


/** Install default Config */




/** Install Omega Website Config */

$website = Mage::getResourceModel('core/website_collection')->addFieldToFilter('code', 'omega')->getData()[0];

if (isset($website['website_id']))
{
	$installer->run("
		DELETE FROM core_config_data
		WHERE scope = 'websites'
		AND scope_id = {$website['website_id']};
	;");

	$installer->run("
		INSERT INTO `core_config_data` (`scope`, `scope_id`, `path`, `value`) VALUES
		('websites', '{$website['website_id']}', 'ajax_pro/catalogCategoryView/type', 'button'),
		('websites', '{$website['website_id']}', 'ajax_pro/checkoutCart/messageHandle', 'tm_ajaxpro_checkout_cart_add_suggestpage'),
		('websites', '{$website['website_id']}', 'ajax_pro/effect/enabled_overlay', '1'),
		('websites', '{$website['website_id']}', 'ajax_pro/effect/overlay_opacity', '0.5'),
		('websites', '{$website['website_id']}', 'ajax_pro/general/enabled', '1'),
		('websites', '{$website['website_id']}', 'ajax_pro/general/useLoginFormBlock', '1'),
		('websites', '{$website['website_id']}', 'carriers/freeshipping/active', '1'),
		('websites', '{$website['website_id']}', 'carriers/freeshipping/name', 'Frete a combinar'),
		('websites', '{$website['website_id']}', 'carriers/freeshipping/title', 'Envio peclam'),
		('websites', '{$website['website_id']}', 'carriers/tablerate/import', '1409953796'),
		('websites', '{$website['website_id']}', 'catalog/frontend/list_mode', 'list-grid'),
		('websites', '{$website['website_id']}', 'catalog/placeholder/image_placeholder', 'websites/2/avatar_produto.jpg'),
		('websites', '{$website['website_id']}', 'catalog/placeholder/small_image_placeholder', 'websites/2/small_produto.jpg'),
		('websites', '{$website['website_id']}', 'catalog/price/group_as_normal', '1'),
		('websites', '{$website['website_id']}', 'checkout/options/enable_agreements', '1'),
		('websites', '{$website['website_id']}', 'confirmcustomer/admin_notification/enabled', '0'),
		('websites', '{$website['website_id']}', 'confirmcustomer/admin_notification/recipients', NULL),
		('websites', '{$website['website_id']}', 'confirmcustomer/admin_notification/template', '22'),
		('websites', '{$website['website_id']}', 'confirmcustomer/email/template', '19'),
		('websites', '{$website['website_id']}', 'confirmcustomer/general/enabled', '1'),
		('websites', '{$website['website_id']}', 'confirmcustomer/general/welcome_email', '1'),
		('websites', '{$website['website_id']}', 'confirmcustomer/redirect/cms_page', 'account-awaiting-approval|33'),
		('websites', '{$website['website_id']}', 'customer/create_account/email_confirmed_template', '19'),
		('websites', '{$website['website_id']}', 'customer/create_account/email_template', '17'),
		('websites', '{$website['website_id']}', 'customer/password/forgot_email_template', '18'),
		('websites', '{$website['website_id']}', 'customer/password/remind_email_template', '20'),
		('websites', '{$website['website_id']}', 'custom_menu/columns/count', '3'),
		('websites', '{$website['website_id']}', 'custom_menu/columns/divided_horizontally', '1'),
		('websites', '{$website['website_id']}', 'custom_menu/columns/integrate', '1'),
		('websites', '{$website['website_id']}', 'custom_menu/general/ajax_load_content', '0'),
		('websites', '{$website['website_id']}', 'custom_menu/general/display_empty_categories', '0'),
		('websites', '{$website['website_id']}', 'custom_menu/general/enabled', '1'),
		('websites', '{$website['website_id']}', 'custom_menu/general/mobile_menu', '1'),
		('websites', '{$website['website_id']}', 'custom_menu/general/rtl', '0'),
		('websites', '{$website['website_id']}', 'custom_menu/general/show_home_link', '0'),
		('websites', '{$website['website_id']}', 'custom_menu/popup/delay_displaying', '10'),
		('websites', '{$website['website_id']}', 'custom_menu/popup/delay_hiding', '10'),
		('websites', '{$website['website_id']}', 'custom_menu/popup/top_offset', '0'),
		('websites', '{$website['website_id']}', 'custom_menu/popup/width', '0'),
		('websites', '{$website['website_id']}', 'design/email/logo', 'websites/3/logo_email.png'),
		('websites', '{$website['website_id']}', 'design/email/logo_alt', 'Omega Online'),
		('websites', '{$website['website_id']}', 'design/footer/copyright', '&copy; 2014 Omega Online. Todos os direitos reservados.'),
		('websites', '{$website['website_id']}', 'design/head/default_description', 'Omega Online'),
		('websites', '{$website['website_id']}', 'design/head/default_keywords', 'Omega, 4Vets'),
		('websites', '{$website['website_id']}', 'design/head/default_title', 'Omega Online'),
		('websites', '{$website['website_id']}', 'design/head/includes', NULL),
		('websites', '{$website['website_id']}', 'design/head/shortcut_icon', 'websites/3/favicon.ico'),
		('websites', '{$website['website_id']}', 'design/head/title_prefix', 'Omega Online - '),
		('websites', '{$website['website_id']}', 'design/header/logo_alt', 'Omega Online'),
		('websites', '{$website['website_id']}', 'design/header/logo_src', 'websites/3/logo-omega_1.png'),
		('websites', '{$website['website_id']}', 'design/header/welcome', 'Bem-vindo(a)!'),
		('websites', '{$website['website_id']}', 'design/package/name', 'argento'),
		('websites', '{$website['website_id']}', 'design/theme/after_default', 'fvets_pure'),
		('websites', '{$website['website_id']}', 'design/theme/layout', 'omega'),
		('websites', '{$website['website_id']}', 'design/theme/locale', 'omega'),
		('websites', '{$website['website_id']}', 'design/theme/skin', 'omega'),
		('websites', '{$website['website_id']}', 'design/theme/template', 'omega'),
		('websites', '{$website['website_id']}', 'dev/css/merge_css_files', '1'),
		('websites', '{$website['website_id']}', 'dev/debug/template_hints', '0'),
		('websites', '{$website['website_id']}', 'dev/debug/template_hints_blocks', '0'),
		('websites', '{$website['website_id']}', 'dev/js/merge_files', '0'),
		('websites', '{$website['website_id']}', 'dev/translate_inline/active', '0'),
		('websites', '{$website['website_id']}', 'general/country/allow', 'BR'),
		('websites', '{$website['website_id']}', 'general/country/default', 'BR'),
		('websites', '{$website['website_id']}', 'general/store_information/address', 'Avenida Presidente Wilson, 1628, São Paulo - SP\r\nCEP 03107-001'),
		('websites', '{$website['website_id']}', 'general/store_information/is_brand', '0'),
		('websites', '{$website['website_id']}', 'general/store_information/name', 'Omega Online'),
		('websites', '{$website['website_id']}', 'general/store_information/phone', '(11) 2060-0258'),
		('websites', '{$website['website_id']}', 'lightboxpro/gallery/behaviour_enableLightboxEfect', '0'),
		('websites', '{$website['website_id']}', 'magefm_customer/general/self_customer_addres_phone', '1'),
		('websites', '{$website['website_id']}', 'newsletter/subscription/disablenewslettersuccesses', '1'),
		('websites', '{$website['website_id']}', 'onestepcheckout/ajax_update/ajax_save_billing_fields', 'country'),
		('websites', '{$website['website_id']}', 'onestepcheckout/exclude_fields/enable_address_fields', '1'),
		('websites', '{$website['website_id']}', 'onestepcheckout/exclude_fields/enable_comments', '1'),
		('websites', '{$website['website_id']}', 'onestepcheckout/exclude_fields/enable_comments_default', '1'),
		('websites', '{$website['website_id']}', 'onestepcheckout/exclude_fields/enable_discount', '1'),
		('websites', '{$website['website_id']}', 'onestepcheckout/exclude_fields/exclude_bairro', '0'),
		('websites', '{$website['website_id']}', 'onestepcheckout/exclude_fields/exclude_cnpj', '0'),
		('websites', '{$website['website_id']}', 'onestepcheckout/exclude_fields/exclude_company', '1'),
		('websites', '{$website['website_id']}', 'onestepcheckout/exclude_fields/exclude_cpf', '0'),
		('websites', '{$website['website_id']}', 'onestepcheckout/exclude_fields/exclude_fax', '1'),
		('websites', '{$website['website_id']}', 'onestepcheckout/exclude_fields/exclude_inscricao_estadual', '0'),
		('websites', '{$website['website_id']}', 'onestepcheckout/exclude_fields/exclude_mobilephone', '0'),
		('websites', '{$website['website_id']}', 'onestepcheckout/exclude_fields/exclude_razao_social', '0'),
		('websites', '{$website['website_id']}', 'onestepcheckout/exclude_fields/exclude_saveaddress', '1'),
		('websites', '{$website['website_id']}', 'onestepcheckout/general/checkout_description', 'Por favor, insira  as informações abaixo para completar seu pedido'),
		('websites', '{$website['website_id']}', 'onestepcheckout/general/checkout_title', 'Finalizar Pedido'),
		('websites', '{$website['website_id']}', 'onestepcheckout/general/default_country', 'BR'),
		('websites', '{$website['website_id']}', 'onestepcheckout/general/default_shipping_if_one', '1'),
		('websites', '{$website['website_id']}', 'onestepcheckout/general/enable_different_shipping', '0'),
		('websites', '{$website['website_id']}', 'onestepcheckout/general/hide_payment_method', '1'),
		('websites', '{$website['website_id']}', 'onestepcheckout/general/hide_shipping_method', '1'),
		('websites', '{$website['website_id']}', 'onestepcheckout/general/rewrite_checkout_links', '1'),
		('websites', '{$website['website_id']}', 'onestepcheckout/general/single_address', '1'),
		('websites', '{$website['website_id']}', 'onestepcheckout/general/update_address_and_customer', '1'),
		('websites', '{$website['website_id']}', 'onestepcheckout/registration/registration_mode', 'disable_registration'),
		('websites', '{$website['website_id']}', 'onestepcheckout/sortordering_fields/bairro', '8'),
		('websites', '{$website['website_id']}', 'onestepcheckout/sortordering_fields/city', '7'),
		('websites', '{$website['website_id']}', 'onestepcheckout/sortordering_fields/cnpj', '2'),
		('websites', '{$website['website_id']}', 'onestepcheckout/sortordering_fields/company', '99'),
		('websites', '{$website['website_id']}', 'onestepcheckout/sortordering_fields/confirm_password', '99'),
		('websites', '{$website['website_id']}', 'onestepcheckout/sortordering_fields/country_id', '99'),
		('websites', '{$website['website_id']}', 'onestepcheckout/sortordering_fields/cpf', '12'),
		('websites', '{$website['website_id']}', 'onestepcheckout/sortordering_fields/create_account', '99'),
		('websites', '{$website['website_id']}', 'onestepcheckout/sortordering_fields/dob', '99'),
		('websites', '{$website['website_id']}', 'onestepcheckout/sortordering_fields/email', '14'),
		('websites', '{$website['website_id']}', 'onestepcheckout/sortordering_fields/fax', '99'),
		('websites', '{$website['website_id']}', 'onestepcheckout/sortordering_fields/firstname', '10'),
		('websites', '{$website['website_id']}', 'onestepcheckout/sortordering_fields/gender', '99'),
		('websites', '{$website['website_id']}', 'onestepcheckout/sortordering_fields/inscricao_estadual', '3'),
		('websites', '{$website['website_id']}', 'onestepcheckout/sortordering_fields/lastname', '11'),
		('websites', '{$website['website_id']}', 'onestepcheckout/sortordering_fields/mobilephone', '13'),
		('websites', '{$website['website_id']}', 'onestepcheckout/sortordering_fields/password', '99'),
		('websites', '{$website['website_id']}', 'onestepcheckout/sortordering_fields/postcode', '5'),
		('websites', '{$website['website_id']}', 'onestepcheckout/sortordering_fields/razao_social', '1'),
		('websites', '{$website['website_id']}', 'onestepcheckout/sortordering_fields/region_id', '6'),
		('websites', '{$website['website_id']}', 'onestepcheckout/sortordering_fields/save_in_address_book', '99'),
		('websites', '{$website['website_id']}', 'onestepcheckout/sortordering_fields/street', '9'),
		('websites', '{$website['website_id']}', 'onestepcheckout/sortordering_fields/taxvat', '99'),
		('websites', '{$website['website_id']}', 'onestepcheckout/sortordering_fields/telephone', '4'),
		('websites', '{$website['website_id']}', 'onestepcheckout/sortordering_fields/use_for_shipping_yes', '99'),
		('websites', '{$website['website_id']}', 'onestepcheckout/terms/enable_default_terms', '0'),
		('websites', '{$website['website_id']}', 'onestepcheckout/terms/enable_terms', '1'),
		('websites', '{$website['website_id']}', 'onestepcheckout/terms/enable_textarea', '1'),
		('websites', '{$website['website_id']}', 'onestepcheckout/terms/terms_contents', '<strong>INFORMAÇÕES PRELIMINARES</strong><br />\r\n<p>Estes Termos e Condições de Uso do site são aplicáveis ao uso dos serviços de compra online pelo site “<a href =''http://comercialomega.com.br''>comercialomega.com.br”</a>.</p>\r\n<p> Para utilizar os serviços oferecidos pelo nosso site, você deverá necessariamente aceitar as regras estabelecidas nestes Termos e Condições de Uso.</p>\r\n<p> Caso você não concorde com estes Termos e Condições de Uso, não poderá fazer uso dos serviços disponibilizados no site Comercial Omega.</p>\r\n<p> Antes de efetuar seu cadastro e ter acesso aos produtos e serviços disponibilizados no site Comercial Omega, você deverá ler, entender e concordar com todas as disposições expostas nestes Termos e Condições de Uso.</p>\r\n<p> Em caso de dúvidas acesse o Portal <a href=''http://www.comercialomega.com.br''>www.comercialomega.com.br</a> ou ligue para a central de atendimento 0800 70 70 512.</p>\r\n<p><strong>1. DO SITE Comercial Omega</strong></p>\r\n<p> A Comercial Omega é uma plataforma online destinada exclusivamente a atender petshops e clínicas veterinárias (“Usuário” ou “Usuários”). A Comercial Omega tem por objetivo ser um canal entre os Usuários e os fabricantes de produtos veterinários (“Fornecedor” ou “Fornecedores”). Os Usuários interessados em adquirir produtos veterinários podem encontrar e adquirir, através da plataforma Comercial Omega, produtos de Fornecedores que a Comercial Omega representa e distribui em sua região (“Produto” ou “Produtos”).</p>\r\n<p>A compra e a venda dos Produtos são realizadas diretamente entre os Usuários e a Comercial Omega, no ambiente da plataforma Comercial Omega ou diretamente com seu representante de venda. A plataforma online Comercial Omega existe para facilitar a comercialização de produtos com seus Usuários.</p>\r\n<p>A Comercial Omega é um canal facilitador para vender seus produtos para os Usuários interessados em adquirir os Produtos ofertados.</p>\r\n<p><strong>2.CADASTRO DE USUÁRIO</strong></p>\r\n<p>a)<u>Cadastro de Usuários – CNPJ / CRMV</u><br />\r\nPara efetuar o cadastro na plataforma Comercial Omega, além das informações comuns, será necessário o número de Cadastro Nacional de Pessoa Jurídica (CNPJ) para Usuários pessoas jurídicas ou o número de registro no Conselho Regional de Medicina Veterinária (CRMV) para Usuários veterinários autônomos. Usuários que não possuam inscrição no CNPJ ou CRMV, conforme o caso, não poderão se cadastrar nem adquirir os Produtos disponibilizados na Comercial Omega. Caso você não tenha nenhuma das duas informações, por favor, entre em contato para podermos analisar seu caso.</p>\r\n<p>b)<u>Capacidade:</u><br/>\r\nOs Usuários deverão ser legalmente capazes para utilizar os serviços disponibilizados na plataforma Comercial Omega, bem como ter poderes para adquirir os Produtos em nome da pessoa jurídica cadastrada, sendo nulos os negócios jurídicos realizados com pessoas incapazes. Todas as informações prestadas pelos Usuários no cadastramento e/ou no procedimento de compra de Produtos na plataforma Comercial Omega são de inteira responsabilidade do Usuário.</p>\r\n<p>c)<u>Unicidade do Cadastro:</u><br />\r\nA cada Usuário será permitida a efetivação de somente 1 (um) cadastro. Na verificação de cadastros duplicados, o Usuário será notificado e um de seus cadastros será desabilitado.</p>\r\n<p>d)<u>Acesso à Conta:</u></br>\r\nAo se cadastrar, o Usuário criará uma conta que poderá sempre ser acessada pela inscrição de um login (e-mail do Usuário) e senha. O Usuário deverá fornecer informações verdadeiras e se responsabilizará por atualizá-las sempre que necessário. A Comercial Omega não se responsabiliza pelas informações oferecidas nem pela veracidade ou correção das mesmas. Assim, o Usuário responderá de todas as formas legais pela veracidade, autenticidade e exatidão das informações cadastradas e se compromete a informar a Comercial Omega no caso de qualquer acesso não autorizado, à conta, por terceiros.</p>\r\n<p>e)<u>Informações Adicionais:</u><br/>\r\nCaso a Comercial Omega julgue por bem solicitar informações adicionais sobre o Usuário, ela poderá assim proceder e, na recusa do Usuário de prestar referidas informações, a Comercial Omega poderá suspender ou cancelar a sua conta.</p>\r\n<p>f)<u>Recusa de Cadastro:</u><br/>\r\nA Comercial Omega se reserva o direito de recusar qualquer pedido de cadastro e/ou de cancelar qualquer cadastro efetivado, se julgar, a seu critério, que está em desacordo com as políticas e regras destes Termos e Condições de Uso ou outra política interna da Comercial Omega.</p>\r\n<p><strong>3.CADASTRO DE FORNECEDOR</strong></p>\r\n<p>Os Fornecedores interessados em firmar parceria com a Comercial Omega para a disponibilização de seus produtos na plataforma Comercial Omega poderão contatar a equipe comercial da Comercial Omega por meio do link de cadastro. Após a formalização do referido contrato, o fornecedor passará a integrar a lista de Fornecedores da Comercial Omega e poderá ofertar na plataforma Comercial Omega os seus Produtos para os Usuários.</p>\r\n<p><strong>4.         ALTERAÇÃO DOS TERMOS E CONDIÇÕES DE USO DO SITE Comercial Omega:</strong></p>\r\n<p>A Comercial Omega se reserva o direito de alterar, a qualquer tempo, quando jugar necessário, o disposto nestes Termos e Condições de Uso do site, para fins de promover a melhoria dos serviços prestados.</p>\r\n<p>As alterações passarão a ser válidas a partir da sua implementação e não prejudicarão as compras já efetuadas ou operações em andamento, que tenham sido iniciadas anteriormente às alterações.</p>\r\n<p><strong>5.RESPONSABILIDADE</strong></p>\r\n<p>a)<u>Informações sobre os Produtos Ofertados:</u>\r\nA Comercial Omega não se responsabiliza pelas informações descritas para os Produtos. Todos os dados, especificações, informações quantitativas e qualitativas, descrições, preços, prazos de entrega, condições, imagens e demais informações referentes aos Produtos são de inteira responsabilidade da Comercial Omega.</p>\r\n<p>b)<u>Falhas na Operacionalização do Site Comercial Omega:</u>\r\nA Comercial Omega se compromete a envidar seus melhores esforços e empregar bons recursos técnicos para manter o bom funcionamento do site Comercial Omega. Contudo, a Comercial Omega não se responsabiliza por qualquer falha técnica ou operacional no site Comercial Omega decorrente de defeitos advindos do sistema do Usuário ou de qualquer outra razão externa, fora de seu controle, tais como, sem se limitar a, vírus, ação de hackers, quedas de sistema, interrupções de servidores ou fornecedores de serviços de hospedagem, operadoras de telecomunicações e/ou energia elétrica, dentre outras hipótese que possam ocasionar dificuldade na operacionalização do site Comercial Omega.</p>\r\n<p>c)<u>Vícios e/ou Defeitos dos Produtos ou Falhas na Entrega dos Produtos:</u><br/>\r\nA compra e a venda dos Produtos são realizadas diretamente entre Usuários e a Comercial Omega.</p>\r\n<p>  A Comercial Omega será responsável por qualquer falha no cumprimento das obrigações dela para com o Usuário, por qualquer divergência qualitativa ou quantitativa dos Produtos adquiridos e entregues ou por qualquer atraso na entrega dos Produtos.</p>\r\n<p><strong>7.OBRIGAÇÕES DO USUÁRIO</strong></p>\r\n<p> O Usuário, para adquirir os Produtos da Comercial Omega obriga-se a seguir todas as regras destes Termos e Condições de Uso.</p>\r\n<p> Na aquisição de Produtos, o Usuário obriga-se a respeitar as políticas e condições de vendas descritas no anúncio pela Comercial Omega para o respectivo Produto de interesse.</p>\r\n<p>  O Usuário se compromete a indenizar a Comercial Omega por qualquer ação ajuizada por outros Usuários ou terceiros, em razão de qualquer ato praticado pelo Usuário em descumprimento destes Termos e Condições de Uso e/ou violação de lei ou direitos de terceiros.</p>\r\n<p><strong>8.PRÁTICAS PROIBIDAS NO USO DO SITE</strong></p>\r\n<p> Qualquer prática criminosa, fraudulenta, ilícita ou que expresse má-fé, dolosa ou culposamente, contrária aos bons costumes, que posa gerar danos à Comercial Omega, ou a outros Usuários, ou, ainda, ao sistema de funcionamento e operacionalização do site Comercial Omega, seja pelo descumprimento destes Termos e Condições de Uso, seja por qualquer outra prática lesiva, implicará a exclusão do cadastro do Usuário responsável do site Comercial Omega, sem prejuízo do direito da Comercial Omega de tomar as medidas legais cabíveis para a responsabilização civil e/ou criminal do Usuário e reparação dos danos suportados.</p>\r\n<p><strong>9.PEDIDO E PAGAMENTO</strong></p>\r\n<p> Ao efetuar um pedido, a Comercial Omega entrará em contato com o Usuário em no máximo 48 horas para que as condições de pagamento sejam discutidas.</p>\r\n<p> Apenas após a finalização do pagamento que os produtos serão enviados ao Usuário.</p>\r\n<p> Em razão de a transação de compra e venda ocorrer diretamente entre os Usuários e os Fornecedores, em nenhuma hipótese a Comercial Omega será responsável por restituir valores aos Usuários, sob qualquer fundamento.</p>\r\n<p><strong> 10.TROCAS E DEVOLUÇÕES</strong></p>\r\n<p> As trocas e devoluções dos Produtos deverão ser solicitadas diretamente a Comercial Omega. As políticas referentes à garantia, troca e devolução dos Produtos deverão ser consultadas nas Políticas da Comercial Omega. Para saber mais, acesse <a href = ''http://http://www.comercialomega.com.br''>www.comercialomega.com.br</a></p>\r\n<p><strong>11.PROPRIEDADE INTELECTUAL</strong></p>\r\n<p> O conteúdo disponibilizado no site Comercial Omega, tais como, sem se limitar a, textos, gráficos, logotipos, símbolos, compilação de dados, layout, imagens, informações, dados, figuras, bem como o uso comercial da expressão “Comercial Omega”, como marca, nome de domínio ou denominação social, são de propriedade da Comercial Omega (ressalvados aqueles que pertencem aos Fornecedores, conforme o caso). Qualquer forma de violação, uso indevido, reprodução, apropriação desses direitos de propriedade intelectual pelo Usuário ou qualquer terceiro, será passível de medidas legais para preservação e manutenção dos direitos a seu titular bem como para reparação de danos, sem prejuízo da eventual responsabilização civil e/ou criminal aplicável.</p>\r\n<p><strong> 12.PROTEÇÃO E USO DE DADOS</strong></p>\r\n<p> A Comercial Omega manterá sigiloe confidencialidade de todas as informações pessoais dos Usuários que efetivarem cadastro no site. A Comercial Omega envidará seus melhores esforços para garantir a segurança das informações dos Usuários, utilizando adequados sistemas de segurança, mas não se responsabilizará por ações externas de terceiros que violem as medidas de segurança aplicadas pela Comercial Omega e que tenham ocorrido de forma inevitável e imprevisível à Comercial Omega.</p>\r\n<p> Ao se cadastrar no site, o Usuário concorda que a Comercial Omega poderá armazenar em seu banco de dados, por tempo indeterminado, as informações por ele fornecidas. O banco de dados bem como o seu conteúdo são de propriedade da Comercial Omega, podendo esta dispor e usar livremente as informações que integram esse banco de dados, contanto que não viole a privacidade das informações estritamente pessoais dos Usuários, assim entendidas aquelas que permitem a identificação individualizada de uma pessoa física ou jurídica.</p>\r\n<p> As informações dos Usuários poderão ser utilizadas para auxiliar a Comercial Omega a desenvolver estudos e analisar o comportamento, perfil, bem como as áreas e assuntos de interesse desses Usuários na utilização do site Comercial Omega, com o objetivo de melhorar e aprimorar os serviços oferecidos.</p>\r\n<p> Ao se cadastrar no site, o Usuário concorda também que a Comercial Omega poderá disponibilizar as informações do seu banco de dados, que não sejam estritamente pessoais, de forma genérica, sem identificação específica e pessoal, para outras finalidades, a seu critério, tais como, sem se limitar a, realização de pesquisas, análises, estudos e levantamentos, operacionalização e aprimoramento dos serviços disponibilizados pelo site, facilitação do uso de sites, serviços ou funcionalidades, comunicação com o Usuário, envio de informativos, anúncios, questionários ou e-mails promocionais, dentre outros.</p>\r\n<p> Em caso de ordem judicial ou exigência de autoridades governamentais competentes, a Comercial Omega poderá divulgar as informações pessoais do Usuário. Em nenhuma hipótese, a Comercial Omega compactuará com qualquer ato fraudulento e ou de qualquer outra natureza ilícita que tente violar ou que viole direitos de propriedade intelectual ou direitos de qualquer outra natureza do site ou de seus Usuários.</p>\r\n<p> A qualquer momento, o Usuário poderá solicitar a exclusão da sua conta e, consequentemente, das informações cadastradas. Para certos casos, a Comercial Omega se reserva o direito de manter, em seus arquivos, os dados pessoais do Usuário da conta excluída para o exclusivo fim de utilizá-los em eventuais litígios ou solução de problemas decorrentes do uso do site Comercial Omega.</p>\r\n<p><strong> 13.COOKIES</strong></p>\r\n <p>Ao se cadastrar, o Usuário concorda que a Comercial Omega poderá utilizar um sistema de monitoramento – <i>cookies<i> – com a finalidade de personalizar o acesso do Usuário no site.</p>\r\n<p> Quando o navegador do Usuário acessar a Comercial Omega, os cookies permitirão monitorar e identificar assuntos de interesse, perfil e comportamento do Usuário, para que a Comercial Omega possa entender melhor as necessidades e interesses do Usuário e possibilitar acesso facilitado a informações a eles relacionadas.</p>\r\n<p><strong> 14.LEI APLICÁVEL</strong></p>\r\n<p> Estes Termos e Condições de Uso serão regidos e interpretados de acordo com as leis da República Federativa do Brasil.</p>\r\n<p><strong>15.ACEITE E CONCORDÂNCIA</strong></p>\r\n<p> O Usuário declara que leu e entendeu todo o teor dos presentes Termos e Condições de Uso. O Usuário aceita e concorda com todos os termos aqui dispostos e, ao efetuar o seu cadastro, ratifica e confirma o seu aceite e concordância.</p>'),
		('websites', '{$website['website_id']}', 'onestepcheckout/terms/terms_title', 'Bem vindo à Comercial Omega.'),
		('websites', '{$website['website_id']}', 'privatesales/access/authonly', '0'),
		('websites', '{$website['website_id']}', 'privatesales/access/catalog', '1'),
		('websites', '{$website['website_id']}', 'privatesales/access/navigation', '1'),
		('websites', '{$website['website_id']}', 'privatesales/general/enable', '1'),
		('websites', '{$website['website_id']}', 'privatesales/registration/disable', '0'),
		('websites', '{$website['website_id']}', 'privatesales/registration/login_panel', '0'),
		('websites', '{$website['website_id']}', 'sales_email/order/copy_to', NULL),
		('websites', '{$website['website_id']}', 'sales_email/order/copy_to_rep', '1'),
		('websites', '{$website['website_id']}', 'sales_email/order/template', '21'),
		('websites', '{$website['website_id']}', 'tm_ajaxsearch/general/attributes', 'name,sku'),
		('websites', '{$website['website_id']}', 'tm_ajaxsearch/general/enabled', '1'),
		('websites', '{$website['website_id']}', 'tm_ajaxsearch/general/show_category_filter', '0'),
		('websites', '{$website['website_id']}', 'trans_email/ident_custom1/email', 'julio@4vets.com.br'),
		('websites', '{$website['website_id']}', 'trans_email/ident_custom1/name', 'Omega'),
		('websites', '{$website['website_id']}', 'trans_email/ident_custom2/email', 'julio@4vets.com.br'),
		('websites', '{$website['website_id']}', 'trans_email/ident_custom2/name', 'Omega'),
		('websites', '{$website['website_id']}', 'trans_email/ident_general/email', 'julio@4vets.com.br'),
		('websites', '{$website['website_id']}', 'trans_email/ident_general/name', 'Omega'),
		('websites', '{$website['website_id']}', 'trans_email/ident_sales/email', 'julio@4vets.com.br'),
		('websites', '{$website['website_id']}', 'trans_email/ident_sales/name', 'Omega Vendas'),
		('websites', '{$website['website_id']}', 'trans_email/ident_support/email', 'julio@4vets.com.br'),
		('websites', '{$website['website_id']}', 'trans_email/ident_support/name', 'Omega Suporte ao cliente'),
		('websites', '{$website['website_id']}', 'web/secure/base_url', 'https://omega.4vets.com.br/'),
		('websites', '{$website['website_id']}', 'web/unsecure/base_url', 'http://omega.4vets.com.br/'),
		('websites', '{$website['website_id']}', 'web/url/use_store', '0')
	;");
}

/** Install MSD Website Config */

$website = Mage::getResourceModel('core/website_collection')->addFieldToFilter('code', 'msd')->getData()[0];

if (isset($website['website_id']))
{
	$installer->run("
		DELETE FROM core_config_data
		WHERE scope = 'websites'
		AND scope_id = {$website['website_id']};
	;");

	$installer->run("
		INSERT INTO `core_config_data` (`scope`, `scope_id`, `path`, `value`) VALUES
		('websites', '{$website['website_id']}', 'ajax_pro/catalogCategoryView/type', 'button'),
		('websites', '{$website['website_id']}', 'ajax_pro/checkoutCart/messageHandle', 'tm_ajaxpro_checkout_cart_add_suggestpage'),
		('websites', '{$website['website_id']}', 'ajax_pro/effect/enabled_overlay', '1'),
		('websites', '{$website['website_id']}', 'ajax_pro/effect/overlay_opacity', '0.5'),
		('websites', '{$website['website_id']}', 'ajax_pro/general/enabled', '1'),
		('websites', '{$website['website_id']}', 'ajax_pro/general/useLoginFormBlock', '1'),
		('websites', '{$website['website_id']}', 'carriers/freeshipping/active', '1'),
		('websites', '{$website['website_id']}', 'carriers/freeshipping/name', 'Frete a combinar'),
		('websites', '{$website['website_id']}', 'carriers/freeshipping/title', 'Envio peclam'),
		('websites', '{$website['website_id']}', 'carriers/tablerate/import', '1409953796'),
		('websites', '{$website['website_id']}', 'catalog/frontend/list_mode', 'list-grid'),
		('websites', '{$website['website_id']}', 'catalog/placeholder/image_placeholder', 'websites/2/avatar_produto.jpg'),
		('websites', '{$website['website_id']}', 'catalog/placeholder/small_image_placeholder', 'websites/2/small_produto.jpg'),
		('websites', '{$website['website_id']}', 'catalog/price/group_as_normal', '1'),
		('websites', '{$website['website_id']}', 'catalog/search/search_type', '3'),
		('websites', '{$website['website_id']}', 'checkout/options/enable_agreements', '1'),
		('websites', '{$website['website_id']}', 'confirmcustomer/admin_notification/template', '28'),
		('websites', '{$website['website_id']}', 'customer/create_account/email_template', '23'),
		('websites', '{$website['website_id']}', 'customer/password/forgot_email_template', '24'),
		('websites', '{$website['website_id']}', 'custom_menu/columns/count', '3'),
		('websites', '{$website['website_id']}', 'custom_menu/columns/divided_horizontally', '1'),
		('websites', '{$website['website_id']}', 'custom_menu/columns/integrate', '1'),
		('websites', '{$website['website_id']}', 'custom_menu/general/ajax_load_content', '0'),
		('websites', '{$website['website_id']}', 'custom_menu/general/display_empty_categories', '0'),
		('websites', '{$website['website_id']}', 'custom_menu/general/enabled', '1'),
		('websites', '{$website['website_id']}', 'custom_menu/general/max_level', '3'),
		('websites', '{$website['website_id']}', 'custom_menu/general/mobile_menu', '1'),
		('websites', '{$website['website_id']}', 'custom_menu/general/rtl', '0'),
		('websites', '{$website['website_id']}', 'custom_menu/general/show_home_link', '0'),
		('websites', '{$website['website_id']}', 'custom_menu/popup/delay_displaying', '10'),
		('websites', '{$website['website_id']}', 'custom_menu/popup/delay_hiding', '10'),
		('websites', '{$website['website_id']}', 'custom_menu/popup/top_offset', '0'),
		('websites', '{$website['website_id']}', 'custom_menu/popup/width', '0'),
		('websites', '{$website['website_id']}', 'design/email/logo', 'websites/4/logo-msd.png'),
		('websites', '{$website['website_id']}', 'design/email/logo_alt', 'MSD Online'),
		('websites', '{$website['website_id']}', 'design/footer/absolute_footer', NULL),
		('websites', '{$website['website_id']}', 'design/footer/copyright', '&copy; 2014 MSD Online. Todos os direitos reservados.'),
		('websites', '{$website['website_id']}', 'design/head/default_description', 'MSD Online'),
		('websites', '{$website['website_id']}', 'design/head/default_keywords', 'MSD, 4Vets'),
		('websites', '{$website['website_id']}', 'design/head/default_title', 'MSD Online'),
		('websites', '{$website['website_id']}', 'design/head/includes', NULL),
		('websites', '{$website['website_id']}', 'design/head/shortcut_icon', 'websites/4/favicon_1_.ico'),
		('websites', '{$website['website_id']}', 'design/head/title_prefix', 'MSD Online - '),
		('websites', '{$website['website_id']}', 'design/head/title_suffix', NULL),
		('websites', '{$website['website_id']}', 'design/header/logo_alt', 'MSD Online'),
		('websites', '{$website['website_id']}', 'design/header/logo_src', 'websites/4/logo-msd.png'),
		('websites', '{$website['website_id']}', 'design/header/welcome', 'Bem-vindo(a)!'),
		('websites', '{$website['website_id']}', 'design/package/name', 'argento'),
		('websites', '{$website['website_id']}', 'design/theme/after_default', 'fvets_pure'),
		('websites', '{$website['website_id']}', 'design/theme/layout', 'msd'),
		('websites', '{$website['website_id']}', 'design/theme/locale', 'msd'),
		('websites', '{$website['website_id']}', 'design/theme/skin', 'msd'),
		('websites', '{$website['website_id']}', 'design/theme/skin_ua_regexp', 'a:0:{}'),
		('websites', '{$website['website_id']}', 'design/theme/template', 'msd'),
		('websites', '{$website['website_id']}', 'design/theme/template_ua_regexp', 'a:0:{}'),
		('websites', '{$website['website_id']}', 'dev/css/merge_css_files', '1'),
		('websites', '{$website['website_id']}', 'dev/debug/template_hints', '0'),
		('websites', '{$website['website_id']}', 'dev/debug/template_hints_blocks', '0'),
		('websites', '{$website['website_id']}', 'dev/js/merge_files', '1'),
		('websites', '{$website['website_id']}', 'dev/translate_inline/active', '0'),
		('websites', '{$website['website_id']}', 'general/country/allow', 'BR'),
		('websites', '{$website['website_id']}', 'general/country/default', 'BR'),
		('websites', '{$website['website_id']}', 'general/store_information/is_brand', '1'),
		('websites', '{$website['website_id']}', 'general/store_information/name', 'msd'),
		('websites', '{$website['website_id']}', 'general/store_information/phone', '(35) 3271-1674 / (35) 9162-7413 (Tim)'),
		('websites', '{$website['website_id']}', 'lightboxpro/gallery/behaviour_enableLightboxEfect', '0'),
		('websites', '{$website['website_id']}', 'magefm_customer/general/self_customer_addres_phone', '1'),
		('websites', '{$website['website_id']}', 'newsletter/subscription/disablenewslettersuccesses', '1'),
		('websites', '{$website['website_id']}', 'onestepcheckout/ajax_update/ajax_save_billing_fields', 'country'),
		('websites', '{$website['website_id']}', 'onestepcheckout/exclude_fields/enable_address_fields', '1'),
		('websites', '{$website['website_id']}', 'onestepcheckout/exclude_fields/enable_comments', '1'),
		('websites', '{$website['website_id']}', 'onestepcheckout/exclude_fields/enable_comments_default', '1'),
		('websites', '{$website['website_id']}', 'onestepcheckout/exclude_fields/enable_discount', '1'),
		('websites', '{$website['website_id']}', 'onestepcheckout/exclude_fields/exclude_bairro', '0'),
		('websites', '{$website['website_id']}', 'onestepcheckout/exclude_fields/exclude_cnpj', '0'),
		('websites', '{$website['website_id']}', 'onestepcheckout/exclude_fields/exclude_company', '1'),
		('websites', '{$website['website_id']}', 'onestepcheckout/exclude_fields/exclude_cpf', '0'),
		('websites', '{$website['website_id']}', 'onestepcheckout/exclude_fields/exclude_fax', '1'),
		('websites', '{$website['website_id']}', 'onestepcheckout/exclude_fields/exclude_inscricao_estadual', '0'),
		('websites', '{$website['website_id']}', 'onestepcheckout/exclude_fields/exclude_mobilephone', '0'),
		('websites', '{$website['website_id']}', 'onestepcheckout/exclude_fields/exclude_razao_social', '0'),
		('websites', '{$website['website_id']}', 'onestepcheckout/exclude_fields/exclude_saveaddress', '1'),
		('websites', '{$website['website_id']}', 'onestepcheckout/general/checkout_description', 'Por favor, insira  as informações abaixo para completar seu pedido'),
		('websites', '{$website['website_id']}', 'onestepcheckout/general/checkout_title', 'Finalizar Pedido'),
		('websites', '{$website['website_id']}', 'onestepcheckout/general/default_country', 'BR'),
		('websites', '{$website['website_id']}', 'onestepcheckout/general/default_shipping_if_one', '1'),
		('websites', '{$website['website_id']}', 'onestepcheckout/general/enable_different_shipping', '0'),
		('websites', '{$website['website_id']}', 'onestepcheckout/general/hide_payment_method', '1'),
		('websites', '{$website['website_id']}', 'onestepcheckout/general/hide_shipping_method', '1'),
		('websites', '{$website['website_id']}', 'onestepcheckout/general/rewrite_checkout_links', '1'),
		('websites', '{$website['website_id']}', 'onestepcheckout/general/single_address', '1'),
		('websites', '{$website['website_id']}', 'onestepcheckout/general/update_address_and_customer', '1'),
		('websites', '{$website['website_id']}', 'onestepcheckout/registration/registration_mode', 'disable_registration'),
		('websites', '{$website['website_id']}', 'onestepcheckout/sortordering_fields/bairro', '8'),
		('websites', '{$website['website_id']}', 'onestepcheckout/sortordering_fields/city', '7'),
		('websites', '{$website['website_id']}', 'onestepcheckout/sortordering_fields/cnpj', '2'),
		('websites', '{$website['website_id']}', 'onestepcheckout/sortordering_fields/company', '99'),
		('websites', '{$website['website_id']}', 'onestepcheckout/sortordering_fields/confirm_password', '99'),
		('websites', '{$website['website_id']}', 'onestepcheckout/sortordering_fields/country_id', '99'),
		('websites', '{$website['website_id']}', 'onestepcheckout/sortordering_fields/cpf', '12'),
		('websites', '{$website['website_id']}', 'onestepcheckout/sortordering_fields/create_account', '99'),
		('websites', '{$website['website_id']}', 'onestepcheckout/sortordering_fields/dob', '99'),
		('websites', '{$website['website_id']}', 'onestepcheckout/sortordering_fields/email', '14'),
		('websites', '{$website['website_id']}', 'onestepcheckout/sortordering_fields/fax', '99'),
		('websites', '{$website['website_id']}', 'onestepcheckout/sortordering_fields/firstname', '10'),
		('websites', '{$website['website_id']}', 'onestepcheckout/sortordering_fields/gender', '99'),
		('websites', '{$website['website_id']}', 'onestepcheckout/sortordering_fields/inscricao_estadual', '3'),
		('websites', '{$website['website_id']}', 'onestepcheckout/sortordering_fields/lastname', '11'),
		('websites', '{$website['website_id']}', 'onestepcheckout/sortordering_fields/mobilephone', '13'),
		('websites', '{$website['website_id']}', 'onestepcheckout/sortordering_fields/password', '99'),
		('websites', '{$website['website_id']}', 'onestepcheckout/sortordering_fields/postcode', '5'),
		('websites', '{$website['website_id']}', 'onestepcheckout/sortordering_fields/razao_social', '1'),
		('websites', '{$website['website_id']}', 'onestepcheckout/sortordering_fields/region_id', '6'),
		('websites', '{$website['website_id']}', 'onestepcheckout/sortordering_fields/save_in_address_book', '99'),
		('websites', '{$website['website_id']}', 'onestepcheckout/sortordering_fields/street', '9'),
		('websites', '{$website['website_id']}', 'onestepcheckout/sortordering_fields/taxvat', '99'),
		('websites', '{$website['website_id']}', 'onestepcheckout/sortordering_fields/telephone', '4'),
		('websites', '{$website['website_id']}', 'onestepcheckout/sortordering_fields/use_for_shipping_yes', '99'),
		('websites', '{$website['website_id']}', 'onestepcheckout/terms/enable_default_terms', '0'),
		('websites', '{$website['website_id']}', 'onestepcheckout/terms/enable_terms', '1'),
		('websites', '{$website['website_id']}', 'onestepcheckout/terms/enable_textarea', '1'),
		('websites', '{$website['website_id']}', 'onestepcheckout/terms/terms_contents', '<strong>INFORMAÇÕES PRELIMINARES</strong><br />\r\n<p>Estes Termos e Condições de Uso do site são aplicáveis ao uso dos serviços de compra online pelo site “<a href =''http://peclam.com.br''>PEC-LAM.com.br”</a>.</p>\r\n<p> Para utilizar os serviços oferecidos pelo nosso site, você deverá necessariamente aceitar as regras estabelecidas nestes Termos e Condições de Uso.</p>\r\n<p> Caso você não concorde com estes Termos e Condições de Uso, não poderá fazer uso dos serviços disponibilizados no site PEC-LAM.</p>\r\n<p> Antes de efetuar seu cadastro e ter acesso aos produtos e serviços disponibilizados no site PEC-LAM, você deverá ler, entender e concordar com todas as disposições expostas nestes Termos e Condições de Uso.</p>\r\n<p> Em caso de dúvidas acesse o Portal <a href=''http://www.peclam.com.br''>www.PEC-LAM.com.br</a> ou ligue para a central de atendimento (35) 3271-1674.</p>\r\n<p><strong>1. DO SITE PEC-LAM</strong></p>\r\n<p> A PEC-LAM é uma plataforma online destinada exclusivamente a atender petshops e clínicas veterinárias (“Usuário” ou “Usuários”). A PEC-LAM tem por objetivo ser um canal entre os Usuários e os fabricantes de produtos veterinários (“Fornecedor” ou “Fornecedores”). Os Usuários interessados em adquirir produtos veterinários podem encontrar e adquirir, através da plataforma PEC-LAM, produtos de Fornecedores que a PEC-LAM representa e distribui em sua região (“Produto” ou “Produtos”).</p>\r\n<p>A compra e a venda dos Produtos são realizadas diretamente entre os Usuários e a PEC-LAM, no ambiente da plataforma PEC-LAM ou diretamente com seu representante de venda. A plataforma online PEC-LAM existe para facilitar a comercialização de produtos com seus Usuários.</p>\r\n<p>A PEC-LAM é um canal facilitador para vender seus produtos para os Usuários interessados em adquirir os Produtos ofertados.</p>\r\n<p><strong>2.CADASTRO DE USUÁRIO</strong></p>\r\n<p>a)<u>Cadastro de Usuários – CNPJ / CRMV</u><br />\r\nPara efetuar o cadastro na plataforma PEC-LAM, além das informações comuns, será necessário o número de Cadastro Nacional de Pessoa Jurídica (CNPJ) para Usuários pessoas jurídicas ou o número de registro no Conselho Regional de Medicina Veterinária (CRMV) para Usuários veterinários autônomos. Usuários que não possuam inscrição no CNPJ ou CRMV, conforme o caso, não poderão se cadastrar nem adquirir os Produtos disponibilizados na PEC-LAM. Caso você não tenha nenhuma das duas informações, por favor, entre em contato para podermos analisar seu caso.</p>\r\n<p>b)<u>Capacidade:</u><br/>\r\nOs Usuários deverão ser legalmente capazes para utilizar os serviços disponibilizados na plataforma PEC-LAM, bem como ter poderes para adquirir os Produtos em nome da pessoa jurídica cadastrada, sendo nulos os negócios jurídicos realizados com pessoas incapazes. Todas as informações prestadas pelos Usuários no cadastramento e/ou no procedimento de compra de Produtos na plataforma PEC-LAM são de inteira responsabilidade do Usuário.</p>\r\n<p>c)<u>Unicidade do Cadastro:</u><br />\r\nA cada Usuário será permitida a efetivação de somente 1 (um) cadastro. Na verificação de cadastros duplicados, o Usuário será notificado e um de seus cadastros será desabilitado.</p>\r\n<p>d)<u>Acesso à Conta:</u></br>\r\nAo se cadastrar, o Usuário criará uma conta que poderá sempre ser acessada pela inscrição de um login (e-mail do Usuário) e senha. O Usuário deverá fornecer informações verdadeiras e se responsabilizará por atualizá-las sempre que necessário. A PEC-LAM não se responsabiliza pelas informações oferecidas nem pela veracidade ou correção das mesmas. Assim, o Usuário responderá de todas as formas legais pela veracidade, autenticidade e exatidão das informações cadastradas e se compromete a informar a PEC-LAM no caso de qualquer acesso não autorizado, à conta, por terceiros.</p>\r\n<p>e)<u>Informações Adicionais:</u><br/>\r\nCaso a PEC-LAM julgue por bem solicitar informações adicionais sobre o Usuário, ela poderá assim proceder e, na recusa do Usuário de prestar referidas informações, a PEC-LAM poderá suspender ou cancelar a sua conta.</p>\r\n<p>f)<u>Recusa de Cadastro:</u><br/>\r\nA PEC-LAM se reserva o direito de recusar qualquer pedido de cadastro e/ou de cancelar qualquer cadastro efetivado, se julgar, a seu critério, que está em desacordo com as políticas e regras destes Termos e Condições de Uso ou outra política interna da PEC-LAM.</p>\r\n<p><strong>3.CADASTRO DE FORNECEDOR</strong></p>\r\n<p>Os Fornecedores interessados em firmar parceria com a PEC-LAM para a disponibilização de seus produtos na plataforma PEC-LAM poderão contatar a equipe comercial da PEC-LAM por meio do link de cadastro. Após a formalização do referido contrato, o fornecedor passará a integrar a lista de Fornecedores da PEC-LAM e poderá ofertar na plataforma PEC-LAM os seus Produtos para os Usuários.</p>\r\n<p><strong>4.         ALTERAÇÃO DOS TERMOS E CONDIÇÕES DE USO DO SITE PEC-LAM:</strong></p>\r\n<p>A PEC-LAM se reserva o direito de alterar, a qualquer tempo, quando jugar necessário, o disposto nestes Termos e Condições de Uso do site, para fins de promover a melhoria dos serviços prestados.</p>\r\n<p>As alterações passarão a ser válidas a partir da sua implementação e não prejudicarão as compras já efetuadas ou operações em andamento, que tenham sido iniciadas anteriormente às alterações.</p>\r\n<p><strong>5.RESPONSABILIDADE</strong></p>\r\n<p>a)<u>Informações sobre os Produtos Ofertados:</u>\r\nA PEC-LAM não se responsabiliza pelas informações descritas para os Produtos. Todos os dados, especificações, informações quantitativas e qualitativas, descrições, preços, prazos de entrega, condições, imagens e demais informações referentes aos Produtos são de inteira responsabilidade da PEC-LAM.</p>\r\n<p>b)<u>Falhas na Operacionalização do Site PEC-LAM:</u>\r\nA PEC-LAM se compromete a envidar seus melhores esforços e empregar bons recursos técnicos para manter o bom funcionamento do site PEC-LAM. Contudo, a PEC-LAM não se responsabiliza por qualquer falha técnica ou operacional no site PEC-LAM decorrente de defeitos advindos do sistema do Usuário ou de qualquer outra razão externa, fora de seu controle, tais como, sem se limitar a, vírus, ação de hackers, quedas de sistema, interrupções de servidores ou fornecedores de serviços de hospedagem, operadoras de telecomunicações e/ou energia elétrica, dentre outras hipótese que possam ocasionar dificuldade na operacionalização do site PEC-LAM.</p>\r\n<p>c)<u>Vícios e/ou Defeitos dos Produtos ou Falhas na Entrega dos Produtos:</u><br/>\r\nA compra e a venda dos Produtos são realizadas diretamente entre Usuários e a PEC-LAM.</p>\r\n<p>  A PEC-LAM será responsável por qualquer falha no cumprimento das obrigações dela para com o Usuário, por qualquer divergência qualitativa ou quantitativa dos Produtos adquiridos e entregues ou por qualquer atraso na entrega dos Produtos.</p>\r\n<p><strong>7.OBRIGAÇÕES DO USUÁRIO</strong></p>\r\n<p> O Usuário, para adquirir os Produtos da PEC-LAM obriga-se a seguir todas as regras destes Termos e Condições de Uso.</p>\r\n<p> Na aquisição de Produtos, o Usuário obriga-se a respeitar as políticas e condições de vendas descritas no anúncio pela PEC-LAM para o respectivo Produto de interesse.</p>\r\n<p>  O Usuário se compromete a indenizar a PEC-LAM por qualquer ação ajuizada por outros Usuários ou terceiros, em razão de qualquer ato praticado pelo Usuário em descumprimento destes Termos e Condições de Uso e/ou violação de lei ou direitos de terceiros.</p>\r\n<p><strong>8.PRÁTICAS PROIBIDAS NO USO DO SITE</strong></p>\r\n<p> Qualquer prática criminosa, fraudulenta, ilícita ou que expresse má-fé, dolosa ou culposamente, contrária aos bons costumes, que posa gerar danos à PEC-LAM, ou a outros Usuários, ou, ainda, ao sistema de funcionamento e operacionalização do site PEC-LAM, seja pelo descumprimento destes Termos e Condições de Uso, seja por qualquer outra prática lesiva, implicará a exclusão do cadastro do Usuário responsável do site PEC-LAM, sem prejuízo do direito da PEC-LAM de tomar as medidas legais cabíveis para a responsabilização civil e/ou criminal do Usuário e reparação dos danos suportados.</p>\r\n<p><strong>9.PEDIDO E PAGAMENTO</strong></p>\r\n<p> Ao efetuar um pedido, a PEC-LAM entrará em contato com o Usuário em no máximo 48 horas para que as condições de pagamento sejam discutidas.</p>\r\n<p> Apenas após a finalização do pagamento que os produtos serão enviados ao Usuário.</p>\r\n<p> Em razão de a transação de compra e venda ocorrer diretamente entre os Usuários e os Fornecedores, em nenhuma hipótese a PEC-LAM será responsável por restituir valores aos Usuários, sob qualquer fundamento.</p>\r\n<p><strong> 10.TROCAS E DEVOLUÇÕES</strong></p>\r\n<p> As trocas e devoluções dos Produtos deverão ser solicitadas diretamente a PEC-LAM. As políticas referentes à garantia, troca e devolução dos Produtos deverão ser consultadas nas Políticas da PEC-LAM. Para saber mais, acesse <a href = ''http://http://www.peclam.com.br''>www.PEC-LAM.com.br</a></p>\r\n<p><strong>11.PROPRIEDADE INTELECTUAL</strong></p>\r\n<p> O conteúdo disponibilizado no site PEC-LAM, tais como, sem se limitar a, textos, gráficos, logotipos, símbolos, compilação de dados, layout, imagens, informações, dados, figuras, bem como o uso comercial da expressão “PEC-LAM”, como marca, nome de domínio ou denominação social, são de propriedade da PEC-LAM (ressalvados aqueles que pertencem aos Fornecedores, conforme o caso). Qualquer forma de violação, uso indevido, reprodução, apropriação desses direitos de propriedade intelectual pelo Usuário ou qualquer terceiro, será passível de medidas legais para preservação e manutenção dos direitos a seu titular bem como para reparação de danos, sem prejuízo da eventual responsabilização civil e/ou criminal aplicável.</p>\r\n<p><strong> 12.PROTEÇÃO E USO DE DADOS</strong></p>\r\n<p> A PEC-LAM manterá sigilo e confidencialidade de todas as informações pessoais dos Usuários que efetivarem cadastro no site. A PEC-LAM envidará seus melhores esforços para garantir a segurança das informações dos Usuários, utilizando adequados sistemas de segurança, mas não se responsabilizará por ações externas de terceiros que violem as medidas de segurança aplicadas pela PEC-LAM e que tenham ocorrido de forma inevitável e imprevisível à PEC-LAM.</p>\r\n<p> Ao se cadastrar no site, o Usuário concorda que a PEC-LAM poderá armazenar em seu banco de dados, por tempo indeterminado, as informações por ele fornecidas. O banco de dados bem como o seu conteúdo são de propriedade da PEC-LAM, podendo esta dispor e usar livremente as informações que integram esse banco de dados, contanto que não viole a privacidade das informações estritamente pessoais dos Usuários, assim entendidas aquelas que permitem a identificação individualizada de uma pessoa física ou jurídica.</p>\r\n<p> As informações dos Usuários poderão ser utilizadas para auxiliar a PEC-LAM a desenvolver estudos e analisar o comportamento, perfil, bem como as áreas e assuntos de interesse desses Usuários na utilização do site PEC-LAM, com o objetivo de melhorar e aprimorar os serviços oferecidos.</p>\r\n<p> Ao se cadastrar no site, o Usuário concorda também que a PEC-LAM poderá disponibilizar as informações do seu banco de dados, que não sejam estritamente pessoais, de forma genérica, sem identificação específica e pessoal, para outras finalidades, a seu critério, tais como, sem se limitar a, realização de pesquisas, análises, estudos e levantamentos, operacionalização e aprimoramento dos serviços disponibilizados pelo site, facilitação do uso de sites, serviços ou funcionalidades, comunicação com o Usuário, envio de informativos, anúncios, questionários ou e-mails promocionais, dentre outros.</p>\r\n<p> Em caso de ordem judicial ou exigência de autoridades governamentais competentes, a PEC-LAM poderá divulgar as informações pessoais do Usuário. Em nenhuma hipótese, a PEC-LAM compactuará com qualquer ato fraudulento e ou de qualquer outra natureza ilícita que tente violar ou que viole direitos de propriedade intelectual ou direitos de qualquer outra natureza do site ou de seus Usuários.</p>\r\n<p> A qualquer momento, o Usuário poderá solicitar a exclusão da sua conta e, consequentemente, das informações cadastradas. Para certos casos, a PEC-LAM se reserva o direito de manter, em seus arquivos, os dados pessoais do Usuário da conta excluída para o exclusivo fim de utilizá-los em eventuais litígios ou solução de problemas decorrentes do uso do site PEC-LAM.</p>\r\n<p><strong> 13.COOKIES</strong></p>\r\n <p>Ao se cadastrar, o Usuário concorda que a PEC-LAM poderá utilizar um sistema de monitoramento – <i>cookies<i> – com a finalidade de personalizar o acesso do Usuário no site.</p>\r\n<p> Quando o navegador do Usuário acessar a PEC-LAM, os cookies permitirão monitorar e identificar assuntos de interesse, perfil e comportamento do Usuário, para que a PEC-LAM possa entender melhor as necessidades e interesses do Usuário e possibilitar acesso facilitado a informações a eles relacionadas.</p>\r\n<p><strong> 14.LEI APLICÁVEL</strong></p>\r\n<p> Estes Termos e Condições de Uso serão regidos e interpretados de acordo com as leis da República Federativa do Brasil.</p>\r\n<p><strong>15.ACEITE E CONCORDÂNCIA</strong></p>\r\n<p> O Usuário declara que leu e entendeu todo o teor dos presentes Termos e Condições de Uso. O Usuário aceita e concorda com todos os termos aqui dispostos e, ao efetuar o seu cadastro, ratifica e confirma o seu aceite e concordância.</p>'),
		('websites', '{$website['website_id']}', 'onestepcheckout/terms/terms_title', 'Bem vindo à PEC-LAM.'),
		('websites', '{$website['website_id']}', 'privatesales/access/authonly', '0'),
		('websites', '{$website['website_id']}', 'privatesales/access/catalog', '1'),
		('websites', '{$website['website_id']}', 'privatesales/access/navigation', '1'),
		('websites', '{$website['website_id']}', 'privatesales/general/enable', '1'),
		('websites', '{$website['website_id']}', 'privatesales/registration/disable', '0'),
		('websites', '{$website['website_id']}', 'privatesales/registration/login_panel', '0'),
		('websites', '{$website['website_id']}', 'sales_email/order/copy_to', NULL),
		('websites', '{$website['website_id']}', 'sales_email/order/copy_to_rep', '1'),
		('websites', '{$website['website_id']}', 'tm_ajaxsearch/general/attributes', 'name,sku'),
		('websites', '{$website['website_id']}', 'tm_ajaxsearch/general/enabled', '1'),
		('websites', '{$website['website_id']}', 'tm_ajaxsearch/general/show_category_filter', '1'),
		('websites', '{$website['website_id']}', 'trans_email/ident_custom1/email', 'ti+msd@4vets.com.br'),
		('websites', '{$website['website_id']}', 'trans_email/ident_custom1/name', 'MSD'),
		('websites', '{$website['website_id']}', 'trans_email/ident_custom2/email', 'ti+msd@4vets.com.br'),
		('websites', '{$website['website_id']}', 'trans_email/ident_custom2/name', 'MSD'),
		('websites', '{$website['website_id']}', 'trans_email/ident_general/email', 'ti+msd@4vets.com.br'),
		('websites', '{$website['website_id']}', 'trans_email/ident_general/name', 'MSD'),
		('websites', '{$website['website_id']}', 'trans_email/ident_sales/email', 'ti+msd@4vets.com.br'),
		('websites', '{$website['website_id']}', 'trans_email/ident_sales/name', 'MSD Vendas'),
		('websites', '{$website['website_id']}', 'trans_email/ident_support/email', 'ti+msd@4vets.com.br'),
		('websites', '{$website['website_id']}', 'trans_email/ident_support/name', 'MSD Suporte ao cliente'),
		('websites', '{$website['website_id']}', 'web/secure/base_url', 'https://msd.4vets.com.br/'),
		('websites', '{$website['website_id']}', 'web/unsecure/base_url', 'http://msd.4vets.com.br/'),
		('websites', '{$website['website_id']}', 'web/url/use_store', '0')
	;");
}


/** Install Omega Purina MSD Store Config */

$store = Mage::getResourceModel('core/store_collection')->addFieldToFilter('code', 'omega')->getData()[0];

if (isset($store['store_id']))
{
	$installer->run("
		DELETE FROM core_config_data
		WHERE scope = 'stores'
		AND scope_id = {$store['store_id']};
	;");

	$installer->run("
		INSERT INTO `core_config_data` (`scope`, `scope_id`, `path`, `value`) VALUES
		('stores', '{$store['store_id']}', 'askit/general/enabled', '1'),
		('stores', '{$store['store_id']}', 'custom_menu/general/headline1', 'Compre por'),
		('stores', '{$store['store_id']}', 'custom_menu/general/headline2', 'Marcas'),
		('stores', '{$store['store_id']}', 'design/categories/menu_root_category', '3'),
		('stores', '{$store['store_id']}', 'dev/debug/template_hints', '0'),
		('stores', '{$store['store_id']}', 'dev/debug/template_hints_blocks', '0'),
		('stores', '{$store['store_id']}', 'easycatalogimg/category/enabled_for_anchor', '0'),
		('stores', '{$store['store_id']}', 'easycatalogimg/category/enabled_for_default', '0'),
		('stores', '{$store['store_id']}', 'easycatalogimg/general/enabled', '1'),
		('stores', '{$store['store_id']}', 'easyslide/general/load', '1'),
		('stores', '{$store['store_id']}', 'facebooklb/category_products/color', 'light'),
		('stores', '{$store['store_id']}', 'facebooklb/category_products/enabled', '0'),
		('stores', '{$store['store_id']}', 'facebooklb/category_products/layout', 'button_count'),
		('stores', '{$store['store_id']}', 'facebooklb/category_products/send', '0'),
		('stores', '{$store['store_id']}', 'facebooklb/category_products/showfaces', '0'),
		('stores', '{$store['store_id']}', 'facebooklb/category_products/width', '350'),
		('stores', '{$store['store_id']}', 'facebooklb/productlike/color', 'light'),
		('stores', '{$store['store_id']}', 'facebooklb/productlike/enabled', '1'),
		('stores', '{$store['store_id']}', 'facebooklb/productlike/layout', 'button_count'),
		('stores', '{$store['store_id']}', 'facebooklb/productlike/send', '1'),
		('stores', '{$store['store_id']}', 'facebooklb/productlike/showfaces', '0'),
		('stores', '{$store['store_id']}', 'facebooklb/productlike/width', '350'),
		('stores', '{$store['store_id']}', 'general/categories/menu_root_category', '240'),
		('stores', '{$store['store_id']}', 'general/country/allow', 'BR'),
		('stores', '{$store['store_id']}', 'general/country/default', 'BR'),
		('stores', '{$store['store_id']}', 'general/store_information/alias', NULL),
		('stores', '{$store['store_id']}', 'lightboxpro/general/enabled', '1'),
		('stores', '{$store['store_id']}', 'lightboxpro/size/main', '512x512'),
		('stores', '{$store['store_id']}', 'lightboxpro/size/maxWindow', '800x600'),
		('stores', '{$store['store_id']}', 'lightboxpro/size/thumbnail', '112x112'),
		('stores', '{$store['store_id']}', 'navigationpro/top/enabled', '1'),
		('stores', '{$store['store_id']}', 'richsnippets/general/enabled', '1'),
		('stores', '{$store['store_id']}', 'richsnippets/general/manufacturer', 'manufacturer'),
		('stores', '{$store['store_id']}', 'soldtogether/customer/enabled', '1'),
		('stores', '{$store['store_id']}', 'soldtogether/general/enabled', '1'),
		('stores', '{$store['store_id']}', 'soldtogether/general/random', '1'),
		('stores', '{$store['store_id']}', 'soldtogether/order/addtocartcheckbox', '0'),
		('stores', '{$store['store_id']}', 'soldtogether/order/amazonestyle', '1'),
		('stores', '{$store['store_id']}', 'soldtogether/order/enabled', '1'),
		('stores', '{$store['store_id']}', 'suggestpage/general/show_after_addtocart', '1'),
		('stores', '{$store['store_id']}', 'tm_easytabs/general/enabled', '1'),
		('stores', '{$store['store_id']}', 'web/secure/base_url', 'https://omega.4vets.com.br/'),
		('stores', '{$store['store_id']}', 'web/unsecure/base_url', 'http://omega.4vets.com.br/'),
		('stores', '{$store['store_id']}', 'web/url/use_store', '0')
	;");

}


/** Install Omega MSD Store Config */

$store = Mage::getResourceModel('core/store_collection')->addFieldToFilter('code', 'omegamsd')->getData()[0];

if (isset($store['store_id']))
{
	$installer->run("
		DELETE FROM core_config_data
		WHERE scope = 'stores'
		AND scope_id = {$store['store_id']};
	;");

	$installer->run("
		INSERT INTO `core_config_data` (`scope`, `scope_id`, `path`, `value`) VALUES
		('stores', '{$store['store_id']}', 'askit/general/enabled', '1'),
		('stores', '{$store['store_id']}', 'custom_menu/general/headline1', 'Compre por'),
		('stores', '{$store['store_id']}', 'custom_menu/general/headline2', 'Marcas'),
		('stores', '{$store['store_id']}', 'design/categories/menu_root_category', '3'),
		('stores', '{$store['store_id']}', 'dev/debug/template_hints', '0'),
		('stores', '{$store['store_id']}', 'dev/debug/template_hints_blocks', '0'),
		('stores', '{$store['store_id']}', 'easycatalogimg/category/enabled_for_anchor', '0'),
		('stores', '{$store['store_id']}', 'easycatalogimg/category/enabled_for_default', '0'),
		('stores', '{$store['store_id']}', 'easycatalogimg/general/enabled', '1'),
		('stores', '{$store['store_id']}', 'easyslide/general/load', '1'),
		('stores', '{$store['store_id']}', 'facebooklb/category_products/color', 'light'),
		('stores', '{$store['store_id']}', 'facebooklb/category_products/enabled', '0'),
		('stores', '{$store['store_id']}', 'facebooklb/category_products/layout', 'button_count'),
		('stores', '{$store['store_id']}', 'facebooklb/category_products/send', '0'),
		('stores', '{$store['store_id']}', 'facebooklb/category_products/showfaces', '0'),
		('stores', '{$store['store_id']}', 'facebooklb/category_products/width', '350'),
		('stores', '{$store['store_id']}', 'facebooklb/productlike/color', 'light'),
		('stores', '{$store['store_id']}', 'facebooklb/productlike/enabled', '1'),
		('stores', '{$store['store_id']}', 'facebooklb/productlike/layout', 'button_count'),
		('stores', '{$store['store_id']}', 'facebooklb/productlike/send', '1'),
		('stores', '{$store['store_id']}', 'facebooklb/productlike/showfaces', '0'),
		('stores', '{$store['store_id']}', 'facebooklb/productlike/width', '350'),
		('stores', '{$store['store_id']}', 'general/categories/menu_root_category', '332'),
		('stores', '{$store['store_id']}', 'general/country/allow', 'BR'),
		('stores', '{$store['store_id']}', 'general/country/default', 'BR'),
		('stores', '{$store['store_id']}', 'general/store_information/alias', NULL),
		('stores', '{$store['store_id']}', 'lightboxpro/general/enabled', '1'),
		('stores', '{$store['store_id']}', 'lightboxpro/size/main', '512x512'),
		('stores', '{$store['store_id']}', 'lightboxpro/size/maxWindow', '800x600'),
		('stores', '{$store['store_id']}', 'lightboxpro/size/thumbnail', '112x112'),
		('stores', '{$store['store_id']}', 'navigationpro/top/enabled', '1'),
		('stores', '{$store['store_id']}', 'richsnippets/general/enabled', '1'),
		('stores', '{$store['store_id']}', 'richsnippets/general/manufacturer', 'manufacturer'),
		('stores', '{$store['store_id']}', 'soldtogether/customer/enabled', '1'),
		('stores', '{$store['store_id']}', 'soldtogether/general/enabled', '1'),
		('stores', '{$store['store_id']}', 'soldtogether/general/random', '1'),
		('stores', '{$store['store_id']}', 'soldtogether/order/addtocartcheckbox', '0'),
		('stores', '{$store['store_id']}', 'soldtogether/order/amazonestyle', '1'),
		('stores', '{$store['store_id']}', 'soldtogether/order/enabled', '1'),
		('stores', '{$store['store_id']}', 'suggestpage/general/show_after_addtocart', '1'),
		('stores', '{$store['store_id']}', 'tm_easytabs/general/enabled', '1'),
		('stores', '{$store['store_id']}', 'web/secure/base_url', 'https://msd-omega.4vets.com.br/'),
		('stores', '{$store['store_id']}', 'web/unsecure/base_url', 'http://msd-omega.4vets.com.br/'),
		('stores', '{$store['store_id']}', 'web/url/use_store', '0');
	;");

}

/** Install Omega Purina Store Config */

$store = Mage::getResourceModel('core/store_collection')->addFieldToFilter('code', 'omegapurina')->getData()[0];

if (isset($store['store_id']))
{
	$installer->run("
		DELETE FROM core_config_data
		WHERE scope = 'stores'
		AND scope_id = {$store['store_id']};
	;");

	$installer->run("
		INSERT INTO `core_config_data` (`scope`, `scope_id`, `path`, `value`) VALUES
		('stores', '{$store['store_id']}', 'askit/general/enabled', '1'),
		('stores', '{$store['store_id']}', 'custom_menu/general/headline1', 'Compre por'),
		('stores', '{$store['store_id']}', 'custom_menu/general/headline2', 'Marcas'),
		('stores', '{$store['store_id']}', 'design/categories/menu_root_category', '3'),
		('stores', '{$store['store_id']}', 'dev/debug/template_hints', '0'),
		('stores', '{$store['store_id']}', 'dev/debug/template_hints_blocks', '0'),
		('stores', '{$store['store_id']}', 'easycatalogimg/category/enabled_for_anchor', '0'),
		('stores', '{$store['store_id']}', 'easycatalogimg/category/enabled_for_default', '0'),
		('stores', '{$store['store_id']}', 'easycatalogimg/general/enabled', '1'),
		('stores', '{$store['store_id']}', 'easyslide/general/load', '1'),
		('stores', '{$store['store_id']}', 'facebooklb/category_products/color', 'light'),
		('stores', '{$store['store_id']}', 'facebooklb/category_products/enabled', '0'),
		('stores', '{$store['store_id']}', 'facebooklb/category_products/layout', 'button_count'),
		('stores', '{$store['store_id']}', 'facebooklb/category_products/send', '0'),
		('stores', '{$store['store_id']}', 'facebooklb/category_products/showfaces', '0'),
		('stores', '{$store['store_id']}', 'facebooklb/category_products/width', '350'),
		('stores', '{$store['store_id']}', 'facebooklb/productlike/color', 'light'),
		('stores', '{$store['store_id']}', 'facebooklb/productlike/enabled', '1'),
		('stores', '{$store['store_id']}', 'facebooklb/productlike/layout', 'button_count'),
		('stores', '{$store['store_id']}', 'facebooklb/productlike/send', '1'),
		('stores', '{$store['store_id']}', 'facebooklb/productlike/showfaces', '0'),
		('stores', '{$store['store_id']}', 'facebooklb/productlike/width', '350'),
		('stores', '{$store['store_id']}', 'general/categories/menu_root_category', '308'),
		('stores', '{$store['store_id']}', 'general/country/allow', 'BR'),
		('stores', '{$store['store_id']}', 'general/country/default', 'BR'),
		('stores', '{$store['store_id']}', 'general/store_information/alias', NULL),
		('stores', '{$store['store_id']}', 'lightboxpro/general/enabled', '1'),
		('stores', '{$store['store_id']}', 'lightboxpro/size/main', '512x512'),
		('stores', '{$store['store_id']}', 'lightboxpro/size/maxWindow', '800x600'),
		('stores', '{$store['store_id']}', 'lightboxpro/size/thumbnail', '112x112'),
		('stores', '{$store['store_id']}', 'navigationpro/top/enabled', '1'),
		('stores', '{$store['store_id']}', 'richsnippets/general/enabled', '1'),
		('stores', '{$store['store_id']}', 'richsnippets/general/manufacturer', 'manufacturer'),
		('stores', '{$store['store_id']}', 'soldtogether/customer/enabled', '1'),
		('stores', '{$store['store_id']}', 'soldtogether/general/enabled', '1'),
		('stores', '{$store['store_id']}', 'soldtogether/general/random', '1'),
		('stores', '{$store['store_id']}', 'soldtogether/order/addtocartcheckbox', '0'),
		('stores', '{$store['store_id']}', 'soldtogether/order/amazonestyle', '1'),
		('stores', '{$store['store_id']}', 'soldtogether/order/enabled', '1'),
		('stores', '{$store['store_id']}', 'suggestpage/general/show_after_addtocart', '1'),
		('stores', '{$store['store_id']}', 'tm_easytabs/general/enabled', '1'),
		('stores', '{$store['store_id']}', 'web/secure/base_url', 'https://purina-omega.4vets.com.br/'),
		('stores', '{$store['store_id']}', 'web/unsecure/base_url', 'http://purina-omega.4vets.com.br/'),
		('stores', '{$store['store_id']}', 'web/url/use_store', '0')
	;");

}

/** Install MSD Omega  Store Config */

$store = Mage::getResourceModel('core/store_collection')->addFieldToFilter('code', 'omega_msd')->getData()[0];

if (isset($store['store_id']))
{
	$installer->run("
		DELETE FROM core_config_data
		WHERE scope = 'stores'
		AND scope_id = {$store['store_id']};
	;");

	$installer->run("
		INSERT INTO `core_config_data` (`scope`, `scope_id`, `path`, `value`) VALUES
		('stores', '{$store['store_id']}', 'askit/general/enabled', '1'),
		('stores', '{$store['store_id']}', 'confirmcustomer/admin_notification/template', '28'),
		('stores', '{$store['store_id']}', 'confirmcustomer/email/template', '25'),
		('stores', '{$store['store_id']}', 'customer/create_account/email_confirmed_template', '25'),
		('stores', '{$store['store_id']}', 'customer/create_account/email_template', '23'),
		('stores', '{$store['store_id']}', 'customer/password/forgot_email_template', '24'),
		('stores', '{$store['store_id']}', 'customer/password/remind_email_template', '26'),
		('stores', '{$store['store_id']}', 'custom_menu/general/headline1', 'Compre por'),
		('stores', '{$store['store_id']}', 'custom_menu/general/headline2', 'Departamento'),
		('stores', '{$store['store_id']}', 'design/categories/menu_root_category', '251'),
		('stores', '{$store['store_id']}', 'design/email/logo', 'stores/7/msd-omega.png'),
		('stores', '{$store['store_id']}', 'design/head/default_description', 'Omega MSD Online'),
		('stores', '{$store['store_id']}', 'design/head/default_keywords', 'Omega MSD Online, 4Vets'),
		('stores', '{$store['store_id']}', 'design/head/default_title', 'Omega MSD Online'),
		('stores', '{$store['store_id']}', 'design/head/includes', NULL),
		('stores', '{$store['store_id']}', 'design/head/shortcut_icon', 'stores/7/favicon_1_.ico'),
		('stores', '{$store['store_id']}', 'design/head/title_prefix', 'Omega MSD Online'),
		('stores', '{$store['store_id']}', 'design/header/brand_logo', 'stores/7/logo-msd.png'),
		('stores', '{$store['store_id']}', 'design/header/brand_logo_alt', 'MSD Online'),
		('stores', '{$store['store_id']}', 'design/header/use_brand_logo', '1'),
		('stores', '{$store['store_id']}', 'design/theme/layout', 'msd'),
		('stores', '{$store['store_id']}', 'design/theme/locale', 'msd'),
		('stores', '{$store['store_id']}', 'design/theme/skin', 'msd'),
		('stores', '{$store['store_id']}', 'design/theme/template', 'msd'),
		('stores', '{$store['store_id']}', 'easycatalogimg/category/enabled_for_anchor', '0'),
		('stores', '{$store['store_id']}', 'easycatalogimg/category/enabled_for_default', '0'),
		('stores', '{$store['store_id']}', 'easycatalogimg/general/enabled', '1'),
		('stores', '{$store['store_id']}', 'easyslide/general/load', '1'),
		('stores', '{$store['store_id']}', 'facebooklb/category_products/color', 'light'),
		('stores', '{$store['store_id']}', 'facebooklb/category_products/enabled', '0'),
		('stores', '{$store['store_id']}', 'facebooklb/category_products/layout', 'button_count'),
		('stores', '{$store['store_id']}', 'facebooklb/category_products/send', '0'),
		('stores', '{$store['store_id']}', 'facebooklb/category_products/showfaces', '0'),
		('stores', '{$store['store_id']}', 'facebooklb/category_products/width', '350'),
		('stores', '{$store['store_id']}', 'facebooklb/productlike/color', 'light'),
		('stores', '{$store['store_id']}', 'facebooklb/productlike/enabled', '1'),
		('stores', '{$store['store_id']}', 'facebooklb/productlike/layout', 'button_count'),
		('stores', '{$store['store_id']}', 'facebooklb/productlike/send', '1'),
		('stores', '{$store['store_id']}', 'facebooklb/productlike/showfaces', '0'),
		('stores', '{$store['store_id']}', 'facebooklb/productlike/width', '350'),
		('stores', '{$store['store_id']}', 'general/categories/menu_root_category', '332'),
		('stores', '{$store['store_id']}', 'general/country/allow', 'BR'),
		('stores', '{$store['store_id']}', 'general/country/default', 'BR'),
		('stores', '{$store['store_id']}', 'general/store_information/alias', NULL),
		('stores', '{$store['store_id']}', 'general/store_information/name', 'Omega MSD'),
		('stores', '{$store['store_id']}', 'lightboxpro/general/enabled', '1'),
		('stores', '{$store['store_id']}', 'lightboxpro/size/main', '512x512'),
		('stores', '{$store['store_id']}', 'lightboxpro/size/maxWindow', '800x600'),
		('stores', '{$store['store_id']}', 'lightboxpro/size/thumbnail', '112x112'),
		('stores', '{$store['store_id']}', 'navigationpro/top/enabled', '1'),
		('stores', '{$store['store_id']}', 'onestepcheckout/general/checkout_logo', 'stores/7/logo_-_msd_-_omega.jpg'),
		('stores', '{$store['store_id']}', 'richsnippets/general/enabled', '1'),
		('stores', '{$store['store_id']}', 'richsnippets/general/manufacturer', 'manufacturer'),
		('stores', '{$store['store_id']}', 'sales_email/order/template', '27'),
		('stores', '{$store['store_id']}', 'soldtogether/customer/enabled', '1'),
		('stores', '{$store['store_id']}', 'soldtogether/general/enabled', '1'),
		('stores', '{$store['store_id']}', 'soldtogether/general/random', '1'),
		('stores', '{$store['store_id']}', 'soldtogether/order/addtocartcheckbox', '0'),
		('stores', '{$store['store_id']}', 'soldtogether/order/amazonestyle', '1'),
		('stores', '{$store['store_id']}', 'soldtogether/order/enabled', '1'),
		('stores', '{$store['store_id']}', 'suggestpage/general/show_after_addtocart', '1'),
		('stores', '{$store['store_id']}', 'tm_easytabs/general/enabled', '1'),
		('stores', '{$store['store_id']}', 'web/secure/base_url', 'https://omega-msd.4vets.com.br/'),
		('stores', '{$store['store_id']}', 'web/unsecure/base_url', 'http://omega-msd.4vets.com.br/'),
		('stores', '{$store['store_id']}', 'web/url/use_store', '0')
	;");

}

/** Install MSD Store Config */

$store = Mage::getResourceModel('core/store_collection')->addFieldToFilter('code', 'msd')->getData()[0];

if (isset($store['store_id']))
{
	$installer->run("
		DELETE FROM core_config_data
		WHERE scope = 'stores'
		AND scope_id = {$store['store_id']};
	;");

	$installer->run("
		INSERT INTO `core_config_data` (`scope`, `scope_id`, `path`, `value`) VALUES
		('stores', '{$store['store_id']}', 'askit/general/enabled', '1'),
		('stores', '{$store['store_id']}', 'design/categories/menu_root_category', '241'),
		('stores', '{$store['store_id']}', 'dev/debug/template_hints', '0'),
		('stores', '{$store['store_id']}', 'dev/debug/template_hints_blocks', '0'),
		('stores', '{$store['store_id']}', 'dev/restrict/allow_ips', NULL),
		('stores', '{$store['store_id']}', 'easycatalogimg/category/enabled_for_anchor', '0'),
		('stores', '{$store['store_id']}', 'easycatalogimg/category/enabled_for_default', '0'),
		('stores', '{$store['store_id']}', 'easycatalogimg/general/enabled', '1'),
		('stores', '{$store['store_id']}', 'easyslide/general/load', '1'),
		('stores', '{$store['store_id']}', 'facebooklb/category_products/color', 'light'),
		('stores', '{$store['store_id']}', 'facebooklb/category_products/enabled', '0'),
		('stores', '{$store['store_id']}', 'facebooklb/category_products/layout', 'button_count'),
		('stores', '{$store['store_id']}', 'facebooklb/category_products/send', '0'),
		('stores', '{$store['store_id']}', 'facebooklb/category_products/showfaces', '0'),
		('stores', '{$store['store_id']}', 'facebooklb/category_products/width', '350'),
		('stores', '{$store['store_id']}', 'facebooklb/productlike/color', 'light'),
		('stores', '{$store['store_id']}', 'facebooklb/productlike/enabled', '1'),
		('stores', '{$store['store_id']}', 'facebooklb/productlike/layout', 'button_count'),
		('stores', '{$store['store_id']}', 'facebooklb/productlike/send', '1'),
		('stores', '{$store['store_id']}', 'facebooklb/productlike/showfaces', '0'),
		('stores', '{$store['store_id']}', 'facebooklb/productlike/width', '350'),
		('stores', '{$store['store_id']}', 'general/country/allow', 'BR'),
		('stores', '{$store['store_id']}', 'general/country/default', 'BR'),
		('stores', '{$store['store_id']}', 'general/store_information/address', 'Avenida das Nações Unidas 14171\r\nTorre C - Crystal Tower - 8º Andar\r\nCEP 04794-000\r\nVila Gertrudes - São Paulo/SP'),
		('stores', '{$store['store_id']}', 'general/store_information/name', 'MSD'),
		('stores', '{$store['store_id']}', 'general/store_information/phone', '0800 70 70 512'),
		('stores', '{$store['store_id']}', 'lightboxpro/general/enabled', '1'),
		('stores', '{$store['store_id']}', 'lightboxpro/size/main', '512x512'),
		('stores', '{$store['store_id']}', 'lightboxpro/size/maxWindow', '800x600'),
		('stores', '{$store['store_id']}', 'lightboxpro/size/thumbnail', '112x112'),
		('stores', '{$store['store_id']}', 'navigationpro/top/enabled', '1'),
		('stores', '{$store['store_id']}', 'richsnippets/general/enabled', '1'),
		('stores', '{$store['store_id']}', 'richsnippets/general/manufacturer', 'manufacturer'),
		('stores', '{$store['store_id']}', 'soldtogether/customer/enabled', '1'),
		('stores', '{$store['store_id']}', 'soldtogether/general/enabled', '1'),
		('stores', '{$store['store_id']}', 'soldtogether/general/random', '1'),
		('stores', '{$store['store_id']}', 'soldtogether/order/addtocartcheckbox', '0'),
		('stores', '{$store['store_id']}', 'soldtogether/order/amazonestyle', '1'),
		('stores', '{$store['store_id']}', 'soldtogether/order/enabled', '1'),
		('stores', '{$store['store_id']}', 'suggestpage/general/show_after_addtocart', '1'),
		('stores', '{$store['store_id']}', 'tm_easytabs/general/enabled', '1'),
		('stores', '{$store['store_id']}', 'web/secure/base_url', 'https://msd.4vets.com.br/'),
		('stores', '{$store['store_id']}', 'web/unsecure/base_url', 'http://msd.4vets.com.br/'),
		('stores', '{$store['store_id']}', 'web/url/use_store', '0')
	;");

}

$installer->endSetup();