<?php

namespace CflApi\Resources;

use CflApi\RestApi;

abstract class Resource extends RestApi
{
	protected $_identifierKey;

	public function getIdentifierKey(): string
	{
		return $this->_identifierKey;
	}

	protected function _setIdentifierKey(string $identifierKey)
	{
		$this->_identifierKey = $identifierKey;
	}

	/**
	 * Retrieve a single resource in CFL.
	 *
	 * @param string $identifier
	 *
	 * @return array
	 * @throws \GuzzleHttp\Exception\RequestException
	 */
	abstract public function retrieve(string $identifier): array;

	/**
	 * Retrieve all resources in CFL.
	 *
	 * @return array
	 * @throws \GuzzleHttp\Exception\RequestException
	 */
	abstract public function retrieveCollection(): array;

	/**
	 * Create a single resource in CFL.
	 *
	 * @param array $data
	 *
	 * @return array
	 * @throws \GuzzleHttp\Exception\RequestException
	 */
	abstract public function create(array $data): array;

	/**
	 * Update a single resource in CFL.
	 *
	 * @param string $identifier
	 * @param array  $data
	 *
	 * @return array
	 * @throws \GuzzleHttp\Exception\RequestException
	 */
	abstract public function update(string $identifier, array $data): array;

	/**
	 * Delete a single resource in CFL.
	 *
	 * @param string $identifier
	 *
	 * @return array
	 * @throws \GuzzleHttp\Exception\RequestException
	 */
	abstract public function delete(string $identifier): array;
}
