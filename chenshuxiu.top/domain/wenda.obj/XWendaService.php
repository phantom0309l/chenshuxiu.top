<?php

// 问答系统
class XWendaService
{

    // 返回最大的xanswer
    public static function doPost (array $sheets, $owner, $objtype = '', $objid = 0, &$logContent = '') {
        $maxXAnswer = null;

        // 新增
        if (isset($sheets['XQuestionSheet'])) {
            $maxXAnswer = self::postXAnswerSheetAdd($sheets, $owner, $objtype, $objid);
        }

        // 修改
        if (isset($sheets['XAnswerSheet'])) {
            $maxXAnswer = self::postXAnswerSheetModify($sheets, $logContent);
        }

        return $maxXAnswer;
    }

    // 返回最大的xanswer
    public static function doPostMustQuestion (array $sheets, $owner, $objtype = '', $objid = 0) {
        $maxXAnswer = null;

        // 新增
        if (isset($sheets['XQuestionSheet'])) {
            $maxXAnswer = self::postXAnswerSheetAddMustQuestion($sheets, $owner, $objtype, $objid);
        }

        // 修改
        if (isset($sheets['XAnswerSheet'])) {
            $maxXAnswer = self::postXAnswerSheetModify($sheets);
        }

        return $maxXAnswer;
    }

    // 返回最大的xanswer
    public static function doModifyAll (array $sheets) {
        $maxXAnswer = self::postXAnswerSheetModifyAll($sheets);

        return $maxXAnswer;
    }

    // 返回最大的xanswer
    private static function postXAnswerSheetAddMustQuestion (array $sheets, $owner, $objtype = '', $objid = 0) {
        $xanswers = array();
        $maxXAnswer = null;

        $wxuserid = 0;
        $userid = 0;
        $patientid = 0;

        if ($owner instanceof WxUser) {
            // WxUser
            $wxuserid = $owner->id;
            $userid = $owner->userid;
            $patientid = $owner->user->patientid;
        } elseif ($owner instanceof User) {
            // User
            $userid = $owner->id;
            $patientid = $owner->patientid;
        } elseif ($owner instanceof Patient) {
            // Patient
            $patientid = $owner->id;
        }

        $mywxuser = XContext::getValueEx('mywxuser', null);
        if ($mywxuser instanceof WxUser && $mywxuser->userid == $userid) {
            $wxuserid = $mywxuser->id;
        }

        // 后门,后台代用户填答卷
        $patient4sheet = XContext::getValueEx('patient4sheet', null);
        if ($patient4sheet instanceof Patient) {
            $wxuserid = 0;
            $userid = 0;
            $patientid = $patient4sheet->id;
        }

        foreach ($sheets['XQuestionSheet'] as $xquestionsheetid => $arr) {
            $xquestionsheet = XQuestionSheet::getById($xquestionsheetid);
            $row = array();
            $row["wxuserid"] = $wxuserid;
            $row["userid"] = $userid;
            $row["patientid"] = $patientid;
            $row["xquestionsheetid"] = $xquestionsheetid;
            $row["objtype"] = $objtype;
            $row["objid"] = $objid;
            $xanswersheet = XAnswerSheet::createByBiz($row);

            foreach ($arr as $xquestionid => $arr2) {
                $q = XQuestion::getById($xquestionid);

                // 创建xanswer
                $xanswer = self::createXanswer($xanswersheet, $q, $arr2);

                // 设置新的选项
                $xoptions = self::getNeedAddXoptions($arr2);
                $xanswer->addXAnswerOptionRefsAndScore($xoptions);

                // 修正
                $xanswers[] = $maxXAnswer = $xanswer;
            }
        }

        return $maxXAnswer;
    }

