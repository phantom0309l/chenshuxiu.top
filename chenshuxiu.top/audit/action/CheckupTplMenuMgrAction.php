<?php

class CheckupTplMenuMgrAction extends AuditBaseAction
{

    // 数据库菜单(医生)新建
    public function doAdd() {
        $doctorid = XRequest::getValue('doctorid', '');
        $diseaseid = XRequest::getValue('diseaseid', '');

        $doctor = Dao::getEntityById('Doctor', $doctorid);
        DBC::requireNotEmpty($doctor, "医生为空");
        $disease = Dao::getEntityById('Disease', $diseaseid);
        DBC::requireNotEmpty($disease, "疾病为空");

        XContext::setValue('doctor', $doctor);
        XContext::setValue('disease', $disease);
        return self::SUCCESS;
    }

    public function doAddPost() {
        $diseaseid = XRequest::getValue('diseaseid', '');

        $doctorid = XRequest::getValue('doctorid', '');
        $cond = ' AND diseaseid=:diseaseid ';
        $bind[':diseaseid'] = $diseaseid;
        $checkupTplMenu = Dao::getEntityByCond('CheckupTplMenu', $cond, $bind);
        DBC::requireNotEmpty($checkupTplMenu, '请先创建疾病菜单');
        $row = array();
        $row['diseaseid'] = $diseaseid;
        $row['doctorid'] = $doctorid;
        $row['content'] = $checkupTplMenu->content;

        CheckupTplMenu::createByBiz($row);
        XContext::setJumpPath('/doctordbmgr/index');

    }

    // 数据库菜单(医生)修改
    public function doModify() {
        $checkuptplmenuid = XRequest::getValue('checkuptplmenuid', '');
        $diseaseid = XRequest::getValue('diseaseid', '');

        $doctorid = XRequest::getValue('doctorid', '');
        if ($checkuptplmenuid) {
            $checkupTplMenu = Dao::getEntityById('CheckupTplMenu', $checkuptplmenuid);
        } else {
            $cond = ' AND doctorid=:doctorid AND diseaseid=:diseaseid';
            $bind = array(
                ':doctorid' => $doctorid,
                ':diseaseid' => $diseaseid,
            );
            $checkupTplMenu = Dao::getEntityByCond('CheckupTplMenu', $cond, $bind);
        }

        XContext::setValue('checkupTplMenu', $checkupTplMenu);
        return self::SUCCESS;
    }

    public function doModifyPost() {
        $checkuptplmenuid = XRequest::getValue('checkuptplmenuid', 0);
        $content = XRequest::getUnSafeValue('content', '');
        $simple_content = XRequest::getUnSafeValue('simple_content', '');
        $checkupTplMenu = Dao::getEntityById('CheckupTplMenu', $checkuptplmenuid);
        $checkupTplMenu->content = $content;
        $checkupTplMenu->simple_content = $simple_content;

        XContext::setJumpPath('/checkuptplmenumgr/modify?checkuptplmenuid=' . $checkuptplmenuid);
        return self::SUCCESS;
    }

    //////////////////////////
    //     疾病菜单         //
    /////////////////////////
    public function doAddDisease() {
        return self::SUCCESS;
    }

    public function doAddDiseasePost() {
        $diseaseid = XRequest::getValue('diseaseid', 0);
        $disease = Disease::getById($diseaseid);
        DBC::requireNotEmpty($disease, "疾病为空");

        $cond = ' AND diseaseid=:diseaseid ';
        $bind[':diseaseid'] = $diseaseid;
        $checkupTplMenu = Dao::getEntityByCond('CheckupTplMenu', $cond, $bind);
        if ($checkupTplMenu) {
            XContext::setJumpPath('/checkuptplmenumgr/listdisease?preMsg=' . urlencode('疾病菜单已存在，不能重复添加'));
            return self::SUCCESS;
        }
        $content = XRequest::getUnSafeValue('content', '');

        $row = array();
        $row['diseaseid'] = $diseaseid;
        $row['content'] = $content;

        CheckupTplMenu::createByBiz($row);

        XContext::setJumpPath('/checkuptplmenumgr/listdisease');

        return self::SUCCESS;
    }

    public function doModifyDisease() {
        $checkuptplmenuid = XRequest::getValue('checkuptplmenuid', 0);
        $checkupTplMenu = Dao::getEntityById('CheckupTplMenu', $checkuptplmenuid);

        XContext::setValue('checkupTplMenu', $checkupTplMenu);
        return self::SUCCESS;
    }

    public function doModifyDiseasePost() {
        $checkuptplmenuid = XRequest::getValue('checkuptplmenuid', 0);
        $content = XRequest::getUnSafeValue('content', '');
        $checkupTplMenu = Dao::getEntityById('CheckupTplMenu', $checkuptplmenuid);
        $checkupTplMenu->content = $content;

        XContext::setJumpPath('/checkuptplmenumgr/modifydisease?checkuptplmenuid=' . $checkuptplmenuid);
        return self::SUCCESS;
    }

