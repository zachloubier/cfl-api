<?php

namespace CflApi\Resources;

use CflApi\RestApi;

abstract class Resource extends RestApi
{
	/**
	 * Retrieve a single resource in CFL.
	 *
	 * @param $identifier
	 *
	 * @return array
	 * @throws \GuzzleHttp\Exception\RequestException
	 */
	abstract public function retrieve($identifier): array;

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
	 * @param $identifier
	 * @param array  $data
	 *
	 * @return array
	 * @throws \GuzzleHttp\Exception\RequestException
	 */
	abstract public function update($identifier, array $data): array;

	/**
	 * Delete a single resource in CFL.
	 *
	 * @param $identifier
	 *
	 * @return array
	 * @throws \GuzzleHttp\Exception\RequestException
	 */
	abstract public function delete($identifier): array;
}
