<?php

namespace CflApi\Resources;

use CflApi\Traits;

class Order extends Resource
{
	use Traits\Retrieve;
	use Traits\RetrieveCollection;
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
		$this->_setPath("order");
	}

	/**
	 * Create a single order in CFL.
	 */
	public function create(array $data): array
	{
		$this->_setPath("order");

		$payload = $this->_generateCreateUpdateDeleteOrderPayload($data);

		return $this->traitCreate($payload);
	}

	/**
	 * Update an order in CFL.
	 *
	 * @param        $orderNumber
	 * @param array  $data
	 *
	 * @return array
	 * @throws \GuzzleHttp\Exception\RequestException
	 */
	public function update(string $orderNumber, array $data): array
	{
		$this->_setPath("order/${orderNumber}");

		$payload = $this->_generateCreateUpdateDeleteOrderPayload($data);

		return $this->traitUpdate($payload);
	}

	/**
	 * Cancel an order in CFL.
	 */
	public function delete(string $orderNumber): array
	{
		$this->_setPath("order/${orderNumber}");

		$payload = $this->_generateCreateUpdateDeleteOrderPayload([]);

		return $this->traitDelete($payload);
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
