<?php

require_once '../../../app/Mage.php';
umask(0);

ini_set('display_errors', 1);
ini_set('max_execution_time', 600);

Mage::app()->setCurrentStore(2);

$customers  = array(
	array("eny.visionclinic@gmail.com","adm.visionclinic@yahoo.com.br"),
	array("vida@vidaagropecuaria.com.br","adm@vidaagropecuaria.com.br"),
	array("vendasea@gmail.com","agrobeijaflor@gmail.com"),
	array("rafaelacimed@hotmail.com","agropecuariadobalaio@hotmail.com"),
	array("tomasalvess@gmail.com","agrosantaedwigef@hotmail.com"),
	array("agroveterinariacoimbra@yahoo.com.br","agrovetcoimbra@yahoo.com.br"),
	array("cao.com.pet@live.com","alan@petshopcao.com"),
	array("ritttacgp@ig.com.br","ampvendas@yahoo.com.br"),
	array("casadocampo10@gmail.com","andreacamposmello@hotmail.com"),
	array("vetchico@yahoo.com.br","ant-cesar@hotmail.com"),
	array("ceacomercial@hotmail.com","armazemdocriador@yahoo.com.br"),
	array("alexabs@hotmail.com","batistaleco@hotmail.com"),
	array("mah.sousa88@gmail.com","batistaventania@gmail.com"),
	array("bichochic@hotmail.com","bichochicuai@hotmail.com"),
	array("fernandapequivet@yahoo.com.br","bichochik@hotmail.com"),
	array("visuali9@hotmail.com","bichosecaprichosps@gmail.com"),
	array("camiladsp_18@hotmail.com","camiladsr2010@hotmail.com"),
	array("casaracao@yahoo.com.br","casadaracao@rededocampo.com.br"),
	array("casadocampo10@gmail.com","casadocampo.pet@hotmail.com"),
	array("tatinhaveterinaria@hotmail.com","casinhapetcenter@hotmail.com"),
	array("casaagricola.compras@yahoo.com.br","compras.casaagricola@rededocampo.com.br"),
	array("mecchi@amicus.com.br","compras@amicus.com.br"),
	array("stellapires@yahoo.com.br","contato@misterdospetshop.com.br"),
	array("banhotosaagrofal@yahoo.com.br","cpjmundo@yahoo.com.br"),
	array("henryjfmv@hotmail.com","decisaocont@veloxmail.com.br"),
	array("petcareclinicaveterinaria@yahoo.com.br","diegoantonioleao@gmail.com"),
	array("vetallpet@gmail.com","evandro.imoveis@yahoo.com.br"),
	array("vendas@minasbrasilmed.com","farmacia@minasbrasilmed.com"),
	array("clinicaparaisoanimal@hotmail.com","faturamentoparaisoanimal@hotmail.com"),
	array("clinicaparaisoanimal@hotmail.com","faturamentoparaisoanimal@hotmail.com"),
	array("contato@focinhosmolhados.com.br","focinhosmolhados@hotmail.com"),
	array("giselekarina@hotmail.com","giselekarina@hotmail.com.br"),
	array("supermercadostecca@gmail.com","guilhermeulyssescorrea@gmail.com"),
	array("realfinanceiro@yahoo.com.br","hospitalveterinarioer@yahoo.com.br"),
	array("ptjr01@gmail.com","institutopreserve@gmail.com"),
	array("agenciatrans@hospitalmontesinai.com.br","jacinthoerasmo@gmail.com"),
	array("tigrinhovet@gmail.com","janimoreyra@gmail.com"),
	array("mah_sereno@hotmail.com","josehound@yahoo.com.br"),
	array("paulohvet@uol.com.br","jucylene.petclin@gmail.com"),
	array("petbompc@gmail.com","laercio@powerline.com.br"),
	array("latemiapetcenter@yahoo.com.br","latemia_adm@yahoo.com.br"),
	array("italomineiro@bol.com.br","latemia_adm@yahoo.com.br"),
	array("rittasaid@hotmail.com","m-ako@hotmail.com"),
	array("joselito2066@hotmail.com","m.gislane@yahoo.com.br"),
	array("josehound@yahoo.com.br","mah_sereno@hotmail.com"),
	array("sylmarvet@hotmail.com","marciaandreia16@hotmail.com"),
	array("maytheattie@hotmail.com","marise.costa@ig.com.br"),
	array("lucianachara@yahoo.com.br","minasvetguaxute@gmail.com"),
	array("monaneri@yahoo.com.br","monalisa.neri@yahoo.com.br"),
	array("marisleycamillo@yahoo.com.br","monicafiora@ig.com.br"),
	array("arturtadeu2@hotmail.com","mundopetns@hotmail.com"),
	array("nayararocunhagarcia@gmail.com","nayararocunha@hotmail.com"),
	array("valeria.ofermentao@ig.com.br","ofermentao.compras@gmail.com"),
	array("dayennep4@hotmail.com","paradaruralp4@gmail.com"),
	array("paulaabrao@hotmail.com","paulaabraoalvim@hotmail.com"),
	array("pedropaganelli@yahoo.com.br","pedropaganeli@hotmail.com"),
	array("clinicavetanimed@live.com","pepeluz_9@hotmail.com"),
	array("comercialalvorada13@hotmail.com","petmundoanimalj.a@hotmail.com"),
	array("laishelenacarvalho@yahoo.com.br","petsformiga@gmail.com"),
	array("hallimedvet@hotmail.com","petshopdoguinho@gmail.com"),
	array("petshopmelhoramigo1e2@hotmail.com","petshopmelhoramigo2@hotmail.com"),
	array("planeta_animalltda@yahoo.com.br","planetaanimal@starweb.com.br"),
	array("westeticaanimal@gmail.com","provetshospitalanimal@gmail.com"),
	array("maristela.mello@gmail.com","provetshospitalanimal@gmail.com"),
	array("romenesalvador@r7.com","ratodemoco-17@hotmail.com"),
	array("walbert1980@hotmail.com","regina_sspp@hotmail.com"),
	array("bitt.capozzoli@uol.com.br","remracoesecia@gmail.com"),
	array("renasort@gmail.com","renatasort@hotmail.com"),
	array("quatropatasveterinaria@hotmail.com","renato_medvet@yahoo.com.br"),
	array("mpires53@gmail.com","renatofrade@invest.com.br"),
	array("raianavet@gmail.com","solangepaschoal@hotmail.com"),
	array("terraboaagropecuaria@gmail.com","terraboagropecuaria@gmail.com"),
	array("aapapc@yahoo.com.br","verafacce@hotmail.com"),
	array("pugcaoecia@hotmail.com","vetcaoecia@hotmail.com"),
	array("dudaromao@yahoo.com.br","vetcentercaboverde@hotmail.com"),
	array("marcilenyrodrigues@hotmail.com","vitoriapett@hotmail.com"),
	array("ronaldogomes_266@hotmail.com","wandergram1@yahoo.com.br")
);

