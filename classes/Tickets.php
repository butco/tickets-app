<?php

class Tickets
{

    private $db;
    public function __construct($database)
    {
        $this->db = $database;
    }

    //Add New Ticket
    public function AddNew($projectId, $userId, $title, $details, $status)
    {
        $sql = "INSERT INTO tickets(project_id,user_id,title,details,status) VALUES(:projectId,:userId,:title,:details,:status)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindparam(":projectId", $projectId);
        $stmt->bindparam(":userId", $userId);
        $stmt->bindparam(":title", $title);
        $stmt->bindparam(":details", $details);
        $stmt->bindparam(":status", $status);
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
    public function TicketDetails($id)
    {
        $sql = "SELECT * FROM tickets WHERE id=:id";
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

    //Update Details
    public function UpdateTicket($id, $projectId, $userId, $title, $details, $status)
    {
        $sql = "UPDATE tickets SET project_id=:projectId,user_id=:userId,title=:title,details=:details,status=:status WHERE id=:id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindparam(":id", $id);
        $stmt->bindparam(":projectId", $projectId);
        $stmt->bindparam(":userId", $userId);
        $stmt->bindparam(":title", $title);
        $stmt->bindparam(":details", $details);
        $stmt->bindparam(":status", $status);
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
    public function DeleteTicket($id)
    {
        $sql = "DELETE FROM tickets WHERE id=:id";
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

    //Start Ticket
    public function StartTicket($id)
    {
        $sql = "UPDATE tickets SET status='IN_PROGRESS',start_date=CURRENT_TIMESTAMP WHERE id=:id";
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

    //Close Ticket
    public function CloseTicket($id, $close_details)
    {
        $sql = "UPDATE tickets SET status='CLOSED',close_details=:close_details,close_date=" . date("Y-m-d H:i:s") . " WHERE id=:id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindparam(":id", $id);
        $stmt->bindparam(":close_details", $close_details);
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

    //Get all the project's tickets
    public function GetAllProjectsTickets($projId, $status)
    {
        $sql = "SELECT t.*,u.user_fullname FROM tickets t,users u WHERE t.user_id = u.id AND t.project_id = :projId AND t.status IN (" . $status . ")";
        $stmt = $this->db->prepare($sql);
        $stmt->bindparam(":projId", $projId);
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
}