    // 返回最大的xanswer
    private static function postXAnswerSheetAdd (array $sheets, $owner, $objtype = '', $objid = 0) {
        $xanswers = array();
        $maxXAnswer = null;

        $wxuserid = 0;
        $userid = 0;
        $patientid = 0;

        if ($owner instanceof WxUser) {
            // WxUser
            $wxuserid = $owner->id;
            $userid = $owner->userid;
            $patientid = $owner->user->patientid;
        } elseif ($owner instanceof User) {
            // User
            $userid = $owner->id;
            $patientid = $owner->patientid;
        } elseif ($owner instanceof Patient) {
            // Patient
            $patientid = $owner->id;
        }

        // 微信,页面
        $mywxuser = XContext::getValueEx('mywxuser', null);
        if ($mywxuser instanceof WxUser) {
            $wxuserid = $mywxuser->id;
            $userid = $mywxuser->userid;
            $patientid = $mywxuser->user->patientid;
        }

        // 后门,后台待用户填答卷
        $patient4sheet = XContext::getValueEx('patient4sheet', null);
        if ($patient4sheet instanceof Patient) {
            $wxuserid = 0;
            $userid = 0;
            $patientid = $patient4sheet->id;
        }

        foreach ($sheets['XQuestionSheet'] as $xquestionsheetid => $arr) {
            $xquestionsheet = XQuestionSheet::getById($xquestionsheetid);
            $row = array();
            $row["wxuserid"] = $wxuserid;
            $row["userid"] = $userid;
            $row["patientid"] = $patientid;
            $row["doctorid"] = 0; // need pcard fix
            $row["xquestionsheetid"] = $xquestionsheetid;
            $row["objtype"] = $objtype;
            $row["objid"] = $objid;
            $xanswersheet = XAnswerSheet::createByBiz($row);

            foreach ($xquestionsheet->getQuestions() as $q) {
                if (false == isset($arr[$q->id])) {
                    $arr2 = array();
                    $arr2['content'] = '';
                    $arr2['unit'] = '';
                    $arr2['qualitative'] = '';
                } else {
                    $arr2 = $arr[$q->id];
                }

                // 创建xanswer
                $xanswer = self::createXanswer($xanswersheet, $q, $arr2);

                // 设置新的选项
                $xoptions = self::getNeedAddXoptions($arr2);
                $xanswer->addXAnswerOptionRefsAndScore($xoptions);

                // 修正
                $xanswers[] = $maxXAnswer = $xanswer;
            }
        }

        return $maxXAnswer;
    }

    // 提交答卷修改
    private static function postXAnswerSheetModify (array $sheets, &$logContent = '') {
        $maxXAnswer = null;

        $logContent = '';

        foreach ($sheets['XAnswerSheet'] as $xanswersheetid => $arr) {
            $xanswersheet = XAnswerSheet::getById($xanswersheetid);

            foreach ($arr as $xquestionid => $arr2) {
                $q = XQuestion::getById($xquestionid);

                $xanswerSnap = '';
                $xanswerOptionRefSnaps = '';
                $xanswer = $xanswersheet->getAnswer($xquestionid);
                if(false == $xanswer instanceof XAnswer){
                    $xanswer = self::createXanswer($xanswersheet, $q, $arr2);
                }else{
                    $xanswerSnap = clone $xanswer;
                    $xanswerOptionRefSnaps = $xanswer->getXAnswerOptionRefs();
                }

                $xoptions = self::getNeedAddXoptions($arr2);

                //清除旧的选项
                $xanswer->removeXanswerOptionRefsAndScore();
                //修正
                $xanswer = self::fixXanswer($xanswer, $arr2);

                // 修正
                $maxXAnswer = $xanswer;

                // 计算变化日志
                $logContent .= self::getLogContent($xanswerSnap, $xanswer, $xanswerOptionRefSnaps, $xoptions, $q);
            }
        }

        return $maxXAnswer;
    }

    // 提交答卷修改
    private static function postXAnswerSheetModifyAll (array $sheets) {
        $maxXAnswer = null;

        foreach ($sheets['XAnswerSheet'] as $xanswersheetid => $arr) {
            $xanswersheet = XAnswerSheet::getById($xanswersheetid);

            $xanswers = $xanswersheet->getAnswers();

            foreach ($xanswers as $xanswer) {
                //清除旧的选项
                $xanswer->removeXanswerOptionRefsAndScore();

                $q = $xanswer->xquestion;
                $arr2 = [];
                if(isset($arr[$q->id])){
                    $arr2 = $arr[$q->id];
                    $xanswer = self::fixXanswer($xanswer, $arr2);
                }
            }

            // 修正
            $maxXAnswer = $xanswer;
        }

        return $maxXAnswer;
    }

    private static function fixXanswer (XAnswer $xanswer, $arr2) {
        //重新赋值
        $xanswer = self::modifyXanswer($xanswer, $arr2);

        $xoptions = self::getNeedAddXoptions($arr2);

        //设置新的选项
        $xanswer->addXAnswerOptionRefsAndScore($xoptions);

        return $xanswer;
    }

