<?php

include "include/BasePage.php";


class CreditsPage extends BasePage {

private $xmlStr = <<<XML
<data legend='Credits'>Desined and developed by Robert Berlinski</data>
XML;

    public function getContent() {
		return parent::processXML([], $this->xmlStr);
	}

}

?>
