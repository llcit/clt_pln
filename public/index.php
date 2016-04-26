<?php
// base controller
include_once ('../app/config/api.php');

// header for json format
header ( 'Content-type: application/json; charset=utf-8' );

use Phalcon\Mvc\Micro;
use Phalcon\Http\Response;

$app = new Micro ();

$app->get ( '/', function () use ($app) {
	echo "<h3>Personal Learning Network Development Sites.<h3>";
} );

// Retrieves all apps
$app->get ( '/get/apps', function () use ($app) {
	$api = new Api ();
	$result = $api->getAllApps ();
	echo json_encode ( $result, JSON_UNESCAPED_SLASHES );
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

$app->get ( '/login/user/{name}', function () use ($app) {
	echo "login";
} );

$app->post ( '/post/apps', function () use ($app) {
	$api = new Api ();
	$response = new Response ();
	$newApp = $app->request->getJsonRawBody ();

	// Check app name
	if (! array_key_exists ( 'name', $newApp ) || $newApp->name == NULL) {
		$response->setStatusCode ( 400, "MissingRequeredQueryParameter" );
		$response->setJsonContent ( array (
				'status' => 'ERROR',
				'messages' => 'Application name must exist'
		) );
		return $response;
	}

	// Check format
	if (! array_key_exists ( 'format', $newApp ) || $newApp->format == NULL) {
		$response->setStatusCode ( 400, "MissingRequiredQueryParameter" );
		$response->setJsonContent ( array (
				'status' => 'ERROR',
				'messages' => 'Application format must exist.'
		) );
		return $response;
	}

	// Check function
	if (! array_key_exists ( 'function', $newApp ) || $newApp->function == NULL) {
		$response->setStatusCode ( 400, "MissingRequestedQueryParameter" );
		$response->setJsonContent ( array (
				'status' => 'ERROR',
				'messages' => 'Application function must exist.'
		) );
		return $response;
	}

	// Check type
	if (! array_key_exists ( 'type', $newApp ) || $newApp->type == NULL) {
		$response->setStatusCode ( 400, "MissingRequestedQueryParameter" );
		$response->setJsonContent ( array (
				'status' => 'ERROR',
				'messages' => 'Application type must exist.'
		) );
		return $response;
	}

	$result = $api->addApp ( $newApp->name );

	if (! $result) {
		$response->setStatuscode ( 409, "Conflict" );
		$response->setJsonContent ( array (
				'status' => 'ERROR',
				'messages' => 'The application already exists.'
		) );
	} else {
		// Add additional information
		$result = $api->updateAppDescription ( $newApp->name, $newApp->description );
		$result = $api->updateAppIcon ( $newApp->name, $newApp->icon );
		$result = $api->updateAppPrice ( $newApp->name, $newApp->price );
		$result = $api->updateAppPrivacy ( $newApp->name, $newApp->privacy );
		$result = $api->updateAppSupport ( $newApp->name, $newApp->support );
		$result = $api->updateAppTestimonial ( $newApp->name, $newApp->testimonial );
		$result = $api->updateAppTutorial ( $newApp->name, $newApp->tutorial );
		$result = $api->updateAppUri ( $newApp->name, $newApp->uri );

		// Split the data and input all data
		$formats = explode ( ',', $newApp->format );
		foreach ( $formats as $format ) {
			$result = $api->addAppFormat ( $newApp->name, $format );
		}

		$functions = explode ( ',', $newApp->function );
		foreach ( $functions as $function ) {
			$result = $api->addAppFunction ( $newApp->name, $function );
		}

		$types = explode ( ',', $newApp->type );
		foreach ( $types as $type ) {
			$result = $api->addAppType ( $newApp->name, $type );
		}
		$response->setStatusCode ( 201, "Created" );
		$response->setJsonContent ( array (
				'status' => 'OK'
		) );
	}
	return $response;
} );

// TODO Update an app
$app->put ( '/put/apps', function () use ($app) {
	$api = new Api ();
	$response = new Response ();
	$modApp = $app->request->getJsonRawBody ();

	if (! array_key_exists ( 'name', $modApp ) || $modApp->name == NULL) {
		$response->setStatusCode ( 400, "MissingRequestedQueryParameter" );
		$response->setJsonContent ( array (
				'status' => 'ERROR',
				'messages' => 'Application name requered.'
		) );
		return $response;
	}

	$result = $api->getAppId ( $modApp->name );
	if (! $result) {
		$response->setStatusCode ( 400, "MissingRequestedQueryParameter" );
		$response->setJsonContent ( array (
				'status' => "ERROR",
				'Messages' => " The application does not exist."
		) );
		return $response;
	}

	if ($modApp->description != NULL) {
		$result = $api->updateAppDescription ( $modName->name, $modApp->description );
		if (! $result) {
			$response->setStatusCode ( 400, "InvalidInput" );
			$response->setJsonContent ( array (
					'status' => 'Error',
					'messages' => 'The description data update error.'
			) );
			return $response;
		}
	}
} );

$app->delete('/del/apps', function () use($app){
	$api = new Api();
	$response = new Response();
	$delApp = $app->request->getJsonRawBody();

	if(!array_key_exists('name', $delApp) || $delApp->name == NULL) {
		$response->setStatusCode(400, "MissingRequestedQueryParameter");
		$response->setJsonContent( array (
				'status' => 'ERROR',
				'messages' => 'The application name requered.'
		));
	} elseif (!$api->getAppId($delApp->name)){
		$response->setStatusCode(400, "MissingRequestQueryParameter");
		$response->setJsonContent( array (
				'status' => 'ERROR',
				'messages' => 'The aplication does not exist'
		));
	} else {
		$result = $api->delApp($delApp->name);
		if(!$result) {
			$response->setStatusCode(400, "InvalidInput");
			$response->setJsonContent( array (
					'status' => 'ERROR',
					'messages' => 'The application delete error.'
			));
		} else {
			$response->setStatusCode(200, "OK");
			$response->setJsonContent( array (
					'status' => 'OK',
					'messages' => "Deleted"
			));
		}
	}
    return $response;
});

$app->handle ();