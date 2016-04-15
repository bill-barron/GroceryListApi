<?php
class Config {

	// Database connection info and credentials
    const HOSTNAME = 'localhost';
    const DATABASE = 'mydb';
    const USERNAME = 'myuser';
    const PASSWORD = 'mypass';

	// List all valid SQL tables in your database
    public static $TABLES = array(
        'grocery_items',
        'grocery_list'
	);

	// List all valid SQL table field names
    public static $FIELDS = array(
        'id',
        'name',
        'list_id',
        'grocery_item_id',
        'quantity',
    );
}
?>
