<?php
// DoctorMedicinePkgMgrAction
class DoctorMedicinePkgMgrAction extends AuditBaseAction
{

    public function doList () {
        $doctorid = XRequest::getValue('doctorid', 0);
        $doctor_name = XRequest::getValue('doctor_name', '');

        XContext::setValue('doctorid', $doctorid);
        XContext::setValue('doctor_name', $doctor_name);

        $cond = "";
        $bind = [];

        if ($doctorid) {
            $cond .= " and doctorid = :doctorid ";
            $bind[':doctorid'] = $doctorid;
        }

        $cond .= " order by pos , id ";

        $doctormedicinepkgs = Dao::getEntityListByCond('DoctorMedicinePkg', $cond, $bind);

        XContext::setValue('doctormedicinepkgs', $doctormedicinepkgs);

        return self::SUCCESS;
    }

    public function doPosModifyPost () {
        $doctorid = XRequest::getValue('doctorid', 0);
        $posArray = XRequest::getValue('pos', array());

        foreach ($posArray as $doctormedicinepkgid => $pos) {
            $doctormedicinepkg = DoctorMedicinePkg::getById($doctormedicinepkgid);
            $doctormedicinepkg->pos = $pos;
        }

        XContext::setJumpPath("/doctormedicinepkgmgr/list?doctorid={$doctorid}");

        return self::SUCCESS;
    }

    public function doOne () {
        return self::SUCCESS;
    }

    public function doAdd () {
        $doctorid = XRequest::getValue('doctorid', 0);

        XContext::setValue('doctorid', $doctorid);

        return self::SUCCESS;
    }

    public function doAddPost () {
        $doctorid = XRequest::getValue('doctorid', 0);
        $name = XRequest::getValue('name', '');

        DBC::requireNotEmpty($this->mydisease, "必须选疾病");
        $diseaseid = $this->mydisease->id;

        $row = array();
        $row['doctorid'] = $doctorid;
        $row['diseaseid'] = $diseaseid;
        $row['name'] = $name;

        DoctorMedicinePkg::createByBiz($row);

        XContext::setJumpPath("/doctormedicinepkgmgr/list?doctorid={$doctorid}");

        return self::SUCCESS;
    }

    // 链接还没加
    public function doDeletePost () {
        $doctormedicinepkgid = XRequest::getValue('doctormedicinepkgid', 0);

        $doctormedicinepkg = DoctorMedicinePkg::getById($doctormedicinepkgid);

        $doctormedicinepkg->remove();

        XContext::setJumpPath("/doctormedicinepkgmgr/list?doctorid={$doctormedicinepkg->doctorid}");

        return self::SUCCESS;
    }

    public function doModify () {
        $doctormedicinepkgid = XRequest::getValue('doctormedicinepkgid', 0);

        $doctormedicinepkg = DoctorMedicinePkg::getById($doctormedicinepkgid);

        XContext::setValue('doctormedicinepkg', $doctormedicinepkg);
        return self::SUCCESS;
    }

    public function doModifyPost () {
        $doctormedicinepkgid = XRequest::getValue('doctormedicinepkgid', 0);
        $diseaseid = XRequest::getValue('diseaseid', 0);
        $disease = Disease::getById($diseaseid);
        DBC::requireNotEmpty($disease, "疾病为空");

        $name = XRequest::getValue('name', '');

        $doctormedicinepkg = DoctorMedicinePkg::getById($doctormedicinepkgid);
        $doctormedicinepkg->diseaseid = $diseaseid;
        $doctormedicinepkg->name = $name;

        XContext::setJumpPath("/doctormedicinepkgmgr/modify?doctormedicinepkgid={$doctormedicinepkgid}");

        return self::SUCCESS;
    }
}
