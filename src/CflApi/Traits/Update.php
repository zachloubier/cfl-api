<?php

namespace CflApi\Traits;

trait Update
{
	/**
	 * @param array $payload
	 *
	 * @return array
	 */
	public function update(array $payload): array
	{
		$response = $this->put($this->_path, $payload);

		return $this->_processResponse($response);
	}
}