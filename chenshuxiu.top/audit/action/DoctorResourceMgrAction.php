<?php
// DoctorResourceMgrAction
class DoctorResourceMgrAction extends AuditBaseAction
{

    public function doList () {
        $pagesize = XRequest::getValue("pagesize", 20);
        $pagenum = XRequest::getValue("pagenum", 1);

        $word = XRequest::getValue('word', '');

        $cond = '';
        $bind = [];

        if ($word) {
            $cond .= ' AND name LIKE :word ';
            $bind[':word'] = "%{$word}%";
        }

        $doctorResources = Dao::getEntityListByCond4Page("DoctorResource", $pagesize, $pagenum, $cond, $bind);

        // 翻页begin
        $countSql = "select count(*) as cnt from doctorresources where 1=1 " . $cond;
        $cnt = Dao::queryValue($countSql, $bind);
        $url = "/doctorresourcemgr/list?word={$word}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);
        XContext::setValue("pagelink", $pagelink);

        XContext::setValue('doctorResources', $doctorResources);
        XContext::setValue('word', $word);

        return self::SUCCESS;
    }

    public function doAdd () {
        return self::SUCCESS;
    }

    public function doAddPost () {
        $name = XRequest::getValue('name', '');
        $content = XRequest::getValue('content', '');
        $action = XRequest::getValue('_action', '');
        $method = XRequest::getValue('_method', '');

        $doctorResource = DoctorResourceDao::getByName($name);
        DBC::requireEmpty($doctorResource, '资源名称已存在, 请重新命名');

        if ($action && $method) {
            $doctorResource = DoctorResourceDao::getByActionMethod($action, $method);
            DBC::requireEmpty($doctorResource, '资源已存在, 请前往修改，勿重复添加');
        }

        $row = array();
        $row['name'] = $name;
        $row['content'] = $content;
        $row['action'] = $action;
        $row['method'] = $method;

        DoctorResource::createByBiz($row);

        $preMsg = '添加成功';
        XContext::setJumpPath("/doctorresourcemgr/list?preMsg=" . urlencode($preMsg));
        return self::SUCCESS;
    }

    public function doModify () {
        $doctorresourceid = XRequest::getValue('doctorresourceid', '');
        $doctorResource = Dao::getEntityById('DoctorResource', $doctorresourceid);

        XContext::setValue('doctorResource', $doctorResource);
        return self::SUCCESS;
    }

    public function doModifyPost () {
        $doctorresourceid = XRequest::getValue('doctorresourceid', '');
        $doctorResource = Dao::getEntityById('DoctorResource', $doctorresourceid);

        $name = XRequest::getValue('name', '');
        $content = XRequest::getValue('content', '');
        $action = XRequest::getValue('_action', '');
        $method = XRequest::getValue('_method', '');

        if ($name != $doctorResource->name) {
            $entity = DoctorResourceDao::getByName($name);
            DBC::requireEmpty($entity, '资源名称已存在, 请重新命名');
        }

        if ($action && $method && ($action != $doctorResource->action || $method != $doctorResource->method)) {
            $entity = DoctorResourceDao::getByActionMethod($action, $method);
            DBC::requireEmpty($entity, '资源已存在, 请前往修改，勿重复添加');
        }

        $doctorResource->name = $name;
        $doctorResource->content = $content;
        $doctorResource->action = $action;
        $doctorResource->method = $method;

        $preMsg = '修改资源成功';
        XContext::setJumpPath("/doctorresourcemgr/list?preMsg=" . urlencode($preMsg));
        return self::SUCCESS;
    }

    public function doDeleteJson () {
        $doctorresourceid = XRequest::getValue('doctorresourceid', '');
        $doctorResource = Dao::getEntityById('DoctorResource', $doctorresourceid);

        DBC::requireNotEmpty($doctorResource, '资源不存在');

        $sql = 'SELECT * FROM assistants WHERE doctorresourceids LIKE :doctorresourceids';
        $bind = array(
            ':doctorresourceids' => "%{$doctorresourceid}%");

        $assistants = Dao::loadEntityList('Assistant', $sql, $bind);
        if (is_array($assistants)) {
            foreach ($assistants as $one) {
                $tmp = $one->doctorresourceids;
                if (empty(trim($tmp))) {
                    continue;
                }
                $arr = explode(',', $one->doctorresourceids);
                foreach ($arr as $key => $a) {
                    if ($a == $doctorresourceid) {
                        unset($arr[$key]);
                    }
                }
                $str = '';
                if ($arr) {
                    $str = implode(',', $arr);
                }
                $one->doctorresourceids = $str;
            }
        }
        $doctorResource->remove();
        echo 'ok';
        return self::BLANK;
    }
}
