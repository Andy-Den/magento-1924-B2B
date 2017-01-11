<?php

class FVets_Allin_Model_Customers extends Mage_Core_Model_Abstract
{
	protected $camposLayout10 = '
	nm_email;
	email_secundario;
	grupo_acesso;
	grupo_cliente;
	nome_fantasia;
	nome;
	sobrenome;
	telefone;
	cidade;
	estado;
	distribuidora;
	ja_fez_login;
	comprou_a_mais_de_45;
	comprou_a_menos_de_45;
	rep_nome1;
	rep_fone1;
	rep_foto1;
	rep_nome2;
	rep_fone2;
	rep_foto2;
	dt_alteracao;
	status_allin;
	status_email_secundario';

	protected $camposLayoutV2 = '
	nm_email;
	email_secundario;
	grupo_cliente;
	nome_fantasia;
	nome;
	sobrenome;
	telefone;
	cidade;
	estado;
	distribuidora;
	ja_fez_login;
	comprou_a_mais_de_45;
	comprou_a_menos_de_45;
	rep_nome1;
	rep_fone1;
	rep_foto1;
	rep_nome2;
	rep_fone2;
	rep_foto2;
	dt_alteracao;
	status_allin;
	status_email_secundario;';

	protected $camposLayoutV3 = '
	nm_email;
	email_secundario;
	grupo_cliente;
	nome_fantasia;
	nome;
	sobrenome;
	telefone;
	cidade;
	estado;
	distribuidora;
	ja_fez_login;
	comprou_a_mais_de_45;
	comprou_a_menos_de_45;
	rep_nome1;
	rep_fone1;
	rep_foto1;
	rep_nome2;
	rep_fone2;
	rep_foto2;
	dt_alteracao;
	status_allin;
	status_email_secundario;
	id_erp;
	grupo_restricao;';

	public $camposLayoutV4 = array('campos' => array(
		array("nome" => "nm_email", "tipo" => "texto", "tamanho" => "255", "unico" => "1"),
		array("nome" => "email_secundario", "tipo" => "texto", "tamanho" => "100"),
		array("nome" => "grupo_cliente", "tipo" => "texto", "tamanho" => "50"),
		array("nome" => "nome_fantasia", "tipo" => "texto", "tamanho" => "100"),
		array("nome" => "nome", "tipo" => "texto", "tamanho" => "50"),
		array("nome" => "sobrenome", "tipo" => "texto", "tamanho" => "50"),
		array("nome" => "telefone", "tipo" => "texto", "tamanho" => "30"),
		array("nome" => "cidade", "tipo" => "texto", "tamanho" => "30"),
		array("nome" => "estado", "tipo" => "texto", "tamanho" => "30"),
		array("nome" => "distribuidora", "tipo" => "texto", "tamanho" => "30"),
		array("nome" => "ja_fez_login", "tipo" => "texto", "tamanho" => "3"),
		array("nome" => "comprou_a_mais_de_45", "tipo" => "texto", "tamanho" => "3"),
		array("nome" => "comprou_a_menos_de_45", "tipo" => "texto", "tamanho" => "3"),
		array("nome" => "rep_nome1", "tipo" => "texto", "tamanho" => "50"),
		array("nome" => "rep_fone1", "tipo" => "texto", "tamanho" => "30"),
		array("nome" => "rep_foto1", "tipo" => "texto", "tamanho" => "150"),
		array("nome" => "rep_nome2", "tipo" => "texto", "tamanho" => "50"),
		array("nome" => "rep_fone2", "tipo" => "texto", "tamanho" => "30"),
		array("nome" => "rep_foto2", "tipo" => "texto", "tamanho" => "150"),
		array("nome" => "dt_alteracao", "tipo" => "data", "formato" => "dd-mm-aaaa"),
		array("nome" => "status_allin", "tipo" => "numero"),
		array("nome" => "status_email_secundario", "tipo" => "texto", "tamanho" => "1"),
		array("nome" => "id_erp", "tipo" => "texto", "tamanho" => "30"),
		array("nome" => "grupo_restricao", "tipo" => "texto", "tamanho" => "100"),
		array("nome" => "marcas", "tipo" => "texto", "tamanho" => "255"),
	));

