<?php
/**
 * This is a database connection class using mysqli
 * Before using this, et the database information on setting.ini
 * 
 * @author songmink
 */
class Database {
	// Database Connection
	protected static $connection;
	
	/**
	 * Database connection
	 *
	 * @return boolean false on failure / object instant on success
	 */
	public function connect() {
		// Try to connect database with the setting.ini
		if (! isset ( self::$connection )) {
			$setting = parse_ini_file ( "config.ini" );
			self::$connection = new mysqli ( $setting ['host'], $setting ['username'], $setting ['password'], $setting ['dbname'] );
		}
		
		// Connection error contro
		if (self::$connection === false) {
			return false;
		}
		return self::$connection;
	}
	
	/**
	 * Query
	 *
	 * @param string $query 
	 * @return mixed The result of the mysqli::query() function
	 */
	public function query($query) {
		// Creat an database connection
		$connection = $this->connect ();
		// Query
		$result = $connection->query ( $query );
		return $result;
	}
	
	/**
	 * Fetch rows from the database (SELECT query)
	 *
	 * @param string $query        	
	 * @param string $appFormatId
	 * @param array $rows 
	 * @return boolean false on failure / mixed data on success
	 */
	public function select($query) {
		$rows = array ();
		$result = $this->query ( $query );
		if ($result === false) {
			return false;
		}
		while ( $row = $result->fetch_assoc () ) {
			$rows [] = $row;
		}
		return $rows;
	}
	
	/**
	 * Quote and escape value for use in a database query
	 *
	 * @param string $value
	 * @return string The quoted and escaped string
	 */
	public function quote($value) {
		$connection = $this->connect ();
		return "'" . $connection->real_escape_string ( $value ) . "'";
	}
	
	/**
	 * error control
	 *
	 * @return error code
	 */
	public function error() {
		$connection = $this->connect ();
		return $connection->error;
	}
}