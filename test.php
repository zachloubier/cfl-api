<?php
use CflApi\Resources\ItemMaster;
use CflApi\Resources\Inventory;
use CflApi\Resources\Tracker;
use CflApi\Resources\Order;
require_once('./vendor/autoload.php');
$token          = 'cJnmuMSniJnWSOoWsSfdljghdvES3zW379grLWFTuQEcuQWKy1';
$baseUrl        = 'https://cfl-lpm.azurewebsites.net/api/';
$customerCode   = 'betabrand';
$trackerToken   = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJuYmYiOiIxNTg1NzEwMDMxIiwiZXhwIjoyMDkwNjMxNjMxLCJodHRwOi8vc2NoZW1hcy54bWxzb2FwLm9yZy93cy8yMDA1LzA1L2lkZW50aXR5L2NsYWltcy9uYW1lIjoiQkVUQSIsImlzcyI6IkNGTCIsImF1ZCI6ImN1c3RvbWVyIn0.R66-RBSK6w7V6u2BpjH7PaerFzM-GQeHCmT7fm5qM0w';
$trackerBaseUrl = 'https://cfldev.eastasia.cloudapp.azure.com:8077/api/';
$itemMaster     = new ItemMaster($token, $baseUrl, $customerCode);
$tracker        = new Tracker($trackerToken, $trackerBaseUrl, $customerCode);
$inventory      = new Inventory($token, $baseUrl, strtolower($customerCode));
$order          = new Order($token, $baseUrl, strtolower($customerCode));

$sku            = "W2000-CFL-L000";
$status         = "RTW";
$description    = "Test product 1 - Medium";
$size           = "Small";
$color          = "Black";
$hsCode         = "BLABLABLA";

$inventoryUpdateData = [
	"Item" => [
		[
	        "itemNumber" => "W2000-CFL-M000",
	        "qty"      => -420,
		],
		[
			"itemNumber" => "W2000-CFL-M999",
	        "qty"      => 420,
		],
		[
			"itemNumber" => "W2000-CFL-M000",
	        "qty"      => -10,
		],
	],
];

$inventoryUpdateData = [
	"Item" => [
		[
			"itemNumber" => $sku,
			"qty"      => 420,
		],
	],
];

$itemData = [
	"ItemNumber"  => $sku,
	"Status"      => $status,
	"HSCode"      => $hsCode,
	"Description" => $description,
	"Size"        => $size,
	"Color"       => $color,
];

$trackingNumber = 'CF200700108678';

$trackerData = [
	'carrier'       => "CFLLogistic",
	'tracking_code' => $trackingNumber,
];

$orderData = [
	'RefOrderId'      => '100'.rand(1000000, 9999999),
	'RefOrderDate'    => date('m/d/y'),
	'CustomerRef'     => rand(1000000000, 9999999999),
	'DeliveryMethod'  => 'Flat Rate - Standard (5-7 Business Days)',
	'Currency'        => 'USD',
	'SubTotal'        => '50',
	'Discount'        => '0',
	'ShippingFee'     => '0',
	'Total'           => '50',
	'PaymentMethod'   => 'cryozonic_stripe',
	'Email'           => 'russell@betabrand.com',
	'Tel'             => '6038013776',
	'Notes'           => 'Testing Order',
	'ShippingAddress' => [
		'Name'        => 'russell preston',
		'FullAddress' => '123 Steet St, San Francisco, CA 94110',
		'Address1'    => '609 E 23rd St, Apt 106',
		'Address2'    => 'San Francisco',
		'Address3'    => 'California',
		'City'        => 'San Francisco',
		'State'       => 'California',
		'CountryCode' => 'US',
		'PostalCode'  => '94110',
	],
	'BillingAddress' => [	
		'Name'        => 'russell preston',
		'FullAddress' => '123 Steet St, San Francisco, CA 94110',
		'Address1'    => '123 Steet St',
		'Address2'    => 'San Francisco',
		'Address3'    => 'California',
		'City'        => 'San Francisco',
		'State'       => 'California',
		'CountryCode' => 'US',
		'PostalCode'  => '94110',
	],
	'Details' => [
		[
			'Sequence'        => rand('1000000', '9999999'),
			'ItemNumber'      => $sku,
			'ItemDescription' => $description,
			'Qty'             => '1',
			'UnitPrice'       => '50',
		],
	],
];



