<?php

namespace CflApi;

class Api
{
	const REQUEST_ID_PREFIX = 'SO';

	const REPONSE_CODE_SUCCESS_ITEM_CREATED           = 'S71';
	const REPONSE_CODE_SUCCESS_ITEM_UPDATED           = 'S72';
	const REPONSE_CODE_SUCCESS_ITEM_DELETED           = 'S73';
	const REPONSE_CODE_ERROR_ITEM_EXISTS              = 'F71';
	const REPONSE_CODE_ERROR_ITEM_MISSING             = 'F72';
	const REPONSE_CODE_ERROR_ORDER_EXISTS             = 'F73';
	const REPONSE_CODE_ERROR_STOCK_EXISTS             = 'F74';
	const REPONSE_CODE_ERROR_ORDER_AND_STOCK_EXIST    = 'F75';
	const REPONSE_CODE_ERROR_ITEM_CHANGE_TO_CF_DENIED = 'F76';
	const REPONSE_CODE_ERROR_INVALID_INPUT_FIELD      = 'F77';
	const REPONSE_CODE_ERROR_UNCLASSIFIED             = 'F79';

	const RESPONSE_CODE_DESCRIPTIONS = [
		self::REPONSE_CODE_SUCCESS_ITEM_CREATED           => 'Item has been created',
		self::REPONSE_CODE_SUCCESS_ITEM_UPDATED           => 'Item has been updated',
		self::REPONSE_CODE_SUCCESS_ITEM_DELETED           => 'Item has been deleted',
		self::REPONSE_CODE_ERROR_ITEM_EXISTS              => 'Item already exists, it could not be created',
		self::REPONSE_CODE_ERROR_ITEM_MISSING             => 'Item does not exist, it could not be updated/deleted',
		self::REPONSE_CODE_ERROR_ORDER_EXISTS             => 'Item could not be deleted (order exists)',
		self::REPONSE_CODE_ERROR_STOCK_EXISTS             => 'Item could not be deleted (stock exists)',
		self::REPONSE_CODE_ERROR_ORDER_AND_STOCK_EXIST    => 'Item could not be deleted (order and stock exists)',
		self::REPONSE_CODE_ERROR_ITEM_CHANGE_TO_CF_DENIED => 'RTW/PO item could not be changed to CF',
		self::REPONSE_CODE_ERROR_INVALID_INPUT_FIELD      => 'Invalid value provided',
		self::REPONSE_CODE_ERROR_UNCLASSIFIED             => 'Unclassified error',
	];

	protected $_connection;
	protected $_lastResponse = [];
	protected $_lastResponseCode = '';
	protected $_lastResponseCodeDescription = '';

	protected $_baseUrl;
	protected $_apiKey;
	protected $_apiUserId;
	protected $_warehouseCode;
	protected $_customerCode;

	protected $_defaultContentType = 'text/xml';

	/**
	 * Set some required properties
	 *
	 * @param string $apiUserId
	 * @param string $apiKey
	 * @param string $baseUrl
	 * @param string $warehouseCode
	 * @param string $customerCode
	 */
	public function __construct(string $apiUserId, string $apiKey, string $baseUrl, string $warehouseCode, string $customerCode)
	{
		$this->_apiUserId     = $apiUserId;
		$this->_apiKey        = $apiKey;
		$this->_baseUrl       = $baseUrl;
		$this->_warehouseCode = $warehouseCode;
		$this->_customerCode  = $customerCode;
	}

	/**
	 * Was response code success? If first char is 'S', this means success
	 *
	 * @param $responseCode
	 *
	 * @return bool
	 */
	protected function _getResponseCodeSuccess(string $responseCode): bool
	{
		return (bool) ('S' == substr($responseCode,0,1));
	}

	/**
	 * Return description of response code
	 *
	 * @param $responseCode
	 *
	 * @return string
	 */
	protected function _getResponseCodeDescription(string $responseCode): string
	{
		if (array_key_exists($responseCode, self::RESPONSE_CODE_DESCRIPTIONS)) {
			return self::RESPONSE_CODE_DESCRIPTIONS[$responseCode];
		} else {
			return "Unknown response code: $responseCode";
		}
	}

	/**
	 * Return last response code description
	 *
	 * @return string
	 */
	public function getLastResponseCodeDescription(): string
	{
		return $this->_lastResponseCodeDescription;
	}

	/**
	 * Return last response code description
	 *
	 * @return string
	 */
	public function getLastResponseCode(): string
	{
		return $this->_lastResponseCode;
	}

