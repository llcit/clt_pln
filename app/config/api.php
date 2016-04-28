<?php
include_once ('config.php');
/**
 * pln_api
 *
 * @author fairisle
 *
 */
class Api {
	/**
	 * New database
	 */
	protected static $db;

	/**
	 * Check the data connection
	 */
	public function newDb() {
		if (! isset ( self::$db )) {
			self::$db = new Database ();
		}
	}

	/**
	 * Add a new app name
	 *
	 * @param string $name
	 * @return If the app is added, return true, otherwise false
	 */
	public function addApp($name) {
		self::newDb ();

		// Check the app exists
		$result = self::getAppInfo ( $name, 'id' );

		// Insert an app data only if the app does not exist
		if (! $result) {
			$query = "INSERT INTO app (name) VALUES ('$name')";
			$result = self::$db->query ( $query );
			return $result;
		}
		// Return false when fail to insert
		return;
	}

	/**
	 * Add a new format, function or type
	 *
	 * @param string $name
	 * @return boolean false / mixed data on true
	 */
	public function addExt($ext, $extName) {
		self::newDb ();

		// Check the format exists
		$result = self::getExtId ( $ext, $extName );
		if ($result) {
			return;
		} else {
			$query = "INSERT INTO $ext (name) VALUES ('$extName')";
			$result = self::$db->query ( $query );
			return $result;
		}
		// Return false when fail to insert
		return;
	}

	/**
	 * Add user
	 *
	 * @param string $id
	 * @param string $password
	 * @param string $name
	 * @return boolean false / mixed data on success
	 */
	public function addUser($id, $name, $password) {
		self::newDb ();

		$hash = md5 ( $password );
		// Insert a user only if the id does not exist
		$result = self::isUserId ( $id );

		if (! $result) {
			$query = "INSERT INTO user (id, name, password) VALUES ('$id','$name', '$hash')";
			$result = self::$db->query ( $query );
			return true;
		} else {
			return;
		}
	}

	/**
	 * Add an app's extentions
	 *
	 * @param string $appName
	 * @param string $ext
	 * @param string $extName
	 * @return If success return true, otherwise return false
	 */
	public function addAppExt($appName, $ext, $extName) {
		self::newDb ();

		// Check the app and key value exists
		$appId = self::getAppInfo ( $appName, 'id' );
		$appId = $appId [0] ['id'];
		$extId = self::getExtId ( $ext, $extName );
		$extId = $keyId [0] ['id'];

		if (! $appId || ! $extId) {
			return;
		} else {
			$table = "app_" . $key;
			$keyCol = $key . "_id";

			$query = "INSERT INTO app_" . $ext . " (app_id, " . $ext . "_id) VALUES ('.$appId', '$keyId')";
			$result = self::$db->query ( $query );
			return $result;
		}
		// Return false when fail to insert
		return;
	}

	/**
	 * Fetch rows from the database query: all apps
	 *
	 * @return array Data rows
	 */
	public function getAllApps() {
		self::newDb ();

		// Select all data
		$query = "SELECT app.id, app.name, app.description, app.icon, app.privacy, app.tutorial, app.uri, app.price, app.support, app.testimonial,
	       	 	  GROUP_CONCAT(DISTINCT format.name) as format, GROUP_CONCAT(DISTINCT function.name) as function, GROUP_CONCAT(DISTINCT type.name) as type
	       	 	  FROM app
	       	 	  LEFT JOIN app_format  ON app.id = app_format.app_id
				  INNER JOIN format ON app_format.format_id = format.id
				  LEFT JOIN app_function ON app.id = app_function.app_id
				  INNER JOIN function ON app_function.function_id = function.id
				  LEFT JOIN app_type ON app.id = app_type.app_id
				  INNER JOIN type ON app_type.type_id = type.id
				  GROUP BY app.id
				  ORDER BY app.id";
		$result = self::$db->select ( $query );
		foreach($result as $root=>$keys) {
			foreach($keys as $key=>$value) {
				if($key == "format" || $key == "function" || $key == "type") {
					$result[$root][$key] = explode(",", $value);
				}
			}
		}
		return $result;
	}

	/**
	 * Get all function, format, or type ids
	 *
	 * @param string $table
	 * @return $result for array Data rows
	 */
	public function getAllExtId($table) {
		self::newDb ();

		$query = "SELECT * FROM $table";
		$result = self::$db->select ( $query );
		return $result;
	}