	protected $camposLayoutClassic = '
	nm_email;
	nome;
	sobrenome;
	ramo_atividade;
	status_allin';

	public function getCustomers()
	{
		$collection = new FVets_Allin_Model_Customers_Collection();
		$collection = $collection->getCustomers($this->getListName());
		return $collection;
	}

	function massRemoteUpdate($idAccountToUpdate, $customersToUpdate, $version = null)
	{
		$count = 1;

		if (!$version) {
			$remoteUpdateFunctionName = "remoteUpdate";
		} else {
			$remoteUpdateFunctionName = "remoteUpdate" . $version;
			$initCamposLayoutFunctionName = "initCamposLayout" . $version;
			$this->{$initCamposLayoutFunctionName}($idAccountToUpdate);
		}

		$allRowsValues = '';
		foreach ($customersToUpdate as $customer) {
			try {
				$allRowsValues .= $this->{$remoteUpdateFunctionName}($idAccountToUpdate, $customer);
				$allRowsValues .= "\n";
			} catch (Exception $ex) {
				Mage::throwException($ex->__toString());
			}
			$count++;
		}

		//registra a atualização
		$synchronize = Mage::getModel('fvets_allin/synchronize');
		$synchronize->setIdAccount($idAccountToUpdate);
		$synchronize->save();

		return $allRowsValues;
	}

	function remoteUpdate($idAccountToUpdate, $customer)
	{
		$account = Mage::getModel('fvets_allin/account')->load($idAccountToUpdate);

		$helper = $this->getHelper();

		$ticket = $helper->getTicket($account);

		if ($ticket == 'Usuario e senha invalidos!') {
			Mage::throwException($ticket);
		}

		$jaRealizouLogin = $helper->jaRealizouLogin($customer);
		if ($jaRealizouLogin) {
			$jaRealizouLogin = 'sim';
		} else {
			$jaRealizouLogin = 'nao';
		}

		$nmEmail = $customer->getEmail();
		$statusAllin = $customer->getFvetsAllinStatus() ? ($customer->getFvetsAllinStatus() == 'V' ? '1' : '0') : '0';
		$statusEmailSecundario = $customer->getStatusSecondaryEmail() ? $customer->getStatusSecondaryEmail() : 'I';
		$emailSecundario = $customer->getSecondEmail();
		$grupoAcesso = $this->getAccessGroupDescription($customer->getStoreView());
		$grupoCliente = $this->getCustomerGroupDescription($customer->getGroupId());
		$nomeFantasia = utf8_decode($customer->getRazaoSocial());
		$nome = utf8_decode($customer->getFirstname());
		$lastNome = utf8_decode($customer->getLastname());
		$telefone = utf8_decode($customer->getTelefone());
		$cidade = utf8_decode($customer->getCidade());
		$estado = utf8_decode($customer->getEstado());
		$distribuidora = utf8_decode($customer->getWebsiteName());
		//$jaRealizouLogin
		$comprouAMaisde45Dias = $this->comprouAMaisDeNDias($customer->getId(), '45');
		$comprouAMenosde45Dias = $this->comprouAMenosDeNDias($customer->getId(), '45');
		$reps = utf8_decode($this->getReps($customer));
		$dtAlteracao = $customer->getUpdatedAt();

		$valores = $nmEmail . ';' . $emailSecundario . ';' . $grupoAcesso . ';' . $grupoCliente . ';' . $nomeFantasia . ';' . $nome . ';' . $lastNome . ';' . $telefone . ';' . $cidade . ';' . $estado . ';' . $distribuidora . ';' . $jaRealizouLogin . ';' . $comprouAMaisde45Dias . ';' . $comprouAMenosde45Dias . ';' . $reps . ';' . $dtAlteracao . ';' . $statusAllin . ';' . $statusEmailSecundario;

		$client = new nusoap_client($helper->getInserirEmailBaseUrl(), true);
		$arr = array("nm_lista" => "{$account->getListName()}", "campos" => $this->camposLayout10, "valor" => $valores);
		$client->call('inserirEmailBase', array($ticket, $arr));

		if ($this->_validateResponse($client->return['return'], $idAccountToUpdate, $nmEmail)) {
			return $valores;
		} else {
			return '';
		}
	}

