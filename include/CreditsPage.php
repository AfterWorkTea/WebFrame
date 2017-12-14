<?php

class CreditsPage {

private $xml = <<<XML
<data legend='Credits'>Desined and developed by Robert Berlinski</data>
XML;

   public function CreditsPage() {
   }
   
    public function getXSLT($xslt) {        
        return $xslt;
    }

    public function getXML() {
        return ['R' => $this->xml, 'S' => 'OK'];
    }

}

?>
