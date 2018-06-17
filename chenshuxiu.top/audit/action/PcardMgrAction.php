<?php

// PcardMgrAction
class PcardMgrAction extends AuditBaseAction
{

    public function doList () {
        return self::SUCCESS;
    }

    public function doOne () {
        return self::SUCCESS;
    }

    public function doModify () {
        return self::SUCCESS;
    }

    public function doModifyPost () {
        return self::SUCCESS;
    }

    // 修改患者疾病
    public function doModifyDisease () {
        $pcardid = XRequest::getValue('pcardid', 0);
        $pcard = Pcard::getById($pcardid);
        DBC::requireNotEmpty($pcard, "pcard为空");
        $diseaseid = XRequest::getValue('diseaseid', 0);

        $diseaseid = $diseaseid == 0 ? $pcard->diseaseid : $diseaseid;

        $diseases = $pcard->doctor->getDiseases();

        XContext::setValue('pcard', $pcard);
        XContext::setValue('diseaseid', $diseaseid);
        XContext::setValue('diseases', $diseases);

        return self::SUCCESS;
    }

    // 修改患者疾病，提交
    public function doModifyDiseasePost () {
        $pcardid = XRequest::getValue('pcardid', 0);
        $pcard = Pcard::getById($pcardid);
        DBC::requireNotEmpty($pcard, "pcard为空");
        $diseaseid = XRequest::getValue('diseaseid', 0);
        $disease = Disease::getById($diseaseid);
        DBC::requireNotEmpty($disease, "疾病为空");

        $patient = $pcard->patient;

        $count = [];
        if ($diseaseid && $pcard->diseaseid != $diseaseid) {

            $patientid_diseaseidEntityClassNames = TableUtil::getEntityClassNameArray_with_patientid_diseaseid();

            foreach ($patientid_diseaseidEntityClassNames as $entityClassName) {
                if (! $entityClassName || $entityClassName == 'Pcard') {
                    continue;
                }

                $cond = " and patientid = :patientid and diseaseid = :diseaseid ";
                $bind = [];
                $bind[':patientid'] = $pcard->patientid;
                $bind[':diseaseid'] = $pcard->diseaseid;

                $entitys = Dao::getEntityListByCond($entityClassName, $cond, $bind);

                $i = 0;
                foreach ($entitys as $entity) {
                    $entity->set4lock('diseaseid', $diseaseid);
                    $i ++;
                }

                if ($i > 0) {
                    $count["{$entityClassName}"] = $i;
                }
            }

            if ($patient->diseaseid == $pcard->diseaseid && $patient->doctorid == $pcard->doctorid) {
                Debug::warn("[{$this->myauditor->name}] 将 patient[{$patient->name}]->diseaseid [{$pcard->diseaseid} => {$diseaseid}]");
                $patient->set4lock('diseaseid', $diseaseid);
            }

            Debug::warn("[{$this->myauditor->name}] 将 patient[{$patient->name}]->pcard[{$pcard->id}]->diseaseid [{$pcard->diseaseid} => {$diseaseid}]");

            $pcard->set4lock('diseaseid', $diseaseid);
        }

        $countstr = "";

        $sum = 0;
        foreach ($count as $k => $v) {
            $sum += $v;

            $countstr .= "[{$k}=>{$v}条]   \n";
        }

        $preMsg = '修改成功,共修改数据[' . $sum . ']条,明细 : ' . $countstr;
        XContext::setJumpPath("/pcardmgr/modifydisease?pcardid={$pcardid}&preMsg=" . urlencode($preMsg));

        return self::SUCCESS;
    }

    public function doMergePost () {
        $pcardid = XRequest::getValue('pcardid', 0);

        DBC::requireNotEmpty($pcardid, 'pcardid is null');
        $pcard = Pcard::getById($pcardid);

        $keys = XRequest::getValue('keys', array());
        $values = XRequest::getValue('values', array());

        foreach ($keys as $i => $a) {
            $pcard->$a = $values[$i];
        }

        echo 'success';
        return self::BLANK;
    }

    public function doModifydiseasenameshowJson () {
        $pcardid = XRequest::getValue('pcardid', 0);
        $diseasename_show = XRequest::getValue('diseasename_show', '');

        $pcard = Pcard::getById($pcardid);
        DBC::requireNotEmpty($pcard, "pcard为空");

        $pcard->diseasename_show = $diseasename_show;

        echo "ok";

        return self::BLANK;
    }
}
