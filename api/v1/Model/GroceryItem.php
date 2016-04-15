<?php
namespace Model;
require_once 'Database.php';

class GroceryItem extends Database {

    public static function Find($search_text) {
        self::CreateConnection();
        $entries = array();
        $sql = self::generateSearchQuery($search_text, "=");
        $results = self::$dbh->query($sql);
        if(!empty($results)) {
            foreach($results as $row) {
                array_push($entries, $row);
            }
        }
        self::$dbh = null;
        return $entries;
    }

    public static function GetByName($name) {
        return Database::GetEntryById("grocery_items", $name, 'name');
    }

    public static function GetByID($id) {
        return Database::GetEntryById("grocery_items", $id, 'id');
    }

    public static function ListItems() {
        return Database::GetAll("grocery_items");
    }

    public static function CreateItem($name) {
        $item = self::GetByName($name);
        if($item !== false) {
            return $item;
        }

        self::CreateConnection();
        $stmt = self::$dbh->prepare("INSERT INTO grocery_items (name) VALUES (:name)");
        $stmt->bindParam(':name', $name, \PDO::PARAM_STR);
        $stmt->execute();
        $id = self::$dbh->lastInsertId();
        self::$dbh = null;
        return array(array("id" => $id, "name" => $name));

        return "Item does not exist";
    }

    public static function DeleteItem($id) {
        self::CreateConnection();

        $stmt1 = self::$dbh->prepare("DELETE FROM grocery_list_items WHERE item = :id");
        $stmt2 = self::$dbh->prepare("DELETE FROM grocery_items WHERE id = :id");

        $stmt1->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt2->bindParam(':id', $id, \PDO::PARAM_INT);

        $stmt1->execute();
        $stmt2->execute();

        self::$dbh = null;
    }

    /**
     * Take the given search text and generate the SQL query
     */
    private static function generateSearchQuery($search_text, $escapeChar) {
        $keys = self::_cleanInputs($search_text, $escapeChar);
        $sql = 'SELECT * FROM grocery_items WHERE ';
        $conditions = array();
        foreach($keys as $key) {
            array_push($conditions, 'name LIKE "%' . $key . '%" ESCAPE "' . $escapeChar . '"');
            array_push($conditions, 'name LIKE "' . $key . '%" ESCAPE "' . $escapeChar . '"');
            array_push($conditions, 'name LIKE "%' . $key . '" ESCAPE "' . $escapeChar . '"');
        }
        $sql .= implode(" OR ", $conditions);
        return $sql;
    }

    /**
     * parameter: $subkect  The search text to escape and clean up.
     * parameter: $escapeChar   The character to put in front of any special character to escape it, so it is not interpreted as a special char.
     */
    private static function escapeSQLLikeString($subject, $escapeChar)
    {
        return str_replace(array($escapeChar, '_', '%'), array($escapeChar . $escapeChar, $escapeChar . '_', $escapeChar . '%'), $subject);
    }

    /* Split the search text by words and escape each word */
    private static function _cleanInputs($data, $escapeChar) {
        $clean_input = Array();
        $parts = explode(' ', $data);
        foreach($parts as $k) {
            if(!empty($k)) {
                array_push ($clean_input, self::escapeSQLLikeString($k, $escapeChar));
            }
        }
        return $clean_input;
    }

}

?>
