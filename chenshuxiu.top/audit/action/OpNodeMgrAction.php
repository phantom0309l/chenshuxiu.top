<?php
// OpNodeMgrAction
class OpNodeMgrAction extends AuditBaseAction
{
    public function doListforoptasktpl () {
        $optasktplid = XRequest::getValue('optasktplid', 0);
        $optasktpl = OpTaskTpl::getById($optasktplid);
        DBC::requireNotEmpty($optasktpl, "optasktpl is null");

        $opnodes = OpNodeDao::getListByOptaskTpl($optasktpl);

        XContext::setValue('opnodes', $opnodes);
        XContext::setValue('optasktpl', $optasktpl);

        return self::SUCCESS;
    }

    private function checkCode ($code, $optasktplid) {
        DBC::requireNotEmpty($code, "code不能为空");

        $opnode = OpNodeDao::getByCodeOpTaskTplId($code, $optasktplid);

        if ($opnode instanceof OpNode) {
            return 'fail';
        } else {
            return 'ok';
        }
    }

    public function doAddOrModifyJson () {
        $optasktplid = XRequest::getValue('optasktplid', 0);
        $optasktpl = OpTaskTpl::getById($optasktplid);
        DBC::requireNotEmpty($optasktpl, "optasktpl is null");

        $opnodeid = XRequest::getValue('opnodeid', 0);
        $code = XRequest::getValue('code', '');
        $title = XRequest::getValue('title', 0);
        $is_show_next_plantime = XRequest::getValue('is_show_next_plantime', 0);
        $content = XRequest::getValue('content', '');

        if ($opnodeid) {
            // 修改
            $opnode = OpNode::getById($opnodeid);
            DBC::requireNotEmpty($opnode, "opnode is null");

            if ($opnode->code != $code) {
                $check_result_str = $this->checkCode($code, $optasktplid);
                if ($check_result_str != 'ok') {
                    echo $check_result_str;

                    return self::BLANK;
                }
            }

            $opnode->code = $code;
            $opnode->title = $title;
            $opnode->is_show_next_plantime = $is_show_next_plantime;
            $opnode->content = $content;
        } else {
            // 添加
            $check_result_str = $this->checkCode($code, $optasktplid);
            if ($check_result_str != 'ok') {
                echo $check_result_str;

                return self::BLANK;
            }

            $row = [];
            $row['optasktplid'] = $optasktplid;
            $row['code'] = $code;
            $row['title'] = $title;
            $row['is_show_next_plantime'] = $is_show_next_plantime;
            $row['content'] = $content;

            $opnode = OpNode::createByBiz($row);
        }

        echo 'ok';

        return self::BLANK;
    }

    public function doChangeIs_hang_upJson () {
        $opnodeid = XRequest::getValue('opnodeid', 0);
        $opnode = OpNode::getById($opnodeid);
        DBC::requireNotEmpty($opnode, "opnode is null");
        $is_hang_up = XRequest::getValue('is_hang_up', '-1');

        if ($is_hang_up != -1) {
            $opnode->is_hang_up = $is_hang_up;
            echo 'ok';
        } else {
            echo 'fail';
        }

        return self::BLANK;
    }

    public function doDeleteJson () {
        $opnodeid = XRequest::getValue('opnodeid', 0);
        $opnode = OpNode::getById($opnodeid);
        DBC::requireNotEmpty($opnode, "opnode is null");

        if ($opnode->getOpNodeFlowCnt() > 0) {
            echo 'fail-notempty';
        } else {
            echo 'ok';
            $opnode->remove();
        }

        return self::BLANK;
    }

    public function doGetArrByOptasktplidJson () {
        $optasktplid = XRequest::getValue('optasktplid', 0);
        $optasktpl = OpTaskTpl::getById($optasktplid);
        DBC::requireNotEmpty($optasktpl, "optasktpl is null");

        $opnodes = OpNodeDao::getListByOpTaskTpl($optasktpl);
        $arr = CtrHelper::toOpnodeCtrArray($opnodes);
        $this->result["opnodes"] = $arr;
        return self::TEXTJSON;
    }

    // 一键创建通用节点
    public function doCreateCommonOpNode () {
        $optasktplid = XRequest::getValue('optasktplid', 0);
        $optasktpl = OpTaskTpl::getById($optasktplid);
        DBC::requireNotEmpty($optasktpl, "optasktpl is null");

        $common_optasks = [
            'root' => '根节点',
            'again_follow' => '再跟进',
            'appoint_follow' => '约定跟进',
            'phone_follow' => '电话跟进',
            'finish' => '完成',
            'refuse' => '患者拒绝',
            'time_out_close' => '挂起超时关闭',
        ];

        foreach ($common_optasks as $code => $title) {
            $opnode = OpNodeDao::getByCodeOpTaskTplId($code, $optasktplid);
            if (false == $opnode instanceof OpNode) {
                $row = [];
                $row['optasktplid'] = $optasktplid;
                $row['code'] = $code;
                $row['title'] = $title;
                $row['content'] = '';

                $opnode = OpNode::createByBiz($row);
            }
        }

        XContext::setJumpPath("/opnodemgr/listforoptasktpl?optasktplid={$optasktplid}");

        return self::SUCCESS;
    }
}