    private static function createXanswer($xanswersheet, $xquestion, $arr){
        $row = array();
        $row["xanswersheetid"] = $xanswersheet->id;
        $row["xquestionid"] = $xquestion->id;
        $row["pos"] = $xquestion->pos;
        $row["content"] = isset($arr['content']) ? $arr['content'] : '';
        $row["content2"] = isset($arr['content2']) ? $arr['content2'] : '';
        $row["content3"] = isset($arr['content3']) ? $arr['content3'] : '';
        $row["content4"] = isset($arr['content4']) ? $arr['content4'] : '';
        $row["content5"] = isset($arr['content5']) ? $arr['content5'] : '';
        $row["unit"] = isset($arr['unit']) ? $arr['unit'] : $xquestion->units;
        $row["qualitative"] = isset($arr['qualitative']) ? $arr['qualitative'] : '';
        $row['isnd'] = isset($arr['isnd']) ? $arr['isnd'] : 0;
        $row["text11"] = $xquestion->text11;
        $row["text21"] = $xquestion->text21;
        $row["text31"] = $xquestion->text31;
        $row["text41"] = $xquestion->text41;
        $row["text51"] = $xquestion->text51;
        $row["text12"] = $xquestion->text12;
        $row["text22"] = $xquestion->text22;
        $row["text32"] = $xquestion->text32;
        $row["text42"] = $xquestion->text42;
        $row["text52"] = $xquestion->text52;
        return XAnswer::createByBiz($row);
    }

    private static function modifyXanswer ($xanswer, $arr) {
        //重新赋值
        $xanswer->content = isset($arr['content']) ? $arr['content'] : '';
        $xanswer->content2 = isset($arr['content2']) ? $arr['content2'] : '';
        $xanswer->content3 = isset($arr['content3']) ? $arr['content3'] : '';
        $xanswer->content4 = isset($arr['content4']) ? $arr['content4'] : '';
        $xanswer->content5 = isset($arr['content5']) ? $arr['content5'] : '';
        $xanswer->unit = isset($arr['unit']) ? $arr['unit'] : $xanswer->xquestion->units;
        $xanswer->qualitative = isset($arr['qualitative']) ? $arr['qualitative'] : '';
        $xanswer->isnd = isset($arr['isnd']) ? $arr['isnd'] : 0;

        return $xanswer;
    }

    private static function getNeedAddXoptions ($arr) {
        $xoptions = array();
        if (isset($arr['options'])) {
            foreach ($arr['options'] as $xoptionid) {
                if ($xoptionid === '' || $xoptionid === null || $xoptionid === '-1') {
                    continue;
                }
                $xoptions[] = XOption::getById($xoptionid);
            }
        }

        return $xoptions;
    }

    private static function getLogContent ($xanswerSnap, $xanswer, $xanswerOptionRefSnaps, $xoptions, $question) {
        if (false == $xanswerSnap instanceof XAnswer) {
            return "";
        }
        $xanswerArrSnap = $xanswerSnap->toJsonArray();
        $xanswerArr = $xanswer->toJsonArray();
        $str1 = '';
        foreach ($xanswerArr as $key => $val) {
            if ($key == 'content' || $key == 'unit' || $key == 'qualitative') {
                if ($xanswerArrSnap[$key] != $val) {
                    $keyDesc = XAnswerHelper::getFieldDesc($key);
                    $str1 .= "{$keyDesc} 从[{$xanswerArrSnap[$key]}]修改为[{$val}], ";
                }
            }
        }
        // 计算option变化
        $xoptionids = array();
        $xoptionidSnaps = array();
        $xoptionSnaps = array();
        $str2 = '';

        if ($xanswerOptionRefSnaps && $xoptions) {
            foreach ($xanswerOptionRefSnaps as $xanswerOptionRefSnap) {
                $xoption = $xanswerOptionRefSnap->xoption;
                $xoptionidSnaps[] = $xoption->id;
                $xoptionSnaps[] = $xoption;
            }

            foreach ($xoptions as $xoption) {
                $xoptionids[] = $xoption->id;
            }

            if (array_diff($xoptionidSnaps, $xoptionids) || array_diff($xoptionids, $xoptionidSnaps)) { // 有变化
                $snapOptionContent = '';
                $optionContent = '';
                foreach ($xoptionSnaps as $xoptionSnap) {
                    $snapOptionContent .= $xoptionSnap->content . ' ';
                }
                foreach ($xoptions as $xoption) {
                    $optionContent .= $xoption->content . ' ';
                }

                $str2 .= "选项 从[{$snapOptionContent}]修改为[{$optionContent}], ";
            }
        } else
            if ($xanswerOptionRefSnaps && ! $xoptions) {
                foreach ($xanswerOptionRefSnaps as $xanswerOptionRefSnap) {
                    $xoption = $xanswerOptionRefSnap->xoption;
                    $snapOptionContent .= $xoption->content . ' ';
                }
                $str2 .= "选项 从[{$snapOptionContent}]修改为[], ";
            } else
                if (! $xanswerOptionRefSnaps && $xoptions) {
                    foreach ($xoptions as $xoption) {
                        $optionContent .= $xoption->content . ' ';
                    }
                    $str2 .= "选项 从[]修改为[{$optionContent}], ";
                }
        if ($str1 || $str2) {
            $logContent .= "将<{$question->content}>";
            $logContent .= $str1 . $str2;
        }

        return $logContent;
    }
}
