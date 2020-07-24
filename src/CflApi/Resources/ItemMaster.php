<?php

namespace CflApi\Resources;

use CflApi\Traits;

class ItemMaster extends Resource
{
	use Traits\Retrieve;
	use Traits\Create {
		create as traitCreate;
	}
	use Traits\Update {
		update as traitUpdate;
	}
	use Traits\Delete {
		delete as traitDelete;
	}

	/**
	 * Set the path for this resource.
	 *
	 * @return void
	 */
	protected function _construct()
	{
		$this->_setPath("ItemMaster");
	}

	/**
	 * Create a single item in CFL.
	 *
	 * @param array $data
	 *
	 * @return array
	 * @throws \GuzzleHttp\Exception\RequestException
	 */
	public function create(array $data): array
	{
		$payload = $this->_generateCreateUpdateDeleteItemPayload($data);

		return $this->traitCreate($payload);
	}

	/**
	 * Update a single item in CFL.
	 *
	 * @param        $itemNumber
	 * @param array  $data
	 *
	 * @return array
	 * @throws \GuzzleHttp\Exception\RequestException
	 */
	public function update($itemNumber, array $data): array
	{
		$data['ItemNumber'] = $itemNumber;
		$payload            = $this->_generateCreateUpdateDeleteItemPayload($data);

		return $this->traitUpdate($payload);
	}

	/**
	 * Delete a single item in CFL.
	 *
	 * @param $itemNumber
	 *
	 * @return array
	 * @throws \GuzzleHttp\Exception\RequestException
	 */
	public function delete($itemNumber): array
	{
		$data     = [
			'ItemNumber' => $itemNumber,
			'Status'     => true,
		];
		$itemData = $this->_generateCreateUpdateDeleteItemPayload($data);

		return $this->traitDelete($itemData);
	}

	/**
	 * Generate payload to create, update, or delete an item in CFL.
	 *
	 * @param array $data
	 *
	 * @return array
	 */
	protected function _generateCreateUpdateDeleteItemPayload(array $data): array
	{
		return [
			"RequestID" => $this->_generateRequestId(),
			"Customer"  => $this->_customerCode,
			"Item"      => [
				$data,
			],
		];
	}

	protected function _getErrorCodeDescriptions(): array
	{
		return array_merge(parent::_getErrorCodeDescriptions(), []);
	}
}