	/**
	 * Return last response XML reformatted as an array
	 *
	 * @return array
	 */
	public function getLastResponse(): array
	{
		return $this->_lastResponse;
	}

	/**
	 * Build the CURL request
	 *
	 * @param null $payload
	 *
	 * @return \CflApi\API
	 */
	protected function _buildRequest(&$payload = null): \CflApi\API
	{
		$this->_connection = curl_init();
		curl_setopt($this->_connection, CURLOPT_HTTPHEADER, ["Content-Type: {$this->_defaultContentType}"]);
		curl_setopt($this->_connection, CURLOPT_CONNECTTIMEOUT, 2);
		curl_setopt($this->_connection, CURLOPT_RETURNTRANSFER, 1);

		$requestId = uniqid(self::REQUEST_ID_PREFIX, true);
		$time      = date('c');

		$payload = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<Request>
	<Auth>
		<user_code><![CDATA[{$this->_apiUserId}]]></user_code>
		<password><![CDATA[{$this->_apiKey}]]></password>
	</Auth>
	<RequestID><![CDATA[{$requestId}]]></RequestID>
	<RequestDate><![CDATA[{$time}]]></RequestDate>
{$payload}
</Request>
XML;

		return $this;
	}

	/**
	 * Make a CURL request
	 *
	 * @param string $endpoint
	 * @param string $method
	 * @param null   $payload
	 *
	 * @return array
	 */
	protected function _request(string $endpoint, string $method, $payload = null): bool
	{
		$this->_buildRequest($payload);

		switch (strtolower($method)) {
			case 'post':
				curl_setopt($this->_connection, CURLOPT_POST, true);
				curl_setopt($this->_connection, CURLOPT_POSTFIELDS, $payload);
				break;
			case 'put':
				curl_setopt($this->_connection, CURLOPT_CUSTOMREQUEST, 'PUT');
				curl_setopt($this->_connection, CURLOPT_POSTFIELDS, $payload);
				break;
			case 'delete':
				curl_setopt($this->_connection, CURLOPT_CUSTOMREQUEST, 'DELETE');
				break;
			case 'get':
			default:
				// GET is the default request method, so we add the payload params onto the query string
				if (!is_null($payload)) {
					$endpoint .= '?' . http_build_query($payload);
				}
				break;
		}

		$endpoint = $this->_baseUrl . $endpoint;

		curl_setopt($this->_connection, CURLOPT_URL, $endpoint);

		$data = curl_exec($this->_connection);

		return $this->_processResponse($data);
	}

	/**
	 * Process the response and return an array
	 *
	 * @param string $response
	 *
	 * @return bool
	 */
	protected function _processResponse(string $response): bool
	{
		$httpCode = curl_getinfo($this->_connection, CURLINFO_HTTP_CODE);

		// If a 5xx error was returned or the response is empty, return failure
		if ($httpCode >= 500 || empty($response)) {
			$curlError = curl_error($this->_connection);
			$this->_lastResponseCodeDescription = $curlError;
			$this->_lastResponseCode = (string)$httpCode;
			return false;
		}

		// Load string into XML, json encode, then decode as an array
		$response = simplexml_load_string($response, 'SimpleXMLElement', LIBXML_NOCDATA);
		$this->_lastResponse = $response = (array)json_decode(json_encode($response), true);
		$status = false;
		if (empty($response['ResponseID'])) {
			throw new \Exception("Invalid response:" . print_r($response, true));
		} else {
			if (empty($response['ResponseStatus'])) {
				$this->_lastResponseCode = (string)$httpCode;
				$this->_lastResponseCodeDescription = '';
				$status = true;
			} else {
				$this->_lastResponseCode            = (string)$response['ResponseStatus'];
				$this->_lastResponseCodeDescription = $this->_getResponseCodeDescription($response['ResponseStatus']);
				$status = $this->_getResponseCodeSuccess($response['ResponseStatus']);
			}
		}
		return $status;
	}


	/* ======= HTTP Methods ======= */


	/**
	 * Make a POST request
	 *
	 * @param string $endpoint
	 * @param string $payload
	 *
	 * @return bool
	 */
	protected function _post(string $endpoint, string $payload): bool
	{
		$data = $this->_request($endpoint, 'post', $payload);

		curl_close($this->_connection);

		return $data;
	}


	/* ======= Product/Item Functions ======= */


