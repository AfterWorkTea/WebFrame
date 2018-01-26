<?php

include "include/BasePage.php";

class AdminPage extends BasePage {

private $xmlStr = <<<XML
<data legend='Admin'>Here is the admin page!</data>
XML;

    public function getContent() {
        return parent::processXML([], $this->xmlStr);
    }

}

?>
