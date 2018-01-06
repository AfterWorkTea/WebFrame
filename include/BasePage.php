<?php

class BasePage {

private $attributes = null;
private $folder = null;

	public function __construct($attributes, $folder) {
		$this->attributes = $attributes;
		$this->folder = $folder;
	}

    public function getAttribute($name) {
		return $this->attributes[$name];
	}

	public function getPageName() {
		return $this->getAttribute('name');
	}

    public function getContent() {
		return "<p>Done ". $this->getPageName(). "</p>";
	}

	public function __destruct() {
		$this->xslt = null;
    }

	public function processXML($params, $xmlString) {
		$file = $this->folder. $this->getAttribute('format');
        if(! is_readable($file)) {
			return "<p>Cannot read page formater!</p>";
	    }
		$formatContent = file_get_contents($file);
		if(strlen($formatContent) == 0) {
			return "<p>Empty page formater!</p>";
		}
		foreach($params as $key => $value) {
			$formatContent = str_replace($key, $value, $formatContent);
		}
		$xslt = simplexml_load_string($formatContent);
		if($xslt === false) {
			return "<p>Wrong page formater!</p>";
		}
		$xml = simplexml_load_string($xmlString);
		if($xml === false) {
			return "<p>Wrong page data!</p>";
		}
		$processor = new XSLTProcessor();
		$processor->importStylesheet($xslt);
        $html = $processor->transformToXml($xml);
        $processor = null;
        return $html;
    }

}

?>
