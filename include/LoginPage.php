<?php

include "include/BasePage.php";

class LoginPage extends BasePage {

private $xmlLoging = <<<XML
<data status="LOGIN" legend="Login"></data>
XML;

private $xmlLogingError = <<<XML
<data status="ERROR" legend="Login"></data>
XML;

private $xmlLogingSuccess = <<<XML
<data status="SUCCESS" legend="Login">_NAME_</data>
XML;

    public function getContent() {
        if(!isset($_POST['user']) or !isset($_POST['password'])) {
            return parent::processXML([], $this->xmlLoging);
        }
        if(!$this->validateUser($_POST['user'], $_POST['password'])) {
            return parent::processXML([], $this->xmlLogingError);
        }
        $xml = str_replace('_NAME_', $_POST['user'], $this->xmlLogingSuccess);
        echo "<script> document.getElementById('UserName').innerHTML = '". $_POST['user']. "';</script>";
        $_SESSION['user'] = $_POST['user'];
        return parent::processXML([], $xml);
    }

    private function validateUser($user, $password) {
        return (strlen($user) > 2) and (strlen($password) > 2);
    }


}

?>
