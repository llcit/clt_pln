<?php
include_once ('../app/config/api.php');

use phalcon\Mvc\Micro;
$app = new Micro ();
/*
 * $api = new Api();
 * echo "Test start.";
 * $result=$api->getApp("Skype");
 * print_r($result);
 * echo "Test end.";
 */

// Retrieves all apps
$app->get ( '/get/apps', function () use ($app) {
	$api = new Api ();
	$value = $api->getAllApps ();
	
	//echo json_encode ( $value, JSON_UNESCAPED_SLASHES );
	
	// TODO Iteration for format, function, and type
	foreach ( $value as $key => $val ) {
		// App information
		echo json_encode($value[$key], JSON_UNESCAPED_SLASHES);
		foreach ( $val as $k => $v ) {
			if ($k == 'name') {
				// App format
				$result = $api->getAppFormatName ( $v );
				echo json_encode ( $result );
				
				// App function
				$result = $api->getAppFunctionName ( $v );
				echo json_encode ( $result );
				
				// App type
				$result = $api->getAppTypeName ( $v );
				echo json_encode ( $result );
			}
		}
		//echo "<br>";
	}
} );

// Search for $appName
$app->get ( '/get/apps/{name}', function ($name) use ($app) {
	$api = new Api ();
	
	// App information
	$value = $api->getAppByName ( $name );
	echo json_encode ( $value, JSON_UNESCAPED_SLASHES );
	
	// App format
	$value = $api->getAppFormatName ( $name );
	echo json_encode ( $value );
	
	// App function
	$value = $api->getAppFunctionName ( $name );
	echo json_encode ( $value );
	
	// App type
	$value = $api->getAppTypeName ( $name );
	echo json_encode ( $value );
} );

// Search for $appId
$app->get ( '/get/apps/id/{id:[0-9]+}', function ($id) use ($app) {
	$api = new Api ();
	
	// App information
	$value = $api->getAppById ( $id );
	echo json_encode ( $value, JSON_UNESCAPED_SLASHES );
	
	// App name from app id
	$name = $api->getAppName ( $id );
	$name = $name [0] ['name'];
	
	// App format
	$value = $api->getAppFormatName ( $name );
	echo json_encode ( $value );
	
	// App function
	$value = $api->getAppFunctionName ( $name );
	echo json_encode ( $value );
	
	// App type
	$value = $api->getAppTypeName ( $name );
	echo json_encode ( $value );
} );

$app->handle ();