	function remoteUpdateV2($idAccountToUpdate, $customer)
	{
		$account = Mage::getModel('fvets_allin/account')->load($idAccountToUpdate);

		$helper = $this->getHelper();

		$ticket = $helper->getTicket($account);

		if ($ticket == 'Usuario e senha invalidos!') {
			Mage::throwException($ticket);
		}

		$jaRealizouLogin = $helper->jaRealizouLogin($customer);
		if ($jaRealizouLogin) {
			$jaRealizouLogin = 'sim';
		} else {
			$jaRealizouLogin = 'nao';
		}

		$nmEmail = $customer->getEmail();
		$statusAllin = $customer->getFvetsAllinStatus() ? ($customer->getFvetsAllinStatus() == 'V' ? '1' : '0') : '0';
		$statusEmailSecundario = $customer->getStatusSecondaryEmail() ? $customer->getStatusSecondaryEmail() : 'I';
		$emailSecundario = $customer->getSecondEmail();
		$grupoCliente = $this->getCustomerGroupDescription($customer->getGroupId());
		$nomeFantasia = utf8_decode($customer->getRazaoSocial());
		$nome = utf8_decode($customer->getFirstname());
		$lastNome = utf8_decode($customer->getLastname());
		$telefone = utf8_decode($customer->getTelefone());
		$cidade = utf8_decode($customer->getCidade());
		$estado = utf8_decode($customer->getEstado());
		$distribuidora = utf8_decode($customer->getWebsiteName());
		//$jaRealizouLogin
		$comprouAMaisde45Dias = $this->comprouAMaisDeNDias($customer->getId(), '45');
		$comprouAMenosde45Dias = $this->comprouAMenosDeNDias($customer->getId(), '45');
		$reps = utf8_decode($this->getReps($customer));
		$dtAlteracao = $customer->getUpdatedAt();

		$brandsAllowed = $this->getBrandsFields($customer);

		$valores = $nmEmail . ';' . $emailSecundario . ';' . $grupoCliente . ';' . $nomeFantasia . ';' . $nome . ';' . $lastNome . ';' . $telefone . ';' . $cidade . ';' . $estado . ';' . $distribuidora . ';' . $jaRealizouLogin . ';' . $comprouAMaisde45Dias . ';' . $comprouAMenosde45Dias . ';' . $reps . ';' . $dtAlteracao . ';' . $statusAllin . ';' . $statusEmailSecundario . ';' . $brandsAllowed;

		$client = new nusoap_client($helper->getInserirEmailBaseUrl(), true);
		$arr = array("nm_lista" => "{$account->getListName()}", "campos" => $this->camposLayoutV2, "valor" => $valores);
		$client->call('inserirEmailBase', array($ticket, $arr));

		if ($this->_validateResponse($client->return['return'], $idAccountToUpdate, $nmEmail)) {
			return $valores;
		} else {
			return '';
		}
	}

