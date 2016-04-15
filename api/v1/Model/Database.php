<?php
namespace Model;

require_once "Config.php";
/**
 * Handles basic database connectivity. Get all rows or get a single row by id.
 **/
class Database {

	// The database connection object.
    protected static $dbh;
	/**
	 * Creates a database connection if none exists.
	 **/
    protected static function CreateConnection() {
        if(!isset(self::$dbh) || self::$dbh == null) {
            self::$dbh = new \PDO('mysql:host=' . \Config::HOSTNAME . ';dbname=' . \Config::DATABASE, \Config::USERNAME, \Config::PASSWORD);
			if(self::$dbh != null) {
				self::$dbh->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_OBJ);
			}
        }
    }
    /**
     * Gets all entries in a table
     * @return array    All the entries
     */
    public static function GetAll($tableName) {
        $entries = array();
        try {
            self::CreateConnection();
			self::VerifyTableName($tableName);
            $results = self::$dbh->query("SELECT * FROM $tableName");
			if(!empty($results)) {
                foreach($results as $row) {
                    array_push($entries, $row);
                }
            }
            self::$dbh = null;
        } catch(\PDOExecption $e) {
            print "Error!: " . $e->getMessage() . "</br>";
        }
        return $entries;
    }

    /**
     * Gets a single entry from the given table using the given id and optional ID column name
     * @return array    The matching entry or null
     */
    public static function GetEntryById($tableName, $id = 1, $fieldName = 'id') {
        $entry = null;
		try {
            self::CreateConnection();
			self::VerifyTableName($tableName);
			self::VerifyFieldName($tableName, $fieldName);
			$stmt = self::$dbh->prepare("SELECT * FROM $tableName WHERE $fieldName = :id");
            $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
            $stmt->execute();
            $entry = $stmt->fetch();
            self::$dbh = null;
        } catch(\PDOExecption $e) {
            print "Error!: " . $e->getMessage() . "</br>";
        }
        return $entry;
    }


	/**
	 * Ensure that the given table name is valid
	 * @param tableName	string	The name of the table to check.
	 **/
	private static function VerifyTableName($tableName) {
		if(!in_array($tableName, \Config::$TABLES))
			throw new InvalidArgumentException('Attempted to get all entries from invalid table name. Check config file for valid table names. Table name was: ' . $tableName);
	}

	/**
	 * Ensure that the given field name is valid
	 * @param tableName	string	The name of the table to check.
	 **/
	private static function VerifyFieldName($tableName, $fieldName) {
		if(!in_array($fieldName, \Config::$FIELDS))
			throw new InvalidArgumentException('Attempted to get an entry from $tableName using an invalid field name. Check config file for valid field names. Field name was: ' . $fieldName);
	}
}
?>
