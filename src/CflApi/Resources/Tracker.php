<?php

namespace CflApi\Resources;

use CflApi\Traits;

class Tracker extends Resource
{
	use Traits\Retrieve;
	use Traits\RetrieveCollection;
	use Traits\Create;

	protected function _construct()
	{
		$this->_setPath("trackers");
	}

	/**
	 * Not implemented
	 */
	public function update($identifier, array $data): array
	{
		return [];
	}

	/**
	 * Not implemented
	 */
	public function delete($identifier): array
	{
		return [];
	}

	protected function _getErrorCodeDescriptions(): array
	{
		return array_merge(parent::_getErrorCodeDescriptions(), []);
	}
}
