<?php
// Sfda_medicineMgrAction
class Sfda_medicineMgrAction extends AuditBaseAction
{

    public function doList () {
        $pagesize = XRequest::getValue("pagesize", 100);
        $pagenum = XRequest::getValue("pagenum", 1);

        // 模糊搜索框
        $word = XRequest::getValue("word", '');

        // 精确匹配 (提供搜索框)
        $sfda_id = XRequest::getValue("sfda_id", '');
        $piwenhao = XRequest::getValue("piwenhao", '');
        $piwenhao_old = XRequest::getValue("piwenhao_old", '');
        $benweima = XRequest::getValue("benweima", '');

        // 类型筛选 (链接)
        $type_jixing = XRequest::getValue("type_jixing", '');
        $type_chanpin = XRequest::getValue("type_chanpin", '');

        $cond = " ";
        $bind = [];

        // 模糊搜索
        if ($word != '') {
            $cond .= " and ( name_brand like :word
                    or name_common like :word
                    or name_common_en like :word
                    or company_name like :word)";
            $bind[':word'] = "%$word%";
        }

        // 精确匹配
        if ($sfda_id != '') {
            $cond .= " and sfda_id = :sfda_id ";
            $bind[':sfda_id'] = $sfda_id;
        }

        // 精确匹配
        if ($piwenhao != '') {
            $cond .= " and piwenhao = :piwenhao ";
            $bind[':piwenhao'] = $piwenhao;
        }

        // 精确匹配
        if ($piwenhao_old != '') {
            $cond .= " and piwenhao_old = :piwenhao_old ";
            $bind[':piwenhao_old'] = $piwenhao_old;
        }

        // 精确匹配
        if ($benweima != '') {
            $cond .= " and benweima = :benweima ";
            $bind[':benweima'] = $benweima;
        }

        // 补充条件
        if ($type_jixing != '') {
            $cond .= " and type_jixing = :type_jixing ";
            $bind[':type_jixing'] = $type_jixing;
        }

        // 补充条件
        if ($type_chanpin != '' && $type_chanpin != 'all') {
            $cond .= " and type_chanpin = :type_chanpin ";
            $bind[':type_chanpin'] = $type_chanpin;
        }

        $sfda_medicines = Dao::getEntityListByCond4Page('Sfda_medicine', $pagesize, $pagenum, $cond . " order by id ", $bind);

        $cnt = Dao::queryValue("select count(*) from sfda_medicines where 1=1 $cond", $bind);

        $url = "/sfda_medicinemgr/list?";
        $url .= "&word=" . urlencode($word);
        $url .= "&sfda_id=" . urlencode($sfda_id);
        $url .= "&piwenhao=" . urlencode($piwenhao);
        $url .= "&piwenhao_old=" . urlencode($piwenhao_old);
        $url .= "&benweima=" . urlencode($benweima);
        $url .= "&type_jixing=" . urlencode($type_jixing);
        $url .= "&type_chanpin=" . urlencode($type_chanpin);

        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);
        XContext::setValue("pagelink", $pagelink);

        XContext::setValue('sfda_medicines', $sfda_medicines);
        XContext::setValue('word', $word);
        XContext::setValue('sfda_id', $sfda_id);
        XContext::setValue('piwenhao', $piwenhao);
        XContext::setValue('piwenhao_old', $piwenhao_old);
        XContext::setValue('benweima', $benweima);
        XContext::setValue('type_jixing', $type_jixing);
        XContext::setValue('type_chanpin', $type_chanpin);

        return self::SUCCESS;
    }
}