    // 数据库菜单(疾病)列表
    public function doListDisease() {
        $diseaseid = XRequest::getValue('diseaseid', '');
        $cond = '';
        $bind = [];
        if ($diseaseid) {
            $cond .= ' AND diseaseid=:diseaseid ';
            $bind[':diseaseid'] = $diseaseid;
        }
        $cond .= ' AND doctorid=0';
        $checkupTplMenus = Dao::getEntityListByCond('CheckupTplMenu', $cond, $bind);
        XContext::setValue('checkupTplMenus', $checkupTplMenus);
        return self::SUCCESS;
    }

    public function doCheckupTplOfDiseaseJson() {
        $diseaseid = XRequest::getValue('diseaseid', 0);

        $cond = ' AND diseaseid=:diseaseid AND doctorid=0';
        $bind = array(
            ':diseaseid' => $diseaseid,
        );
        $checkuptpls = Dao::getEntityListByCond('CheckupTpl', $cond, $bind);
        $checkupTplArr = array();
        foreach ($checkuptpls as $checkuptpl) {
            $tmp = array();
            $tmp['ename'] = $checkuptpl->ename;
            $tmp['title'] = $checkuptpl->title;
            $checkupTplArr[] = $tmp;
        }
        XContext::setValue('json', $checkupTplArr);
        return self::TEXTJSON;
    }

    private function getAllMenus($doctorid) {
        $cond = ' AND doctorid = :doctorid AND ename != "" ';
        $bind = [
            ':doctorid' => $doctorid,
        ];
        $checkuptpls = Dao::getEntityListByCond('CheckupTpl', $cond, $bind);
        $checkupTplArr = array();
        foreach ($checkuptpls as $checkuptpl) {
            $tmp = array();
            $tmp['id'] = "{$checkuptpl->title}-_-{$checkuptpl->ename}";
            $tmp['text'] = "{$checkuptpl->title}({$checkuptpl->ename})";
            $checkupTplArr[$checkuptpl->ename . $checkuptpl->title] = $tmp;
        }
        $tmp = [
            'id' => '治疗-_-zhiliao',
            'text' => '治疗(固有)(zhiliao)',
        ];
        $tmp1 = [
            'id' => '日常服用药物-_-richangfuyongyaowu',
            'text' => '日常服用药物(固有)(richangfuyongyaowu)',
        ];
        $checkupTplArr["zhiliao治疗"] = $tmp;
        $checkupTplArr["richangfuyongyaowu日常服用药物"] = $tmp1;
        return $checkupTplArr;
    }

    // 数据库菜单(医生)新建
    public function doListOfDoctor() {
        $doctorid = XRequest::getValue("doctorid", 0);
        $doctor = Doctor::getById($doctorid);
        DBC::requireTrue($doctor instanceof Doctor, '医生不存在');

        $checkuptplmenus = CheckupTplMenuDao::getListByDoctorid($doctorid);

        XContext::setValue('doctor', $doctor);
        XContext::setValue('checkuptplmenus', $checkuptplmenus);
        return self::SUCCESS;
    }

    // 数据库菜单(医生)新建
    public function doAddOfDoctor() {
        $doctorid = XRequest::getValue("doctorid", 0);
        $doctor = Doctor::getById($doctorid);
        DBC::requireTrue($doctor instanceof Doctor, '医生不存在');

        $diseases = $doctor->getDiseases();
        DBC::requireTrue(count($diseases), '医生没有疾病');

        $allMenus = $this->getAllMenus($doctorid);
        ksort($allMenus);
        $sortArr = array_values($allMenus);
        $allMenusJson = json_encode($sortArr, JSON_UNESCAPED_UNICODE);

        XContext::setValue('menus', $allMenusJson);
        XContext::setValue('doctor', $doctor);
        XContext::setValue('diseases', $diseases);

        return self::SUCCESS;
    }

    public function doAjaxAddOfDoctorPost() {
        $doctorid = XRequest::getValue("doctorid");
        DBC::requireNotEmpty($doctorid, '医生不能为空');
        $doctor = Doctor::getById($doctorid);
        DBC::requireTrue($doctor instanceof Doctor, '医生不存在');

        $diseaseids = XRequest::getValue("diseaseids", []);
        DBC::requireNotEmpty($diseaseids, '请选择疾病');

        $menus = XRequest::getValue("menus", []);
        $content = json_encode($menus);
        DBC::requireTrue($content, '菜单错误');

        foreach ($diseaseids as $diseaseid) {
            $checkupTplMenu = CheckupTplMenuDao::getByDoctorIdAndDiseaseId($doctorid, $diseaseid);
            if ($checkupTplMenu instanceof CheckupTplMenu) {
                $checkupTplMenu->content = $content;
            } else {
                $row = [];
                $row['diseaseid'] = $diseaseid;
                $row['doctorid'] = $doctorid;
                $row['content'] = $content;
                CheckupTplMenu::createByBiz($row);
            }
        }
        return self::TEXTJSON;
    }

