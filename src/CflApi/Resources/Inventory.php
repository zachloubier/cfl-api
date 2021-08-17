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

	const UPDATE_TYPE_INCREMENT = 2;
	const UPDATE_TYPE_REPLACE   = 3;

	/**
	 * Set the path for this resource.
	 *
	 * @return void
	 */
	protected function _construct()
	{
		$this->_setPath("Inventory");

		$this->_setIdentifierKey('itemNumbers');
	}

	/**
	 * Retrieve a single resource in CFL.
	 *
	 * @param string $identifier
	 *
	 * @return array
	 * @throws \GuzzleHttp\Exception\RequestException
	 */
	public function retrieve(string $identifier): array
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
	public function retrieveCollection(array $identifiers = []): array
	{
		$this->_setPath("Inventory");

		return $this->traitRetrieveCollection($identifiers);
	}

	/**
	 *
	 * (Re-)set inventory from single item in CFL.
	 *
	 * @param string $identifier
	 * @param array  $data
	 *
	 * @return array
	 * @throws \GuzzleHttp\Exception\RequestException
	 */
	public function create(array $data): array
	{
		$this->_setPath("Inventory/Prety/update");

		$newData = [];

		$newData['Item'] = [
			'itemNumber' => $data['ItemNumber'],
			'qty'        => $data['qty'],
		];

		$newData['Type'] = 3; // 3 = set/reset existing qty to new number

		$payload = $this->_generateCreateUpdateDeleteItemPayload($newData);

		return $this->traitUpdate($payload);
	}

	/**
	 *
	 * Add or subtract inventory from single item in CFL.
	 *
	 * @param string $identifier
	 * @param array  $data
	 *
	 * @return array
	 * @throws \GuzzleHttp\Exception\RequestException
	 */
	public function update(string $identifier, array $data): array
	{
		$this->_setPath("Inventory/PreQty/update");

		$newData = [];

		$newData['Item'] = [
			'itemNumber' => $identifier,
			'qty'        => $data['qty'],
		];

		$newData['Type'] = 2; // 2 = change existing qty by +X or -X

		$payload = $this->_generateCreateUpdateDeleteItemPayload($newData);

		return $this->traitUpdate($payload);
	}

	/**
	 * Not implemented
	 */
	public function delete(string $identifier): array
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
		$data["RequestID"] = $this->_generateRequestId();
		$data["Customer"]  = $this->_customerCode;
		return $data;
	}

	protected function _getErrorCodeDescriptions(): array
	{
		return array_merge(parent::_getErrorCodeDescriptions(), []);
	}
}
