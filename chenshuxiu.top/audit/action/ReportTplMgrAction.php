<?php

// ReportTplMgrAction
class ReportTplMgrAction extends AuditBaseAction
{

    const Config_items = [
        [
            'key' => 'baseInfo',
            'title' => '基本信息',
            'checked' => true,
        ],
        [
            'key' => 'appeal',
            'title' => '患者诉求',
            'checked' => true,
        ],
        [
            'key' => 'remark',
            'title' => '运营备注',
            'checked' => true,
        ],
        [
            'key' => 'patientRemark',
            'title' => '症状体征及不良反应',
            'checked' => true,
        ],
        [
            'key' => 'checkuptpls',
            'title' => '检查',
            'checked' => true,
        ],
        [
            'key' => 'patientmedicinepkg',
            'title' => '用药',
            'checked' => true,
        ],
        [
            'key' => 'diagnose',
            'title' => '诊断和分期',
            'checked' => false,
        ],
        [
            'key' => 'chemo',
            'title' => '最新化疗方案',
            'checked' => false,
        ],
        [
            'key' => 'nexthualiaodate',
            'title' => '预计下次化疗日期',
            'checked' => false,
        ],
        [
            'key' => 'wbc_checkup',
            'title' => '近期血常规',
            'checked' => false,
        ],
    ];

    public function doList() {
        $reporttpls = ReportTplDao::getAll();
        XContext::setValue('reporttpls', $reporttpls);
        return self::SUCCESS;
    }

    public function doAdd() {
        XContext::setValue('config_items', self::Config_items);
        return self::SUCCESS;
    }

    public function doAddPost() {
        $title = XRequest::getValue('title', '');
        DBC::requireNotEmptyString($title, '标题不能为空');

        $brief = XRequest::getValue('brief', '');

        $items = XRequest::getValue('items');
        DBC::requireNotEmpty($items, '内容不能为空');

        $arr = [];
        foreach ($items as $key => $item) {
            $arr[] = $key;
        }

        $row = array();
        $row["title"] = $title;
        $row["brief"] = $brief;
        $row["content"] = json_encode($arr, JSON_UNESCAPED_UNICODE);
        $reporttpl = ReportTpl::createByBiz($row);

        XContext::setJumpPath("/reporttplmgr/list?preMsg=创建成功");
    }

    public function doOne() {
        return self::SUCCESS;
    }

    public function doModify() {
        $reporttplid = XRequest::getValue('reporttplid', 0);
        DBC::requireTrue($reporttplid > 0, 'reporttplid 不能为空');
        $reporttpl = ReportTpl::getById($reporttplid);
        DBC::requireTrue($reporttpl instanceof ReportTpl, 'reporttpl 不存在');

        XContext::setValue('config_items', self::Config_items);
        XContext::setValue('reporttpl', $reporttpl);
        return self::SUCCESS;
    }

    public function doModifyPost() {
        $reporttplid = XRequest::getValue('reporttplid', 0);
        DBC::requireTrue($reporttplid > 0, 'reporttplid 不能为空');
        $reporttpl = ReportTpl::getById($reporttplid);
        DBC::requireTrue($reporttpl instanceof ReportTpl, 'reporttpl 不存在');

        $title = XRequest::getValue('title', '');
        DBC::requireNotEmptyString($title, '标题不能为空');

        $brief = XRequest::getValue('brief', '');

        $items = XRequest::getValue('items');
        DBC::requireNotEmpty($items, '内容不能为空');

        $arr = [];
        foreach ($items as $key => $item) {
            $arr[] = $key;
        }

        $reporttpl->title = $title;
        $reporttpl->brief = $brief;
        $reporttpl->content = json_encode($arr, JSON_UNESCAPED_UNICODE);

        XContext::setJumpPath("/reporttplmgr/list?preMsg=修改成功");
    }

    public function doDeletePost() {
        $reporttplid = XRequest::getValue('reporttplid', 0);
        DBC::requireTrue($reporttplid > 0, 'reporttplid 不能为空');
        $reporttpl = ReportTpl::getById($reporttplid);
        DBC::requireTrue($reporttpl instanceof ReportTpl, 'reporttpl 不存在');

        $reporttpl->remove();

        XContext::setJumpPath("/reporttplmgr/list?preMsg=删除成功");
    }
}
