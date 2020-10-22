<?php

namespace CflApi\Resources;

use CflApi\Traits;

class Inventory extends Resource
{
	use Traits\Retrieve {
		retrieve as traitRetrieve;
	}
	use Traits\RetrieveCollection {
		retrieveCollection as traitRetrieveCollection;
	}
	use Traits\Create {
		create as traitCreate;
	}
	use Traits\Update {
		update as traitUpdate;
	}

	/**
	 * Set the path for this resource.
	 *
	 * @return void
	 */
	protected function _construct()
	{
		$this->_setPath("Inventory");
	}

	/**
	 * Retrieve a single resource in CFL.
	 *
	 * @param $identifier
	 *
	 * @return array
	 * @throws \GuzzleHttp\Exception\RequestException
	 */
	public function retrieve($identifier): array
	{
		$this->_setPath("Inventory");

		return $this->traitRetrieve($identifier);
	}

	/**
	 * Retrieve all resources in CFL.
	 *
	 * @return array
	 * @throws \GuzzleHttp\Exception\RequestException
	 */
	public function retrieveCollection(): array
	{
		$this->_setPath("Inventory");

		return $this->traitRetrieveCollection();
	}

	/**
	 * Not implemented
	 */
	public function create(array $data): array
	{
		return [];
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
		$this->_setPath("Inventory/PreQty/update");

		$data['itemNumber'] = $itemNumber;
		$data['qty']        = $data['qty'];
		$payload            = $this->_generateCreateUpdateDeleteItemPayload($data);

		return $this->traitUpdate($payload);
	}

	/**
	 * Not implemented
	 */
	public function delete($itemNumber): array
	{
		return [];
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
