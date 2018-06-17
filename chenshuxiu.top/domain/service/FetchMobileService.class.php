<?php

/**
 * Created by PhpStorm.
 * User: Jerry
 * Date: 2018/5/14
 * Time: 10:00
 */
class FetchMobileService
{

    /* 抓去错误 或者 抓取不到 返回null
     * 返回格式为标准json：
    {
        "status": "0",
        "t": "",
        "set_cache_time": "",
        "data": [{
            "StdStg": 6004,
            "StdStl": 8,
            "_update_time": "1522310082",
            "cambrian_appid": "0",
            "city": "北京",
            "key": "1831137",
            "prov": "",
            "showurl": "http:\/\/haoma.baidu.com",
            "title": "XXX",
            "type": "中国移动",
            "url": "http:\/\/haoma.baidu.com",
            "loc": "https:\/\/ss1.baidu.com\/8aQDcnSm2Q5IlBGlnYG\/q?r=2002696&k=1831137",
            "SiteId": 2002696,
            "_version": 24614,
            "_select_time": 1522310070,
            "querytype": "手机号码",
            "phoneinfo": "手机号码&quot;18311374180&quot;",
            "phoneno": "18311374180",
            "origphoneno": "18311374180",
            "titlecont": "手机号码归属地查询",
            "showlamp": "1",
            "clickneed": "0",
            "ExtendedLocation": "",
            "OriginQuery": "18311374180",
            "tplt": "mobilephone",
            "resourceid": "6004",
            "fetchkey": "6004_1831137",
            "appinfo": "",
            "role_id": 1,
            "disp_type": 0
        }]
    }
     */
    public static function fetchByMobile($mobile) {
        //电话号码归属地查询地址（使用的是百度免费的）
        $fetch_url = "https://sp0.baidu.com/8aQDcjqpAAV3otqbppnN2DJv/api.php?resource_name=guishudi&query={$mobile}";

        $ch = curl_init();
        // 超时设置
        $this_header = array(
            "content-type: application/json;charset=UTF-8"
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this_header);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_URL, $fetch_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $content = curl_exec($ch);
        $errno = curl_errno($ch);

        if ($errno) {
            Debug::trace('Curl error: ' . curl_error($ch));
            return null;
        }
        curl_close($ch);

        //返回的不是utf-8，这里需要转换
        $content = mb_convert_encoding($content, 'UTF-8', 'UTF-8,GBK,GB2312,BIG5');

        $ret = json_decode($content, true);
        if (!is_array($ret)) {
            Debug::trace(__METHOD__ . " {$fetch_url} fetch mobile return is not a json string " . $content);
            return null;
        }
        if ($ret['status'] != 0) {
            Debug::trace(__METHOD__ . " fetch mobile return err " . $ret['msg']);
            return null;
        }

        Debug::trace(__METHOD__ . " fetch mobile success " . $content);
        $cityName = $ret['data'][0]['city'] ?? '';

        //直辖市只会返回市，对应补上省
        if (in_array($cityName, ['北京', '天津', '重庆', '上海'])) {
            $ret['data'][0]['prov'] = $cityName;
        }

        return $ret;
    }
}