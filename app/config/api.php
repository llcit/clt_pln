<?php
include_once ('config.php');
/**
 * pln_api
 *
 * @author fairisle
 *
 */
class Api {
	// New database
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
	 * **************************
	 * get functions start here *
	 * **************************
	 */

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
		return $result;
	}

	/**
	 * Fetch rows from the database query: format ids
	 *
	 * @return array Data rows
	 */
	public function getAllFormatId() {
		self::newDb ();

		$query = "SELECT * FROM format";
		$result = self::$db->select ( $query );
		return $result;
	}

	/**
	 * Fetch rows from the database query: function ids
	 *
	 * @return array Data rows
	 */
	public function getAllFunctionId() {
		self::newDb ();

		$query = "SELECT * FROM function";
		$result = self::$db->select ( $query );
		return $result;
	}

	/**
	 * Fetch rows from the database query: type ids
	 *
	 * @return array Data rows
	 */
	public function getAllTypeId() {
		self::newDb ();

		$query = "SELECT * FROM type";
		$result = self::$db->select ( $query );
		return $result;
	}

	/**
	 * Fetch rows from the dtabase query: all data of an app
	 *
	 * @param string $name
	 * @return array Data rows
	 */
	public function getAppByName($name) {
		self::newDb ();
		$query = "SELECT app.id, app.name, app.description, app.icon, app.privacy, app.tutorial, app.uri, app.price, app.support, app.testimonial,
                                   GROUP_CONCAT(DISTINCT format.name) as format, GROUP_CONCAT(DISTINCT function.name) as function, GROUP_CONCAT(DISTINCT type.name) as type
                                   FROM app
                                   LEFT JOIN app_format ON app.id = app_format.app_id and app.name = '$name'
                                   INNER JOIN format ON app_format.format_id = format.id
                                   LEFT JOIN app_function ON app.id = app_function.app_id and app.name ='$name'
                                   INNER JOIN function ON app_function.function_id = function.id
                                   LEFT JOIN app_type ON app.id = app_type.app_id and app.name ='$name'
                                   INNER JOIN type ON app_type.type_id = type.id
                                   GROUP BY app.id
                                   ORDER BY app.id";
		$result = self::$db->select ( $query );
		return $result;
	}

	/**
	 * Fetch rows from the dtabase query: all data of an app
	 *
	 * @param integer $id
	 * @return array Data rows
	 */
	public function getAppById($id) {
		self::newDb ();
		$query = "SELECT app.id, app.name, app.description, app.icon, app.privacy, app.tutorial, app.uri, app.price, app.support, app.testimonial,
                                  GROUP_CONCAT(DISTINCT format.name) as format, GROUP_CONCAT(DISTINCT function.name) as function, GROUP_CONCAT(DISTINCT type.name) as type
                                  FROM app
                                  LEFT JOIN app_format ON app.id = app_format.app_id and app.id = '$id'
                                  INNER JOIN format ON app_format.format_id = format.id
                                  LEFT JOIN app_function ON app.id = app_function.app_id and app.id ='$id'
                                  INNER JOIN function ON app_function.function_id = function.id
                                  LEFT JOIN app_type ON app.id = app_type.app_id and app.id ='$id'
                                  INNER JOIN type ON app_type.type_id = type.id
                                  GROUP BY app.id
                                  ORDER BY app.id";
		$result = self::$db->select ( $query );
		return $result;
	}

	/**
	 * get an description by app name
	 *
	 * @param string $name, $key
	 * @return $key or false
	 */
	public function getAppInfo($name, $key) {
		self::newDb ();

		$query = "SELECT $key FROM app WHERE name = '$name'";
		$result = self::$db->select ( $query );
		return $result;
	}

	/**
	 * get an format id by format name
	 *
	 * @param string $name
	 * @return format id or false
	 */
	public function getFormatId($name) {
		self::newDb ();

		$query = "SELECT id FROM format WHERE name = '$name'";
		$result = self::$db->select ( $query );
		return $result;
	}

	/**
	 * get an format name by format id
	 *
	 * @param string $id
	 * @return boolean false / mixed data on success
	 */
	public function getFormatName($id) {
		self::newDb ();

		$query = "SELECT name FROM format WHERE id = '$id'";
		$result = self::$db->select ( $query );
		return $result;
	}

	/**
	 * get an type id by type name
	 *
	 * @param string $name
	 * @return type id or false
	 */
	public function getTypeId($name) {
		self::newDb ();
		$query = "SELECT id FROM type WHERE name = '$name'";
		$result = self::$db->select ( $query );
		return $result;
	}

	/**
	 * get an type name by type id
	 *
	 * @param string $id
	 * @return boolean false / mixed data on success
	 */
	public function getTypeName($id) {
		self::newDb ();
		$query = "SELECT name FROM type WHERE id = '$id'";
		$result = self::$db->select ( $query );
		return $result;
	}

	/**
	 * get an function id by function name
	 *
	 * @param string $name
	 * @return boolean false / mixed data on success
	 */
	public function getFunctionId($name) {
		self::newDb ();
		$query = "SELECT id FROM function WHERE name = '$name'";
		$result = self::$db->select ( $query );
		return $result;
	}

	/**
	 * get an function name by function id
	 *
	 * @param string $id
	 * @return boolean false / mixed data on success
	 */
	public function getFunctionName($id) {
		self::newDb ();
		$query = "SELECT name FROM function WHERE id = '$id'";
		$result = self::$db->select ( $query );
		return $result;
	}

	/**
	 * get an app format id by app name
	 *
	 * @param string $name
	 * @return boolean false / mixed data on success
	 */
	public function getAppFormatId($name) {
		self::newDb ();

		$appId = self::getAppInfo ( $name, 'id' );
		$appId = $appId [0] ['id'];
		if (! $appId) {
			return;
		} else {
			$query = "SELECT format_id FROM app_format WHERE app_id = '$appId'";
			$result = self::$db->select ( $query );
			return $result;
		}
	}

	/**
	 * get an app format name by app name
	 *
	 * @param string $name
	 * @return boolean false / mixed data on success
	 */
	public function getAppFormatName($name) {
		self::newDb ();

		$appId = self::getAppInfo ( $name, 'id' );
		$appId = $appId [0] ['id'];
		if (! $appId) {
			return;
		} else {
			$query = "SELECT format.name FROM app_format INNER JOIN format ON app_format.format_id = format.id WHERE app_format.app_id = '$appId'";
			$result = self::$db->select ( $query );
			return $result;
		}
	}

	/**
	 * get an app function id by app name
	 *
	 * @param string $name
	 * @return boolean false / mixed data on success
	 */
	public function getAppFunctionId($name) {
		self::newDb ();

		$appId = self::getAppInfo ( $name, 'id' );
		$appId = $appId [0] ['id'];
		if (! $appId) {
			return;
		} else {
			$query = "SELECT function_id FROM app_function WHERE app_id = '$appId'";
			$result = self::$db->select ( $query );
			return $result;
		}
	}

	/**
	 * get an app function name by app name
	 *
	 * @param string $name
	 * @return boolean false / mixed data on success
	 */
	public function getAppFunctionName($name) {
		self::newDb ();

		$appId = self::getAppInfo ( $name, 'id' );
		$appId = $appId [0] ['id'];
		if (! $appId) {
			return;
		} else {
			$query = "SELECT function.name FROM app_function INNER JOIN function ON app_function.function_id = function.id WHERE app_function.app_id = '$appId'";
			$result = self::$db->select ( $query );
			return $result;
		}
	}

	/**
	 * get an app format id by app name
	 *
	 * @param string $name
	 * @return boolean false / mixed data on success
	 */
	public function getAppTypeId($name) {
		self::newDb ();

		$appId = self::getAppInfo ( $name, 'id' );
		$appId = $appId [0] ['id'];
		if (! $appId) {
			return;
		} else {
			$query = "SELECT type_id FROM app_type WHERE app_id = '$appId'";
			$result = self::$db->select ( $query );
			return $result;
		}
	}

	/**
	 * get an app type name by app name
	 */
	public function getAppTypeName($name) {
		self::newDb ();

		$appId = self::getAppIinfo ( $name, 'id' );
		$appId = $appId [0] ['id'];
		if (! $appId) {
			return;
		} else {
			$query = "SELECT type.name FROM app_type INNER JOIN type ON app_type.type_id = type.id WHERE app_type.app_id = '$appId'";
			$result = self::$db->select ( $query );
			return $result;
		}
	}

	/**
	 * Fetch rows from the database query: user id
	 *
	 * @param string $id
	 * @return boolean user exist true / otherwise false
	 */
	public function getUserName($id) {
		self::newDb ();

		$query = "SELECT name FROM user WHERE id = '$id'";
		$result = self::$db->select ( $query );
		return $result;
	}

	/**
	 * Fetch rows from the database query: user name
	 *
	 * @param string $name
	 * @return boolean user exist true / otherwise false
	 */
	public function getUserId($name) {
		self::newDb ();

		$query = "SELECT id FROM user WHERE name = '$name'";
		$result = self::$db->select ( $query );
		return $result;
	}

	/**
	 * Fetch rows from the database query: user id
	 *
	 * @param string $id
	 * @return boolena true when id exists / otherwise false
	 */
	public function isUserId($id) {
		self::newDb ();

		$query = "SELECT id FROM user WHERE id = '$id'";
		$result = self::$db->select ( $query );
		return $result;
	}

	/**
	 * **************************
	 * add functions start here *
	 * **************************
	 */

	/**
	 * Insert a row from the database query on users table
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
	 * add App name only if it does not exist
	 *
	 * @param string $name
	 * @return boolean false / mixed data on success
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

	// TODO check again about update data

    /**
     * Update App
     */

    public function updateApp($name, $key, $value) {
        self::newDb();
        $appId = self::getAppInfo($name, 'id');

        if(!$appId) {
            return ;
        } else {
            $query = "UPDATE app SET $key = '$value' WHERE name = '$name'";
            $result = self::$db->query($query);
            return $result;
        }
        return;
    }

	/**
	 * add format only if it does not exist
	 *
	 * @param string $name
	 * @return boolean false / mixed data on true
	 */
	public function addFormat($name) {
		self::newDb ();

		// Check the format exists
		$result = self::getFormatId ( $name );
		if ($result) {
			return;
		} else {
			$query = "INSERT INTO format (name) VALUES ('$name')";
			$result = self::$db->query ( $query );
			return $result;
		}
		// Return false when fail to insert
		return;
	}

	/**
	 * add function only if it does not exist
	 *
	 * @param string $name
	 * @return boolena false / mixed data on true
	 */
	public function addFunction($name) {
		self::newDb ();

		// Check the function exists
		$result = self::getFunctionId ( $name );
		if ($result) {
			return;
		} else {
			$query = "INSERT INTO function (name) VALUES ('$name')";
			$result = self::$db->query ( $query );
			return $result;
		}
		// Return false when fail to insert
		return;
	}

	/**
	 * add type only if it does not exist
	 *
	 * @param string $name
	 * @return bloolean false / mixed data on true
	 */
	public function addType($name) {
		self::newDb ();

		// Check the type exists
		$result = self::getTypeId ( $name );
		if ($result) {
			return;
		} else {
			$query = "INSERT INTO type (name) VALUES ('$name')";
			$result = self::$db->query ( $query );
			return $result;
		}
		// Return when fail to insert
		return;
	}

	/**
	 * add app_format (multiplication)
	 *
	 * @param string $name
	 * @param string $format
	 * @return boolean false or appFomatId / mixed data on success
	 */
	public function addAppFormat($name, $format) {
		self::newDb ();

		// Check the app exists
		$appId = self::getAppInfo ( $name, 'id' );
		$appId = $appId [0] ['id'];
		$formatId = self::getFormatId ( $format );
		$formatId = $formatId [0] ['id'];

		if (! $appId || ! $formatId) {
			return;
		} else {
			$query = "INSERT INTO app_format (app_id, format_id) VALUES ('$appId', '$formatId')";
			$result = self::$db->query ( $query );
			return $result;
		}
		// Return false when fail to insert
		return;
	}

	/**
	 * add app_function (multiplication)
	 *
	 * @param string $name
	 * @param string $function
	 * @return boolean false or appFunctionId/ mixed data on success
	 */
	public function addAppFunction($name, $function) {
		self::newDb ();

		// Check the app exists
		$appId = self::getAppInfo ( $name, 'id' );
		$appId = $appId [0] ['id'];
		$functionId = self::getFunctionId ( $function );
		$functionId = $functionId [0] ['id'];

		if (! appId || ! $functionId) {
			return;
		} else {
			$query = "INSERT INTO app_function (app_id, function_id) VALUES ('$appId', '$functionId')";
			$result = self::$db->query ( $query );
			return $result;
		}
		// Return false when fail to insert
		return;
	}

	/**
	 * add App type
	 *
	 * @param string $name
	 * @param string $type
	 * @return boolean false / true on success
	 */
	public function addAppType($name, $type) {
		self::newDb ();
		// Check the app exists
		$appId = self::getAppInfo ( $name, 'id' );
		$appId = $appId [0] ['id'];
		$typeId = self::getTypeId ( $type );
		$typeId = $typeId [0] ['id'];

		if (! $appId || ! $typeId) {
			return;
		} else {
			$query = "INSERT INTO app_type (app_id, type_id) VALUES ('$appId', '$typeId')";
			$result = self::$db->query ( $query );
			return $result;
		}
		// Return when fail to insert
		return;
	}

	/**
	 * ****************************
	 * delete function start here *
	 * ****************************
	 */

	/**
	 * Delete a row from the database query on users table
	 *
	 * @param string $id
	 * @return boolean false / mixed data on success
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
	 * Delete app format
	 *
	 * @param string $name
	 * @return boolean false on fail
	 */
	public function delAppFormat($name) {
		self::newDb ();
		// Check the app exists
		$appId = self::getAppInfo ( $name, 'id' );
		$appId = $appId [0] ['id'];

		if (! $appId) {
			return;
		} else {
			$query = " DELETE FROM app_format WHERE app_id = '$appId'";
			$result = self::$db->query ( $query );
			return $result;
		}
		return;
	}

	/**
	 * Delete app function
	 *
	 * @param string $name
	 * @return boolean false on fail
	 */
	public function delAppFunction($name) {
		self::newDb ();
		// Check the app exists
		$appId = self::getAppInfo ( $name, 'id' );
		$appId = $appId [0] ['id'];

		if (! $appId) {
			return;
		} else {
			$query = " DELETE FROM app_function WHERE app_id = '$appId'";
			$result = self::$db->query ( $query );
			return $result;
		}

		return;
	}

	/**
	 * Delete app type
	 *
	 * @param string $name
	 * @return boolean false on fail
	 */
	public function delAppType($name) {
		self::newDb ();
		// Check the app exists
		$appId = self::getAppInfo ( $name, 'id' );
		$appId = $appId [0] ['id'];

		if (! $appId) {
			return;
		} else {
			$query = " DELETE FROM app_type WHERE app_id = '$appId'";
			$result = self::$db->query ( $query );
			return $result;
		}

		return;
	}

	/**
	 * Delete an app
	 *
	 * @param string $name
	 * @return boolean false / mixed data on success
	 */
	public function delApp($name) {
		self::newDb ();

		// Check the app exists
		$appId = self::getAppInfo ( $name, 'id' );
		$appId = $appId [0] ['id'];

		if (! $appId) {
			return;
		} else {
			// Delete an app from app_format, app_function, and app_type when success to delete the app
			$result = self::delAppFormat ( $name );
			if (! $result) {
				return;
			}
			$result = self::delAppFunction ( $name );
			if (! $result) {
				return;
			}
			$result = self::delAppType ( $name );
			if (! $result) {
				return;
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
	 * Delete format
	 *
	 * @param string $name
	 * @return boolean false on fail
	 */
	public function delFormat($name) {
		self::newDb ();

		// check the available
		$result = self::getFormatId ( $name );
		$result = $result [0] ['id'];

		if (! $result) {
			return;
		} else {
			$query = "DELETE FROM format WHERE id = '$result'";
			$result = self::$db->query ( $query );
			return $result;
		}
		return;
	}

	/**
	 * Delete function
	 *
	 * @param string $name
	 * @return boolean false on fail
	 */
	public function delFunction($name) {
		self::newDb ();

		// check the available
		$result = self::getFunctionId ( $name );
		$result = $result [0] ['id'];

		if (! $result) {
			return;
		} else {
			$query = "DELETE FROM function WHERE id = '$result'";
			$result = self::$db->query ( $query );
			return $result;
		}
		return;
	}

	/**
	 * Delete type
	 *
	 * @param string $name
	 * @return boolean false on fail
	 */
	public function delType($name) {
		self::newDb ();

		// check the available
		$result = self::getTypeId ( $name );
		$result = $result [0] ['id'];

		if (! $result) {
			return;
		} else {
			$query = "DELETE FROM type WHERE id = '$result'";
			$result = self::$db->query ( $query );
			return $result;
		}
		return;
	}
}

?>