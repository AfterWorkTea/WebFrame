<?php

include "include/BasePage.php";

class HomePage extends BasePage {

private $xmlStr = <<<XML
<data legend='Welcome'>Welcome to demo home!</data>
XML;

    public function getContent() {
		return parent::processXML([], $this->xmlStr);
	}

}

?>