	function remoteUpdateV3($idAccountToUpdate, $customer)
	{
		$account = Mage::getModel('fvets_allin/account')->load($idAccountToUpdate);

		$helper = $this->getHelper();

		$ticket = $helper->getTicket($account);

		if ($ticket == 'Usuario e senha invalidos!') {
			Mage::throwException($ticket);
		}

		$jaRealizouLogin = $helper->jaRealizouLogin($customer);
		if ($jaRealizouLogin) {
			$jaRealizouLogin = 'sim';
		} else {
			$jaRealizouLogin = 'nao';
		}

		$nmEmail = $customer->getEmail();
		$statusAllin = $customer->getFvetsAllinStatus() ? ($customer->getFvetsAllinStatus() == 'V' ? '1' : '0') : '0';
		$statusEmailSecundario = $customer->getStatusSecondaryEmail() ? $customer->getStatusSecondaryEmail() : 'I';
		$emailSecundario = $customer->getSecondEmail();
		$grupoCliente = $this->getCustomerGroupDescription($customer->getGroupId());
		$nomeFantasia = utf8_decode($customer->getRazaoSocial());
		$nome = utf8_decode($customer->getFirstname());
		$lastNome = utf8_decode($customer->getLastname());
		$telefone = utf8_decode($customer->getTelefone());
		$cidade = utf8_decode($customer->getCidade());
		$estado = utf8_decode($customer->getEstado());
		$distribuidora = utf8_decode($customer->getWebsiteName());
		//$jaRealizouLogin
		$comprouAMaisde45Dias = $this->comprouAMaisDeNDias($customer->getId(), '45');
		$comprouAMenosde45Dias = $this->comprouAMenosDeNDias($customer->getId(), '45');
		$reps = utf8_decode($this->getReps($customer));
		$dtAlteracao = $customer->getUpdatedAt();

		$brandsAllowed = $this->getBrandsFields($customer);

		$idErp = $customer->getIdErp();
		$restrictionGroups = $this->getRestrictionsGroups($customer);

		$valores = $nmEmail . ';' . $emailSecundario . ';' . $grupoCliente . ';' . $nomeFantasia . ';' . $nome . ';' . $lastNome . ';' . $telefone . ';' . $cidade . ';' . $estado . ';' . $distribuidora . ';' . $jaRealizouLogin . ';' . $comprouAMaisde45Dias . ';' . $comprouAMenosde45Dias . ';' . $reps . ';' . $dtAlteracao . ';' . $statusAllin . ';' . $statusEmailSecundario . ';' . $idErp . ';' . $restrictionGroups . ';' . $brandsAllowed;

		$client = new nusoap_client($helper->getInserirEmailBaseUrl(), true);
		$arr = array("nm_lista" => "{$account->getListName()}", "campos" => $this->camposLayoutV3, "valor" => $valores);
		$client->call('inserirEmailBase', array($ticket, $arr));

		if ($this->_validateResponse($client->return['return'], $idAccountToUpdate, $nmEmail)) {
			return $valores;
		} else {
			return '';
		}
	}

	function remoteUpdateV4($idAccountToUpdate, $customer)
	{
		$account = Mage::getModel('fvets_allin/account')->load($idAccountToUpdate);

		$helper = $this->getHelper();

		$ticket = $helper->getTicket($account);

		if ($ticket == 'Usuario e senha invalidos!') {
			Mage::throwException($ticket);
		}

		$jaRealizouLogin = $helper->jaRealizouLogin($customer);
		if ($jaRealizouLogin) {
			$jaRealizouLogin = 'sim';
		} else {
			$jaRealizouLogin = 'nao';
		}

		$nmEmail = $customer->getEmail();
		$statusAllin = $customer->getFvetsAllinStatus() ? ($customer->getFvetsAllinStatus() == 'V' ? '1' : '0') : '0';
		$statusEmailSecundario = $customer->getStatusSecondaryEmail() ? $customer->getStatusSecondaryEmail() : 'I';
		$emailSecundario = $customer->getSecondEmail();
		$grupoCliente = $this->getCustomerGroupDescription($customer->getGroupId());
		$nomeFantasia = utf8_decode($customer->getRazaoSocial());
		$nome = utf8_decode($customer->getFirstname());
		$lastNome = utf8_decode($customer->getLastname());
		$telefone = utf8_decode($customer->getTelefone());

		$address = $this->getCustomerAddress($customer);
		$cidade = '';
		$estado = '';
		if ($address) {
			$cidade = $helper->clearString($address->getCity());
			$estado = $helper->clearString($address->getRegion());
		}

		$distribuidora = utf8_decode($customer->getWebsiteName());
		//$jaRealizouLogin
		$comprouAMaisde45Dias = $this->comprouAMaisDeNDias($customer->getId(), '45');
		$comprouAMenosde45Dias = $this->comprouAMenosDeNDias($customer->getId(), '45');
		$reps = utf8_decode($this->getReps($customer));
		$dtAlteracao = $customer->getUpdatedAt();

		$brandsAllowed = $this->getBrandsFieldsAsString($customer);

		$idErp = $customer->getIdErp();
		$restrictionGroups = $this->getRestrictionsGroups($customer);

		$valores = $nmEmail . ';' . $emailSecundario . ';' . $grupoCliente . ';' . $nomeFantasia . ';' . $nome . ';' . $lastNome . ';' . $telefone . ';' . $cidade . ';' . $estado . ';' . $distribuidora . ';' . $jaRealizouLogin . ';' . $comprouAMaisde45Dias . ';' . $comprouAMenosde45Dias . ';' . $reps . ';' . $dtAlteracao . ';' . $statusAllin . ';' . $statusEmailSecundario . ';' . $idErp . ';' . $restrictionGroups . ';' . $brandsAllowed;

		$client = new nusoap_client($helper->getInserirEmailBaseUrl(), true);
		$arr = array("nm_lista" => "{$account->getListName()}", "campos" => $this->camposLayoutV4, "valor" => $valores);
		$client->call('inserirEmailBase', array($ticket, $arr));

		if ($this->_validateResponse($client->return['return'], $idAccountToUpdate, $nmEmail)) {
			return $valores;
		} else {
			return '';
		}
	}

