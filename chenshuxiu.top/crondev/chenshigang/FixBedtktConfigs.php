<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once (dirname(__FILE__) . "/../../sys/PathDefine.php");
include_once (ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

$bedtktConfigs = Dao::getEntityListByCond('BedTktConfig', ' AND typestr="treat"');
$unitofwork = BeanFinder::get("UnitOfWork");

$i = 0;
foreach ($bedtktConfigs as $one) {
    $content = json_decode($one->content, true);
    //医保类型
    if (!isset($content['is_feetype_show'])) {
        $content['is_feetype_show'] = 0;
    }
    if (!isset($content['is_feetype_must'])) {
        $content['is_feetype_must'] = 0;
    }
    //入住日期
    if (!isset($content['is_plandate_show'])) {
        $content['is_plandate_show'] = 0;
    }
    if (!isset($content['is_plandate_must'])) {
        $content['is_plandate_must'] = 0;
    }
    //住院证照片
    if (!isset($content['is_zhuyuan_photo_show'])) {
        $content['is_zhuyuan_photo_show'] = 0;
    }
    if (!isset($content['is_zhuyuan_photo_must'])) {
        $content['is_zhuyuan_photo_must'] = 0;
    }
    //血常规照片
    if (!isset($content['is_xuechanggui_photo_show'])) {
        $content['is_xuechanggui_photo_show'] = 0;
    }
    if (!isset($content['is_xuechanggui_photo_must'])) {
        $content['is_xuechanggui_photo_must'] = 0;
    }
    //肝肾功照片
    if (!isset($content['is_gangongneng_photo_show'])) {
        $content['is_gangongneng_photo_show'] = 0;
    }
    if (!isset($content['is_gangongneng_photo_must'])) {
        $content['is_gangongneng_photo_must'] = 0;
    }

    //身份证号
    if (!isset($content['is_idcard_show'])) {
        $content['is_idcard_show'] = 0;
    }
    if (!isset($content['is_idcard_must'])) {
        $content['is_idcard_must'] = 0;
    }
    //住院号
    if (!isset($content['is_zhuyuanhao_show'])) {
        $content['is_zhuyuanhao_show'] = 0;
    }
    if (!isset($content['is_zhuyuanhao_must'])) {
        $content['is_zhuyuanhao_must'] = 0;
    }
    //病史
    if (!isset($content['is_bingshi_show'])) {
        $content['is_bingshi_show'] = 0;
    }
    if (!isset($content['is_bingshi_must'])) {
        $content['is_bingshi_must'] = 0;
    }
    //临床表现
    if (!isset($content['is_linchuangbiaoxian_show'])) {
        $content['is_linchuangbiaoxian_show'] = 0;
    }
    if (!isset($content['is_linchuangbiaoxian_must'])) {
        $content['is_linchuangbiaoxian_must'] = 0;
    }
    //其他疾病
    if (!isset($content['is_otherdisease_show'])) {
        $content['is_otherdisease_show'] = 0;
    }
    if (!isset($content['is_otherdisease_must'])) {
        $content['is_otherdisease_must'] = 0;
    }
    //心功能分级
    if (!isset($content['is_xingongnengfenji_show'])) {
        $content['is_xingongnengfenji_show'] = 0;
    }
    if (!isset($content['is_xingongnengfenji_must'])) {
        $content['is_xingongnengfenji_must'] = 0;
    }
    //手术日期
    if (!isset($content['is_shoushuriqi_show'])) {
        $content['is_shoushuriqi_show'] = 0;
    }
    if (!isset($content['is_shoushuriqi_must'])) {
        $content['is_shoushuriqi_must'] = 0;
    }
    //心电图
    if (!isset($content['is_xindiantu_show'])) {
        $content['is_xindiantu_show'] = 0;
    }
    if (!isset($content['is_xindiantu_must'])) {
        $content['is_xindiantu_must'] = 0;
    }
    //血栓弹力图
    if (!isset($content['is_xueshuantanlitu_show'])) {
        $content['is_xueshuantanlitu_show'] = 0;
    }
    if (!isset($content['is_xueshuantanlitu_must'])) {
        $content['is_xueshuantanlitu_must'] = 0;
    }
    //风湿免疫检查
    if (!isset($content['is_fengshimianyijiancha_show'])) {
        $content['is_fengshimianyijiancha_show'] = 0;
    }
    if (!isset($content['is_fengshimianyijiancha_must'])) {
        $content['is_fengshimianyijiancha_must'] = 0;
    }
    //术前其他检查
    if (!isset($content['is_shuqianqitajiancha_show'])) {
        $content['is_shuqianqitajiancha_show'] = 0;
    }
    if (!isset($content['is_shuqianqitajiancha_must'])) {
        $content['is_shuqianqitajiancha_must'] = 0;
    }
    echo ++$i, "\t", $one->doctor->name, "\n";
    $one->content = json_encode($content, JSON_UNESCAPED_UNICODE);
}
$unitofwork->commitAndRelease();

Debug::flushXworklog();
