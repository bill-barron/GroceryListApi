<?php
namespace Model;
require_once 'Database.php';

class GroceryListItem extends Database {

    /**
     * Looks for a specific grocery item within this grocery list
     * If you are trying to see whether the list has carrots in it
     */
    public static function GetEntry($grocery_item_id, $list_id = '1') {
        $entry = null;
        self::CreateConnection();
		$stmt = self::$dbh->prepare("SELECT * FROM grocery_list_items WHERE list_id = :list AND grocery_item_id = :item");
        $stmt->bindParam(':list', $list_id, \PDO::PARAM_INT);
        $stmt->bindParam(':item', $grocery_item_id, \PDO::PARAM_INT);
        $stmt->execute();
        $entry = $stmt->fetch();
        self::$dbh = null;
        return $entry;
    }

    public static function ListItems($list_id = 1) {
        $entries = null;
        self::CreateConnection();
		$stmt = self::$dbh->prepare("SELECT a.list_id as 'list_id', a.grocery_item_id as grocery_item_id, a.quantity as quantity, a.comment as comment, b.name as name FROM grocery_list_items as a, grocery_items as b WHERE list_id = :list AND grocery_item_id = id");
        $stmt->bindParam(':list', $list_id, \PDO::PARAM_INT);
        $stmt->execute();
        $entries = $stmt->fetchAll();
        self::$dbh = null;
        return $entries;
    }

    public static function Upsert($grocery_item_id, $quantity, $comment, $list_id = '1') {
        $entry = self::GetEntry($grocery_item_id, $list_id = 1);
        if($entry !== false) {

            // Use the existing quantity if omitted
            if(!isset($quantity)) {
                $quantity = $entry->quantity;
            }

            // Use the existing comment if omitted
            if(!isset($comment)) {
                $comment = $entry->comment;
            }

            if($quantity == $entry->quantity && $comment == $entry->comment) {
                return $entry;
            }

            self::CreateConnection();
    		$stmt = self::$dbh->prepare("UPDATE grocery_list_items WHERE list_id = :list AND grocery_item_id = :item SET quantity = :quantity, comment = :comment");
            $stmt->bindParam(':list', $list_id, \PDO::PARAM_INT);
            $stmt->bindParam(':item', $grocery_item_id, \PDO::PARAM_INT);
            $stmt->bindParam(':quantity', $quantity, \PDO::PARAM_INT);
            $stmt->bindParam(':comment', $comment, \PDO::PARAM_STR);
            $stmt->execute();
            self::$dbh = null;
            return array(
                "list_id" => $list_id,
                "grocery_item_id" => $grocery_item_id,
                "quantity" => $quantity,
                "comment" => $comment,
            );
        }

        self::CreateConnection();
        $stmt = self::$dbh->prepare("INSERT INTO grocery_list_items (list_id, grocery_item_id, quantity, comment) VALUES (:list, :item, :quantity, :comment)");
        $stmt->bindParam(':list', $list_id, \PDO::PARAM_INT);
        $stmt->bindParam(':item', $grocery_item_id, \PDO::PARAM_INT);
        $stmt->bindParam(':quantity', $quantity, \PDO::PARAM_INT);
        $stmt->bindParam(':comment', $comment, \PDO::PARAM_STR);
        $stmt->execute();
        self::$dbh = null;
        return array(
            "list_id" => $list_id,
            "grocery_item_id" => $grocery_item_id,
            "quantity" => $quantity,
            "comment" => $comment,
        );
    }

    public static function DeleteItem($grocery_item_id, $list_id) {
        self::CreateConnection();
        $stmt = self::$dbh->prepare("DELETE FROM grocery_list_items WHERE list_id = :list AND grocery_item_id = :item");
        $stmt->bindParam(':list', $list_id, \PDO::PARAM_INT);
        $stmt->bindParam(':item', $grocery_item_id, \PDO::PARAM_INT);
        $stmt->execute();
        self::$dbh = null;
    }
}

?>
