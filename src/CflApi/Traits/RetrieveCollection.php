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
			$uri = "{$this->_path}?";

			foreach ($identifiers as $identifier) {
				$uri .= "{$this->getIdentifierKey()}={$identifier}&";
			}

			rtrim($uri, "&");

			$response = $this->get($uri);
		} else {
			$response = $this->get("{$this->_path}");
		}

		return $this->_processResponse($response);
	}
}