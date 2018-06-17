<?php

class WxTemplateService
{

    public static function createTemplateContent($first, $keywords, $remark = "") {
        $content = array(
            "first" => array(
                "value" => "{$first['value']}",
                "color" => "{$first['color']}"),
            "remark" => array(
                "value" => "{$remark}",
                "color" => "#000000"));

        foreach ($keywords as $i => $v) {
            $content["keyword" . ($i + 1)] = array(
                "value" => "{$v['value']}",
                "color" => "{$v['color']}");
        }

        return json_encode($content, JSON_UNESCAPED_UNICODE);
    }

    public static function getSendContent ($ename, $first, $keywords = [], $remark = "") {
        $wxTemplateConfigArrr = WxTemplateService::getWxTemplateConfigArr();

        $arr = $wxTemplateConfigArrr[$ename];

        if(is_array($arr)){
            $arr["first"]["value"] = $first;
            foreach ($keywords as $k => $keyword) {
                $arr["keywords"][$k]["value"] = $keyword;
            }
        } else {
            Debug::warn(__METHOD__ . "没有此ename[{$ename}]的配置");
            return "";
            // $arr = [];
            // $arr["first"] = array(
            //         "value" => $first,
            //         "color" => ""
            //     );
            // foreach ($keywords as $k => $keyword) {
            //     $arr["keywords"][$k]["value"] = $keyword;
            //     $arr["keywords"][$k]["color"] = "";
            // }
        }

        return WxTemplateService::createTemplateContent($arr["first"], $arr["keywords"], $remark);
    }

    // http://audit.fangcunyisheng.com/wxtemplatemgr/list 各个模版格式列表页
    public static function getWxTemplateConfigArr () {
        $arr = array(
            "adminNotice" => array(
                "first" => array(
                    "value" => "",
                    "color" => ""
                ),
                "keywords" => array(
                    array(
                        "title" => "管理员",
                        "value" => "",
                        "color" => "#aaa"),
                    array(
                        "title" => "通知内容",
                        "value" => "",
                        "color" => "#ff6600")
                )
            ),
            "followupNotice" => array(
                "first" => array(
                    "value" => "",
                    "color" => ""
                ),
                "keywords" => array(
                    array(
                        "title" => "姓名",
                        "value" => "",
                        "color" => "#aaa"),
                    array(
                        "title" => "随访时间",
                        "value" => "",
                        "color" => "#aaa"),
                    array(
                        "title" => "随访内容",
                        "value" => "",
                        "color" => "#ff6600")
                )
            ),
            "doctornotice" => array(
                "first" => array(
                    "value" => "",
                    "color" => ""
                ),
                "keywords" => array(
                    array(
                        "title" => "医生",
                        "value" => "",
                        "color" => "#ff6600"),
                    array(
                        "title" => "医嘱内容",
                        "value" => "",
                        "color" => "#ff6600")
                )
            ),
            "PatientMgrNotice" => array(
                "first" => array(
                    "value" => "",
                    "color" => "#3366ff"
                ),
                "keywords" => array(
                    array(
                        "title" => "时间",
                        "value" => "",
                        "color" => ""),
                    array(
                        "title" => "数量",
                        "value" => "",
                        "color" => "")
                )
            ),
            "BedTktAuditNotice" => array(
                "first" => array(
                    "value" => "",
                    "color" => "#009900"
                ),
                "keywords" => array(
                    array(
                        "title" => "患者姓名",
                        "value" => "",
                        "color" => ""),
                    array(
                        "title" => "住院日期",
                        "value" => "",
                        "color" => "")
                )
            ),
            "jyjc_remind" => array(
                "first" => array(
                    "value" => "",
                    "color" => ""
                ),
                "keywords" => array(
                    array(
                        "title" => "项目",
                        "value" => "",
                        "color" => "#ff6600"),
                    array(
                        "title" => "时间",
                        "value" => "",
                        "color" => "#ff6600"),
                    array(
                        "title" => "位置",
                        "value" => "",
                        "color" => "#ff6600")
                )
            ),
            "BedTktPatientConfirm" => array(
                "first" => array(
                    "value" => "",
                    "color" => "#3366ff"
                ),
                "keywords" => array(
                    array(
                        "title" => "患者姓名",
                        "value" => "",
                        "color" => ""),
                    array(
                        "title" => "住院日期",
                        "value" => "",
                        "color" => ""),
                    array(
                        "title" => "处理结果",
                        "value" => "",
                        "color" => "#3366ff")
                )
            ),
            "info_check_notice" => array(
                "first" => array(
                    "value" => "",
                    "color" => ""
                ),
                "keywords" => array(
                    array(
                        "title" => "患者姓名",
                        "value" => "",
                        "color" => "#ff6600"),
                    array(
                        "title" => "医生",
                        "value" => "",
                        "color" => "#ff6600"),
                    array(
                        "title" => "核对事项",
                        "value" => "",
                        "color" => "#ff6600")
                )
            ),
            "RevisitTktList" => array(
                "first" => array(
                    "value" => "",
                    "color" => "#3366ff"
                ),
                "keywords" => array(
                    array(
                        "title" => "接诊时间",
                        "value" => "",
                        "color" => ""),
                    array(
                        "title" => "接诊人数",
                        "value" => "",
                        "color" => ""),
                    array(
                        "title" => "患者名单",
                        "value" => "",
                        "color" => "")
                )
            ),
            "RevisitTktRemind" => array(
                "first" => array(
                    "value" => "",
                    "color" => ""
                ),
                "keywords" => array(
                    array(
                        "title" => "姓名",
                        "value" => "",
                        "color" => ""),
                    array(
                        "title" => "电话",
                        "value" => "",
                        "color" => ""),
                    array(
                        "title" => "期望日期",
                        "value" => "",
                        "color" => "")
                )
            ),
            "auditor2doctor" => array(
                "first" => array(
                    "value" => "",
                    "color" => "#415a93"
                ),
                "keywords" => array(
                    array(
                        "title" => "咨询人姓名",
                        "value" => "",
                        "color" => "#aaa"),
                    array(
                        "title" => "咨询医院",
                        "value" => "",
                        "color" => "#aaa"),
                    array(
                        "title" => "所属科室",
                        "value" => "",
                        "color" => "#aaa"),
                    array(
                        "title" => "咨询医生",
                        "value" => "",
                        "color" => "#aaa"),
                    array(
                        "title" => "申请时间",
                        "value" => "",
                        "color" => "#aaa")
                )
            ),
            "zxhfxxtx" => array(
                "first" => array(
                    "value" => "",
                    "color" => ""
                ),
                "keywords" => array(
                    array(
                        "title" => "咨询名称",
                        "value" => "",
                        "color" => ""),
                    array(
                        "title" => "消息回复",
                        "value" => "",
                        "color" => "")
                )
            ),
        );
        return $arr;
    }

}