echo 'Atualizando a base da Raira:' . "\n";


$clientesNaoEncontrados = array();
$clientesAtualizados = array();
$errors = array();

foreach ($customers as $customer)
{
	echo '+';
	$model = Mage::getModel('customer/customer')
		->getCollection()
		->addAttributeToSelect('email')
		->addAttributeToFilter('email', $customer[0])
		->addAttributeToFilter('website_id', '2')
		->getFirstItem();
	if ($model->getId())
	{
		try
		{
			$model->setEmail($customer[1]);
			$model->save();
			$clientesAtualizados[] = $model->getData();
		}
		catch(Exception $e)
		{
			$errors[] = (string)$e->getMessage() . ',' . implode(',', $model->getData());
		}
	}
	else
	{
		$clientesNaoEncontrados[] = $customer;
	}
}

$string = 'Clientes atualizados:' . "\n\n\n";
foreach($clientesAtualizados as $cliente)
{
	$string .= implode(',', $cliente)."\n";
}

$string  .= "\n\n\n\n\n\n\n\n" . 'Clientes não encontrados: ' . "\n\n\n";

foreach($clientesNaoEncontrados as $cliente)
{
	$string .= implode(',', $cliente)."\n";
}

$string  .= "\n\n\n\n\n\n\n\n" . 'Erros ao tentar salvar clientes: ' . "\n\n\n";

foreach($errors as $error)
{
	$string .= (string)$error . "\n";
}

echo "\n";
echo count($clientesAtualizados) . ' - Clientes atualizados' . "\n";
echo count($clientesNaoEncontrados) . ' - Clientes não encontrados' . "\n";
echo count($errors) . ' - Erros ao salvar clientes' . "\n";

file_put_contents('extras/'.date('Ymd').'-arrumar-dados-clientes-somente_email.csv', $string);