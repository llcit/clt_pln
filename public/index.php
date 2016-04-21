<?php
include_once ('../app/config/api.php');

use phalcon\Mvc\Micro;
$app = new Micro ();

// dummy index

$app->get('/',function () use($app) {
	echo "<h3>Personal Learning Network Development Sites.<h3>";
});

// Retrieves all apps
$app->get ( '/get/apps', function () use ($app) {
	$api = new Api ();
	$value = $api->getAllApps ();
	
	// TODO Iteration for format, function, and type
	foreach ( $value as $key => $val ) {
		// App information
		$result["app"] = $value[$key];
		
		foreach ( $val as $k => $v ) {
			if ($k == 'name') {
				// App format
				$result["format"] = $api->getAppFormatName ( $v );
						
				// App function
				$result["function"] = $api->getAppFunctionName ( $v );
						
				// App type
				$result["type"] = $api->getAppTypeName ( $v );
			}
		}
		echo json_encode($result, JSON_UNESCAPED_SLASHES);
		//echo "<br>";
	}
} );

// Search for $appName
$app->get ( '/get/apps/{name}', function ($name) use ($app) {
	$api = new Api ();
	
	// App information
	$value = $api->getAppByName ( $name );
	$result["app"] = $value;
	
	// App format
	$result["format"] = $api->getAppFormatName ( $name );
	
	// App function
	$result["function"] = $api->getAppFunctionName ( $name );
	
	// App type
	$result["type"] = $api->getAppTypeName ( $name );	

	echo json_encode ( $result, JSON_UNESCAPED_SLASHES );
} );

// Search for $appId
$app->get ( '/get/apps/id/{id:[0-9]+}', function ($id) use ($app) {
	$api = new Api ();
	
	// App information
	$value = $api->getAppById ( $id );
	
	// new array for incloding all
	$result["app"] = $value;
	// App name from app id
	$name = $api->getAppName ( $id );
	$name = $name [0] ['name'];
	
	// App format
	$result["format"] = $api->getAppFormatName ( $name );
	
	// App function
	$result["function"] = $api->getAppFunctionName ( $name );
	
	// App type
	$result["type"] = $api->getAppTypeName ( $name );	

	echo json_encode ( $result, JSON_UNESCAPED_SLASHES );
} );

// TODO login URI

$app->get('/login/user/{name}', function () use ($app) {
	
	
});

$app->handle ();