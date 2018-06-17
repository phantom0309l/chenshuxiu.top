<?php
// 问题管理
class XQuestionMgrAction extends AuditBaseAction
{

    // 问题列表
    public function doList () {
        ini_set('memory_limit', '200M');
        $xquestionsheetid = XRequest::getValue('xquestionsheetid', 0);

        $xquestionsheet = XQuestionSheet::getById($xquestionsheetid);
        XContext::setValue('xquestionsheet', $xquestionsheet);

        $xquestions = $xquestionsheet->getQuestions();

        $i = 0;
        foreach ($xquestions as $a) {

            if ($a->issub) {

                // 为了解决问题号为 3.10 的问题,改变存储方式为 3.901
                $x = intval($i * 1000);
                $y = $x % 1000;
                if ($y < 900) {
                    $i += 0.1;
                } else {
                    $i += 0.001;
                }
                $a->pos = $i;
            } else {
                $i = floor($i);
                $i ++;
                $a->pos = $i;
            }
        }

        XContext::setValue('list', $xquestions);

        return self::SUCCESS;
    }

    // 问题预览
    public function doModify () {
        $xquestionid = XRequest::getValue('xquestionid', 0);
        $xquestion = XQuestion::getById($xquestionid);
        XContext::setValue('xquestion', $xquestion);
        return self::SUCCESS;
    }

    // 修改排序
    public function doPosModifyPost () {
        $xquestionsheetid = XRequest::getValue('xquestionsheetid', 0);
        $posArray = XRequest::getValue('pos', array());

        foreach ($posArray as $xquestionid => $pos) {
            $xquestion = XQuestion::getById($xquestionid);
            $xquestion->pos = $pos;
        }

        $preMsg = "已保存顺序调整,并修正序号 " . XDateTime::now();
        XContext::setJumpPath("/xquestionmgr/list?xquestionsheetid={$xquestionsheetid}&preMsg=" . urlencode($preMsg));
        return self::SUCCESS;
    }

    // 问题新建
    public function doAddPost () {
        $xquestionsheetid = XRequest::getValue('xquestionsheetid', 0);
        $pos = XRequest::getValue('pos', 0);
        $type = XRequest::getValue('type', '');
        $ename = XRequest::getValue('ename', '');

        // 可以富文本, 可以不安全
        $content = XRequest::getUnSafeValue('content', '');
        $tip = XRequest::getUnSafeValue('tip', '');

        $units = XRequest::getValue('units', '');
        $qualitatives = XRequest::getValue('qualitatives', '');
        $issimple = XRequest::getValue('issimple', 0);
        $issub = XRequest::getValue('issub', 0);
        $ismust = XRequest::getValue('ismust', 0);
        $minvalue = XRequest::getValue('minvalue', 0);
        $maxvalue = XRequest::getValue('maxvalue', 0);
        // 选择题的快捷备选项
        $optionstrs = XRequest::getValue('optionstrs', '');
        $shownd = XRequest::getValue('shownd', 0);

        $xquestionsheet = XQuestionSheet::getById($xquestionsheetid);
        XContext::setValue('xquestionsheet', $xquestionsheet);

        $row = array();
        $row["xquestionsheetid"] = $xquestionsheetid;
        $row["issimple"] = $issimple;
        $row["pos"] = $pos;
        $row["type"] = $type;
        $row["ename"] = $ename;
        $row["content"] = $content;

        for ($i = 1; $i <= 5; $i ++) {
            $k = "text{$i}1";
            $row[$k] = XRequest::getValue($k, '');
            $k = "text{$i}2";
            $row[$k] = XRequest::getValue($k, '');
            $k = "content{$i}";
            $row[$k] = XRequest::getValue($k, '');
            $k = "ctype{$i}";
            $row[$k] = XRequest::getValue($k, '');
        }

        $row["tip"] = $tip;
        $row["units"] = $units;
        $row["qualitatives"] = $qualitatives;
        $row["issub"] = $issub;
        $row["ismust"] = $ismust;
        $row["minvalue"] = $minvalue;
        $row["maxvalue"] = $maxvalue;
        $row['shownd'] = $shownd;

        $q = XQuestion::createByBiz($row);

        $optionstrs = explode('|', $optionstrs);
        foreach ($optionstrs as $str) {
            if ($str === '' || $str === null) {
                continue;
            }
            $row = array();
            $row["xquestionid"] = $q->id;
            $row["content"] = $str;
            XOption::createByBiz($row);
        }

        XContext::setJumpPath(urldecode(XContext::getValue('refererUrl')));

        return self::SUCCESS;
    }

    public function doQuickCreate () {
        $xquestionsheetid = XRequest::getValue('xquestionsheetid', 0);
        XContext::setValue('xquestionsheetid', $xquestionsheetid);
        return self::SUCCESS;
    }

