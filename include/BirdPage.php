<?php

include_once  'WebDB.php';

class BirdPage {

   public function BirdPage() {
   }
   
    public function getXSLT($xslt) {        
        $xslt = str_replace("_group_", "Birds", $xslt);
        return $xslt;
    }

    public function getXML() {
        $db = new WebDB();
        $resp = $db->getAnimal($_POST['prms']);
        echo htmlspecialchars($resp, ENT_XML1);
        $db = null;
        return $resp;
    }

}

?>
