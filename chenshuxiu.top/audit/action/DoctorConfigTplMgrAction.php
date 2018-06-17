<?php
// DoctorConfigTplMgrAction
class DoctorConfigTplMgrAction extends AuditBaseAction
{

    public function doList () {
        $doctorconfigtpls = Dao::getEntityListByCond("DoctorConfigTpl");

        XContext::setValue("doctorconfigtpls", $doctorconfigtpls);
        return self::SUCCESS;
    }

    public function doAdd () {
        return self::SUCCESS;
    }

    public function doAddPost () {
        $title = XRequest::getValue('title', '');
        $code = XRequest::getValue('code', '');
        $groupstr = XRequest::getValue('groupstr', '');
        $brief = XRequest::getValue('brief', '');

        $row = array();
        $row["title"] = $title;
        $row["code"] = $code;
        $row["groupstr"] = $groupstr;
        $row["brief"] = $brief;

        $doctorConfigTpl = DoctorConfigTpl::createByBiz($row);
        XContext::setJumpPath("/doctorconfigtplmgr/add");

        return self::BLANK;
    }

    public function doModify () {
        $doctorconfigtplid = XRequest::getValue('doctorconfigtplid', 0);

        $doctorconfigtpl = DoctorConfigTpl::getById($doctorconfigtplid);
        XContext::setValue("doctorconfigtpl", $doctorconfigtpl);

        return self::SUCCESS;
    }

    public function doModifyPost () {
        $doctorconfigtplid = XRequest::getValue('doctorconfigtplid', 0);
        $title = XRequest::getValue("title", '');
        $code = XRequest::getValue("code", '');
        $groupstr = XRequest::getValue("groupstr", '');
        $brief = XRequest::getValue("brief", '');

        $doctorconfigtpl = DoctorConfigTpl::getById($doctorconfigtplid);
        $doctorconfigtpl->title = $title;
        $doctorconfigtpl->code = $code;
        $doctorconfigtpl->groupstr = $groupstr;
        $doctorconfigtpl->brief = $brief;

        XContext::setJumpPath("/doctorconfigtplmgr/modify?doctorconfigtplid={$doctorconfigtplid}&preMsg=" . urlencode('修改已保存'));

        return self::BLANK;
    }

    public function doPosModifyPost () {
        $posArray = XRequest::getValue('pos', array());

        foreach ($posArray as $doctorconfigtplid => $pos) {
            $doctorconfigtpl = DoctorConfigTpl::getById($doctorconfigtplid);
            $doctorconfigtpl->pos = $pos;
        }

        XContext::setJumpPath("/doctorconfigtplmgr/list");

        return self::SUCCESS;
    }

    public function doGenerateConfig () {
        $create_cnt = 0;

        $doctorconfigtpls = Dao::getEntityListByCond("DoctorConfigTpl");
        $doctorconfigtplcnt = count($doctorconfigtpls);

        $sql = "select id from doctors";
        $doctorids = Dao::queryValues($sql, []);

        foreach ($doctorids as $doctorid) {
            $sql = "select count(*) from doctorconfigs where doctorid = :doctorid ";
            $bind = [];
            $bind[':doctorid'] = $doctorid;
            $doctorconfigcnt = Dao::queryValue($sql, $bind);

            if ($doctorconfigtplcnt == $doctorconfigcnt) {
                continue;
            }

            foreach ($doctorconfigtpls as $doctorconfigtpl) {
                $doctorconfig = DoctorConfigDao::getByDoctoridDoctorConfigTplid($doctorid, $doctorconfigtpl->id);

                if (false == $doctorconfig instanceof DoctorConfig) {
                    $row = array();
                    $row['doctorid'] = $doctorid;
                    $row['doctorconfigtplid'] = $doctorconfigtpl->id;

                    $doctorconfig = DoctorConfig::createByBiz($row);
                    $create_cnt ++;
                }
            }
        }

        $preMsg = "已生成{$create_cnt}";
        XContext::setJumpPath("/doctorconfigtplmgr/list?preMsg=" . urlencode($preMsg));
        return self::BLANK;
    }
}
