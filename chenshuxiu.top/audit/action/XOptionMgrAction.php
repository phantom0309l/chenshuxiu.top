<?php
// 备选项管理
class XOptionMgrAction extends AuditBaseAction
{

    // 批量添加
    public function doBatAddPost () {
        $xquestionid = XRequest::getValue('xquestionid', 0);
        $optionstrs = XRequest::getValue('optionstrs', '');

        $optionstrs = explode('|', $optionstrs);
        foreach ($optionstrs as $str) {
            if (empty($str)) {
                continue;
            }
            $row = array();
            $row["xquestionid"] = $xquestionid;
            $row["content"] = $str;
            XOption::createByBiz($row);
        }

        $preMsg = '已批量添加备选项';

        XContext::setJumpPath("/xquestionmgr/modify?xquestionid={$xquestionid}&preMsg=" . urlencode($preMsg));
        return self::SUCCESS;
    }

    // 添加
    public function doAddPost () {

        $xquestionid = XRequest::getValue('xquestionid', 0);
        $content = XRequest::getValue('content', '');
        $score = XRequest::getValue('score', 0);
        $checked = XRequest::getValue('checked', 0);

        if ($content) {
            $row = array();
            $row["xquestionid"] = $xquestionid;
            $row["content"] = $content;
            $row["score"] = $score;
            $row["checked"] = $checked;
            XOption::createByBiz($row);
            $preMsg = "已添加备选项: " . $content;
        } else {
            $preMsg = "备选项添加失败:内容为空";
        }

        XContext::setJumpPath("/xquestionmgr/modify?xquestionid={$xquestionid}&preMsg=" . urlencode($preMsg));
        return self::SUCCESS;
    }

    // 修改
    public function doModifyPost () {

        $xoptionid = XRequest::getValue('xoptionid', 0);
        $content = XRequest::getValue('content', '');
        $score = XRequest::getValue('score', 0);
        $checked = XRequest::getValue('checked', 0);
        $havesub = XRequest::getValue('havesub', 0);
        $showenames = XRequest::getValue('showenames', '');
        $hideenames = XRequest::getValue('hideenames', '');

        $xoption = XOption::getById($xoptionid);

        $contentFrom = $xoption->content;
        $scoreFrom = $xoption->score;

        $xoption->content = $content;
        $xoption->score = $score;
        $xoption->checked = $checked;
        $xoption->havesub = $havesub;
        $xoption->showenames = $showenames;
        $xoption->hideenames = $hideenames;

        $preMsg = "备选项修改: {$contentFrom} => {$content} 分值: {$scoreFrom} => {$score} ";
        XContext::setJumpPath("/xquestionmgr/modify?xquestionid={$xoption->xquestionid}&preMsg=" . urlencode($preMsg));

        return self::SUCCESS;
    }

    // 备选项批量修改提交
    public function doBatModifyPost () {
        $xquestionid = XRequest::getValue('xquestionid', 0);
        $options = XRequest::getValue('options', array());

        foreach ($options as $optionid => $arr) {

            $xoption = XOption::getById($optionid);

            $xoption->content = $arr['content'];
            $xoption->score = $arr['score'];
            $xoption->checked = $arr['checked'];
            $xoption->havesub = $arr['havesub'];
            $xoption->showenames = FUtil::filterInvisibleChar($arr['showenames']);
            $xoption->hideenames = FUtil::filterInvisibleChar($arr['hideenames']);
        }

        $preMsg = "备选项修改已保存";
        XContext::setJumpPath("/xquestionmgr/modify?xquestionid={$xquestionid}&preMsg=" . urlencode($preMsg));
        return self::SUCCESS;
    }
}

