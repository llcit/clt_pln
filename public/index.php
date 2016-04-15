<?php
include_once ('../app/config/api.php');

use phalcon\Mvc\Micro;
$app = new Micro();
/*
$api = new Api();
echo "Test start.";
$result=$api->getApp("Skype");
print_r($result);
echo "Test end.";
*/

// Route start here

// Retrieves all robots
$app->get('/apps', function() use ($app) {
	$api = new Api();
	$value = $api->getAllApps();
	echo json_encode($value);
});

// Search for $appName
$app->get('/apps/name={name}', function ($name) use ($app) {
	$api = new Api();
	$value = $api->getAppByName($name);
	echo json_encode($value);	
});

// Search for $appName
$app->get('/apps/id={id:[0-9]+}', function ($id) use ($app) {
	$api = new Api();
	$value = $api->getAppById($id);
	echo json_encode($value);
});
	

// Searches for robots with $appId in their app id
$app->get('/apps/{id:[0-9]+}', function ($id) {
	$api = new Api();
	
});

$app->handle();