	/**
	 * Get all products from CFL
	 *
	 * @param string $itemNumber
	 *
	 * @return array
	 */
	public function getItems(string $itemNumber = null): array
	{
		$time = date('c');

		$payload = <<<XML
<GroupingLevel>
	<Type>ItemNumber</Type>
	<BalanceDate><![CDATA[{$time}]]></BalanceDate>
</GroupingLevel>
<IncludeZeroBalance>Y</IncludeZeroBalance>
<SearchingCriteria>
	<Criteria1>Customer</Criteria1>
	<Value1><![CDATA[{$this->_customerCode}]]></Value1>
	<Criteria2>Warehouse</Criteria2>
	<Value2><![CDATA[{$this->_warehouseCode}]]></Value2>

XML;

		if ($itemNumber) {
			$payload .= <<<XML
	<Criteria3>ItemNumber</Criteria3>
	<Value3><![CDATA[{$itemNumber}]]></Value3>

XML;
		}

		$payload .= <<<XML
</SearchingCriteria>

XML;

		$this->_post('InventoryService.asmx/Inventory', $payload);

		return $this->_lastResponse;
	}

	/**
	 * Generate XML payload to create a new item or update an existing item in CFL
	 *
	 * @param string  $itemNumber
	 * @param array   $itemInfo
	 * @param boolean $isUpdate
	 *
	 * @return string
	 */
	protected function _generateCreateOrUpdateItemPayload(string $itemNumber, array $itemInfo, bool $isUpdate = false): string
	{
		$transactionType = $isUpdate ? 'UPDATE' : 'NEW';

		if (!isset($itemInfo['FreeText1']))
			throw new \Exception("ItemStatus not specified for item $itemNumber");

		$payload = <<<XML
   <ItemMaster>
      <TransactionType><![CDATA[{$transactionType}]]></TransactionType>
      <ItemNumber><![CDATA[{$itemNumber}]]></ItemNumber>
      <RequestSeq>1</RequestSeq>
	<Customer><![CDATA[{$this->_customerCode}]]></Customer>
	<Warehouse><![CDATA[{$this->_warehouseCode}]]></Warehouse>

XML;
		foreach ($itemInfo as $key => $value) {
			$payload .= <<<XML
      <{$key}><![CDATA[{$value}]]></{$key}>

XML;
		}

		$payload .= <<<XML
   </ItemMaster>

XML;
		return $payload;
	}

	/**
	 * Create a new item or update an existing item in CFL
	 *
	 * @param string $itemNumber
	 * @param array  $itemInfo
	 *
	 * @return bool
	 */
	public function createOrUpdateItem(string $itemNumber, array $itemInfo): bool
	{
		// First attempt to create the item
		$payload = $this->_generateCreateOrUpdateItemPayload($itemNumber, $itemInfo, false);
		$status = $this->_post('ItemMasterService.asmx/Manage', $payload);
		if (!$status) {
			// If we get back a respone code telling us that the item already exists
			if ($this->_lastResponseCode == self::REPONSE_CODE_ERROR_ITEM_EXISTS) {
				// Try updating the item
				$payload = $this->_generateCreateOrUpdateItemPayload($itemNumber, $itemInfo, true);
				$status = $this->_post('ItemMasterService.asmx/Manage', $payload);
			}
		}
		// Return success/failure status
		return $status;
	}

	/**
	 * Create a new item or update an existing item in CFL
	 *
	 * @param string $itemNumber
	 *
	 * @return bool
	 */
	public function deleteItem(string $itemNumber): bool
	{
		$payload = <<<XML
   <ItemMaster>
      <RequestSeq>1</RequestSeq>
      <TransactionType>DELETE</TransactionType>
      <Customer><![CDATA[{$this->_customerCode}]]></Customer>
      <ItemNumber><![CDATA[{$itemNumber}]]></ItemNumber>
   </ItemMaster>

XML;

		return $this->_post('ItemMasterService.asmx/Manage', $payload);
	}


	/* ======= Order Functions ======= */


	/**
	 * Create an order with CFL
	 *
	 * @param $payload
	 *
	 * @return array
	 */
	public function createOrder(string $payload): array
	{
		$this->_post('PDOWebService.asmx/PushOrder', $payload);
		return $this->_lastResponse;
	}

	/**
	 * Update an order with CFL
	 *
	 * @param string $payload
	 *
	 * @return array
	 */
	public function updateOrder(string $payload): array
	{
		$this->_post('PDOWebService.asmx/PushOrder', $payload);
		return $this->_lastResponse;
	}

	/**
	 * Cancel an order with CFL
	 *
	 * @param string $payload
	 *
	 * @return array
	 */
	public function cancelOrder(string $payload): array
	{
		$this->_post('PDOWebService.asmx/PushOrder', $payload);
		return $this->_lastResponse;

	}
}
