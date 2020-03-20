<?php

class Users
{

    private $db;
    public function __construct($database)
    {
        $this->db = $database;
    }

    //Login user
    public function login($email, $password)
    {
        $sql = "SELECT user_id, user_password FROM users WHERE user_email=:email";
        $stmt = $this->db->prepare($sql);
        $stmt->bindparam(":email", $email);
        try {
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $result = $stmt->fetch(PDO::FETCH_OBJ);
                $stored_pass = $result->user_password;
                $user_id = $result->user_id;
                //check if the passwords are the same
                if (password_verify($password, $stored_pass)) {
                    return $user_id;
                } else {
                    return false;
                }
            }
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }
    //Logout user

    //Check if user is logged in
    public function logged_in()
    {
        if (isset($_SESSION['user_id'])) {
            return true;
        } else {
            return false;
        }
    }

    //Redirect logged in user to Dashboard page
    public function logged_in_redirect()
    {
        if ($this->logged_in()) {
            header("location:dashboard.php");
            exit();
        }
    }

    //Redirect logged out user to Login page
    public function logged_out_redirect()
    {
        if (!$this->logged_in()) {
            header("location:index.php");
        }
    }
}