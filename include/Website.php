<?php

class Authorisation {
	//users
	const USER_ADM  = 'Admin';
	const USER_BOB  = 'Bob';
	const USER_JOHN = 'John';
	//privileges
	const PRV_USER = 'U';
	const PRV_ADMIN = 'UA';

	private static $users = [
		self::USER_ADM => self::PRV_ADMIN,
		self::USER_BOB => self::PRV_USER,
		self::USER_JOHN  => self::PRV_USER,
	];

	public static function isAuthorised($rest, $user) {
        if(strcasecmp($rest, '*') == 0) {
            return true;
        }
        return (self::userExists($user)) ? (self::check($rest, $user)) : false;
    }

    private static function userExists($user) {
		return array_key_exists($user, self::$users);
	}

	private static function check($rest, $user) {
		return (strpos(self::$users[$user], $rest) !== false);
	}
}

class Session {

	const ATTR_STRICT  = 'strict';
	const ATTR_TIMEOUT = 'timeout';
	const ATTR_AUTHENT = 'authentication';
	const ATTR_GUEST   = 'guest';
	const P_LOGIN      = 'LOGIN';
	const P_LOGOUT     = 'LOGOUT';
	const P_PAGE       = 'page';
	const S_LAST_ACTVT = 'LAST_ACTIVITY';
	const S_USER       = 'user';

    private $strictMode = false;
    private $isLoged = false;
    private $elapsed = 0;
    private $timeout = 0;
    private $authPage = '';
    private $guest = '';

    public function Session() {
        session_start();
    }

    public function initSession($XML) {
        $this->strictMode = self::equal($XML[self::ATTR_STRICT], "YES");
        $this->timeout = $XML[self::ATTR_TIMEOUT];
        $this->authPage = $XML[self::ATTR_AUTHENT];
        $this->guest = $XML[self::ATTR_GUEST];
        $this->checkSessionTime();
        if($this->checkClearSession()) {
            session_unset();
        }
    }

    private function checkSessionTime() {
		$time = time();
		$lastActivity = $_SESSION[self::S_LAST_ACTVT];
		$this->elapsed = (isset($lastActivity)) ? ($time - $lastActivity) : 0;
        $_SESSION[self::S_LAST_ACTVT] = $time;
	}

    private static function equal($str1, $str2) {
		return (strcasecmp($str1, $str2) == 0);
	}

    private function checkClearSession() {
        return (isset($_POST[self::P_LOGIN]) or isset($_POST[self::P_LOGOUT])) or
               ($this->elapsed > $this->timeout) or
               (isset($_POST[self::P_PAGE]) and (self::equal($_POST[self::P_PAGE], $this->authPage)));
    }

    public function isLoged() {
        return (isset($_SESSION[self::S_USER]) and (!self::equal($_SESSION[self::S_USER], $this->guest)));
    }

    public function getUserName() {
        return ($this->isLoged()) ? $_SESSION[self::S_USER] : $this->guest;
    }

    public function getPageName($page) {
        if(self::equal($page, $this->authPage)) {
            return $page;
        }
        if($this->strictMode) {
            return ($this->isLoged()) ? $page : $this->authPage;
        }
        return (isset($_POST[self::P_LOGIN])) ? $this->authPage : $page;
    }

    public function timeLeft() {
        return $this->timeout - $this->elapsed;
    }

}



class Website {

private $error = "_ERROR_";
private $xmlError = <<<XML
<html><body><p>_ERROR_</pp></body></html>
XML;

    private $folder = 'include/data/';
    private $webFile = null;
    private $page = null;
    private $lastMsg = null;
    private $session = null;

    public function Website($webFile) {
        $this->webFile = $webFile;
        $this->page = $this->getPageName();
        $this->session = new Session();
    }

    public function run() {
        $webXML = $this->loadXML($this->folder. $this->webFile, 'website', []);
        if($webXML === false) {
            echo $this->getErrorHTML($this->lastMsg);
            return;
        }
        $this->session->initSession($webXML->session[0]);
        $isLogIn = ($this->session->isLoged() ? "Y" : "N" );
        $user = $this->session->getUserName();
        $page = $this->session->getPageName($this->page);
        $timeLeft = $this->session->timeLeft();
        $format = $webXML->attributes()['format'];
        $webFormat = $this->loadXML($this->folder. $format, 'format',
               ["_PAGE_" => $page, "_USER_" => $user, "_ISLOGIN_" => $isLogIn, "_TIMELEFT_" => $timeLeft]);
        if($webFormat === false) {
           echo $this->getErrorHTML($this->lastMsg);
           return;
        }
        $attributes = $this->getPageAttributes($webXML, $page);
        $processor = new XSLTProcessor();
        $processor->importStylesheet($webFormat);
        $html = $processor->transformToXml($webXML);
        $elements = explode("_CONTENT_", $html);
        $content = (Authorisation::isAuthorised($attributes['rest'], $user)) ?
             $this->getContent($attributes) : "<p align=\"center\">Your privileges are insufficient to access this page.</p>";
        echo $elements[0];
        echo $content;
        echo $elements[1];
    }

    private function getErrorHTML($msg) {
        return(str_replace($this->error, $msg, $this->xmlError));
    }

    private function getPageName() {
        $method = $_SERVER['REQUEST_METHOD'];
        switch ($method) {
            case 'GET':
                return 'home';
                break;
            case 'PUT':
                return 'Uknown';
                break;
            case 'POST':
                return isset($_POST['LOGOUT']) ?  $_POST['LOGOUT'] : $_POST['page'];
                break;
            case 'DELETE':
                return 'uknown';
                break;
        }
    }

    private function getPageAttributes($webXML, $page) {
        $attributes = ['_STATUS_' => true];
        $pageElement = $webXML->xpath("/website/pages/page[@name='". $page ."']");
        if(!isset($pageElement[0])) {
            return ['_STATUS_' => false];
        }
        foreach($pageElement[0]->attributes() as $atrr => $value) {
            $attributes[$atrr] = $value->__toString();
        }
        return $attributes;
    }

    private function getContent($attributes) {
        if(! $attributes['_STATUS_']) {
            return "<p>Missing page definition!</p>";
        }
        $pageClassName = $attributes['class'];
        if(strlen($pageClassName) == 0) {
            return "Missing page class name!";
        }
        $classFileName = 'include/'. $pageClassName. '.php';
        if(!is_readable($classFileName)) {
            return "Missing class file!";
        }
        include $classFileName;
        $pageObject = new $pageClassName($attributes, $this->folder);
        if(!isset($pageObject)) {
            return "Missing object!";
        }
        if(!is_subclass_of($pageObject, 'BasePage')) {
            return "Wrong object class!";
        }
        $content = $pageObject->getContent();
        $object = null;
        return $content;
    }

    private function loadXML($file, $name, $params) {
        if(!is_readable($file)) {
            $this->lastMsg = "Cannot read a $name file!";
            return false;
        }
        $webCont = file_get_contents($file);
        if(strlen($webCont) == 0) {
            $this->lastMsg = "Empty $name file!";
            return false;
        }
        if($webCont === false) {
            $this->lastMsg = "Error while reading the $name file!";
            return false;
        }
        foreach($params as $key => $value) {
            $webCont = str_replace($key, $value, $webCont);
        }
        $xml = simplexml_load_string($webCont);
        if($xml === false) {
            $this->lastMsg = "Cannot process the $name file!";
            return false;
        }
        return $xml;
    }

    public function __destruct() {
        $this->folder = null;
        $this->webFile = null;
        $this->page = null;
        $this->lastMsg  = null;
        $this->session = null;
    }
}

?>
