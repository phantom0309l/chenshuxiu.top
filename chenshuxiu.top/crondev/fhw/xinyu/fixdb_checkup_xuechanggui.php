<!-- $sql = "select a.*
    from checkups a
    LEFT JOIN patients b on b.id = a.patientid
    LEFT JOIN checkuptpls c on c.id = a.checkuptplid
    where c.id = 104371749
    and b.id in (105623521,105621207,103998911,103349551)
    order by a.patientid,a.check_date";

$checkups = Dao::loadEntityList('Checkup', $sql);

$arr = array();

$i = 0;
foreach ($checkups as $checkup) {
    $xquestionid_WBC = 104372063;
    $xquestionid_N = 104372187;
    $xquestionid_L = 104372201;
    $xquestionid_Hb = 104372211;
    $xquestionid_PLT = 104372219;

    $arr[$i]['name'] = $checkup->patient->name;
    $arr[$i]['thedate'] = $checkup->check_date;
    $arr[$i]['WBC'] = $this->getAanswer($xquestionid_WBC, $checkup->xanswersheetid);
    $arr[$i]['N'] = $this->getAanswer($xquestionid_N, $checkup->xanswersheetid);
    $arr[$i]['L'] = $this->getAanswer($xquestionid_L, $checkup->xanswersheetid);
    $arr[$i]['Hb'] = $this->getAanswer($xquestionid_Hb, $checkup->xanswersheetid);
    $arr[$i]['PLT'] = $this->getAanswer($xquestionid_PLT, $checkup->xanswersheetid);

    $i++;
} -->