    // 快速创建问卷
    public function doQuickAddPost () {
        $xquestionsheetid = XRequest::getValue('xquestionsheetid', 0);
        // 问题标题以\n分割
        $titles = XRequest::getValue('titles', '');
        $titles = str_replace("\r\n", "\n", $titles);
        $titleArr = explode("\n", $titles);
        $xquestionsheet = XQuestionSheet::getById($xquestionsheetid);
        $xquestionsheet->ishidepos = 1;
        if (count($titleArr) > 1) {
            foreach ($titleArr as $i => $content) {
                $first = mb_substr($content, 0, 1, 'utf-8');
                $row = array();
                $row["xquestionsheetid"] = $xquestionsheetid;
                $row["pos"] = $i + 1;
                if ($first == "#") {
                    $row["type"] = 'Section';
                    // $row["issub"] = 1;
                    $row["content"] = mb_substr($content, 1, mb_strlen($content) - 1, 'utf-8');
                } else {
                    $row["type"] = 'Radio';
                    $row["content"] = $content;
                }
                $q = XQuestion::createByBiz($row);

                if ($first != "#") {
                    $this->createOptions($q);
                }
            }
        }

        XContext::setJumpPath("/xquestionmgr/list?xquestionsheetid={$xquestionsheetid}");
        return self::SUCCESS;
    }

    private function createOptions ($q) {
        // 选择题的快捷备选项
        $optionstrs = XRequest::getValue('optionstrs', '');
        $optionstrs = explode('|', $optionstrs);

        $scorestrs = XRequest::getValue('scorestrs', '');
        $scorestrs = explode('|', $scorestrs);

        foreach ($optionstrs as $i => $str) {
            if (empty($str)) {
                continue;
            }
            $row = array();
            $row["xquestionid"] = $q->id;
            $row["content"] = $str;
            $row["score"] = $scorestrs[$i] ?? 0;
            XOption::createByBiz($row);
        }
    }

    // 问题修改
    public function doPreview () {
        $xquestionid = XRequest::getValue('xquestionid', 0);
        $xquestion = XQuestion::getById($xquestionid);
        XContext::setValue('xquestion', $xquestion);
        return self::SUCCESS;
    }

    // 问题修改
    public function doModifyPost () {
        $xquestionid = XRequest::getValue('xquestionid', 0);
        $type = XRequest::getValue('type', '');
        $ename = XRequest::getValue('ename', '');

        // 可以富文本, 可以不安全
        $content = XRequest::getUnSafeValue('content', '');
        $tip = XRequest::getUnSafeValue('tip', '');

        $units = XRequest::getValue('units', '');
        $qualitatives = XRequest::getValue('qualitatives', '');
        $issimple = XRequest::getValue('issimple', 0);
        $issub = XRequest::getValue('issub', 0);
        $ismust = XRequest::getValue('ismust', 0);
        $rightoptionid = XRequest::getValue('rightoptionid', 0);
        $minvalue = XRequest::getValue('minvalue', 0);
        $maxvalue = XRequest::getValue('maxvalue', 0);
        $shownd = XRequest::getValue('shownd', 0);

        $xquestion = XQuestion::getById($xquestionid);
        $xquestion->type = FUtil::filterInvisibleChar($type);
        $xquestion->ename = FUtil::filterInvisibleChar($ename);
        $xquestion->content = FUtil::filterInvisibleChar($content);
        $xquestion->shownd = FUtil::filterInvisibleChar($shownd);

        for ($i = 1; $i <= 5; $i ++) {
            $k = "text{$i}1";
            $xquestion->$k = XRequest::getValue($k, '');
            $k = "text{$i}2";
            $xquestion->$k = XRequest::getValue($k, '');
            $k = "content{$i}";
            $xquestion->$k = XRequest::getValue($k, '');
            $k = "ctype{$i}";
            $xquestion->$k = XRequest::getValue($k, '');
        }

        $xquestion->tip = FUtil::filterInvisibleChar($tip);
        $xquestion->units = FUtil::filterInvisibleChar($units);
        $xquestion->qualitatives = FUtil::filterInvisibleChar($qualitatives);
        $xquestion->issimple = FUtil::filterInvisibleChar($issimple);
        $xquestion->issub = FUtil::filterInvisibleChar($issub);
        $xquestion->ismust = $ismust;
        $xquestion->rightoptionid = $rightoptionid;
        $xquestion->minvalue = $minvalue;
        $xquestion->maxvalue = $maxvalue;

        $preMsg = "修改已保存 " . XDateTime::now();
        XContext::setJumpPath("/xquestionmgr/modify?xquestionid={$xquestionid}&preMsg=" . urlencode($preMsg));
        return self::SUCCESS;
    }

    // 删除问题
    public function doDeleteJson () {
        $xquestionid = XRequest::getValue('xquestionid', 0);
        $xquestion = XQuestion::getById($xquestionid);

        $xanswercnt = $xquestion->getCntOfXanswer();
        if ($xanswercnt > 0) {
            echo 'fail';
        } else {
            $options = $xquestion->getOptions();
            foreach ($options as $o) {
                $o->remove();
            }

            $xquestion->remove();

            echo 'success';
        }


        return self::BLANK;
    }
}