	/**
	 *  All attributes of an app by name
	 *
	 * @param string $appName
	 * @return When the app exists return all attributes of the app, otherwise return false
	 */
	public function getAppByName($appName) {
		self::newDb ();
		$query = "SELECT app.id, app.name, app.description, app.icon, app.privacy, app.tutorial, app.uri, app.price, app.support, app.testimonial,
                                   GROUP_CONCAT(DISTINCT format.name) as format, GROUP_CONCAT(DISTINCT function.name) as function, GROUP_CONCAT(DISTINCT type.name) as type
                                   FROM app
                                   LEFT JOIN app_format ON app.id = app_format.app_id and app.name = '$appName'
                                   INNER JOIN format ON app_format.format_id = format.id
                                   LEFT JOIN app_function ON app.id = app_function.app_id and app.name ='$appName'
                                   INNER JOIN function ON app_function.function_id = function.id
                                   LEFT JOIN app_type ON app.id = app_type.app_id and app.name ='$appName'
                                   INNER JOIN type ON app_type.type_id = type.id
                                   GROUP BY app.id
                                   ORDER BY app.id";
		$result = self::$db->select ( $query );
		foreach($result as $root=>$keys) {
			foreach($keys as $key=>$value) {
				if($key == "format" || $key == "function" || $key == "type") {
					$result[$root][$key] = explode(",", $value);
				}
			}
		}
		return $result;
	}

	/**
	 * All attributes of an app by id
	 *
	 * @param integer $id
	 * @return When the app exists return all attributes of the app, otherwise return false
	 */
	public function getAppById($appId) {
		self::newDb ();
		$query = "SELECT app.id, app.name, app.description, app.icon, app.privacy, app.tutorial, app.uri, app.price, app.support, app.testimonial,
                                  GROUP_CONCAT(DISTINCT format.name) as format, GROUP_CONCAT(DISTINCT function.name) as function, GROUP_CONCAT(DISTINCT type.name) as type
                                  FROM app
                                  LEFT JOIN app_format ON app.id = app_format.app_id and app.id = '$appId'
                                  INNER JOIN format ON app_format.format_id = format.id
                                  LEFT JOIN app_function ON app.id = app_function.app_id and app.id ='$appId'
                                  INNER JOIN function ON app_function.function_id = function.id
                                  LEFT JOIN app_type ON app.id = app_type.app_id and app.id ='$appId'
                                  INNER JOIN type ON app_type.type_id = type.id
                                  GROUP BY app.id
                                  ORDER BY app.id";
		$result = self::$db->select ( $query );
		foreach($result as $root=>$keys) {
			foreach($keys as $key=>$value) {
				if($key == "format" || $key == "function" || $key == "type") {
					$result[$root][$key] = explode(",", $value);
				}
			}
		}
		return $result;
	}

	/**
	 * Get an attribute of the app by app name with the app's attribute name
	 *
	 * @param string $appName,
	 * @param string $col
	 * @return the attribute or false
	 */
	public function getAppInfo($appName, $colName) {
		self::newDb ();

		$query = "SELECT $colName FROM app WHERE name = '$appName'";
		$result = self::$db->select ( $query );
		return $result;
	}

	/**
	 * Get all name of format, function, or type
	 */
	public function getExt($ext) {
		self::newDb();

		$query = "SELECT * FROM $ext";
		$result = self::$db->select($query);
		return $result;
	}

	/**
	 * Get ids of format, function, or type using its name
	 *
	 * @param string $table,
	 * @param string $name
	 * @return when the name exists return an id of name on table, otherwise return false
	 */
	public function getExtId($table, $name) {
		self::newDb ();

		$query = "SELECT id FROM $table WHERE name = '$name'";
		$result = self::$db->select ( $query );
		return $result;
	}

	/**
	 * Get name of format, function, or type using its id
	 * @param string $table
	 * @param string $id
	 * @return when the id exists return an name of id on the table, otherwise return false
	 */
	public function getExtName($table, $id) {
		self::newDb ();

		$query = "SELECT name FROM $table WHERE id = '$id'";
		$result = self::$db->select ( $query );
		return $result;
	}

	/**
	 * Get format, function, or type ids of app by app name
	 *
	 * @param string $appName
	 * @param string $key
	 * @return when the data exists return the data, otherwise return false
	 */
	public function getAppExtId($appName, $ext) {
		self::newDb ();

		$appId = self::getAppInfo ( $appName, 'id' );
		$appId = $appId [0] ['id'];

		if (! $appId) {
			return;
		} else {
			$query = "SELECT " . $ext . "_id FROM app_" . $ext . " WHERE app_id = '$appId'";
			$result = self::$db->select ( $query );
			return $result;
		}
	}

