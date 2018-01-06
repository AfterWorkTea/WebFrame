<?php

include "include/WebDB.php";
include "include/BasePage.php";

class AnimalPage extends BasePage {

    public function getContent() {
		$grpup = parent::getAttribute('group');
        $db = new WebDB();
        $resp = $db->getAnimal($_POST['parm']);
        $db = null;
        if(strcasecmp($resp["S"], "OK") != 0) {
			return "Get data issue: ". $resp["R"];
	    }
        return parent::processXML(["_group_" => $grpup], $resp["R"]);
    }

}

?>
