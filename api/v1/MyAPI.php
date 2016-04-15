<?php

require_once 'API.class.php';
require_once 'Model/GroceryItem.php';
require_once 'Model/GroceryListItem.php';
require_once 'Model/EntrySet.php';
require_once 'Model/ErrorMessage.php';
require_once 'Model/Message.php';


class MyAPI extends \API
{
    //protected $User;

    public function __construct($request, $origin) {
        parent::__construct($request);

        // Abstracted out for example
        //$APIKey = new Models\APIKey();
        //$User = new Models\User();
        //
        //if (!array_key_exists('apiKey', $this->request)) {
        //    throw new Exception('No API Key provided');
        //} else if (!$APIKey->verifyKey($this->request['apiKey'], $origin)) {
        //    throw new Exception('Invalid API Key');
        //} else if (array_key_exists('token', $this->request) &&
        //     !$User->get('token', $this->request['token'])) {
        //
        //    throw new Exception('Invalid User Token');
        //}
        //
        //$this->User = $User;
    }

    /**
     * Example of an Endpoint
     */
     protected function example() {
        if ($this->method == 'GET') {
            return "Your name is john smith."; // . $this->User->name;
        } else {
            return "Only accepts GET requests";
        }
     }

     protected function grocery_items() {

         /**
          * GET REQUESTS
          **/
         if($this->method == 'GET') {
             // Get a specific grocery item by id
             if($this->verb == '' && array_key_exists(0, $this->args) && is_numeric($this->args[0])) {
                 $grocery_item = Model\GroceryItem::GetByID($this->args[0]);
                 if($grocery_item === false) {
                     $this->status = 404;
                     return new \Model\ErrorMessage("Item not found.");
                 }
                 return new \Model\EntrySet($grocery_item);
             }

             // Search for a grocery_item by name
             if($this->verb == 'find') {
                 if(!array_key_exists(0, $this->args)) {
                     $this->status = 422;
                     return Model\ErrorMessage("Missing search_text. use /grocery_items/find/{search_text}");
                 }
                 return new Model\EntrySet(Model\GroceryItem::Find($this->args[0]));
              }

              // Get all grocery items
             if($this->verb == 'list' || !array_key_exists(0, $this->args)) {
                 return new Model\EntrySet(Model\GroceryItem::ListItems());
             }
         }

         /**
          * POST REQUESTS
          **/
         if($this->method == 'POST') {
              if(!array_key_exists("name", $this->request)) {
                  $this->status = 422;
                  return Model\ErrorMessage("Missing parameter. name is required.");
              }
             return new Model\EntrySet(Model\GroceryItem::CreateItem($this->request['name']));
             //return "Adding a grocery item: '" . $this->request['name'] . "'";
         }

         /**
          * DELETE REQUESTS
          **/
         if($this->method == 'DELETE') {
             if(!array_key_exists(0, $this->args) || !is_numeric($this->args[0])) {
                 $this->status = 422;
                 return new Model\ErrorMessage("Parameter Missing. id is required. use /grocery_items/{id}");
             }
             Model\GroceryItem::DeleteItem($this->args[0]);
             return new Model\Message("Item deleted.");
         }
     }

     protected function grocery_list_items() {
         if($this->method == 'GET') {
             $list_id = 1;
             if(array_key_exists(0, $this->args)  && is_numeric($this->args[0])) {
                 $list_id = $this->args[0];
             }

             if(array_key_exists(1, $this->args)  && is_numeric($this->args[1]) && $this->verb == '') {
                 $grocery_item_id = $this->args[1];
                return new Model\EntrySet(Model\GroceryListItem::GetEntry($grocery_item_id, $list_id));
             }

             // GET all tiems in a grocery list
             if($this->verb == 'list' || !array_key_exists(1, $this->args)) {
                 return new Model\EntrySet(Model\GroceryListItem::ListItems($list_id));
             }

             $this->status = 400;   // Bad request
             return new Model/ErrorMessage("Bad request. Use /grocery_list_items/{list-id}/list or /grocery_list_items/{list-id}/{grocery-item-id}");

         }

         if($this->method == 'POST') {
             if(!array_key_exists(0, $this->args)  || !is_numeric($this->args[0])) {
                 $this->status = 422;   // Missing parameter
                 return new Model/ErrorMessage("list-id parameter missing. Use /grocery_list_items/{list-id}/{grocery-item-id}");
             }

             $list_id = $this->args[0];

             if(!array_key_exists(1, $this->args)  || !is_numeric($this->args[1])) {
                $this->status = 422;   // Missing parameter
                return new Model/ErrorMessage("grocery-item-id parameter missing. Use /grocery_list_items/{list-id}/{grocery-item-id}");
             }

             $grocery_item_id = $this->args[1];
             $quantity = null;
             $comment = null;

             if(array_key_exists("quantity", $this->request)  && is_numeric($this->request["quantity"])) {
                 $quantity = $this->request["quantity"];
             }

            if(array_key_exists("comment", $this->request)) {
                $comment = $this->request["comment"];
            }
            return new Model\EntrySet(Model\GroceryListItem::Upsert($grocery_item_id, $quantity, $comment, $list_id));
         }

         if($this->method == 'PUT') {
             if(!array_key_exists(0, $this->args)  || !is_numeric($this->args[0])) {
                 $this->status = 422;   // Missing parameter
                 return new Model/ErrorMessage("list-id parameter missing. Use /grocery_list_items/{list-id}/{grocery-item-id}");
             }

             $list_id = $this->args[0];

             if(!array_key_exists(1, $this->args)  || !is_numeric($this->args[1])) {
                $this->status = 422;   // Missing parameter
                return new Model/ErrorMessage("grocery-item-id parameter missing. Use /grocery_list_items/{list-id}/{grocery-item-id}");
             }

             $grocery_item_id = $this->args[1];
             $quantity = null;
             $comment = null;

             if(!array_key_exists("quantity", $this->args)  || !is_numeric($this->args["quantity"])) {
                 $quantity = $this->args["quantity"];
             }

             if(!array_key_exists("comment", $this->args)) {
                $comment = $this->args["comment"];
                return new Model/ErrorMessage("grocery-item-id parameter missing. Use /grocery_list_items/{list-id}/{grocery-item-id}");
             }

             return new Model\EntrySet(Model\GroceryListItem::Upsert($grocery_item_id, $quantity, $comment, $list_id));
         }

         if($this->method == 'DELETE') {
             if(!array_key_exists(0, $this->args)  || !is_numeric($this->args[0])) {
                 $this->status = 422;   // Missing parameter
                 return new Model/ErrorMessage("list-id parameter missing. Use /grocery_list_items/{list-id}/{grocery-item-id}");
             }

             $list_id = $this->args[0];

             if(!array_key_exists(1, $this->args)  || !is_numeric($this->args[1])) {
                $this->status = 422;   // Missing parameter
                return new Model/ErrorMessage("grocery-item-id parameter missing. Use /grocery_list_items/{list-id}/{grocery-item-id}");
             }

             $grocery_item_id = $this->args[1];
             Model\GroceryListItem::DeleteItem($grocery_item_id, $list_id);
             return new Model\Message("Item deleted.");
         }
     }
 }

 ?>
