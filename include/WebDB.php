<?php

class WebDB {

    private $server = "localhost";
    private $user = "zoo";
    private $password = "zootable!";
    private $schema = "zoodb";
    private $conn = null;

    public function WebDB() {
        $this->conn = new PDO("mysql:host=$this->server;dbname=$this->schema", $this->user, $this->password);
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    private function getResponce($return, $status) {
         $ret = ["R" => $return, "S" => $status];
         return $ret;
    }

    public function getGroup() {
        try {
            $stmt = $this->conn->prepare("select GetGroup() as XML;");            
            if($stmt->execute() === false) {
                return $this->getResponce("GetGroup execute error", "ERR_EXECUTE");
            }
            $xml = $stmt->fetchAll(PDO::FETCH_ASSOC)[0]['XML'];
            return $this->getResponce($xml, "OK");
        } catch(PDOException $e) {
            return $this->getResponce($e->getMessage(), 'PDO_EXC');
        }
    }
    
    public function getZoo($sort, $gid, $from, $pagesize) {
        try {
            $stmt = $this->conn->prepare("select GetZoo5(?, ?, ?, ?) as XML;");
            $stmt->bindParam(1, $sort, PDO::PARAM_STR);
            $stmt->bindParam(2, $gid, PDO::PARAM_STR);
            $stmt->bindParam(3, $from, PDO::PARAM_INT);
            $stmt->bindParam(4, $pagesize, PDO::PARAM_INT);
            if($stmt->execute() === false) {
                return $this->getResponce("GetZoo execute error", "ERR_EXECUTE");
            }
            $xml = $stmt->fetchAll(PDO::FETCH_ASSOC)[0]['XML'];
            return $this->getResponce($xml, "OK");
        } catch(PDOException $e) {
            return $this->getResponce($e->getMessage(), 'PDO_EXC');
        }
    }
    
        public function getAnimal($id) {
        try {
            $stmt = $this->conn->prepare("select GetAnimal(?) as XML;");
            $stmt->bindParam(1, $id, PDO::PARAM_STR);            
            if($stmt->execute() === false) {
                return $this->getResponce("GetAnimal execute error", "ERR_EXECUTE");
            }
            $xml = $stmt->fetchAll(PDO::FETCH_ASSOC)[0]['XML'];
            return $this->getResponce($xml, "OK");
        } catch(PDOException $e) {
            return $this->getResponce($e->getMessage(), 'PDO_EXC');
        }
    }

    public function __destruct() {
        $this->conn = null;
    }

}

?>
