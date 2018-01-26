<?php

include "include/BasePage.php";

class LoginPage extends BasePage {

private $xmlLoging = <<<XML
<data status="LOGIN" legend="Login">Please enter:</data>
XML;

private $xmlLogingError = <<<XML
<data status="ERROR" legend="Login">Something went wrong, please try again:</data>
XML;

    public function getContent() {
        if(!isset($_POST['user']) or !isset($_POST['password'])) {
            return parent::processXML([], $this->xmlLoging);
        }
        if(!$this->validateUser($_POST['user'], $_POST['password'])) {
            return parent::processXML([], $this->xmlLogingError);
        }
        $_SESSION['user'] = $_POST['user'];
        header("Location: #");
    }

    private function validateUser($user, $password) {
        return (strlen($user) > 2) and (strlen($password) > 2);
    }

}

?>
