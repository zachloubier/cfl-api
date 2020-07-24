<?php

namespace CflApi;

use Psr\Http\Message\ResponseInterface;

interface ApiInterface
{
	/**
	 * Make a GET request.
	 *
	 * @param string $uri
	 * @param array  $queryParams
	 *
	 * @return ResponseInterface
	 * @throws \GuzzleHttp\Exception\RequestException
	 */
	public function get(string $uri, array $queryParams = []): ResponseInterface;

	/**
	 * Make a POST request.
	 *
	 * @param string $uri
	 * @param array  $params
	 *
	 * @return ResponseInterface
	 * @throws \GuzzleHttp\Exception\RequestException
	 */
	public function post(string $uri, array $params): ResponseInterface;

	/**
	 * Make a PUT request.
	 *
	 * @param string $uri
	 * @param array  $params
	 *
	 * @return ResponseInterface
	 * @throws \GuzzleHttp\Exception\RequestException
	 */
	public function put(string $uri, array $params): ResponseInterface;

	/**
	 * Make a DELETE request
	 *
	 * @param string $uri
	 * @param array  $params
	 *
	 * @return ResponseInterface
	 * @throws \GuzzleHttp\Exception\RequestException
	 */
	public function del(string $uri, array $params): ResponseInterface;
}