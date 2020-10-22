<?php

namespace CflApi\Traits;

trait RetrieveCollection
{
	/**
	 * @return array
	 */
	public function retrieveCollection(): array
	{
		$response = $this->get("{$this->_path}");

		return $this->_processResponse($response);
	}
}