	/**
	 * Get format, function or type namesof app by app name
	 *
	 * @param string $name
	 * @param string $key
	 * @return when the data exists return the data, otherwise return false
	 */
	public function getAppExtName($appName, $ext) {
		self::newDb ();

		$appId = self::getAppInfo ( $appName, 'id' );
		$appId = $appId [0] ['id'];

		if (! $appId) {
			return;
		} else {
			$query = "SELECT " . $ext . "name FROM app_" . $ext .
						" INNER JOIN " . $ext . " ON app_" . $ext . "." . $ext . "_id = " . $ext . ".id WHERE app_" . $ext . ".app_id = '$appId'";
			$result = self::$db->select ( $query );
			return $result;
		}
	}

	/**
	 * Get user name by user id
	 *
	 * @param string $id
	 * @return When the user id exists return the data, otherwise return false
	 */
	public function getUserName($id) {
		self::newDb ();

		$query = "SELECT name FROM user WHERE id = '$id'";
		$result = self::$db->select ( $query );
		return $result;
	}

	/**
	 * Get user id by user name
	 *
	 * @param string $name
	 * @return When the user name exists return the data, otherwise return false
	 */
	public function getUserId($name) {
		self::newDb ();

		$query = "SELECT id FROM user WHERE name = '$name'";
		$result = self::$db->select ( $query );
		return $result;
	}

	/**
	 * User id validataion check
	 *
	 * @param string $id
	 * @return When the user id exists return the data, otherwise return false
	 */
	public function isUserId($id) {
		self::newDb ();

		$query = "SELECT id FROM user WHERE id = '$id'";
		$result = self::$db->select ( $query );
		return $result;
	}

	/**
	 * Update an app's attributes
	 */
	public function updateApp($appName, $col, $value) {
		self::newDb ();
		$appId = self::getAppInfo ( $appName, 'id' );

		if (! $appId) {
			return;
		} else {
			$query = "UPDATE app SET $col = '$value' WHERE name = '$appName'";
			$result = self::$db->query ( $query );
			return $result;
		}
		return;
	}

	/**
	 * Update existing format, function or type
	 *
	 * @param string $ext
	 * @param string $extName
	 * @return boolean false / mixed data on true
	 */
	public function updateExt($ext, $extName) {
		self::newDb ();

		// Check the format exists
		$result = self::getExtId ( $ext, $extName );
		if (!$result) {
			return;
		} else {
			$query = "UPDATE $ext SET name = '$extName' WHERE name = '$extName'";
			$result = self::$db->query ( $query );
			return $result;
		}
		// Return false when fail to insert
		return;
	}

	/**
	 * Delete an app
	 *
	 * @param string $name
	 * @return boolean false / mixed data on success
	 */
	public function delApp($appName) {
		self::newDb ();

		// Check the app exists
		$appId = self::getAppInfo ( $appName, 'id' );
		$appId = $appId [0] ['id'];

		if (! $appId) {
			return;
		} else {
			// Delete an app from app_format, app_function, and app_type when success to delete the app
			$exts = array("format", "function", "type");

			foreach($exts as $ext) {
				$result = self::delAppExt($appName, $ext);
				if(! $result) {
					return "Extention delete error.";
				}
			}

			// Delete an app
			if (! $result) {
				return;
			} else {
				// Delete an app from app table
				$query = "DELETE FROM app WHERE id = '$appId'";
				$result = self::$db->query ( $query );

				return $result;
			}
			return $result;
		}
		// Return false when fail to delete
		return;
	}

	/**
	 * Delete a format, function or type
	 *
	 * @param string $name
	 * @return boolean false / mixed data on true
	 */
	public function delExt($ext, $extName) {
		self::newDb ();

		// Check the format exists
		$result = self::getExtId ( $ext, $extName );
		if ($result) {
			return;
		} else {
			$query = "DELETE FROM $ext WHERE name = '$extName'";
			$result = self::$db->query ( $query );
			return $result;
		}
		// Return false when fail to insert
		return;
	}

	/**
	 * Delete a row from the database query on users table
	 *
	 * @param string $id
	 * @return If success, return true, otherwise return false
	 */
	public function delUser($id) {
		self::newDb ();

		// Check the id exists
		$result = self::getUserId ( $id );
		if ($result) {
			return;
		} else {
			$query = "DELETE FROM user WHERE id = '$id'";
			$result = self::$db->query ( $query );
			return $result;
		}
		// Return false when fail to delete
		return;
	}

	/**
	 * Delete an app's extention
	 *
	 * @param string $ext
	 * @param string $appName
	 * @return If success, return true, otherwise return false
	 */
	public function delAppExt($appName, $ext) {
		self::newDb ();
		// Check the app exists
		$appId = self::getAppInfo ( $appName, 'id' );
		$appId = $appId [0] ['id'];

		if (! $appId) {
			return;
		} else {
			$query = "DELETE FROM app_" . $ext . " WHERE app_id = '$appId'";
			$result = self::$db->query ( $query );
			return $result;
		}
		return;
	}
}

?>