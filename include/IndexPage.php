<?php

class IndexPage {

private $xml = <<<XML
<data legend='Welcome'>Welcome to demo home!</data>
XML;

   public function IndexPage() {
   }
   
    public function getXSLT($xslt) {        
        return $xslt;
    }

    public function getXML() {
        return ['R' => $this->xml, 'S' => 'OK'];
    }

}

?>
