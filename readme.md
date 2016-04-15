Grocery List API
================

This is a simple demo API using MySQL and PHP
---------------------------------------------

Application: Personal grocery list with autocomplete based on past usage.

This is just the API, no client provided yet.

### Installation

Rename /api/v1/Config.example.php to /api/v1/Config.php and make any changes you need to connect to a MySQL database.


    CREATE TABLE IF NOT EXISTS `grocery_items` (
      `id` int(10) NOT NULL AUTO_INCREMENT,
      `name` varchar(255) NOT NULL,
      PRIMARY KEY (`id`),
      UNIQUE KEY `name` (`name`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=158 ;
    
    CREATE TABLE IF NOT EXISTS `grocery_list` (
      `id` int(10) NOT NULL AUTO_INCREMENT,
      `owner` int(10) NOT NULL,
      `name` varchar(255) NOT NULL,
      `comment` varchar(255) DEFAULT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;
    
    CREATE TABLE IF NOT EXISTS `grocery_list_items` (
      `list_id` int(10) NOT NULL,
      `grocery_item_id` int(10) NOT NULL,
      `quantity` int(10) NOT NULL DEFAULT '1',
      `comment` varchar(255) DEFAULT NULL,
      PRIMARY KEY (`list_id`,`grocery_item_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
    
    CREATE TABLE IF NOT EXISTS `users` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `username` varchar(60) NOT NULL,
      `email` varchar(255) NOT NULL,
      `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

### Endpoints

*   GET	/api/v1/grocery_items/	lists all remembered grocery items
*   GET	/api/v1/grocery_items/list	lists all remembered grocery items
*   GET	/api/v1/grocery_items/find/{search_str}	finds all grocery items where grocery_item_name matches any word of {search_str}~
*   GET	/api/v1/grocery_items/{id}	gets a specific grocery item by {id}
*   POST	/api/v1/grocery_items/	remembers a new grocery item (post data: name={grocery_item_name}
*   DELETE	/api/v1/grocery_items/{id}	lists all remembered grocery items
*   GET	/api/v1/grocery_list_items/{id}	lists all grocery items in the list with id = {id}
*   GET	/api/v1/grocery_list_items/{id}/list	lists all grocery items in the list with id = {id}
*   POST	/api/v1/grocery_list_items/{id}/{grocery_item_id}	adds the grocery item with id = {gorcery_item_id} to the list with id = {id}
	(post data: name={gorcery_item_name}, quantity, comment)
*   PUT	/api/v1/grocery_list_items/{id}/{grocery_item_id}	modifies the quantity or the comment of the item within the grocery list
*   DELETE	/api/v1/grocery_list_items/{id}/{grocery_item_id}	removes the grocery item with id = {grocery_item_id} from the list with id = {id}
