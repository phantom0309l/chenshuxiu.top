<?php

class OpTaskTplMgrAction extends AuditBaseAction
{

    public function dolist () {
        $status = XRequest::getValue('status', - 1);
        $title = XRequest::getValue('title', '');

        $mydisease = $this->mydisease;

        $cond = '';
        $bind = [];

        if ($status >= 0) {
            $cond = " and status = :status ";
            $bind[':status'] = $status;
        }

        $cond .= " order by code, subcode, objtype ";

        $optasktpls = Dao::getEntityListByCond('OpTaskTpl', $cond, $bind);

        if ($mydisease instanceof Disease) {
            $temp = [];
            foreach ($optasktpls as $optasktpl) {
                if (0 == $optasktpl->diseaseids || in_array($mydisease->id, $optasktpl->getDiseaseIdArr())) {
                    $temp[] = $optasktpl;
                }
            }
            $optasktpls = $temp;
        }

        if ($title) {
            $cond = '';
            $bind = [];

            $cond = " and title like :title ";
            $bind[':title'] = "%{$title}%";

            $cond .= " order by code, subcode, objtype ";
            $optasktpls = Dao::getEntityListByCond('OpTaskTpl', $cond, $bind);
        }

        // 查出每个任务模板的任务分布情况
        $sql = "select a.optasktplid,a.status,count(a.id) as cnt, min(a.createtime) as min_date, max(a.createtime) as max_date
                from optasks a
                group by a.optasktplid,a.status
                order by null ";
        $rows = Dao::queryRows($sql);

        $optasktpl_rows = [];
        foreach ($rows as $row) {
            $optasktplid = $row['optasktplid'];
            $optasktpl_rows["{$optasktplid}"][] = $row;
        }

        $optasktpl_list = [];
        foreach ($optasktpl_rows as $optasktplid => $optasktplarrs) {
            $tmp = [
                'cnt_0' => 0,
                'cnt_1' => 0,
                'cnt_2' => 0,
                'min_date' => '',
                'max_date' => ''
            ];
            $min_date = [];
            $max_date = [];
            foreach ($optasktplarrs as $optasktplarr) {
                $cnt_key = "cnt_" . $optasktplarr['status'];
                $tmp["{$cnt_key}"] = $optasktplarr['cnt'];

                $min_date[] = $optasktplarr['min_date'];
                $max_date[] = $optasktplarr['max_date'];
            }
            $tmp['cnt'] = $tmp['cnt_0'] + $tmp['cnt_1'] + $tmp['cnt_2'];
            $tmp['min_date'] = min($min_date);
            $tmp['max_date'] = max($max_date);

            $optasktpl_list[$optasktplid] = $tmp;
        }

        XContext::setValue("status", $status);
        XContext::setValue("title", $title);
        XContext::setValue("optasktpls", $optasktpls);
        XContext::setValue("optasktpl_list", $optasktpl_list);

        return self::SUCCESS;
    }

    public function doAdd () {
        return self::SUCCESS;
    }

    public function doAddPost () {
        $code = XRequest::getValue("code", "");
        $subcode = XRequest::getValue("subcode", "");
        $objtype = XRequest::getValue("objtype", "");
        $diseaseids = XRequest::getValue("diseaseids", "");
        $title = XRequest::getValue("title", "");
        $content = XRequest::getValue("content", "");
        $is_can_handcreate = XRequest::getValue("is_can_handcreate", 0);


        $row = array();
        $row["code"] = $code;
        $row["subcode"] = $subcode;
        $row["objtype"] = $objtype;
        $row["diseaseids"] = $diseaseids;
        $row["title"] = $title;
        $row["content"] = $content;
        $row["status"] = 1;
        $row["is_can_handcreate"] = $is_can_handcreate;

        OpTaskTpl::createByBiz($row);
        XContext::setJumpPath("/optasktplmgr/list?preMsg=创建成功");

        return self::SUCCESS;
    }

