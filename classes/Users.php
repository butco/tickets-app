<?php

class Users
{

    private $db;
    public function __construct($database)
    {
        $this->db = $database;
    }
}