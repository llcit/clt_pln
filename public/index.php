<?php
// base controller
include_once ('../app/config/api.php');

// header for json format
header ( 'Content-type: application/json; charset=utf-8' );

// Load phalcon libraries
use Phalcon\Mvc\Micro;
use Phalcon\Http\Response;

// Create a phalcon Micro
$app = new Micro ();

// Root directory action
$app->get ( '/', function () use ($app) {
	echo "<h3>Personal Learning Network Development Sites.<h3>";
} );

// Get all apps
$app->get ( '/get_apps', function () use ($app) {
	$api = new Api ();
	$result = $api->getAllApps ();
	echo json_encode ( $result, JSON_UNESCAPED_SLASHES );
} );

// Get an app with $appName
$app->get ( '/get_app/{name}', function ($name) use ($app) {
	$api = new Api ();
	$result = $api->getAppByName ( $name );
	echo json_encode ( $result, JSON_UNESCAPED_SLASHES );
} );

// Get an app with $appId
$app->get ( '/get_app/id/{id:[0-9]+}', function ($id) use ($app) {
	$api = new Api ();
	$result = $api->getAppById ( $id );
	echo json_encode ( $result, JSON_UNESCAPED_SLASHES );
} );

// TODO login URI

$app->get ( '/login/{name}', function () use ($app) {
	echo "login";
} );

// Post an app with attribute
$app->post ( '/post_app', function () use ($app) {
	$api = new Api ();
	$response = new Response ();
	$newApp = $app->request->getJsonRawBody ();

	// Check app name: Duplication check
	if (! array_key_exists ( 'name', $newApp ) || $newApp->name == NULL) {
		$response->setStatusCode ( 400, "MissingRequeredQueryParameter" );
		$response->setJsonContent ( array (
				'status' => 'ERROR',
				'messages' => 'Application name must exist'
		) );
		return $response;
	}

	// The extentions must exist
	$exts = array (
			"format",
			"function",
			"type"
	);
	foreach ( $exts as $ext ) {
		if (! array_key_exists ( $ext, $newApp ) || $newApp->$ext == NULL) {
			$response->setStatusCode ( 400, "MissingRequiredQueryParameter" );
			$response->setJsonContent ( array (
					'status' => 'ERROR',
					'messages' => 'Application ' . $ext . ' must exist.'
			) );
			return $response;
		}
	}

	// Add an app name first
	$result = $api->addApp ( $newApp->name );

	// If add an app get error, return error
	if (! $result) {
		$response->setStatuscode ( 409, "Conflict" );
		$response->setJsonContent ( array (
				'status' => 'ERROR',
				'messages' => 'The application already exists.'
		) );
		return $response;
	}

	// Add additional app information
	foreach ( $newApp as $key => $value ) {
		if ($key != 'name' && $value != NULL) {
			// Format, function, and type can have more than the one attribute
			if ($key == 'format' || $key == 'function' || $key == 'type') {
				// If there are more than one attribute, split the data and input all data
				$values = explode ( ',', $newApp->$key );
				foreach ( $values as $value ) {
					$result = $api->addAppExt ( $newApp->name, $key, $value );
				}
			} else {
				$result = $api->updateApp ( $newApp->name, $key, $value );
			}
		}

		$response->setStatusCode ( 201, "Created" );
		$response->setJsonContent ( array (
				'status' => 'OK',
				'messages' => 'Application created.'
		) );
	}
	return $response;
} );

// Update application attribute
$app->put ( '/put_app', function () use ($app) {
	$api = new Api ();
	$response = new Response ();
	$updateApp = $app->request->getJsonRawBody ();
	// App must exist on update information
	if (! array_key_exists ( 'name', $updateApp ) || $updateApp->name == NULL) {
		$response->setStatusCode ( 400, "MissingRequestedQueryParameter" );
		$response->setJsonContent ( array (
				'status' => 'ERROR',
				'messages' => 'Application name requered.'
		) );
		return $response;
	}
	// Get app id must exist on database
	$result = $api->getAppInfo ( $updateApp->name, 'id' );
	if (! $result) {
		$response->setStatusCode ( 400, "MissingRequestedQueryParameter" );
		$response->setJsonContent ( array (
				'status' => "ERROR",
				'Messages' => " The application does not exist."
		) );
		return $response;
	}
	// Call all previous information
	$preAppInfo = $api->getAppByName ( $updateApp->name );

	// Update values matched with key
	foreach ( $updateApp as $key => $value ) {
		if ($value != NULL) {
			// TODO update, format, type
			if ($key == 'format' || $key == 'function' || $key == 'type') {
				$preValues = explode ( ',', $preAppInfo->$key );
				$updateValues = explode ( ',', $updateApp->$key );
				$diffValues = array_diff ( $preValues, $updateValues );

				if ($diffValues != NULL) {
				}
			} else {
				$result = $api->updateApp ( $updateApp->name, $key, $value );

				if (! $result) {
					$response->setStatusCode ( 400, "Unknown" );
					$response->setJsonContent ( array (
							'status' => 'Error',
							'messages' => 'Unknown error when the ' . $key . ' name update.'
					) );
					return $response;
				}
				$response->setStatusCode ( 201, "Updated" );
				$response->setJsonContent ( array (
						'status' => 'OK',
						'messages' => 'Application updated'
				) );
			}
		}
	}
	return $response;
} );

