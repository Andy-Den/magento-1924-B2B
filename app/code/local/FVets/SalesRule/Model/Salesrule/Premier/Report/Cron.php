<?php

class FVets_SalesRule_Model_Salesrule_Premier_Report_Cron
{

	private $_websites;

	public function run()
	{
		$this->getCustomersReport();
	}

	private function getCustomersReport()
	{
		$time = strtotime(date('Y-m') . '-01 00:00:00');

		$collection = Mage::getModel('fvets_salesrule/salesrule_premier_report')
			->getCollection()
			->addFieldToFilter('created_at', array('lt' => date('y-m-d H:i:s', $time)))
			->addFieldToFilter('created_at', array('gt' => date('Y-m-d H:i:s', strtotime ( '-1 month' , $time))))
		;

		$csvData = array();
		foreach ($collection as $report)
		{
			$customer = Mage::getModel('customer/customer')->load($report->getCustomerId());
			$reportData = array();
			$reportData['Cliente Id Erp'] = $customer->getIdErp();
			$reportData['Cliente Nome'] = $customer->getFirstname() . ' ' . $customer->getLastname();
			$reportData['Cliente Razao Social'] = $customer->getFirstname() . ' ' . $customer->getRazaoSocial();
			$reportData['Grupo/Linha'] = $report->getGroup();
			$reportData['Peso de'] = $report->getFrom();
			$reportData['Peso ate'] = $report->getTo();
			$reportData['Modificado em'] = $report->getCreatedAt();
			if ($report->getModifiedBy() == 'system')
			{
				$reportData['Modificado Por'] = 'Sistema';
			}
			else
			{
				$salesrep = Mage::getModel('fvets_salesrep/salesrep')->load($report->getSalesrepId());

				if ($salesrep->getId())
				{
					$reportData['Modificado Por'] = 'Representante';
					$reportData['Representante'] = $salesrep->getName();
				}
			}

			$csvData[$customer->getWebsiteId()][] = $reportData;
		}

		foreach ($csvData as $website => $data)
		{
			if (!isset($this->_websites[$website]))
			{
				$this->_websites[$website] = Mage::getModel('core/website')->load($website);
			}

			$file = Mage::getBaseDir('tmp').'/premier_report_' . $this->_websites[$website]->getCode() . '_' . date('Y-m', strtotime ( '-1 month' , $time)) . '_alteracoes-clientes.csv';
			$csv = new Varien_File_Csv();
			$csv = $csv->saveData($file, $data);

			Mage::app()->setCurrentStore($this->_websites[$website]->getDefaultGroup()->getDefaultStoreId());

			$this->sendEmail($this->_websites[$website], $file);
			Mage::helper('datavalidate')->createChannel('scripts');
			Mage::helper('datavalidate')->sendSlackFile(end(explode('/', $file)), $this->_websites[$website]->getCode() . ' - Relatório Política Comercial Premier - ' . date('m/Y', strtotime ( '-1 month' , $time)), $file, 'csv', 'test');
		}
	}

	private function sendEmail($website, $file)
	{
		$mail = new Zend_Mail();
		$mail->setType(Zend_Mime::MULTIPART_RELATED);
		$mail->setBodyHtml('Relatório dos clientes que tiveram alterações na campanha da premier!');
		$mail->setFrom($website->getCode().'@4vets.com.br', 'Ti 4Vets');
		foreach (explode(',', Mage::getStoreConfig('fvets_salesrule/premier/report_email')) as $emailTo)
		{
			$mail->addTo(trim($emailTo));
		}
		$mail->setSubject('4Vets - Relatório Política Comercial Premier - ' . date('m/Y', strtotime ( '-1 month' , date('Y-m-d H:i:s'))));
		$file = $mail->createAttachment(file_get_contents($file));
		$file ->type        = 'text/csv';
		$file ->disposition = Zend_Mime::DISPOSITION_INLINE;
		$file ->encoding    = Zend_Mime::ENCODING_BASE64;
		$file ->filename    = end(explode('/', $file));
		$mail->send();
	}
}