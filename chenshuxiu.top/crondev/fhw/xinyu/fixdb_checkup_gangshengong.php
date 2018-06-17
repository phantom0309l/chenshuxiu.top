<!-- $sql = "select a.*
    from checkups a
    LEFT JOIN patients b on b.id = a.patientid
    LEFT JOIN checkuptpls c on c.id = a.checkuptplid
    where c.id = 104372245
    and b.id in (105623521,105621207,103998911,103349551)
    order by a.patientid,a.check_date";

$checkups = Dao::loadEntityList('Checkup', $sql);

$arr = array();

$i = 0;
foreach ($checkups as $checkup) {
    $arr[$i]['name'] = $checkup->patient->name;
    $arr[$i]['thedate'] = $checkup->check_date;

    $xquestions = $this->getXquestions("肝肾功");
    foreach ($xquestions as $a) {
        $arr[$i]["{$a->content}"] = $this->getAanswer($a->id, $checkup->xanswersheetid);
    }

    $i++;
} -->
