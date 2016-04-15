<?php
namespace Model;

class EntrySet {

    public $entries;
    public $timestamp;
    public $status;

    function __construct($entries, $status = 'ok') {
        $this->entries = is_array($entries) ? $entries : array($entries);
        $this->timestamp = date("Y-m-d h:i:s",time());
        $this->status = $status;
    }
}

?>
