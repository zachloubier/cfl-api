<?php

namespace CflApi\Traits;

trait Retrieve
{
	/**
	 * @param $identifier
	 *
	 * @return array
	 */
	public function retrieve($identifier): array
	{
		$response = $this->get("{$this->_path}/{$identifier}");

		return $this->_processResponse($response);
	}
}