try {
	// Test Case A.1 - Customer create 1 item
	// $itemData['ItemNumber'] = 'W2000-CFL-XS00';
	// print("Response:\n\n" . json_encode($itemMaster->create([$itemData]), JSON_PRETTY_PRINT)); //Item exists - The item is not created.
	// // Test Case A.2 - Customer create mulitple items
	// $itemData['ItemNumber'] = 'W2000-CFL-XS00';
	// print("Response:\n\n" . json_encode($itemMaster->create([$itemData]), JSON_PRETTY_PRINT)); //Item exists - The item is not created.
	// $itemDataCollection = array_fill(0,5,$itemData);
	// $itemDataCollection[0]['ItemNumber'] = 'W2000-CFL-S000';
	// $itemDataCollection[1]['ItemNumber'] = 'W2000-CFL-XL00';
	// $itemDataCollection[2]['ItemNumber'] = 'W2000-CFL-XXL0';
	// $itemDataCollection[3]['ItemNumber'] = 'W2000-CFL-L000';
	// $itemDataCollection[4]['ItemNumber'] = 'W2000-CFL-M000';
	// print("Response:\n\n" . json_encode($itemMaster->create($itemDataCollection), JSON_PRETTY_PRINT)); //Item does not exist - The item is created.
	// // Test Case A.4 - Customer create 1 item with an existing item number
	// $itemData['ItemNumber'] = 'W2000-CFL-XS00';
	// print("Response:\n\n" . json_encode($itemMaster->create([$itemData]), JSON_PRETTY_PRINT)); //Reject request.
	// // Test Case A.5 - Customer update multiple items (update HTS code)
	// $itemDataCollection = array_fill(0,2,$itemData);
	// $itemDataCollection[0]['ItemNumber'] = 'W2000-CFL-XS00';
	// $itemDataCollection[1]['ItemNumber'] = 'W9999-CFL-M999';
	// print("Response:\n\n" . json_encode($itemMaster->create($itemDataCollection), JSON_PRETTY_PRINT)); //Item exists - The item is updated.
	// // Test Case A.6 - Customer delete multiple items
	// $deleteItems = [
	// 	[ 'ItemNumber' => 'W2000-CFL-XXL0', 'Status' => true ],
	// 	[ 'ItemNumber' => 'W2000-CFL-M999', 'Status' => true ],
	// ];
	// print("Response:\n\n" . json_encode($itemMaster->delete($deleteItems), JSON_PRETTY_PRINT)); //Item exist, item has no stock, item has no pending order - The item is marked deleted.
	// //Stock in item for subsequent test
	// print("Response:\n\n" . json_encode($inventory->create([
	// 	"Item" => [
	// 			[
	// 				"itemNumber" => "W2000-CFL-M000",
	// 				"qty"      => 2,
	// 			],
	// 		],
	// 	]), JSON_PRETTY_PRINT));
	// Item has stock W2000-CFL-L000 (stock in first)
	// $deleteItems = [
	// 	[ 'ItemNumber' => 'W2000-CFL-L000', 'Status' => true ],
	// ];
	// print("Response:\n\n" . json_encode($itemMaster->delete($deleteItems), JSON_PRETTY_PRINT)); //Item has stock - Reject request
	//Push order for subsequent test
	// $orderData['Details'][0]['ItemNumber'] = 'W2000-CFL-M000';
	// print("Response:\n\n" . json_encode($order->create($orderData), JSON_PRETTY_PRINT));
	// Item has pending order W2000-CFL-M000 (push order first)
	// $deleteItems = [
	// 	[ 'ItemNumber' => 'W2000-CFL-M000', 'Status' => true ],
	// ];
	// print("Response:\n\n" . json_encode($itemMaster->delete($deleteItems), JSON_PRETTY_PRINT)); //Item has pending order - Reject request
	// // Test Case A.7 - Customer enquire multiple items
	// print("Response:\n\n" . json_encode($itemMaster->retrieve('W2000-CFL-XS00'), JSON_PRETTY_PRINT)); //Item exists - Return info of the item.
	// print("Response:\n\n" . json_encode($itemMaster->retrieve('W2000-CFL-M999'), JSON_PRETTY_PRINT)); //Item does not exist - Reject request.
	// // Test Case A.8 - Customer enquire 1 item
	// print("Response:\n\n" . json_encode($itemMaster->retrieve('W2000-CFL-XS00'), JSON_PRETTY_PRINT)); //Return info of the item.
	// // Test Case A.8.1 - Customer enquire 1 item which does not exist
	// print("Response:\n\n" . json_encode($itemMaster->retrieve('W2000-CFL-M999'), JSON_PRETTY_PRINT)); //Item does not exist -Reject request.
	// Test Case A.9 - Customer update 1 item (update Description)
	// print("Response:\n\n" . json_encode($itemMaster->update('W2000-CFL-XS00', [ 'Description' => 'Newly updated description 2.0', 'Status' => true ]), JSON_PRETTY_PRINT)); //The item is updated.
	// // Test Case A.10.1 - Customer delete 1 item
	// $deleteItems = [
	// 	[ 'ItemNumber' => 'W2000-CFL-XL00', 'Status' => true ],
	// ];
	// print("Response:\n\n" . json_encode($itemMaster->delete($deleteItems), JSON_PRETTY_PRINT)); //The item is marked delete.
	// // Test Case A.10.2 - Customer push to create an item deleted in 10.1
	// $itemData['ItemNumber'] = 'W2000-CFL-XL00';
	// print("Response:\n\n" . json_encode($itemMaster->create($itemData), JSON_PRETTY_PRINT)); //The item is created.
	// // Test Case A.11 - Customer delete an item that has stock
	// $deleteItems = [
	// 	[ 'ItemNumber' => 'W2000-CFL-L000', 'Status' => true ],
	// ];
	// print("Response:\n\n" . json_encode($itemMaster->delete($deleteItems), JSON_PRETTY_PRINT)); //Reject request.
	// // Test Case A.12 - Customer delete an item that has pending orders
	// $deleteItems = [
	// 	[ 'ItemNumber' => 'W2000-CFL-M000', 'Status' => true ],
	// ];
	// print("Response:\n\n" . json_encode($itemMaster->delete($deleteItems), JSON_PRETTY_PRINT)); //Reject request.
	// // Test Case A.13 - Customer update an item that belongs to another customer
	// $token          = 'cJnmuMSniJnWSOoWsSfdljghdvES3zW379grLWFTuQEcuQWKy1';
	// $baseUrl        = 'https://cfl-lpm.azurewebsites.net/api/';
	// $itemMaster     = new ItemMaster($token, $baseUrl, 'CUSTOMERA');
	// print("Response:\n\n" . json_encode($itemMaster->update('W2000-CFL-M888', [ 'Status' => true ]), JSON_PRETTY_PRINT)); //Reject request.
	// // Test Case A.14 - Volume test: Customer push 100 items to CFL in a single request. Record time spent in test environment.
	// for ($i=1;$i<=100;$i++) {
	// 	print("Response:\n\n" . json_encode($itemMaster->create('W2100-CFL-XS00 - W2199-CFL-XS00'), JSON_PRETTY_PRINT)); //100 items should be created in CFL.
	// }

	// // Test Case B.1 - Customer enquire inventory
	// print("Response:\n\n" . json_encode($inventory->retrieveCollection(), JSON_PRETTY_PRINT));; // Inventory of all items, whether have stock or not, are returned.
	// // Test Case B.2 - Customer enquire inventory of 1 item
	// print("Response:\n\n" . json_encode($inventory->retrieve('W2000-CFL-L000'), JSON_PRETTY_PRINT));; // Inventory of the specified item is returned.
	// Test Case B.3 - Customer update the Available for Sell qty of multiple items
	// $inventory->create([
	// 	"Item" => [
	// 			[
	// 				"itemNumber" => "W2000-CFL-M000",
	// 				"qty"      => 2,
	// 			],
	// 			[
	// 				"itemNumber" => "W2000-CFL-M999",
	// 				"qty"      => 2,
	// 			],
	// 			[
	// 				"itemNumber" => "W2000-CFL-C011",
	// 				"qty"      => -10,
	// 			],
	// 		]
	// ]);
	// // Test Case B.4 - Customer use Item Master API to update the status of an item from RTW to PO. Then use Inventory API to update Available for sell qty to 10000.
	// print("Response:\n\n" . json_encode($inventory->create('W2000-CFL-XS0P'), JSON_PRETTY_PRINT)); // Available for sell qty is updated.
	

	// // Test case C.1:
	// $orderData['Details'][0]['ItemNumber'] = 'W2000-CFL-L000';
	// print("Response:\n\n" . json_encode($order->create($orderData), JSON_PRETTY_PRINT));

	// // Test case C.2 - Push a new order with multipe items, each item with different settings:
	// print("Response:\n\n" . json_encode($inventory->create([
	// 	"Item" => [
	// 		[
	// 			"itemNumber" => "W2000-CFL-L000",
	// 			"qty"      => 1000,
	// 		],
	// 		[
	// 			"itemNumber" => "W2000-CFL-M000",
	// 			"qty"      => 2,
	// 		],
	// 		[
	// 			"itemNumber" => "W2000-CFL-S000",
	// 			"qty"      => 2,
	// 		],
	// 	],
	// ]), JSON_PRETTY_PRINT));;
	// print("Response:\n\n" . json_encode($inventory->retrieve("W2000-CFL-L000"), JSON_PRETTY_PRINT));
	// print("Response:\n\n" . json_encode($inventory->retrieveCollection(), JSON_PRETTY_PRINT));
	// print("Response:\n\n" . json_encode($inventory->retrieve("W2000-CFL-M000"), JSON_PRETTY_PRINT));
	// print("Response:\n\n" . json_encode($inventory->retrieve("W2000-CFL-S000"), JSON_PRETTY_PRINT));
	// $orderData['Details'] = array_fill(0,5,$orderData['Details'][0]);
	// $orderData['Details'][0]['ItemNumber'] = 'W2000-CFL-XYZ1'; // Item does not exist in item master
	// $orderData['Details'][1]['ItemNumber'] = 'W2000-CFL-XL00'; // Item is marked deleted
	// $orderData['Details'][2]['ItemNumber'] = 'W2000-CFL-L000'; // Stock qty>=Order qty
	// $orderData['Details'][3]['Qty'] = 1;
	// $orderData['Details'][3]['ItemNumber'] = 'W2000-CFL-M000'; // Stock qty<Order qty and Available for sell<Order qty
	// $orderData['Details'][3]['Qty'] = 10;
	// $orderData['Details'][4]['ItemNumber'] = 'W2000-CFL-S000'; // Stock qty<Order qty and Available for sell>=Order qty
	// $orderData['Details'][4]['Qty'] = 10;
	// print("Response:\n\n" . json_encode($order->create($orderData), JSON_PRETTY_PRINT));

	// Test case C.5 - Push order update, only the order lines that are changed are pushed. (it is not pushed to 3PL)
	// Need to first make sure order exists with 2 items in it
	// $orderData['Details'][0]['ItemNumber'] = 'W2000-CFL-M000';
	// $orderData['Details'][0]['Qty'] = 2;
	// print("Response:\n\n" . json_encode($order->create($orderData), JSON_PRETTY_PRINT));
	// // Now run the test scenario with 3 updates
	// $orderData['Details'] = array_fill(0,3,$orderData['Details'][0]);
	// $orderData['Details'][0]['Qty'] = 1; // Order qty (2 -> 1)
	// $orderData['Details'][1]['Qty'] = 0; // Order qty (2 -> 0)
	// $orderData['Details'][2]['Qty'] = 3; // Order qty (0 -> 3)
	// print("Response:\n\n" . json_encode($order->update($orderData['RefOrderId'], $orderData), JSON_PRETTY_PRINT));
	// Test case C.6 - With the above order. Push an update to update the qty of newly added order line from 3 to 1.
	// The qty of the specified order line is updated.
	// $orderData['RefOrderId'] = 1003857232;
	// $orderData['Details'][0]['ItemNumber'] = 'W2000-CFL-M000';
	// $orderData['Details'][0]['Qty'] = 1;
	// print("Response:\n\n" . json_encode($order->update($orderData['RefOrderId'], $orderData), JSON_PRETTY_PRINT));

	// Test case C.8 - Push a new order with 2 items. <---- need more work after CFL does some stuff
	// $orderData['RefOrderId'] = 10033172780;
	// $orderData['Details'] = array_fill(0,2,$orderData['Details'][0]);
	// $orderData['Details'][0]['ItemNumber'] = 'W2000-CFL-M000'; // Item does not exist in item master
	// $orderData['Details'][1]['ItemNumber'] = 'W2000-CFL-S000'; // Item is marked deleted
	// print("Response:\n\n" . json_encode($order->create($orderData), JSON_PRETTY_PRINT));
	// Test case C.8 part 2
	// $orderData['RefOrderId'] = 1003317264;
	// $orderData['Details'] = array_fill(0,2,$orderData['Details'][0]);
	// $orderData['Details'][0]['ItemNumber'] = 'W2000-CFL-M000'; // Item does not exist in item master
	// $orderData['Details'][1]['ItemNumber'] = 'W2000-CFL-S000'; // Item is marked deleted
	// $orderData['Details'][0]['Qty'] = 3;
	// $orderData['Details'][1]['Qty'] = 3;
	// print("Response:\n\n" . json_encode($order->update($orderData['RefOrderId'], $orderData), JSON_PRETTY_PRINT));

	// Test case C.9 - Push a new order with 2 items. <---- need more work after CFL does some stuff
	// $orderData['RefOrderId'] = 1003317265;
	// $orderData['Details'] = array_fill(0,2,$orderData['Details'][0]);
	// $orderData['Details'][0]['ItemNumber'] = 'W2000-CFL-M000'; // Item does not exist in item master
	// $orderData['Details'][1]['ItemNumber'] = 'W2000-CFL-S000'; // Item is marked deleted
	// print("Response:\n\n" . json_encode($order->create($orderData), JSON_PRETTY_PRINT));
	// Test case C.9 part 2
	// $orderData['RefOrderId'] = 1003317265;
	// $orderData['Details'] = array_fill(0,2,$orderData['Details'][0]);
	// $orderData['Details'][0]['ItemNumber'] = 'W2000-CFL-M000'; // Item does not exist in item master
	// $orderData['Details'][1]['ItemNumber'] = 'W2000-CFL-S000'; // Item is marked deleted
	// $orderData['Details'][0]['Qty'] = 3;
	// $orderData['Details'][1]['Qty'] = 3;
	// print("Response:\n\n" . json_encode($order->update($orderData['RefOrderId'], $orderData), JSON_PRETTY_PRINT));

	// Test case C.10 - Push order update to update a non-existing order.
	// $orderData['RefOrderId'] = 999696969420;
	// print("Response:\n\n" . json_encode($order->update($orderData['RefOrderId'], $orderData), JSON_PRETTY_PRINT));

	// Test case C.11 - Push a new order but that order no. already exist in D2C platform.
	// $orderData['RefOrderId'] = 1003317265;
	// print("Response:\n\n" . json_encode($order->create($orderData), JSON_PRETTY_PRINT));

} catch (\GuzzleHttp\Exception\RequestException $e) {
	echo "Request Exception:\n\n";
	echo "Status: " . $e->getResponse()->getStatusCode() . "\n";
	$json = @json_decode($e->getResponse()->getBody()->getContents());
	echo ( $json ? json_encode($json, JSON_PRETTY_PRINT) : $e->getResponse()->getBody()->getContents() ) . "\n";
} catch (Exception $e) {
	echo "General Exception:\n\n";
	echo $e->getMessage() . "\n";
}
