<?php

class Session {

	private $enabled = false;
	private $elapsed = 0;
	private $timeout = 0;
	private $page = '';

	public function Session($enabled = false) {
		$this->enabled = $enabled;
	}

	public function setSession($webXML) {
		$session = $webXML->attributes()['session'];
		$this->enabled = (strcasecmp($session, 'YES') == 0);
		if($this->enabled) {
			session_start();
			if(isset($_POST['LOGOFF'])) {
				session_unset();
			}
			$this->elapsed = (isset($_SESSION['LAST_ACTIVITY'])) ? (time() - $_SESSION['LAST_ACTIVITY']) : 0;
			$_SESSION['LAST_ACTIVITY'] = time();
			$this->timeout = $webXML->session->attributes()['timeout'];
			$this->page = ''. $webXML->session;
		}
	}

	public function getPage($page) {
		if(!$this->enabled) {
			return $page;
		}
		if($this->elapsed > $this->timeout) {
			 session_unset();
			return $this->page;
		}
		if(!isset($_SESSION['user'])) {
			 session_unset();
			return $this->page;
		}
		return $page;
	}

	public function getUser() {
		if($this->enabled) {
			return isset($_SESSION['user']) ? $_SESSION['user'] : 'Guest';
		} else {
			return 'Guest';
		}
	}

	private function validSession() {
        return (isset($_SESSION['user']) and ($this->elapsed < 1*60)); //1 x 60 [sec]
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
       $format = $webXML->attributes()['format'];
       $this->session->setSession($webXML);
       $this->page = $this->session->getPage($this->page);
       $user = $this->session->getUser();
       $webFormat = $this->loadXML($this->folder. $format, 'format', ["_PAGE_" => $this->page, "_USER_" => $user]);
       if($webFormat === false) {
		   echo $this->getErrorHTML($this->lastMsg);
		   return;
	   }
	   $attributes = $this->getPageAttributes($webXML, $this->page);
       $processor = new XSLTProcessor();
       $processor->importStylesheet($webFormat);
       $html = $processor->transformToXml($webXML);
       $elements = explode("_CONTENT_", $html);
       echo $elements[0];
       echo $this->getContent($attributes);
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
                return $_POST['page'];
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
