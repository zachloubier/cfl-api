<?php

namespace CflApi\Resources;

use CflApi\Traits;

class Order extends Resource
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
		$this->_setPath("order");
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
		return $this->traitRetrieveCollection();
	}

	/**
	 * Update a single order in CFL.
	 */
	public function create(array $data): array
	{
		$payload = $this->_generateCreateUpdateDeleteOrderPayload($data);

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
	public function update($orderNumber, array $data): array
	{
		$this->_setPath("order/${orderNumber}");

		$payload = $this->_generateCreateUpdateDeleteOrderPayload($data);

		return $this->traitUpdate($payload);
	}

	/**
	 * Cancel an order in CFL.
	 */
	public function delete($orderNumber): array
	{
		$this->_setPath("order/${orderNumber}");
		return $this->traitDelete([]);
	}

	/**
	 * Generate payload to create, update, or delete an item in CFL.
	 *
	 * @param array $data
	 *
	 * @return array
	 */
	protected function _generateCreateUpdateDeleteOrderPayload(array $data): array
	{
		$data["RequestID"] = $this->_generateRequestId();
		$data["Customer"] = $this->_customerCode;

		return $data;
	}

	protected function _getErrorCodeDescriptions(): array
	{
		return [];
	}
}
