<?php

namespace CflApi;

class Tracker
{
	protected $_baseUrl;
	protected $_token;
	protected $_connection;

	/**
	 * Set some required properties
	 *
	 * @param string $token
	 * @param string $baseUrl
	 */
	public function __construct(string $token, string $baseUrl)
	{
		$this->_token   = $token;
		$this->_baseUrl = $baseUrl;
	}

	/**
	 * Create a tracker with CFL.
	 *
	 * @param string $trackingNumber
	 *
	 * @return array
	 * @throws \Exception
	 */
	public function createTracker(string $trackingNumber): array
	{
		$params = [
			'carrier'       => 'CFLLogistic',
			'tracking_code' => $trackingNumber,
		];

		return $this->_post('api/trackers', $params);
	}


	/* ======= Protected Methods ======= */


	/**
	 * Make a CURL request
	 *
	 * @param string $endpoint
	 * @param string $method
	 * @param array  $params
	 *
	 * @return array
	 * @throws \Exception
	 */
	protected function _request(string $endpoint, string $method, array $params = []): array
	{
		$this->_connection = curl_init();

		curl_setopt($this->_connection, CURLOPT_HTTPHEADER, [
			"Authorization: Bearer {$this->_token}",
			'Accept: application/json',
			'Content-Type: application/json'
		]);

		curl_setopt($this->_connection, CURLOPT_RETURNTRANSFER, 1);

		switch (strtolower($method)) {
			case 'post':
				curl_setopt($this->_connection, CURLOPT_POST, true);
				curl_setopt($this->_connection, CURLOPT_POSTFIELDS, json_encode($params));
				break;
			case 'put':
				curl_setopt($this->_connection, CURLOPT_CUSTOMREQUEST, 'PUT');
				curl_setopt($this->_connection, CURLOPT_POSTFIELDS, json_encode($params));
				break;
			case 'delete':
				curl_setopt($this->_connection, CURLOPT_CUSTOMREQUEST, 'DELETE');
				break;
			case 'get':
			default:
				// GET is the default request method, so we add the payload params onto the query string
				if (!is_null($params)) {
					$endpoint .= '?' . http_build_query($params);
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
	 * @throws \Exception
	 */
	protected function _processResponse(string $response): array
	{
		$body     = json_decode($response, true);
		$httpCode = curl_getinfo($this->_connection, CURLINFO_HTTP_CODE);

		// If a 5xx error was returned or the response is empty, throw an exception
		if ($httpCode >= 500 || empty($response)) {
			$error = curl_error($this->_connection);
		}

		if ($httpCode >= 400 || isset($body['errors'])) {
			$error = $body['message'];
		}

		if (isset($error)) {
			throw new \Exception($error);
		}

		return $body;
	}


	/* ======= HTTP Methods ======= */


	/**
	 * Make a POST request
	 *
	 * @param string $endpoint
	 * @param array  $params
	 *
	 * @return array
	 * @throws \Exception
	 */
	protected function _post(string $endpoint, array $params): array
	{
		$data = $this->_request($endpoint, 'post', $params);

		curl_close($this->_connection);

		return $data;
	}
}
