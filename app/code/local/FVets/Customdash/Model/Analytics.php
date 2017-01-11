<?php

include_once Mage::getBaseDir('lib') . '/google-api-php-client/src/Google/Client.php';
include_once Mage::getBaseDir('lib') . '/google-api-php-client/src/Google/Cache/Abstract.php';
include_once Mage::getBaseDir('lib') . '/google-api-php-client/src/Google/Cache/File.php';
include_once Mage::getBaseDir('lib') . '/google-api-php-client/src/Google/Config.php';
include_once Mage::getBaseDir('lib') . '/google-api-php-client/src/Google/Service.php';
include_once Mage::getBaseDir('lib') . '/google-api-php-client/src/Google/Service/Resource.php';
include_once Mage::getBaseDir('lib') . '/google-api-php-client/src/Google/Model.php';
include_once Mage::getBaseDir('lib') . '/google-api-php-client/src/Google/Collection.php';
include_once Mage::getBaseDir('lib') . '/google-api-php-client/src/Google/Service/Drive.php';
include_once Mage::getBaseDir('lib') . '/google-api-php-client/src/Google/Service/Analytics.php';
include_once Mage::getBaseDir('lib') . '/google-api-php-client/src/Google/Auth/Abstract.php';
include_once Mage::getBaseDir('lib') . '/google-api-php-client/src/Google/Exception.php';
include_once Mage::getBaseDir('lib') . '/google-api-php-client/src/Google/Auth/Exception.php';
include_once Mage::getBaseDir('lib') . '/google-api-php-client/src/Google/Auth/AssertionCredentials.php';
include_once Mage::getBaseDir('lib') . '/google-api-php-client/src/Google/Auth/OAuth2.php';
include_once Mage::getBaseDir('lib') . '/google-api-php-client/src/Google/Logger/Abstract.php';
include_once Mage::getBaseDir('lib') . '/google-api-php-client/src/Google/Logger/Null.php';
include_once Mage::getBaseDir('lib') . '/google-api-php-client/src/Google/Utils.php';
include_once Mage::getBaseDir('lib') . '/google-api-php-client/src/Google/Signer/Abstract.php';
include_once Mage::getBaseDir('lib') . '/google-api-php-client/src/Google/Signer/P12.php';
include_once Mage::getBaseDir('lib') . '/google-api-php-client/src/Google/Http/Request.php';
include_once Mage::getBaseDir('lib') . '/google-api-php-client/src/Google/IO/Abstract.php';
include_once Mage::getBaseDir('lib') . '/google-api-php-client/src/Google/IO/Curl.php';
include_once Mage::getBaseDir('lib') . '/google-api-php-client/src/Google/Http/CacheParser.php';

/**
 * Created by PhpStorm.
 * User: julio
 * Date: 2/15/16
 * Time: 2:01 PM
 */
class FVets_Customdash_Model_Analytics extends Mage_Core_Model_Abstract
{

  protected $client;

  protected function _construct()
  {
	$this->_initClient();
	$this->_init('fvets_customdash/analytics');
  }

  public function getClient()
  {
	return $this->client;
  }

  private function _initClient()
  {
	if (!$this->client)
	{
	  $client_email = 'embed-analytics-4vets@ethereal-pride-121915.iam.gserviceaccount.com';
	  $private_key = file_get_contents(Mage::getModuleDir('etc', 'FVets_Customdash') . '/analytics-4vets.p12');
	  $scopes = array('https://www.googleapis.com/auth/analytics.readonly');
	  $credentials = new Google_Auth_AssertionCredentials(
		$client_email,
		$scopes,
		$private_key
	  );

	  $this->client = new Google_Client();

	  $this->client->setAssertionCredentials($credentials);

	  if ($this->client->getAuth()->isAccessTokenExpired())
	  {
		$this->client->getAuth()->refreshTokenWithAssertion();
	  }

//	$analytics = new Google_Service_Analytics($client);
//
//	$optParams = array(
//	  'dimensions' => 'ga:source,ga:keyword',
//	  'sort' => '-ga:sessions,ga:source',
//	  'filters' => 'ga:medium==organic',
//	  'max-results' => '25');
//
//	$analytics->data_ga->get(
//	  'ga:98416883',
//	  '2015-01-01',
//	  '2015-12-15',
//	  'ga:sessions',
//	  $optParams);
//var_dump($analytics);
	}
  }

  function getParams()
  {
	$websiteId = Mage::app()->getRequest()->getParam('website_switcher');
	if ($websiteId == 0) {
		return null;
	}

	$storeviewId = Mage::app()->getRequest()->getParam('storeview_switcher');
	$params = array();

	if ($storeviewId != 0)
	{
	  $params['view_id'] = Mage::getStoreConfig('customdash/general/view_id', $storeviewId);
	  $params['metrics'] = Mage::getStoreConfig('customdash/general/metrics', $storeviewId);
	} else
	{
	  $website = Mage::getModel('core/website')->load($websiteId);
	  $params['view_id'] = $website->getConfig('customdash/general/view_id');
	  $params['metrics'] = $website->getConfig('customdash/general/metrics');
	}

	return $params;
  }
}