    public function doModify () {
        $optasktplid = XRequest::getValue("optasktplid", 0);

        $optasktpl = OpTaskTpl::getById($optasktplid);

        $opnodes = OpNodeDao::getListByOpTaskTpl($optasktpl);

        $opnode_kvs = [];
        foreach ($opnodes as $opnode) {
            $opnode_kvs["{$opnode->code}"] = $opnode->code;
        }

        XContext::setValue("optasktpl", $optasktpl);

        XContext::setValue("opnode_kvs", $opnode_kvs);

        return self::SUCCESS;
    }

    // 修改提交
    public function doModifyPost () {
        $code = XRequest::getValue("code", "");
        $subcode = XRequest::getValue("subcode", "");
        $objtype = XRequest::getValue("objtype", "");
        $diseaseids = XRequest::getValue("diseaseids", "");
        $title = XRequest::getValue("title", "");
        $content = XRequest::getValue("content", "");
        $auto_to_opnode_code = XRequest::getValue("auto_to_opnode_code", "unfinish");
        $auto_to_opnode_daycnt = XRequest::getValue('auto_to_opnode_daycnt', 0);
        $status = XRequest::getValue("status", 1);
        $is_can_handcreate = XRequest::getValue("is_can_handcreate", 0);

        $optasktplid = XRequest::getValue("optasktplid", 0);
        $optasktpl = OpTaskTpl::getById($optasktplid);

        $optasktpl->code = $code;
        $optasktpl->subcode = $subcode;
        $optasktpl->objtype = $objtype;
        $optasktpl->diseaseids = $diseaseids;
        $optasktpl->title = $title;
        $optasktpl->content = $content;
        $optasktpl->status = $status;
        $optasktpl->is_can_handcreate = $is_can_handcreate;

        if ($optasktpl->is_auto_to_opnode == 1) {
            $optasktpl->auto_to_opnode_code = $auto_to_opnode_code;
            $optasktpl->auto_to_opnode_daycnt = $auto_to_opnode_daycnt;
        }

        $preMsg = "修改已保存 " . XDateTime::now();
        XContext::setJumpPath("/optasktplmgr/modify?optasktplid=" . $optasktplid . "&preMsg=" . urlencode($preMsg));
        return self::SUCCESS;
    }

    public function dogetall () {
        $optasktpls = OpTaskTplDao::getList();

        $list = [];

        foreach ($optasktpls as $optasktpl) {
            $list[] = [
                'id' => $optasktpl->id,
                'title' => $optasktpl->title
            ];
        }

        $this->result['data'] = $list;

        return self::TEXTJSON;
    }

    public function doChangeHandcreateJson () {
        $optasktplid = XRequest::getValue('optasktplid', 0);
        $is_can_handcreate = XRequest::getValue('is_can_handcreate', 0);
        $optasktpl = OpTaskTpl::getById($optasktplid);

        if ($optasktpl instanceof OpTaskTpl) {
            $optasktpl->is_can_handcreate = $is_can_handcreate;

            echo 'success';
        } else {
            echo 'fail';
        }

        return self::BLANK;
    }


    public function doChangeAutoSendJson () {
        $optasktplid = XRequest::getValue('optasktplid', 0);
        $is_auto_send = XRequest::getValue('is_auto_send', 0);
        $optasktpl = OpTaskTpl::getById($optasktplid);

        if ($optasktpl instanceof OpTaskTpl) {
            $optasktpl->is_auto_send = $is_auto_send;

            echo 'success';
        } else {
            echo 'fail';
        }

        return self::BLANK;
    }

    public function dochangeautotoopnodejson () {
        $optasktplid = XRequest::getValue('optasktplid', 0);
        $is_auto_to_opnode = XRequest::getValue('is_auto_to_opnode', 0);
        $optasktpl = OpTaskTpl::getById($optasktplid);

        if ($optasktpl instanceof OpTaskTpl) {
            $optasktpl->is_auto_to_opnode = $is_auto_to_opnode;

            echo 'success';
        } else {
            echo 'fail';
        }

        return self::BLANK;
    }
}
