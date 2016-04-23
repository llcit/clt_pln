<?php
// base controller
include_once ('../app/config/api.php');

// header for json format
header('Content-type: application/json; charset=utf-8');

use phalcon\Mvc\Micro;
$app = new Micro ();

// dummy index
$app->get('/',function () use($app) {
	echo "<h3>Personal Learning Network Development Sites.<h3>";
});

// Retrieves all apps
$app->get ( '/get/apps', function () use ($app) {
	$api = new Api ();
	$result = $api->getAllApps ();
	echo json_encode( $result, JSON_UNESCAPED_SLASHES);
} );

// Search for $appName
$app->get ( '/get/apps/{name}', function ($name) use ($app) {
	$api = new Api ();
	$result = $api->getAppByName ( $name );
	echo json_encode ( $result, JSON_UNESCAPED_SLASHES );
} );

// Search for $appId
$app->get ( '/get/apps/id/{id:[0-9]+}', function ($id) use ($app) {
	$api = new Api ();
	$result = $api->getAppById ( $id );
	echo json_encode ( $result, JSON_UNESCAPED_SLASHES );
} );

// TODO login URI

$app->get('/login/user/{name}', function () use ($app) {
	
	
});

$app->handle ();