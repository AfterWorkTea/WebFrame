<?php

include "include/WebDB.php";
include "include/BasePage.php";

class ZooPage extends BasePage {

private $dataXML = <<<XML
<data>
    _GROUPS_
    <table>
        <columns>
            <entry><name>Name</name><sort>N</sort></entry>
            <entry><name>Group</name><sort>G</sort></entry>
            <entry><name>Count</name><sort>C</sort></entry>
            <entry><name>Length</name><sort>L</sort></entry>
        </columns>
        _TABLE_
    </table>
    <buttons count="_COUNT_" from="_FROM_" more="_MORE_" to="_TO_" prev="_PREV_" next="_NEXT_" />
</data>
XML;

private $sort = null;
private $gid = null;
private $from = 0;
private $pagesize = 5;

    public function __construct($attributes, $folder) {
		parent::__construct($attributes, $folder);
        $this->sort = isset($_POST['sort']) ? strtoupper($_POST['sort']) : "N";
        $this->gid = isset($_POST['gid']) ? (int)$_POST['gid'] : 0;
        if (isset($_POST['button_back'])) {
            $this->from = (isset($_POST['BCK']) ? (int)$_POST['BCK'] : 0);
        }
        if (isset($_POST['button_next'])) {
            $this->from = (isset($_POST['FRD']) ? (int)$_POST['FRD'] : 0);
        }
    }

    public function getContent() {
		$db = new WebDB();
		$respGroup = $db->getGroup();
		$respTable = $db->getZoo($this->sort, $this->gid, $this->from, $this->pagesize);
		$db = null;
		if(strcasecmp($respGroup["S"], "OK") != 0) {
			return "Get group issue: ". $respGroup["R"];
	    }
	    if(strcasecmp($respTable["S"], "OK") != 0) {
			return "Get table issue: ". $respTable["R"];
	    }
		$xml = $this->getXML($respGroup["R"], $respTable["R"], $this->getCount($respTable["R"]));
		return parent::processXML(["_gid_" => "".$this->gid, "_sort_" => $this->sort], $xml);
	}

	private function getCount($tableXML) {
		$xmlElement = new SimpleXMLElement($tableXML);
        $count = $xmlElement->attributes()['count'];
        return $count;
	}

    private function getXML($groupXML, $tableXML, $count) {
        $prev = $this->from - $this->pagesize;
        $next = $this->from + $this->pagesize;
        $to = ($next > $count) ? $count : $next;
        $more = $count - $to;
        $dataXML = str_replace("_GROUPS_", $groupXML, $this->dataXML);
        $dataXML = str_replace("_TABLE_", $tableXML, $dataXML);
        $dataXML = str_replace("_COUNT_", $count, $dataXML);
        $dataXML = str_replace("_FROM_", $this->from, $dataXML);
        $dataXML = str_replace("_MORE_", $more, $dataXML);
        $dataXML = str_replace("_TO_", $to, $dataXML);
        $dataXML = str_replace("_PREV_", $prev, $dataXML);
        $dataXML = str_replace("_NEXT_", $next, $dataXML);
        return $dataXML;
    }

}

?>