	function initCamposLayoutV2($idAccount)
	{

		$account = Mage::getModel('fvets_allin/account')->load($idAccount);

		$storeGroup = Mage::getModel('core/store_group')->getCollection()
			->addFieldToFilter('website_id', $account->getWebsiteId())
			->getFirstItem();
		$rootCategory = $storeGroup->getRootCategoryId();

		if (!$this->getAllowedBrands()) {
			$allWebsiteCategories = Mage::getModel('catalog/category')
				->getCollection()
				->addAttributeToSelect('*')
				->addFieldToFilter('parent_id', $rootCategory)
				->addIsActiveFilter()
				->setOrder('name', 'asc');
			$this->setAllWebsiteCategories($allWebsiteCategories);
		} else {
			$allWebsiteCategories = Mage::getModel('catalog/category')
				->getCollection()
				->addAttributeToSelect('*')
				->addFieldToFilter('parent_id', $rootCategory)
				->addFieldToFilter('url_key', array('in' => $this->getAllowedBrands()))
				->setOrder('name', 'asc');
			$this->setAllWebsiteCategories($allWebsiteCategories);
		}

		$aditionalHeader = '';

		foreach ($allWebsiteCategories as $category) {
			$aditionalHeader .= str_replace("-", "_", $category->getUrlKey()) . ';';
		}

		$aditionalHeader = rtrim($aditionalHeader, ";");

		$this->camposLayoutV2 .= $aditionalHeader;
	}

	function initCamposLayoutV3($idAccount)
	{

		$account = Mage::getModel('fvets_allin/account')->load($idAccount);

		$allWebsiteCategories = Mage::helper('fvets_allin')->getArrayAllowedCategories($account->getWebsiteId());

		$aditionalHeader = '';

		foreach ($allWebsiteCategories as $category) {
			$aditionalHeader .= str_replace("-", "_", $category->getUrlKey()) . ';';
		}

		$aditionalHeader = rtrim($aditionalHeader, ";");

		$this->camposLayoutV3 .= $aditionalHeader;

		$this->setAllWebsiteCategories($allWebsiteCategories);
	}

	function initCamposLayoutV4($idAccount)
	{
		$arrayTmp = array();
		foreach ($this->camposLayoutV4['campos'] as $item) {
			$arrayTmp[] = $item['nome'];
		}

		$this->camposLayoutV4 = implode(';', $arrayTmp);

		$account = Mage::getModel('fvets_allin/account')->load($idAccount);
		$allWebsiteCategories = Mage::helper('fvets_allin')->getArrayAllowedCategories($account->getWebsiteId());
		$this->setAllWebsiteCategories($allWebsiteCategories);
	}

	function remoteUpdateClassic($idAccountToUpdate, $customer)
	{
		$account = Mage::getModel('fvets_allin/account')->load($idAccountToUpdate);

		$helper = $this->getHelper();

		$ticket = $helper->getTicket($account);

		if ($ticket == 'Usuario e senha invalidos!') {
			Mage::throwException($ticket);
		}

		$nmEmail = $customer->getEmail();
		$nome = utf8_decode($customer->getFirstname());
		$lastNome = utf8_decode($customer->getLastname());
		$hiveOfActivity = Mage::helper('fvets_allin')->getAttributeLabelByCodeAndValue('hive_of_activity', $customer->getHiveOfActivity());
		$statusAllin = $customer->getFvetsAllinStatus() ? ($customer->getFvetsAllinStatus() == 'V' ? '1' : '0') : '0';

		$valores = $nmEmail . ';' . $nome . ';' . $lastNome . ';' . $hiveOfActivity . ';' . $statusAllin;

		$client = new nusoap_client($helper->getInserirEmailBaseUrl(), true);
		$arr = array("nm_lista" => "{$account->getListName()}", "campos" => $this->camposLayoutClassic, "valor" => $valores);
		$client->call('inserirEmailBase', array($ticket, $arr));

		if ($this->_validateResponse($client->return['return'], $idAccountToUpdate, $nmEmail)) {
			return $valores;
		} else {
			return '';
		}
	}

