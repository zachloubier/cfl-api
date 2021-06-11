<?php

namespace CflApi\Traits;

trait Retrieve
{
	/**
	 * @param string $identifier
	 *
	 * @return array
	 */
	public function retrieve(string $identifier): array
	{
		$response = $this->get("{$this->_path}/{$identifier}");

		return $this->_processResponse($response);
	}
}