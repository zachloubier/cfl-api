<?php

namespace CflApi;

class Api
{
	const REQUEST_ID_PREFIX = 'SO';

	protected $_connection;

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
	 * Build the CURL request
	 *
	 * @param null $payload
	 *
	 * @return CflApi
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
	protected function _request(string $endpoint, string $method, $payload = null): array
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
	 * @return array
	 */
	protected function _processResponse(string $response): array
	{
		$httpCode = curl_getinfo($this->_connection, CURLINFO_HTTP_CODE);

		// If a 5xx error was returned or the response is empty, create a fake response
		if ($httpCode >= 500 || empty($response)) {
			$curlError = curl_error($this->_connection);
			$response  = <<<XML
<Response>
	<ResponseStatus>FAIL</ResponseStatus>
	<ResponseMessage>{$curlError}</ResponseMessage>
	<PreStockOuts>
		<PreStockOut>
			<RequestSeq>1</RequestSeq>
			<ProcessStatus>FAIL</ProcessStatus>
			<ProcessMessage>{$curlError}</ProcessMessage>
		</PreStockOut>
	</PreStockOuts>
</Response>
XML;
		}

		// Load string into XML, json encode, then decode as an array
		$response = simplexml_load_string($response, 'SimpleXMLElement', LIBXML_NOCDATA);
		$response = json_decode(json_encode($response), true);

		$response['HttpCode'] = $httpCode;

		return $response;
	}


	/* ======= HTTP Methods ======= */


	/**
	 * Make a POST request
	 *
	 * @param string $endpoint
	 * @param string $payload
	 *
	 * @return array
	 */
	protected function _post(string $endpoint, string $payload): array
	{
		$data = $this->_request($endpoint, 'post', $payload);

		curl_close($this->_connection);

		return $data;
	}


	/* ======= Product Functions ======= */


	/**
	 * Get all products from CFL
	 *
	 * @return array
	 */
	public function getProducts(): array
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
</SearchingCriteria>
XML;

		return $this->_post('InventoryService.asmx/Inventory', $payload);
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
		return $this->_post('PDOWebService.asmx/PushOrder', $payload);
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
		return $this->_post('PDOWebService.asmx/PushOrder', $payload);
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
		return $this->_post('PDOWebService.asmx/PushOrder', $payload);
	}
}