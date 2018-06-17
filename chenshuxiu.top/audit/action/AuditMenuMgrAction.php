<?php
// AuditMenuMgrAction
class AuditMenuMgrAction extends AuditBaseAction
{

    public function doList () {
        $auditroleidarr = XRequest::getValue("auditroleids", array());
        $parentmenuid = XRequest::getValue("parentmenuid", - 1);

        $sql = "select am.*
            from auditmenus am
            left join auditresources ar on am.auditresourceid=ar.id
            where 1=1 ";

        $cond = '';
        $bind = [];

        // 因为循环,没有做 bind 模式处理
        if (false == empty($auditroleidarr)) {
            $cond .= " and ( 1=0 ";
            foreach ($auditroleidarr as $auditroleid) {
                $cond .= " or ar.auditroleids like '%{$auditroleid}%' ";
            }
            $cond .= " ) ";
        }

        if ($parentmenuid >= 0) {
            $cond .= " and parentmenuid = :parentmenuid ";
            $bind[':parentmenuid'] = $parentmenuid;
        }

        $cond .= ' order by am.parentmenuid asc, am.pos asc';

        $sql .= $cond;

        // echo $cond;exit;
        $auditmenus = Dao::loadEntityList('AuditMenu', $sql, $bind);
        XContext::setValue('auditmenus', $auditmenus);
        XContext::setValue('auditroleidarr', $auditroleidarr);
        XContext::setValue('parentmenuid', $parentmenuid);
        return self::SUCCESS;
    }

    public function doTree () {
        $auditroleidarr = XRequest::getValue("auditroleids", array());

        $sql = "select am.*
            from auditmenus am
            left join auditresources ar on am.auditresourceid=ar.id
            where 1=1 ";

        $sql .= ' order by am.parentmenuid asc, am.pos asc';

        // echo $cond;exit;
        $auditmenus = Dao::loadEntityList('AuditMenu', $sql, []);

        $auditmenutree = array();

        foreach ($auditmenus as $auditmenu) {
            if ($auditmenu->parentmenuid) {
                $auditmenutree[$auditmenu->parentmenuid]['subs'][] = $auditmenu;
            } else {
                $auditmenutree[$auditmenu->id]['self'] = $auditmenu;
            }
        }
        XContext::setValue('auditmenutree', $auditmenutree);
        XContext::setValue('auditroleidarr', $auditroleidarr);
        return self::SUCCESS;
    }

    public function doAdd () {
        return self::SUCCESS;
    }

    public function doAddPost () {
        $parentmenuid = XRequest::getValue("parentmenuid", 0);
        $title = XRequest::getValue("title", "");
        $url = XRequest::getValue("url", "");
        $auditresourceid = XRequest::getValue("auditresourceid", 0);

        $row = array();
        $row["parentmenuid"] = $parentmenuid;
        $row["title"] = $title;
        $row["url"] = $url;
        $row["auditresourceid"] = $auditresourceid;

        AuditMenu::createByBiz($row);

        XContext::setJumpPath("/auditmenumgr/list");

        return self::SUCCESS;
    }

    public function doModify () {
        $auditmenuid = XRequest::getValue("auditmenuid", 0);
        $auditmenu = AuditMenu::getById($auditmenuid);
        XContext::setValue('auditmenu', $auditmenu);
        return self::SUCCESS;
    }

    public function doModifyPost () {
        $auditmenuid = XRequest::getValue("auditmenuid", 0);
        $parentmenuid = XRequest::getValue("parentmenuid", 0);
        $title = XRequest::getValue("title", "");
        $url = XRequest::getValue("url", "");
        $auditresourceid = XRequest::getValue("auditresourceid", 0);

        if ($auditmenuid === $parentmenuid) {
            $preMsg = "父目录不能是自己 " . XDateTime::now();
            XContext::setJumpPath("/auditmenumgr/modify?auditmenuid=" . $auditmenuid . "&preMsg=" . urlencode($preMsg));
            return self::SUCCESS;
        }
        $auditmenu = AuditMenu::getById($auditmenuid);

        $auditmenu->parentmenuid = $parentmenuid;
        $auditmenu->title = $title;
        $auditmenu->url = $url;

        $auditmenu->auditresourceid = $auditresourceid;

        $preMsg = "修改已保存 " . XDateTime::now();
        XContext::setJumpPath("/auditmenumgr/modify?auditmenuid=" . $auditmenuid . "&preMsg=" . urlencode($preMsg));
        return self::SUCCESS;
    }

    public function doPosModifyPost () {
        $parentmenuid = XRequest::getValue('parentmenuid', - 1);
        $posArray = XRequest::getValue('pos', array());

        foreach ($posArray as $auditmenuid => $pos) {
            $auditmenu = AuditMenu::getById($auditmenuid);
            $auditmenu->pos = $pos;
        }

        XContext::setJumpPath("/auditmenumgr/list?parentmenuid={$parentmenuid}");
        return self::SUCCESS;
    }

    public function doDeletePost () {
        $auditmenuid = XRequest::getValue('auditmenuid', 0);
        $parentmenuid = XRequest::getValue("parentmenuid", 0);

        $auditmenu = AuditMenu::getById($auditmenuid);
        $auditmenu->auditresource->auditmenuid = 0;
        $auditmenu->remove();

        XContext::setJumpPath("/auditmenumgr/list?parentmenuid={$parentmenuid}");
        return self::SUCCESS;
    }
}
