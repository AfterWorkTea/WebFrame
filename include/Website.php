<?php

class Website {

private $error = "_ERROR_";
private $xmlError = <<<XML
<html><body><p>_ERROR_</pp></body></html>
XML;

    private $folder = 'include/data/';
    private $webFile = null;
    private $page = null;
    private $lastMsg = null;

    public function Website($webFile) {
        $this->webFile = $webFile;
        $this->page = $this->getPageName();
    }

    public function run() {
       $webXML = $this->loadXML($this->folder. $this->webFile, 'website', ["_PAGE_" => $this->page]);
       if($webXML === false) {
		   echo $this->getErrorHTML($this->lastMsg);
		   return;
	   }
       $format = $webXML->attributes()['format'];
       $webFormat = $this->loadXML($this->folder. $format, 'format', []);
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
    }

}

?>
