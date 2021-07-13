<?php

namespace CflApi\Traits;

trait RetrieveCollection
{
	/**
	 * @return array
	 */
	public function retrieveCollection(array $identifiers = []): array
	{
		if (count($identifiers)) {
			$params = [
				$this->getIdentifierKey() => implode(',', $identifiers),
			];

			$response = $this->get($this->_path, $params);
		} else {
			$response = $this->get($this->_path);
		}

		return $this->_processResponse($response);
	}
}