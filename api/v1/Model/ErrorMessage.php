<?php
namespace Model;

class ErrorMessage {

    public $message;
    public $timestamp;
    public $status;

    function __construct($message, $status = 'error') {
        date_default_timezone_set("UTC");
        $this->message = $message;
        $this->timestamp = date("Y-m-d h:i:s",time());
        $this->status = $status;
    }
}

?>