$app->get ( '/get_extension/{ext}', function ($ext) use ($app) {
	$api = new Api ();
	$result = $api->getExt ( $ext );
	echo json_encode ( $result );

} );


// TODO fix add extension
// Add new format, functin, or type
$app->post ( '/post_extension', function () use ($app) {
	$api = new Api ();
	$response = new Response ();
	$post = (array) $app->request->getJsonRawBody ();
	$exts = array (
			'format'=>NULL,
			'function'=>NULL,
			'type'=>NULL
	);
	// Read only extentions
	$postExts = array_intersect_key( $post, $exts );

	if($postExts == "format") {
		$response->setJsonContent( array (
				'status'=>'array error'
		));
		return $response;
	}

	foreach ( $postExts as $postExt => $extName ) {
		if ($extName == NULL) {
			$response->setStatusCode ( 400, "MissingRequestQueryParameter" );
			$response->setJsonContent ( array (
					'status' => 'ERROR',
					'message' => 'The ' . $postExt . " name must exist."
			) );
			return $response;
		}
		$result = $api->addExt ( $postExt, $extName );

		if (! $result) {
			$response->setStatusCode ( 400, "Unknown" );
			$response->setJsonContent ( array (
					'status' => 'ERROR',
					'messages' => 'Unknown error from api when postting ' . $postExt . '.'
			) );
		} else {
			$response->setStatusCode ( 201, 'Created' );
			$response->setJsonCcontent ( array (
					'status' => 'OK',
					'messages' => 'Extension created.'
			) );
		}
	}
	return $response;
} );

// Update existing format, functin, or type
$app->put ( '/put_extension', function () use ($app) {
	$api = new Api ();
	$response = new Response ();
	$put = $app->request->getJsonRawBody ();
	$exts = array (
			'format',
			'function',
			'type'
	);
	// Read only extentions
	$putExts = array_intersect_key ( $put, $exts);

	foreach ( $putExts as $putExt => $extName ) {
		if ($extName == NULL) {
			$response->setStatusCode ( 400, "MissingRequestQueryParameter" );
			$response->setJsonContent ( array (
					'status' => 'ERROR',
					'message' => 'The ' . $putExt . " name must exist."
			) );
			return $response;
		}
		$result = $api->updateExt ( $putExt, $extName );

		if (! $result) {
			$response->setStatusCode ( 400, "Unknown" );
			$response->setJsonContent ( array (
					'status' => 'ERROR',
					'messages' => 'Unknown error from api when postting ' . $putExt . '.'
			) );
		} else {
			$response->setStatusCode ( 200, 'Updated' );
			$response->setJsonCcontent ( array (
					'status' => 'OK',
					'messages' => 'Extension updated.'
			) );
		}
	}
	return $response;
} );

// Delete an app
$app->delete ( '/del_app', function () use ($app) {
	$api = new Api ();
	$response = new Response ();
	$delApp = $app->request->getJsonRawBody ();

	// Deleted app name must exist on delete information
	if (! array_key_exists ( 'name', $delApp ) || $delApp->name == NULL) {
		$response->setStatusCode ( 400, "MissingRequestedQueryParameter" );
		$response->setJsonContent ( array (
				'status' => 'ERROR',
				'messages' => 'The application name requered.'
		) );
	} elseif (! $api->getAppInfo ( $delApp->name, 'id' )) {
		// App must exist on database
		$response->setStatusCode ( 400, "MissingRequestQueryParameter" );
		$response->setJsonContent ( array (
				'status' => 'ERROR',
				'messages' => 'The aplication does not exist'
		) );
	} else {
		$result = $api->delApp ( $delApp->name );
		if (! $result) {
			$response->setStatusCode ( 400, "Unknown" );
			$response->setJsonContent ( array (
					'status' => 'ERROR',
					'messages' => 'Unknown delete error from api.'
			) );
		} else {
			$response->setStatusCode ( 200, "OK" );
			$response->setJsonContent ( array (
					'status' => 'OK',
					'messages' => "App deleted"
			) );
		}
	}
	return $response;
} );

// TODO delete function
// Delete an extention
$app->delete ( '/del_extension', function () use ($app) {
	$api = new Api ();
	$response = new Response ();
	$del = (array) $app->request->getJsonRawBody ();
	$exts = array (
			'format'=>NULL,
			'function'=>NULL,
			'type'=>NULL
	);
	// Read only extentions
	$delExts = array_intersect_key ( $del, $exts );

	foreach ( $delExts as $delExt => $extName ) {
		if ($extName == NULL) {
			$response->setStatusCode ( 400, "MissingRequestQueryParameter" );
			$response->setJsonContent ( array (
					'status' => 'ERROR',
					'message' => 'The ' . $delExt . " name must exist."
			) );
			return $response;
		}
		$result = $api->delExt ( $delExt, $extName );

		if (! $result) {
			$response->setStatusCode ( 400, "Unknown" );
			$response->setJsonContent ( array (
					'status' => 'ERROR',
					'messages' => 'Unknown error from api when postting ' . $delExt . '.'
			) );
		} else {
			$response->setStatusCode ( 200, 'Deleted' );
			$response->setJsonCcontent ( array (
					'status' => 'OK',
					'messages' => 'Extension deleted.'
			) );
		}
	}
	return $response;
} );

$app->handle ();