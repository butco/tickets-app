<?php

class Projects
{

    private $db;
    public function __construct($database)
    {
        $this->db = $database;
    }

    //Add New Project
    public function AddNew($projName, $projCompany, $projLogo)
    {
        $sql = "INSERT INTO projects(proj_name,proj_company,proj_logo) VALUES(:proj_name,:proj_company,:proj_logo)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindparam(":proj_name", $projName);
        $stmt->bindparam(":proj_company", $projCompany);
        $stmt->bindparam(":proj_logo", $projLogo);
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
    //See project details by ID
    public function ProjectDetails($id)
    {
        $sql = "SELECT * FROM projects WHERE id=:id";
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

    //Assign specific user to specific project
    public function AssignUserToProject($projId, $userId)
    {
        $sql = "INSERT INTO users_on_projects(user_id,project_id) VALUES(:userId,:projId)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindparam(":userId", $userId);
        $stmt->bindparam(":projId", $projId);
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

    //Unassign specific user from specific project
    public function UnassignUserFromProject($projId, $userId)
    {
        $sql = "DELETE FROM users_on_projects WHERE project_id = :projId AND user_id=:userId";
        $stmt = $this->db->prepare($sql);
        $stmt->bindparam(":projId", $projId);
        $stmt->bindparam(":userId", $userId);
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

    //See all users assigned on one project
    public function GetAssignedUsers($projId)
    {
        $sql = "SELECT u.* FROM users as u,projects as p,users_on_projects as uop WHERE u.id=uop.user_id AND p.id=uop.project_id AND p.id=:projId";
        $stmt = $this->db->prepare($sql);
        $stmt->bindparam(":projId", $projId);
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

    //See all users UNassigned on one project
    public function GetUnassignedUsers($projId)
    {
        $sql = "select u.* from users u where u.id not in (select uop.user_id from users_on_projects uop,projects p where uop.project_id = p.id AND p.id = :projId) AND u.user_is_active=1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindparam(":projId", $projId);
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

    //See projects user is assigned on
    public function GetProjectsByUser($userId)
    {
        $sql = "SELECT p.* FROM projects p,users_on_projects uop WHERE p.id = uop.project_id AND uop.user_id = :userId";
        $stmt = $this->db->prepare($sql);
        $stmt->bindparam(":userId", $userId);
        try {
            $stmt->execute();
            if ($stmt->rowCount()) {
                $result = $stmt->fetchAll(PDO::FETCH_OBJ);
                return $result;
            }
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }

    //check if a user is assigned on a specific project
    public function CheckUserAssignedOnProject($userId, $projId)
    {
        $sql = "SELECT * FROM users_on_projects WHERE user_id = :userId AND project_id=:projId";
        $stmt = $this->db->prepare($sql);
        $stmt->bindparam(":userId", $userId);
        $stmt->bindparam(":projId", $projId);
        try {
            $stmt->execute();
            if ($stmt->rowCount()) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }

    //See all projects
    public function GetAllProjects()
    {
        $sql = "SELECT * FROM projects";
        $stmt = $this->db->prepare($sql);
        try {
            $stmt->execute();
            if ($stmt->rowCount()) {
                $result = $stmt->fetchAll(PDO::FETCH_OBJ);
                return $result;
            }
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }

    //Update Details by Admins
    public function UpdateProject($id, $projName, $projCompany, $projLogo)
    {
        $sql = "UPDATE projects SET proj_name=:proj_name,proj_company=:proj_company,proj_logo=:proj_logo WHERE id=:id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindparam(":id", $id);
        $stmt->bindparam(":proj_name", $projName);
        $stmt->bindparam(":proj_company", $projCompany);
        $stmt->bindparam(":proj_logo", $projLogo);
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

    //Delete project by ID
    public function DeleteProject($id)
    {
        $sql = "DELETE FROM projects WHERE id=:id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindparam(":id", $id);
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