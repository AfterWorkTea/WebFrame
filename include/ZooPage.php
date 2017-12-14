<?php

include_once  'WebDB.php';

class ZooPage {

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

    public function ZooPage() {
        $this->sort = isset($_POST['sort']) ? strtoupper($_POST['sort']) : "N";
        $this->gid = isset($_POST['gid']) ? (int)$_POST['gid'] : 0;
        if (isset($_POST['button_back'])) {
            $this->from = (isset($_POST['BCK']) ? (int)$_POST['BCK'] : 0);
        }
        if (isset($_POST['button_next'])) {
            $this->from = (isset($_POST['FRD']) ? (int)$_POST['FRD'] : 0);
        }
    }

    public function getXSLT($xslt) {
        $xslt = str_replace("_gid_", "".$this->gid, $xslt);
        $xslt = str_replace("_sort_", $this->sort, $xslt);
        return $xslt;
    }

    public function getXML() {
        $from = $this->from;
        $pagesize = 5;
        $db = new WebDB();
        $respGr = $db->getGroup();
        if(strcasecmp($respGr["S"], "OK") != 0) {
            $db = null;
            return $respGr;
        }
        $respZ = $db->getZoo($this->sort, $this->gid, $from, $pagesize);
        $db = null;
        if(strcasecmp($respZ["S"], "OK") != 0) {
            return $respZ;
        }
        $xmlElement = new SimpleXMLElement($respZ['R']);
        $count = $xmlElement->attributes()['count'];
        $prev = $from - $pagesize;
        $next = $from + $pagesize;
        $to = ($next > $count) ? $count : ($next);
        $more = $count - $to;        
        $dataXML = str_replace("_GROUPS_", $respGr['R'], $this->dataXML);
        $dataXML = str_replace("_TABLE_", $respZ['R'], $dataXML);        
        $dataXML = str_replace("_COUNT_", $count, $dataXML);
        $dataXML = str_replace("_FROM_", $from, $dataXML);
        $dataXML = str_replace("_MORE_", $more, $dataXML);
        $dataXML = str_replace("_TO_", $to, $dataXML);
        $dataXML = str_replace("_PREV_", $prev, $dataXML);
        $dataXML = str_replace("_NEXT_", $next, $dataXML);
        return ['R' => $dataXML, 'S' => 'OK'];
    }

}

?>
