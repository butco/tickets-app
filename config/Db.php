<?php

class Db {

    private $server;
    private $dbname;
    private $dbuser;
    private $dbpass;

    public function connect()
    {
        $this->server = "localhost";
        $this->dbuser = "butcosof_ticketapp";
        $this->dbpass = "TicketAppPass";
        $this->dbname = "butcosof_ticketapp";

        try {
            $dsn = "mysql:host=" . $this->server . ";dbname=" . $this->dbname;
            $pdo = new PDO($dsn, $this->dbuser, $this->dbpass);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }
}
