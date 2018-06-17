<?php
// DoctorMemoMgrAction
class DoctorMemoMgrAction extends AuditBaseAction
{

    public function doList () {
        $diseaseidstr = $this->getContextDiseaseidStr();

        $sql = "select dm.* from doctormemos dm
            inner join doctors d on d.id=dm.doctorid
            inner join doctordiseaserefs ddr on ddr.doctorid=d.id
            where ddr.diseaseid in ({$diseaseidstr}) and dm.thedate>=:thedate
            order by thedate asc";

        $bind = [];
        $today = date("Y-m-d");
        $bind[':thedate'] = "{$today}";

        $doctormemos = Dao::loadEntityList('DoctorMemo', $sql, $bind);

        XContext::setValue("doctormemos", $doctormemos);

        return self::SUCCESS;
    }

    public function doChangeStatusPost () {
        $doctormemoid = XRequest::getValue("doctormemoid", 0);
        $doctormemo = DoctorMemo::getById($doctormemoid);

        $doctormemo->status = 1 - $doctormemo->status;
        XContext::setJumpPath("/doctormemomgr/list");

        return self::BLANK;
    }

}
