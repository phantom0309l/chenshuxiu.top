<?php
// AuditResourceMgrAction
class AuditResourceMgrAction extends AuditBaseAction
{

    // 运营后台资源列表
    // 有sql注入漏洞 TODO by sjp 20170503
    public function doList () {
        $auditroleInvert = XRequest::getValue('auditroleInvert', 0); // 角色反选
        $auditroleids = XRequest::getValue("auditroleids", array()); // 角色
        $typeInvert = XRequest::getValue('typeInvert', 0); // 类型反选
        $types = XRequest::getValue('types', array()); // 类型
        $bindMenu = XRequest::getValue('bindMenu', 'all'); // 绑定菜单

        $cond = "";

        // 角色筛选
        if (false == $auditroleInvert) {
            if (false == empty($auditroleids)) {
                $cond .= " and ( 1=0 ";
                foreach ($auditroleids as $auditroleid) {
                    if (false == is_numeric($auditroleid)) {
                        continue;
                    }
                    $cond .= " or auditroleids like '%{$auditroleid}%' ";
                }
                $cond .= " ) ";
            }
        } else {
            if (false == empty($auditroleids)) {
                $cond .= " and ( 1=1 ";
                foreach ($auditroleids as $auditroleid) {
                    if (false == is_numeric($auditroleid)) {
                        continue;
                    }
                    $cond .= " and auditroleids not like '%{$auditroleid}%' ";
                }
                $cond .= " ) ";
            }
        }

        // 类型筛选
        $notStr = '';
        if ($typeInvert) {
            $notStr = ' not ';
        }

        $arr = array();
        if (! empty($types)) {
            foreach ($types as $type) {
                $arr[] = "'{$type}'";
            }

            $typestr = implode(",", $arr);

            $cond .= " and type $notStr in ({$typestr}) ";
        }

        switch ($bindMenu) {
            case 'all':
                break;
            case 'yes':
                $cond .= ' and auditmenuid > 0 ';
                break;
            case 'no':
                $cond .= ' and auditmenuid = 0 ';
                break;
        }

        $cond .= ' order by action , method asc';
        $auditresources = Dao::getEntityListByCond('AuditResource', $cond);

        XContext::setValue('auditroleInvert', $auditroleInvert);
        XContext::setValue('auditroleids', $auditroleids);
        XContext::setValue('typeInvert', $typeInvert);
        XContext::setValue('types', $types);
        XContext::setValue('bindMenu', $bindMenu);
        XContext::setValue('auditresources', $auditresources);
        return self::SUCCESS;
    }

    // 运营后台资源新建
    public function doAdd () {
        return self::SUCCESS;
    }

    // 运营后台资源新建提交
    public function doAddPost () {
        $type = XRequest::getValue("type", "");
        $title = XRequest::getValue("title", "");
        $action_add = XRequest::getValue("action_add", "");
        $method_add = XRequest::getValue("method_add", "");
        $auditmenuid = XRequest::getValue("auditmenuid", 0);
        $auditroleidarr = XRequest::getValue("auditroleids", array());
        $owner_auditorid = XRequest::getValue("owner_auditorid", 0);
        $content = XRequest::getValue("content", "");
        $remark = XRequest::getValue("remark", "");

        $row = array();
        $row["type"] = $type;
        $row["title"] = strtolower($title);
        $row["action"] = strtolower($action_add);
        $row["method"] = strtolower($method_add);
        $row["auditmenuid"] = $auditmenuid;
        $row["auditroleids"] = implode(',', $auditroleidarr);
        $row["owner_auditorid"] = $owner_auditorid;
        $row["content"] = $content;
        $row["remark"] = $remark;

        $auditresource = AuditResource::createByBiz($row);

        XContext::setJumpPath("/auditresourcemgr/list");

        return self::SUCCESS;
    }

    // 运营后台资源快速生成menu
    public function doAddMenuPost () {
        $auditresourceid = XRequest::getValue("auditresourceid", 0);
        $auditmenuid = XRequest::getValue("auditmenuid", 0);

        $auditresource = AuditResource::getById($auditresourceid);

        $row = array();
        $row['title'] = $auditresource->title;
        $row['url'] = '/' . $auditresource->action . '/' . $auditresource->method;
        $row['auditresourceid'] = $auditresource->id;
        $row['parentmenuid'] = 121023765;

        $auditmenu = AuditMenu::createByBiz($row);

        $auditresource->auditmenuid = $auditmenu->id;

        XContext::setJumpPath("/auditmenumgr/modify?auditmenuid=" . $auditmenu->id);

        return self::SUCCESS;
    }

    // 运营后台资源修改
    public function doModify () {
        $auditresourceid = XRequest::getValue("auditresourceid", 0);
        $auditresource = AuditResource::getById($auditresourceid);
        XContext::setValue('auditresource', $auditresource);
        return self::SUCCESS;
    }

    // 运营后台资源修改提交
    public function doModifyPost () {
        $auditresourceid = XRequest::getValue("auditresourceid", 0);
        $type = XRequest::getValue("type", "");
        $title = XRequest::getValue("title", "");
        $action_add = XRequest::getValue("action_add", "");
        $method_add = XRequest::getValue("method_add", "");
        $auditmenuid = XRequest::getValue("auditmenuid", 0);
        $auditroleidarr = XRequest::getValue("auditroleids", array());
        $diseasegroupid = XRequest::getValue("diseasegroupid", 0);
        $owner_auditorid = XRequest::getValue("owner_auditorid", "");
        $content = XRequest::getValue("content", "");
        $remark = XRequest::getValue("remark", "");

        $auditresource = AuditResource::getById($auditresourceid);

        $auditresource->type = $type;
        $auditresource->title = $title;
        $auditresource->action = $action_add;
        $auditresource->method = $method_add;
        $auditresource->auditmenuid = $auditmenuid;
        $auditresource->auditroleids = implode(',', $auditroleidarr);
        $auditresource->diseasegroupid = $diseasegroupid;
        $auditresource->owner_auditorid = $owner_auditorid;
        $auditresource->content = $content;
        $auditresource->remark = $remark;

        $preMsg = "修改已保存 " . XDateTime::now();
        XContext::setJumpPath("/auditresourcemgr/modify?auditresourceid=" . $auditresourceid . "&preMsg=" . urlencode($preMsg));
        return self::SUCCESS;
    }

    // 运营后台资源快速提交fastmodifypost
    public function doFastModifyPost () {
        $auditresourceid = XRequest::getValue("auditresourceid", 0);
        $auditmenuid = XRequest::getValue("auditmenuid", 0);

        $auditresource = AuditResource::getById($auditresourceid);

        $auditresource->auditmenuid = $auditmenuid;

        XContext::setJumpPath("/auditresourcemgr/list");
        return self::SUCCESS;
    }

    // 运营后台资源修改提交
    public function doDeletePost () {
        $auditresourceid = XRequest::getValue("auditresourceid", 0);

        $auditresource = AuditResource::getById($auditresourceid);

        $auditresource->remove();

        $preMsg = "已删除:{$auditresource->title}";

        XContext::setJumpPath("/auditresourcemgr/list?preMsg=" . urlencode($preMsg));
        return self::SUCCESS;
    }
}
