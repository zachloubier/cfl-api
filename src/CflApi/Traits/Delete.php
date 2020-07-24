<?php

namespace CflApi\Traits;

trait Delete
{
	/**
	 * @param array $payload
	 *
	 * @return array
	 */
	public function delete(array $payload): array
	{
		$response = $this->del($this->_path, $payload);

		return $this->_processResponse($response);
	}
}