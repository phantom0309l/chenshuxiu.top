<?php

// OpNodeFlowMgrAction
class OpNodeFlowMgrAction extends AuditBaseAction
{

    public function doList () {
        $optasktplid = XRequest::getValue('optasktplid', 0);
        $optasktpl = OpTaskTpl::getById($optasktplid);
        DBC::requireNotEmpty($optasktpl, "optasktpl is null");

        $opnodes = OpNodeDao::getListByOptaskTpl($optasktpl);

        $flow = [];
        foreach ($opnodes as $from_opnode) {
            foreach ($opnodes as $to_opnode) {
                $opnodeflow = OpNodeFlowDao::getByFrom_opnodeTo_opnode($from_opnode, $to_opnode);
                if ($opnodeflow instanceof OpNodeFlow) {
                    $flow["{$from_opnode->id}{$to_opnode->id}"] = $opnodeflow;
                }
            }
        }

        XContext::setValue('optasktpl', $optasktpl);
        XContext::setValue('opnodes', $opnodes);
        XContext::setValue('flow', $flow);

        return self::SUCCESS;
    }

    public function doAddOrModifyJson () {
        $opnodeflowid = XRequest::getValue('opnodeflowid', 0);
        $opnodeflow = OpNodeFlow::getById($opnodeflowid);

        $from_opnodeid = XRequest::getValue('from_opnodeid', 0);
        $from_opnode = OpNode::getById($from_opnodeid);
        DBC::requireNotEmpty($from_opnode, 'from_opnode is null');

        $to_opnodeid = XRequest::getValue('to_opnodeid', 0);
        $to_opnode = OpNode::getById($to_opnodeid);
        DBC::requireNotEmpty($to_opnode, 'to_opnode is null');

        $type = XRequest::getValue('type', '');
        $content = XRequest::getValue('content', '');

        if (! $type) {
            echo 'fail';
            return self::BLANK;
        }

        if ($opnodeflow instanceof OpNodeFlow) {
            $opnodeflow->type = $type;
            $opnodeflow->content = $content;
        } else {
            $row = [];
            $row['from_opnodeid'] = $from_opnodeid;
            $row['to_opnodeid'] = $to_opnodeid;
            $row['type'] = $type;
            $row['content'] = $content;

            $opnodeflow = OpNodeFlow::createByBiz($row);
        }

        echo 'ok';

        return self::BLANK;
    }

    public function doDeleteJson () {
        $opnodeflowid = XRequest::getValue('opnodeflowid', 0);
        $opnodeflow = OpNodeFlow::getById($opnodeflowid);

        if ($opnodeflow instanceof OpNodeFlow) {
            $opnodeflow->remove();
            echo 'ok';
        } else {
            echo 'fail';
        }

        return self::BLANK;
    }

    public function doGenOpTaskTplServiceClass () {
        $optasktplid = XRequest::getValue('optasktplid', 0);
        $optasktpl = OpTaskTpl::getById($optasktplid);
        DBC::requireNotEmpty($optasktpl, "optasktpl is null");

        XContext::setValue('optasktpl', $optasktpl);

        $opnodes = OpNodeDao::getListByOpTaskTpl($optasktpl);
        $enter = "\n";
        $EventStr = "";

        $classStr = "<?php{$enter}{$enter}";
        $className = "OpTaskTpl_{$optasktpl->code}_{$optasktpl->subcode}";

        $classStr .= "class {$className} {{$enter}";
        $EventStr .= $classStr;

        $opnodeflow_methods = [];
        foreach ($opnodes as $opnode) {
            $opnodeflows = OpNodeFlowDao::getListByFrom_opnode($opnode);
            foreach ($opnodeflows as $opnodeflow) {
                $opnodeflow_methods[] = $opnodeflow;
            }
        }

        $methodStr = "";
        foreach ($opnodeflow_methods as $a) {
            $methodStr .= "
    /**
    * {$a->from_opnode->title} => {$a->to_opnode->title}
    * {$a->from_opnode->code}_to_{$a->to_opnode->code}
    */
    public static function flow_{$a->from_opnode->code}_to_{$a->to_opnode->code} (OpTask $" . "optask, OpNodeFlow $" . "opnodeflow, $" . "auditorid = 0) {{$enter}
    }{$enter}";
        }

        $EventStr .= $methodStr;

        $EventStr .= "}";

        XContext::setValue('classstr', $EventStr);

        return self::SUCCESS;
    }
}
