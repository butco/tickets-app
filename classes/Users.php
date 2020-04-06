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
        $sql = "SELECT id, user_password FROM users WHERE user_email=:email";
        $stmt = $this->db->prepare($sql);
        $stmt->bindparam(":email", $email);
        try {
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $result = $stmt->fetch(PDO::FETCH_OBJ);
                $stored_pass = $result->user_password;
                $user_id = $result->id;
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

    //Check if email exists in the database
    public function EmailExists($email)
    {
        $sql = "SELECT id FROM users WHERE user_email=:email";
        $stmt = $this->db->prepare($sql);
        $stmt->bindparam(":email", $email);
        try {
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            die($e->getMessage());
        }

    }

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

    //User Details
    public function UserDetails($id)
    {
        $sql = "SELECT * FROM users WHERE id=:id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindparam(":id", $id);
        try {
            $stmt->execute();
            if ($stmt->rowCount()) {
                $result = $stmt->fetch(PDO::FETCH_OBJ);
                return $result;
            }
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }

    //Check if user is administrator
    public function UserIsAdmin($id)
    {
        $sql = "SELECT user_group_id FROM users WHERE id=:id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindparam(":id", $id);
        try {
            $stmt->execute();
            if ($stmt->rowCount()) {
                $result = $stmt->fetch(PDO::FETCH_OBJ);
                if ($result->user_group_id == 1) {
                    return true;
                } else {
                    return false;
                }

            }
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }

    //User Groups
    public function UsersGroups()
    {
        $sql = "SELECT * FROM users_groups";
        $stmt = $this->db->prepare($sql);
        try {
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $result = $stmt->fetchAll(PDO::FETCH_OBJ);
                return $result;
            }
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }

    //User Groups by ID
    public function UsersGroupsByID($id)
    {
        $sql = "SELECT * FROM users_groups WHERE group_id=:id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindparam(":id", $id);
        try {
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $result = $stmt->fetch(PDO::FETCH_OBJ);
                return $result;
            }
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }

    //Logout user
    public function Logout()
    {
        unset($_SESSION['user_id']);
        session_destroy();
        $this->logged_out_redirect();
    }

    //Update Details by Admins
    public function UpdateByAdmins($id, $email, $password, $fullName, $photo, $group, $active)
    {
        $sql = "UPDATE users SET user_email=:email,user_password=:pass,user_fullname=:fullname,user_photo=:photo,user_group_id=:group_id,user_is_active=:active WHERE id=:id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindparam(":id", $id);
        $stmt->bindparam(":email", $email);
        $stmt->bindparam(":pass", $password);
        $stmt->bindparam(":fullname", $fullName);
        $stmt->bindparam(":photo", $photo);
        $stmt->bindparam(":group_id", $group);
        $stmt->bindparam(":active", $active);
        try {
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }
    //Update Details by Standard Users
    public function UpdateByUsers($id, $email, $password, $fullName, $photo)
    {
        $sql = "UPDATE users SET user_email=:email,user_password=:pass,user_fullname=:fullname,user_photo=:photo WHERE id=:id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindparam(":id", $id);
        $stmt->bindparam(":email", $email);
        $stmt->bindparam(":pass", $password);
        $stmt->bindparam(":fullname", $fullName);
        $stmt->bindparam(":photo", $photo);
        try {
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }
}