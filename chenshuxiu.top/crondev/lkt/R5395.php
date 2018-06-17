<?php
/**
 * Created by PhpStorm.
 * User: hades
 * Date: 2017/12/5
 * Time: 10:45
 */
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once(dirname(__FILE__) . "/../../sys/PathDefine.php");
include_once(ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

echo "\n\n-----begin----- " . XDateTime::now() . "\n\n";

//$unitofwork = BeanFinder::get("UnitOfWork");
$diseaseids = Disease::getCancerDiseaseidsStr();
$sql = "SELECT a.id
        FROM doctors a
        LEFT JOIN doctordiseaserefs b ON a.id = b.doctorid
        WHERE b.diseaseid IN ({$diseaseids})
        AND a.id NOT IN (11, 1583)
        AND a.menzhen_offset_daycnt > 0
        GROUP BY a.id";
$doctorids = Dao::queryValues($sql);

$brief = 0;
$i = 1;
$maximum = 50;

$ids = implode(',', $doctorids);
echo "doctorids（" . count($doctorids) . "）：" . $ids . "\n\n";

$sql = "SELECT DISTINCT *
        FROM wxusers
        WHERE doctorid IN ($ids)
        AND subscribe = 1
        ORDER BY wxshopid ";
$wxusers = Dao::loadEntityList("WxUser", $sql);
echo "共 " . count($wxusers) . " 条wxuser";

// 分组wxuser
$wxshop_wxusers = [];
foreach ($wxusers as $wxuser) {
    if ($wxuser->wxshopid == 1 || $wxuser->wxshopid == 8) {
        continue;
    }
    $arr = $wxshop_wxusers[$wxuser->wxshopid] ?? [];

    $arr[] = $wxuser;

    $wxshop_wxusers[$wxuser->wxshopid] = $arr;
}

echo "，按公众号分为 " . count($wxshop_wxusers) . " 组";
echo "\n";

/* 批量创建标签，批量给微信用户打标签 */
//createTag($wxshop_wxusers);

/* 批量删除标签 */
deleteTag($wxshop_wxusers);

// 创建标签，批量给微信用户打标签
function createTag($wxshop_wxusers) {
    foreach ($wxshop_wxusers as $wxshopid => $wxusers) {
        $wxshop = WxShop::getById($wxshopid);
        echo "\n--------------------------------------------\n";
        echo "\n公众号：" . $wxshop->id . "，" . $wxshop->name . "\n\n";

        $tags = WxApi::getTags($wxshop);

        $tagid = null;
        foreach ($tags as $tag) {
            echo $tag['id'] . "：" . $tag['name'] . "(" . $tag['count'] . ")\n";
            if ($tag['name'] == '#5395临时分组') {
                $tagid = $tag['id'];
                continue;
            }
        }

        if (empty($tagid)) {
            echo "\n未创建临时分组，开始创建 ";
            $tagid = WxApi::createTag($wxshop, '#5395临时分组');
            echo "#5395临时分组id：" . $tagid . "\n";
        }

        $count = count($wxusers);
        echo "公众号：" . $wxshop->name . "下共 " . count($wxusers) . " 个wxuser\n";

        echo "开始给wxuser批量打标签\n";

        $maximum = 50;
        $group_count = ceil($count / $maximum);
        echo "向上取整共 {$group_count} 组\n";
        for ($i = 0; $i < $group_count; $i++) {
            $arr = array_slice($wxusers, $i * $maximum, $maximum);

            $openid_list = [];
            foreach ($arr as $wxuser) {
                $openid_list[] = $wxuser->openid;
            }
            $access_token = $wxshop->getAccessToken();
            $url = "https://api.weixin.qq.com/cgi-bin/tags/members/batchtagging?access_token={$access_token}";
            $fields = [
                "openid_list" => $openid_list,
                "tagid" => $tagid];
            $fields = urldecode(json_encode($fields));
            $jsonStr = XHttpRequest::curl_postUrlContents($url, $fields);
            echo "========batchTagging[{$jsonStr}]==============";
            Debug::trace("========batchTagging[{$jsonStr}]==============");
            $errmsg = json_decode($jsonStr, true);

            echo $errmsg;
            echo "\n\n";
        }
    }
}

// 批量删除标签
function deleteTag($wxshop_wxusers) {
    foreach ($wxshop_wxusers as $wxshopid => $wxusers) {
        $wxshop = WxShop::getById($wxshopid);
        echo "\n--------------------------------------------\n";
        echo "\n公众号：" . $wxshop->id . "，" . $wxshop->name . "\n\n";

        $tags = WxApi::getTags($wxshop);
        foreach ($tags as $tag) {
            echo $tag['id'] . "：" . $tag['name'] . "(" . $tag['count'] . ")\n";
            if ($tag['name'] == '#5395临时分组') {
                echo '发现目标分组，开始删除';
                $errmsg = WxApi::deleteTag($wxshop, $tag['id']);
                echo $errmsg . "\n";
                break;
            }
        }
    }
}
