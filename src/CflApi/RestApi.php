<?php

namespace CflApi;

use CflApi\ApiInterface;
use Exception;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

abstract class RestApi implements ApiInterface
{
	const TIMEOUT = 15;
	const RETRY_LIMIT = 4;

	protected $_retries = 0;
	protected $_shouldRetry = true;

	protected $_client;
	protected $_path;

	protected $_token;
	protected $_baseUrl;
	protected $_customerCode;

	/**
	 * Set authorization token and base url (for dev vs production).
	 *
	 * @param string $token
	 * @param string $baseUrl
	 * @param string $customerCode
	 */
	public function __construct(string $token, string $baseUrl, string $customerCode, bool $shouldRetry = true)
	{
		$this->_token        = $token;
		$this->_baseUrl      = $baseUrl;
		$this->_customerCode = $customerCode;
		$this->_shouldRetry  = $shouldRetry;

		$this->_construct();
	}

	/**
	 * Set _shouldRetry flag.
	 *
	 * @param bool $flag
	 *
	 * @return void
	 */
	public function shouldRetry(bool $flag)
	{
		$this->_shouldRetry = $flag;
	}

	/**
	 * Make a GET request.
	 *
	 * @param string $uri
	 * @param array  $queryParams
	 *
	 * @return ResponseInterface
	 *
	 * @throws \GuzzleHttp\Exception\RequestException
	 * @throws Exception
	 */
	public function get(string $uri, array $queryParams = []): ResponseInterface
	{
		$client = $this->_getClient();

		if ($this->_shouldRetry) {
			return $this->_retryRequest([$client, 'get'], [$uri, ['query' => $queryParams]]);
		} else {
			return $client->get($uri, [
				'query' => $queryParams,
			]);
		}
	}

	/**
	 * Make a POST request.
	 *
	 * @param string $uri
	 * @param array  $params
	 *
	 * @return ResponseInterface
	 *
	 * @throws \GuzzleHttp\Exception\RequestException
	 * @throws Exception
	 */
	public function post(string $uri, array $params): ResponseInterface
	{
		$client = $this->_getClient();

		if ($this->_shouldRetry) {
			return $this->_retryRequest([$client, 'post'], [$uri, ['json' => $params]]);
		} else {
			return $client->post($uri, [
				'json' => $params,
			]);
		}
	}

	/**
	 * Make a PUT request.
	 *
	 * @param string $uri
	 * @param array  $params
	 *
	 * @return ResponseInterface
	 *
	 * @throws \GuzzleHttp\Exception\RequestException
	 * @throws Exception
	 */
	public function put(string $uri, array $params): ResponseInterface
	{
		$client = $this->_getClient();

		if ($this->_shouldRetry) {
			return $this->_retryRequest([$client, 'put'], [$uri, ['json' => $params]]);
		} else {
			return $client->put($uri, [
				'json' => $params,
			]);
		}
	}

	/**
	 * Make a DELETE request
	 *
	 * @param string $uri
	 * @param array  $params
	 *
	 * @return ResponseInterface
	 *
	 * @throws \GuzzleHttp\Exception\RequestException
	 * @throws Exception
	 */
	public function del(string $uri, array $params): ResponseInterface
	{
		$client = $this->_getClient();

		if ($this->_shouldRetry) {
			return $this->_retryRequest([$client, 'delete'], [$uri, ['json' => $params]]);
		} else {
			return $client->delete($uri, [
				'json' => $params,
			]);
		}
	}


	/**
	 * Create a GuzzleHttp Client
	 *
	 * @return Client
	 */
	protected function _getClient(): Client
	{
		if (is_null($this->_client)) {
			$config = [
				'base_uri' => $this->_baseUrl,
				'timeout'  => self::TIMEOUT,
				'headers'  => [
					'Authorization' => "Bearer {$this->_token}",
					'Content-Type'  => 'application/json',
				],
			];

			$this->_client = new Client($config);
		}

		return $this->_client;
	}

	/**
	 * Retry request on a connection exception error.
	 *
	 * @param  mixed $callable
	 * @param  mixed $params
	 * @return ResponseInterface
	 */
	protected function _retryRequest($callable, $params): ResponseInterface
	{
		while ($this->_retries <= self::RETRY_LIMIT) {
			try {
				return call_user_func_array($callable, $params);
			} catch (\GuzzleHttp\Exception\ConnectException $e) {
				$this->_retries++;

				if ($this->_retries > self::RETRY_LIMIT) {
					$this->_retries = 0;

					throw new Exception("Retry limit reached. Request method: " . __CLASS__ . '::' . debug_backtrace()[1]['function'] . "(). URI: {$params[0]} Params: " . json_encode($params[1]));
				}
			}
		}
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
		return (bool)('S' == substr($responseCode, 0, 1));
	}

	/**
	 * Return description of error code
	 *
	 * @param string $errorCode
	 *
	 * @return string
	 */
	protected function _getErrorCodeDescription(string $errorCode): string
	{
		if (array_key_exists($errorCode, $this->_getErrorCodeDescriptions())) {
			return $this->_getErrorCodeDescriptions()[ $errorCode ];
		} else {
			return "Unknown error code: $errorCode";
		}
	}

	/**
	 * Process the response and return an array
	 *
	 * @param ResponseInterface $response
	 *
	 * @return array
	 */
	protected function _processResponse(ResponseInterface $response): array
	{
		$data = [];

		if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
			$data = json_decode($response->getBody()->getContents(), true);
		}

		return $data;
	}

	protected function _generateRequestId()
	{
		$date = date('Ymd');

		try {
			$rand = random_int(0, 100000000);
		} catch (\Exception $e) {
			$rand = rand(0, 100000000);
		}

		return "CFL_{$this->_customerCode}_{$date}_{$rand}";
	}

	protected function _setPath(string $path)
	{
		$this->_path = $path;
	}

	/**
	 * Must call $this->_setPath($path).
	 *
	 * @return mixed
	 */
	abstract protected function _construct();

	/**
	 * Define response code descriptions.
	 */
	abstract protected function _getErrorCodeDescriptions(): array;
}