	function initCamposLayoutClassic($idAccount)
	{
		return $this->camposLayoutClassic;
	}

	function remoteCleanTrash($idAccountToUpdate, $nmEmail)
	{
		$account = Mage::getModel('fvets_allin/account')->load($idAccountToUpdate);

		$helper = $this->getHelper();

		$ticket = $helper->getTicket($account);

		if ($ticket == 'Usuario e senha invalidos!') {
			Mage::throwException($ticket);
		}
		$flagInvalidForAllin = 0;

		$client = new nusoap_client($helper->getInserirEmailBaseUrl(), true);
		$arr = array("nm_lista" => "{$account->getListName()}", "campos" => "nm_email;status_allin", "valor" => "{$nmEmail};{$flagInvalidForAllin}");
		$client->call('inserirEmailBase', array($ticket, $arr));

		return $this->_validateResponse($client->return['return'], $idAccountToUpdate, $nmEmail);
	}

	protected function getHelper()
	{
		return Mage::helper('fvets_allin');
	}

	private function getAccessGroupDescription($grupoAcesso)
	{
		if ($grupoAcesso) {
			$grupoAcesso = explode(',', $grupoAcesso)[0];
			return Mage::getModel('core/store')->load($grupoAcesso)->getData('code');
		} else {
			return null;
		}
	}

	private function getCustomerGroupDescription($customerGroup)
	{
		if ($customerGroup) {
			$customerGroup = explode(',', $customerGroup)[0];
			return Mage::getModel('customer/group')->load($customerGroup)->getData('customer_group_code');
		} else {
			return null;
		}
	}

	private function comprouAMaisDeNDias($customerId, $dias)
	{
		$salesCollection = Mage::getModel('sales/order')->getCollection()
			->addFieldToFilter('customer_id', $customerId)
			->addFieldToFilter('created_at', array(
				'from' => strtotime('-2000 day', time()),
				'to' => strtotime('-45 day', time()),
				'datetime' => true));

		if (count($salesCollection) > 0) {
			return 'sim';
		} else {
			return 'nao';
		}
	}

	private function comprouAMenosDeNDias($customerId, $dias)
	{
		$salesCollection = Mage::getModel('sales/order')->getCollection()
			->addFieldToFilter('customer_id', $customerId)
			->addFieldToFilter('created_at', array(
				'from' => strtotime('-45 day', time()),
				'to' => time(),
				'datetime' => true));

		if (count($salesCollection) > 0) {
			return 'sim';
		} else {
			return 'nao';
		}
	}

	private function getReps($customer)
	{
		$repsIds = explode(',', $customer->getFvetsSalesrep());

		if (!$repsIds || !$customer->getFvetsSalesrep()) {
			return ';;;;;';
		}

		$repsData = array();
		$count = 1;
		foreach ($repsIds as $repId) {
			$rep = Mage::getModel('fvets_salesrep/salesrep')->load($repId);
			if ($rep->getId()) {
				$urlImg = (Mage::app()->getStore(explode(',', $customer->getStoreId())[0])->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK) . 'media/fvets/salesrep/' . $rep->getId() . '.jpg');
			} else {
				$urlImg = '';
			}
			array_push($repsData, array('id' => $rep->getId(), 'name' => $rep->getName(), 'telephone' => $rep->getTelephone(), 'urlImg' => $urlImg));

			if ($count == 2) {
				break;
			}

			$count++;
		}
		$result = '';
		foreach ($repsData as $rep) {
			$result .= $rep['name'] . ';' . $rep['telephone'] . ';' . $rep['urlImg'] . ';';
		}
		$result = substr($result, 0, -1);

