<?php

namespace CflApi\Traits;

trait Create
{
	/**
	 * @param array $payload
	 *
	 * @return array
	 */
	public function create(array $payload): array
	{
		$response = $this->post($this->_path, $payload);

		return $this->_processResponse($response);
	}
}