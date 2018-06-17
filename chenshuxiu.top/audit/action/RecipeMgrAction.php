<?php
// RecipeMgrAction
class RecipeMgrAction extends AuditBaseAction
{
    public function doList () {
        $pagesize = XRequest::getValue("pagesize", 20);
        $pagenum = XRequest::getValue("pagenum", 1);

        $keyword = XRequest::getValue("keyword", '');
        $status = XRequest::getValue("status", 1);

        $fromdate = XRequest::getValue("fromdate", '');
        $todate = XRequest::getValue("todate", '');

        $cond = " ";
        $bind = [];
        $url = "/recipemgr/list?1=1";

        if ($fromdate) {
            $cond .= " and a.createtime >= :fromdate ";
            $bind[':fromdate'] = $fromdate;
            $url .= "&fromdate=".$fromdate;
        }

        if ($todate) {
            $cond .= " and a.createtime <= :todate ";
            $bind[':todate'] = $todate;
            $url .= "&todate=".$todate;
        }

        $cond .= " and a.status = :status ";
        $bind[':status'] = $status;
        $url .= "&status=".$status;

        if ($keyword) {
            if (XPatientIndex::isEqual($keyword)) {
                $cond .= " and xpi.word = :word ";
                $bind[':word'] = "{$keyword}";
            } else {
                $cond .= " and xpi.word like :word ";
                $bind[':word'] = "%{$keyword}%";
            }

            $url .= "&keyword=".$keyword;
        }

        $sql = "select distinct a.*
                from recipes a
                inner join patients p on a.patientid=p.id
                inner join xpatientindexs xpi on p.id = xpi.patientid
                where 1=1
                ";

        $cond .= " order by a.createtime desc ";

        $sql .= $cond;
        $recipes = Dao::loadEntityList4Page("Recipe", $sql, $pagesize, $pagenum, $bind);

        // 翻页begin
        $countSql = "select count(distinct a.id) as cnt
                    from recipes a
                    inner join patients p on a.patientid=p.id
                    inner join xpatientindexs xpi on p.id = xpi.patientid
                    where 1=1 " . $cond;
        // 分页
        $cnt = Dao::queryValue($countSql, $bind);
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);

        XContext::setValue("keyword", $keyword);
        XContext::setValue("fromdate", $fromdate);
        XContext::setValue("todate", $todate);
        XContext::setValue("status", $status);
        XContext::setValue("recipes", $recipes);
        XContext::setValue("pagelink", $pagelink);

        return self::SUCCESS;
    }

    // 运营审核通过
    public function doPassJson () {
        $recipeid = XRequest::getValue('recipeid', 0);
        $thedate = XRequest::getValue('thedate', '0000-00-00');
        DBC::requireNotEmpty($recipeid, "recipeid为空");
        $recipe = Recipe::getById($recipeid);
        DBC::requireNotEmpty($recipe, "recipe为空");

        $recipe->thedate = $thedate;
        $recipe->status = 1;
        echo "success";
        return self::BLANK;
    }

}