		if (count($repsIds) == 1) {
			$result .= ';;;';
		}
		return $result;
	}

	private function getBrandsFields($customer)
	{
		$allWebsiteCategories = $this->getAllWebsiteCategories();

		$returnValue = '';

		foreach ($allWebsiteCategories as $category) {
			$customerCanBuyBrand = $this->customerCanBuy($customer, $category);
			if ($customerCanBuyBrand) {
				$returnValue .= '1;';
			} else {
				$returnValue .= '0;';
			}
		}

		$returnValue = rtrim($returnValue, ";");

		return $returnValue;
	}

	private function getBrandsFieldsAsString($customer)
	{
		$allWebsiteCategories = $this->getAllWebsiteCategories();

		$returnValue = '';

		foreach ($allWebsiteCategories as $category) {
			$customerCanBuyBrand = $this->customerCanBuy($customer, $category);
			if ($customerCanBuyBrand) {
				$returnValue .= '[' . $category->getUrlKey() . ']';
			}
		}

		return $returnValue;
	}

	private function customerCanBuy($customer, $category)
	{
		$helper = Mage::helper('fvets_salesrep/customer');
		$allowedCategories = $helper->getAllowedCategories($customer, false, false);

		foreach ($allowedCategories as $allowedCategory) {
			if ($allowedCategory->getId() == $category->getId()) {
				return true;
			}
		}
		return false;
	}

	private function _validateResponse($response, $idAccount, $email)
	{
		if (!(strpos($response, 'Email inserido na base!') !== false)) {
			if (Mage::getStoreConfig('allin/general/integration_log_enabled')) {
				try {
					$errors = array();
					$errors['type'] = 'error';
					$errors['account'] = $idAccount;
					$errors['email'] = $email;
					$errors['allin_return'] = $response;
					$this->getHelper()->log($errors);
				} catch (Exception $ex) {
					//let the flow continues
				}
			}
			return false;
		}
		return true;
	}

	public function createListIfNotExists($idAccount, $fields)
	{
		$account = Mage::getModel('fvets_allin/account')->load($idAccount);
		$helper = $this->getHelper();
		$ticket = $helper->getTicket($account);
		if ($ticket == 'Usuario e senha invalidos!') {
			Mage::throwException($ticket);
		}
		$url = "https://painel02.allinmail.com.br/allinapi/?method=criarLista&token=" . $ticket;

		$response = $this->doPost($url, $fields);
		echo $response;
	}

	private function getAllLists($idAccount)
	{
		$account = Mage::getModel('fvets_allin/account')->load($idAccount);
		$helper = $this->getHelper();
		$ticket = $helper->getTicket($account);
		if ($ticket == 'Usuario e senha invalidos!') {
			Mage::throwException($ticket);
		}
		$url = "https://painel02.allinmail.com.br/allinapi/?method=getlistas&output=json&token=" . $ticket;

		$jsonData = file_get_contents($url);
		$response = Mage::helper('core')->jsonDecode($jsonData);
		return $response;
	}

	private function doPost($url, $data)
	{
		$client = new Varien_Http_Client($url);
		$client->setMethod(Varien_Http_Client::POST);
		$client->setParameterPost('dados', Mage::helper('core')->jsonEncode($data));
		try {
			$response = $client->request();
			if ($response->isSuccessful()) {
				echo $response->getBody();
			}
		} catch (Exception $e) {
		}
	}

	private function getRestrictionsGroups($customer)
	{
		$customerHelper = Mage::helper('fvets_catalogrestrictiongroup/customer');
		$allinHelper = Mage::helper('fvets_allin');
		$restrictionGroups = $customerHelper->getSelectedCatalogrestrictiongroupsCollection($customer);

		$restrictionGroupsFormated = '';

		foreach ($restrictionGroups as $restrictionGroup) {
			$restrictionGroupsFormated .= '[' . $allinHelper->formatKey($restrictionGroup->getName()) . ']';
		}
		return $restrictionGroupsFormated;
	}

	private function getCustomerAddress($customer)
	{

		$defaultBillingId = $customer->getDefaultBilling();

		if ($defaultBillingId) {
			$address = Mage::getModel('customer/address')->load($defaultBillingId);
			if ($address) {
				return $address;
			}
		}
		return null;
	}
}