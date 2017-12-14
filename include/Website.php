<?php

class Website {

private $error = "_ERROR_";
private $xmlError = <<<XML
<html><body><p>_ERROR_</pp></body></html>
XML;

    private $folder = null;
    private $webDef = null;
    private $xmlDef = null;
    private $webData = null;
    private $webFormat = null;
    private $pages = null;

    public function Website($folder, $webDef) {
        $this->folder = $folder;
        $this->webDef = $webDef;
        $this->pages = [];
        $this->pageNames = [];
    }

    public function run() {
       $this->xmlDef = $this->loadXML($this->folder. '/'. $this->webDef, 'website');
       $this->processWebDef();
       $this->processRequest();
    }

    private function fatalError($msg) {
        exit(str_replace($this->error, $msg, $this->xmlError));
    }

    private function processWebDef() {
        $this->webData = $this->loadXML($this->folder. '/'. $this->xmlDef->attributes()['data'], 'data');
        $this->webFormat = $this->loadXML($this->folder. '/'. $this->xmlDef->attributes()['format'], 'format');
        $this->loadPages();
    }

    private function echoPage($page) {
        $processor = new XSLTProcessor();
        $processor->importStylesheet($this->webFormat);
        $html = $processor->transformToXml($this->webData);
        if(!isset($page)) {
            echo str_replace("_CONTENT_", "Unset request!", $html);
            $processor = null;
            return;
        }
        if(empty($page)) {
            echo str_replace("_CONTENT_", "Empty request!", $html);
            $processor = null;
            return;
        }
        if(!in_array($page, $this->pageNames)) {
            echo str_replace("_CONTENT_", "Unknown request [$page]", $html);
            $processor = null;
            return;
        }
        $this->formatPage($html, $page, $processor);
        $processor = null;
    }

    private function formatPage($html, $page, $processor) {
        $type = ''. $this->pages[$page]['T'];
        $xslt = ''. $this->pages[$page]['F'];
        if(is_null($type) || empty($type)) {
            echo str_replace("_CONTENT_", "Cannot format page [$page] due to missing type!", $html);
            return;
        }
        if(is_null($xslt) || empty($xslt)) {
            echo str_replace("_CONTENT_", "Cannot format page [$page] due to missing foramt!", $html);
            return;
        }
        $file = 'pages/'. $xslt;
        if(!is_readable($file)) {
            echo str_replace("_CONTENT_", "Cannot format page [$page] due to missing format file!", $html);
            return;
        }
        $xsltStr = file_get_contents($file);
        if($xsltStr === false) {
            echo str_replace("_CONTENT_", "Cannot format page [$page] due to an issue while reading format file!", $html);
            return;
        }
        $file = 'include/'. $type. '.php';
        if(!is_readable($file)) {
            $object = null;
            echo str_replace("_CONTENT_", "Cannot format page [$page] due to missing object file!", $html);
            return;
        }
        include $file;
        $object = new $type();
        $xsltStr = $object->getXSLT($xsltStr);
        $xsltObj = simplexml_load_string($xsltStr);
        if($xsltObj === false) {
            $object = null;
            echo str_replace("_CONTENT_", "Cannot format page [$page] due to an issue while processing format file!", $html);
            return;
        }                
        $xmlResp = $object->getXML();        
        $object = null;
        //
        //echo str_replace("_CONTENT_", "Format page [$page]:". htmlspecialchars($xmlResp['R'], ENT_XML1), $html);
        //return;
        //
        if(strcasecmp($xmlResp["S"], "OK") != 0) {
            echo str_replace("_CONTENT_", "Cannot format page [$page] due data issue: ". $xmlResp["S"], $html);
            return;
        }
        $xmlObj = simplexml_load_string($xmlResp['R']);
        if($xmlObj === false) {
            echo str_replace("_CONTENT_", "Cannot format page [$page] due to an issue while processing data!", $html);
            return;
        }
        $processor->importStylesheet($xsltObj);
        $insert = $processor->transformToXml($xmlObj);
        echo str_replace("_CONTENT_", $insert, $html);
    }

    private function processRequest() {
        $method = $_SERVER['REQUEST_METHOD'];
        switch ($method) {
            case 'GET':
                $this->echoPage('index');
                break;
            case 'PUT':
                echo "Uknown Request";
                break;
            case 'POST':
                $this->echoPage($_POST['page']);
                break;
            case 'DELETE':
                echo "Uknown Request";
                break;
        }
    }

    private function loadPages() {
        foreach ($this->xmlDef->page as $page) {
            $name = ''. $page->attributes()['name'];
            $type = ''. $page->content[0];
            $xslt = ''. $page->content[0]->attributes()['xslt'];
            $this->pageNames[] = $name;
            $this->pages[$name] = ['T' => $type, 'F' => $xslt];
        }
    }

    private function loadXML($file, $name) {
        if(!is_readable($file)) {
            $this->fatalError("Cannot read a $name file!");
        }
        $webCont = file_get_contents($file);
        if($webCont === false) {
            $this->fatalError("Error while reading the $name file!");
        }
        $xml = simplexml_load_string($webCont);
        if($xml === false) {
            $this->fatalError("Cannot process the $name file!");
        }
        return $xml;
    }

    public function __destruct() {
        $this->xmlDef = null;
        $this->webData = null;
        $this->webFormat = null;
        $this->pages = null;
        $this->pageNames = null;
    }

}

?>