    // 数据库菜单(医生)修改
    public function doModifyOfDoctor() {
        $checkuptplmenuid = XRequest::getValue("checkuptplmenuid", 0);
        $checkupTplMenu = CheckupTplMenu::getById($checkuptplmenuid);
        DBC::requireTrue($checkupTplMenu instanceof CheckupTplMenu, '菜单不存在');

        $doctor = $checkupTplMenu->doctor;
        DBC::requireTrue($doctor instanceof Doctor, '医生不存在');

        $diseases = $doctor->getDiseases();
        DBC::requireTrue(count($diseases), '医生没有疾病');

        $myMenuArr = [];
        $tempMenuArr = [];
        $content_json = json_decode($checkupTplMenu->content, true);
        foreach ($content_json as $item) {
            $newItem = [
                'id' => "{$item['name']}-_-{$item['link']}"
            ];

            $subMenuArr = [];
            $subMenus = $item['submenus'];
            if (!empty($subMenus)) {
                foreach ($subMenus as $subMenu) {
                    $subMenuArr[] = [
                        'id' => "{$subMenu['name']}-_-{$subMenu['link']}",
                        'text' => $subMenu['name'],
                        'show' => $subMenu['show'],
                    ];
                    $tempMenuArr[$subMenu['link'] . $subMenu['name']] = [
                        'id' => "{$subMenu['name']}-_-{$subMenu['link']}",
                        'text' => "{$subMenu['name']}({$subMenu['link']})",
                    ];
                }
                $newItem['text'] = "{$item['name']}";
            } else {
                $newItem['text'] = "{$item['name']}({$item['link']})";
            }

            $tempMenuArr[$item['link'] . $item['name']] = $newItem;
            if (!empty($subMenuArr)) {
                $newItem['id'] = '0';
                $newItem['submenus'] = $subMenuArr;
            } else {
                $newItem['show'] = $item['show'];
            }
            $myMenuArr[] = $newItem;
        }
        $myMenusJson = json_encode($myMenuArr, JSON_UNESCAPED_UNICODE);

        $allMenus = $this->getAllMenus($doctor->id);
        $mergeArr = array_merge($allMenus, $tempMenuArr);
        ksort($mergeArr);
        $sortArr = array_values($mergeArr);
        $allMenusJson = json_encode($sortArr, JSON_UNESCAPED_UNICODE);

        XContext::setValue('menus', $allMenusJson);
        XContext::setValue('myMenus', $myMenusJson);
        XContext::setValue('checkuptplmenuid', $checkuptplmenuid);
        XContext::setValue('doctor', $doctor);
        XContext::setValue('diseaseid', $checkupTplMenu->diseaseid);
        XContext::setValue('diseases', $diseases);
        return self::SUCCESS;
    }

    public function doAjaxModifyOfDoctorPost() {
        $doctorid = XRequest::getValue("doctorid");
        DBC::requireNotEmpty($doctorid, '医生不能为空');
        $doctor = Doctor::getById($doctorid);
        DBC::requireTrue($doctor instanceof Doctor, '医生不存在');

        $checkuptplmenuid = XRequest::getValue("checkuptplmenuid", 0);
        $checkupTplMenu = CheckupTplMenu::getById($checkuptplmenuid);
        DBC::requireTrue($checkupTplMenu instanceof CheckupTplMenu, '菜单不存在');

        DBC::requireTrue($doctor->id == $checkupTplMenu->doctorid, '当前医生和菜单所属医生不匹配');

        $menus = XRequest::getValue("menus", []);
        $content = json_encode($menus);
        DBC::requireTrue($content, '菜单错误');

        $checkupTplMenu->content = $content;
        return self::TEXTJSON;
    }

    // 数据库菜单(医生)删除
    public function doDeleteOfDoctorPost() {
        $checkuptplmenuid = XRequest::getValue("checkuptplmenuid", 0);
        $checkuptplmenu = CheckupTplMenu::getById($checkuptplmenuid);
        DBC::requireTrue($checkuptplmenu instanceof CheckupTplMenu, '菜单不存在');

        $checkuptplmenu->remove();

        $preMsg = '删除成功';

        $refererUrl = urldecode(XContext::getValue('refererUrl'));
        XContext::setJumpPath("{$refererUrl}&preMsg=" . urlencode($preMsg));
        return self::BLANK;
    }

}
