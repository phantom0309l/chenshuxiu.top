<?php

/*
 * 微信接口
 */

class WxApi
{

    // 请求api前缀
    const API_URL_PREFIX = 'https://api.weixin.qq.com/cgi-bin';
    // 创建二维码ticket
    const QRCODE_CREATE_URL = '/qrcode/create?';

    public function __construct() {
    }

    // 最后调用的结果
    public static $lastJsonStr = '';

    public static $last_errcode = '';

    public static $last_errmsg = '';

    // 字段定义
    public static function getKeysDefine() {
        return array(
            'ToUserName',  //
            'FromUserName',  //
            'CreateTime',  //
            'MsgId',  //
            'MsgType',  //
            'Content',  //
            'MediaId',  //
            'PicUrl',  //
            'Format',  //
            'ThumbMediaId',  //
            'Location_X',  //
            'Location_Y',  //
            'Scale',  //
            'Label',  //
            'Title',  //
            'Description',  //
            'Url'); //
    }

    // 通过code获取openid
    public static function getOpenidByCode($appid, $secret, $code) {
        Debug::trace("WxApi::getOpenidByCode('{$appid}','****','{$code}')");

        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$appid}&secret={$secret}&code={$code}&grant_type=authorization_code";

        // $json = XHttpRequest::my_file_get_contents ( $url );
        $jsonstr = XHttpRequest::curl_getUrlContents($url);

        $json = json_decode($jsonstr, true);

        $openid = $json['openid'] ? $json['openid'] : '';

        Debug::trace("[openid={$openid}]");

        if (empty($openid)) {
            Debug::trace(__METHOD__ . " openid is null url:$url ret:$jsonstr");
        }

        return $openid;
    }

    // 未关注情况下，获取用户的基本信息
    public static function getUserBaseMsg($appid, $secret, $code) {
        Debug::trace("WxApi::getUserBaseMsg('{$appid}','****','{$code}')");

        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$appid}&secret={$secret}&code={$code}&grant_type=authorization_code";

        $jsonstr = XHttpRequest::curl_getUrlContents($url);
        $json = json_decode($jsonstr, true);

        $openid = $json['openid'];
        $access_token = $json['access_token'];
        $url = "https://api.weixin.qq.com/sns/userinfo?access_token={$access_token}&openid={$openid}&lang=zh_CN";
        $jsonstr = XHttpRequest::curl_getUrlContents($url);
        $json = json_decode($jsonstr, true);

        return $json;
    }

    // 获取微信模板列表
    public static function getWxTemplateList($wxshopid) {
        $template_list = array();
        $wxshop = WxShop::getById($wxshopid);
        if ($wxshop instanceof WxShop) {
            $access_token = $wxshop->getAccessToken();
            $url = "https://api.weixin.qq.com/cgi-bin/template/get_all_private_template?access_token={$access_token}";

            $jsonstr = XHttpRequest::curl_getUrlContents($url);
            $json = json_decode($jsonstr, true);
            $template_list = $json['template_list'];
        }
        return $template_list;
    }

    // 获取微信模板内容项的标题
    public static function getWxTemplateContentTitleArr($template_id, $wxshopid) {
        $content = "";

        $template_list = self::getWxTemplateList($wxshopid);
        foreach ($template_list as $item) {
            if ($item["template_id"] == $template_id) {
                $content = $item["content"];
                break;
            }
        }
        $regex = '/\n(.+)：/';
        $match = array();
        preg_match_all($regex, $content, $match);
        return $match[1];
    }

    // 是普通消息
    public static function isCommonMsg($msgType) {
        static $commonMsgTypes = array(
            'text',
            'image',
            'voice',
            'video',
            'location',
            'link');
        return in_array($msgType, $commonMsgTypes);
    }

    // 保存普通消息
    public static function insertCommonMsg($msgobj) {
        if ($msgobj->MsgType == 'image') {
            // need pcard fix
            $wxpicmsg = WxPicMsg::createbyXmlObj($msgobj);

            $picture = Picture::createByFetch($wxpicmsg->getWxPicUrl4Fetch());

            // 上传失败
            if ($picture->picname == 'b/40/b40184b40ae5a546cc6e386218009714') {
                $wxpicmsg->set4lock('pictureid', 445233609);

                Debug::warn("图片抓取失败: wxpicmsgid = {$wxpicmsg->id}");
            } else {
                $wxpicmsg->set4lock("pictureid", $picture->id);
            }

            return $wxpicmsg;
        } elseif ($msgobj->MsgType == 'voice') {
            // need pcard fix
            $wxvoicemsg = WxVoiceMsg::createbyXmlObj($msgobj);

            $voice = Voice::createByFetch($wxvoicemsg->downloadurl, $wxvoicemsg->wxuserid);
            $wxvoicemsg->voiceid = $voice->id;
            return $wxvoicemsg;
        } else if ($msgobj->MsgType == 'video') {
            $wxVideoMsg = WxVideoMsg::createbyXmlObj($msgobj);

            $video = Video::createByFetch($wxVideoMsg->downloadurl, $wxVideoMsg->wxuserid);
            $wxVideoMsg->videoid = $video->id;
            return $wxVideoMsg;
        } else {
            // need pcard fix
            $wxtxtmsg = WxTxtMsg::createbyXmlObj($msgobj);

            return $wxtxtmsg;
        }
    }

    // Dwx_*
    public static function insertDwxCommonMsg($msgobj) {
        if ($msgobj->MsgType == 'image') {
            $dwx_picmsg = Dwx_picmsg::createbyXmlObj($msgobj);

            $picture = Picture::createByFetch($dwx_picmsg->getWxPicUrl4Fetch());

            // 上传失败
            if ($picture->picname == 'b/40/b40184b40ae5a546cc6e386218009714') {
                $dwx_picmsg->set4lock('pictureid', 445233609);

                Debug::warn("图片抓取失败: dwx_picmsgid = {$dwx_picmsg->id}");
            } else {
                $dwx_picmsg->set4lock("pictureid", $picture->id);
            }

            return $dwx_picmsg;
        } elseif ($msgobj->MsgType == 'voice') {
            $dwx_voicemsg = Dwx_voicemsg::createbyXmlObj($msgobj);

            $voice = Voice::createByFetch($dwx_voicemsg->downloadurl, $dwx_voicemsg->wxuserid);
            $dwx_voicemsg->set4lock("voiceid", $voice->id);
            return $dwx_voicemsg;
        } else {
            return Dwx_txtmsg::createbyXmlObj($msgobj);
        }
    }

    public static function getContent4OpsAlarm($msgobj) {
        $msgtype = $msgobj->MsgType;
        if ($msgtype == 'text') {
            $msgstr = "[消息]\n {$msgobj->Content}";
        } elseif ($msgtype == 'image') {
            $msgstr = "[图片]";
        } else {
            $msgstr = "[其他]\n {$msgtype}";
        }
        return $msgstr;
    }

    // 解密操作
    public static function decryptMsg($wxshop, $msg_signature, $timestamp, $nonce, $postdata) {
        $result = '';
        if ($wxshop instanceof WxShop) {
            $wxbizmsgcrypt = self::getWXBizMsgCrypt($wxshop);
            $msg = '';
            $errCode = $wxbizmsgcrypt->decryptMsg($msg_signature, $timestamp, $nonce, $postdata, $msg);
            if ($errCode == 0) {
                $result = $msg;
            } else {
                Debug::warn("解密失败");
            }
        }
        return $result;
    }

    // 加密操作
    public static function encryptMsg($wxshop, $timestamp, $nonce, $xmlstr) {
        if ($xmlstr == '') {
            return $xmlstr;
        }
        $result = $xmlstr;
        if ($wxshop instanceof WxShop) {
            $wxbizmsgcrypt = self::getWXBizMsgCrypt($wxshop);
            $encryptMsg = '';
            $errCode = $wxbizmsgcrypt->encryptMsg($xmlstr, $timestamp, $nonce, $encryptMsg);
            if ($errCode == 0) {
                $result = $encryptMsg;
            } else {
                Debug::warn("加密失败");
            }
        }
        return $result;
    }

    public static function getWXBizMsgCrypt($wxshop) {
        include_once(ROOT_TOP_PATH . "/../core/tools/wxbizmsgcrypt/wxBizMsgCrypt.php");
        $token = $wxshop->token;
        $encodingaeskey = $wxshop->encodingaeskey;
        $appid = $wxshop->appid;
        return new WXBizMsgCrypt($token, $encodingaeskey, $appid);
    }

    // 抓取 WxUser，信息
    public static function fetchWxUser(WxUser $wxuser) {

        // 非认证服务号不能抓取详细信息
        if (false == $wxuser->wxshop->isAuthServiceNo()) {
            return true;
        }

        $access_token = $wxuser->wxshop->getAccessToken();

        $openid = $wxuser->openid;

        $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token={$access_token}&openid={$openid}&lang=zh_CN";
        $jsonStr = XHttpRequest::curl_getUrlContents($url);

        $json = json_decode($jsonStr, true);

        Debug::trace("[ " . __METHOD__ . " ] " . $jsonStr);

        if (false == empty($json['errcode'])) {
            return $jsonStr;
        }

        $row2keys = array();
        $row2keys['unionid'] = 'unionid';
        $row2keys['nickname'] = 'nickname';
        $row2keys['sex'] = 'sex';
        $row2keys['language'] = 'language';
        $row2keys['city'] = 'city';
        $row2keys['province'] = 'province';
        $row2keys['country'] = 'country';
        $row2keys['headimgurl'] = 'headimgurl';
        $row2keys['remark'] = 'remark';
        $row2keys['groupid'] = 'groupid';
        $row2keys['subscribe_time'] = 'subscribe_time';

        if ($json['subscribe']) {
            foreach ($row2keys as $k => $v) {

                if (isset($json[$k])) {

                    $tt = $json[$k];

                    if ($k == 'subscribe_time') {
                        $tt = date("Y-m-d H:i:s", $tt);
                    }

                    $wxuser->$v = $tt;
                }
            }
        } else {
            // 仅修改subscribe,不修改其他字段
            $wxuser->subscribe = 0;
        }

        // 修正一下名字
        if ($wxuser->userid > 0 && $wxuser->user->name == '') {
            $wxuser->user->name = $wxuser->nickname;
        }

        if ($wxuser->userid > 0 && $wxuser->unionid != '' && $wxuser->user->unionid == '') {
            $wxuser->user->unionid = $wxuser->unionid;
        }

        return true;
    }

    // 获取二维码ticket
    public static function getQrTicket($access_token, $scene_str) {
        $url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token={$access_token}";

        $fields = array(
            "action_name" => "QR_LIMIT_STR_SCENE",
            "action_info" => array(
                "scene" => array(
                    "scene_str" => $scene_str)));

        $fields = urldecode(json_encode($fields));
        $jsonStr = XHttpRequest::curl_postUrlContents($url, $fields);
        $json = json_decode($jsonStr, true);

        return $json['ticket'];
    }

    // 获取临时二维码ticket
    public static function getTempQrTicket($access_token, $scene_id) {
        $wx_uri = Config::getConfig("wx_uri");
        // 仅供测试
        // access_token =
        // XHttpRequest::curl_getUrlContents($wx_uri."/wx/getnewaccesstoken");

        $url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token={$access_token}";

        // 七天时间
        $fields = array(
            "expire_seconds" => 2592000,
            "action_name" => "QR_SCENE",
            "action_info" => array(
                "scene" => array(
                    "scene_id" => $scene_id)));

        $fields = urldecode(json_encode($fields));
        $jsonStr = XHttpRequest::curl_postUrlContents($url, $fields);
        $json = json_decode($jsonStr, true);

        return $json['ticket'];
    }

    public static function fetchWxUserList(WxShop $wxshop) {
        echo "\n\n========[{$wxshop->id}][begin]===========\n";
        $next_openid = '';
        $batNo = 1;
        while (true) {
            echo "\n========[{$wxshop->id}][$batNo]===========\n";

            $access_token = $wxshop->getAccessToken();

            $url = "https://api.weixin.qq.com/cgi-bin/user/get?access_token={$access_token}&next_openid={$next_openid}";

            $jsonStr = XHttpRequest::curl_getUrlContents($url);
            $json = json_decode($jsonStr, true);

            // 出错跳出
            if (false == empty($json['errcode'])) {
                return $jsonStr;
            }

            $openids = $json['data']['openid'];
            $total = $json['total'];

            $openidCnt = count($openids);

            $unitofwork = BeanFinder::get("UnitOfWork");

            foreach ($openids as $i => $openid) {
                $next_openid = $openid;
                if ($i % 100 == 0) {
                    echo "\n$i / {$openidCnt} / {$total} : ";

                    // 批量提交
                    $unitofwork->commitAndInit();
                }

                $wxuser = WxUserDao::getByOpenid($openid);
                if (false == $wxuser instanceof WxUser) {
                    echo " [{$openid}] ";

                    $wxuser = WxUser::getOrCreateByOpenid($openid, $wxshop->id);
                    $wxuser->modifySubscribeInfo();
                } else {
                    echo ".";

                    $wxuser->subscribe = 1;
                }
            }

            // 提交补刀
            $unitofwork->commitAndInit();

            if ($openidCnt < 9999) {
                echo "\n========[{$wxshop->id}][break]===========\n";
                break;
            }
        }

        echo "\n========[{$wxshop->id}][end]===========\n\n";

        return $total;
    }

    // //////////////////////////////////////////
    // 以下是更通用的代码和实体表无关

    // 发送客服消息:文本
    public static function kefuTextMsg(WxShop $wxshop, $touser, $content) {
        $access_token = $wxshop->getAccessToken();
        $errcode = self::kefuTextMsgImp($access_token, $touser, $content);

        if ($errcode == '40001' || $errcode == '42001') {

            // 重新获取一次,重发一次
            $wxshop->access_token = '';
            $access_token = $wxshop->getAccessToken();

            $errcode = self::kefuTextMsgImp($access_token, $touser, $content);
        } elseif ($errcode == '-1') {
            $errcode = self::kefuTextMsgImp($access_token, $touser, $content);
        }

        return $errcode;

        // if (empty($errcode)) {
        // return true;
        // } else {

        // return WxErrorCode::getMsg($errcode);
        // }
    }

    // 发送客服消息:文本
    private static function kefuTextMsgImp($access_token, $touser, $content) {
        $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token={$access_token}";

        // 2015091730 遇到奇怪的问题,转换编码就好了
        $touser = mb_convert_encoding($touser, 'ascii', 'utf-8');

        $fields = array();
        $fields['touser'] = $touser;
        $fields['msgtype'] = 'text';
        $fields['text']['content'] = urlencode($content);

        $fields = urldecode(json_encode($fields));

        // echo "\n===============";
        // echo $fields;
        // echo "\n===============";

        $jsonStr = XHttpRequest::curl_postUrlContents($url, $fields, $err);
        $json = json_decode($jsonStr, true);

        // 缓存最后一条发送结果
        self::$lastJsonStr = $jsonStr;
        self::$last_errcode = $json['errcode'];
        self::$last_errmsg = $json['errmsg'];

        $logstr = " 发送文本消息 access_token:$access_token touser:$touser url:$url content:$content err:" . json_encode($err) . " ret:" . json_encode($json);
        Debug::trace("[ " . __METHOD__ . " ] " . $logstr);

        return $json['errcode'];
    }

    // 发送客服消息:图片
    public static function kefuImageMsg(WxShop $wxshop, $touser, $mediaid) {
        $access_token = $wxshop->getAccessToken();
        $errcode = self::kefuImageMsgImp($access_token, $touser, $mediaid);

        if ($errcode == '40001' || $errcode == '42001') {

            // 重新获取一次,重发一次
            $wxshop->access_token = '';
            $access_token = $wxshop->getAccessToken();

            $errcode = self::kefuImageMsgImp($access_token, $touser, $mediaid);
        } elseif ($errcode == '-1') {
            $errcode = self::kefuImageMsgImp($access_token, $touser, $mediaid);
        }

        return $errcode;
    }

    // 发送客服消息:图片
    private static function kefuImageMsgImp($access_token, $touser, $mediaid) {
        $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token={$access_token}";

        // 2015091730 遇到奇怪的问题,转换编码就好了
        $touser = mb_convert_encoding($touser, 'ascii', 'utf-8');

        $fields = array();
        $fields['touser'] = $touser;
        $fields['msgtype'] = 'image';
        $fields['image']['media_id'] = $mediaid;

        $fields = urldecode(json_encode($fields));

        $jsonStr = XHttpRequest::curl_postUrlContents($url, $fields, $err);
        $json = json_decode($jsonStr, true);

        // 缓存最后一条发送结果
        self::$lastJsonStr = $jsonStr;
        self::$last_errcode = $json['errcode'];
        self::$last_errmsg = $json['errmsg'];

        $logstr = " 发送图片消息 access_token:$access_token touser:$touser url:$url media_id:$mediaid err:" . json_encode($err) . " ret:" . json_encode($json);
        Debug::trace("[ " . __METHOD__ . " ] " . $logstr);

        return $json['errcode'];
    }

    // 发送方寸儿童管理服务平台介绍文章
    public static function sendAdhdInfo(WxUser $wxuser) {
        $wxshop = WxShop::getById(1);
        $openid = $wxuser->openid;

        $title = "孩子诊断为多动症，接下来该怎么做？";
        $img = "http://mmbiz.qpic.cn/mmbiz_jpg/5NzlSFKia4L6szzkzMZRyibOTWthtbYEUgNIXDvWF1vEaZmWZCeB6fI6pWrLMDmKMpREich3eHKpKnV555p0Cr1GQ/640?wx_fmt=jpeg&tp=webp&wxfrom=5";
        $content = "如果您不了解什么是多动症，如果您尝试了各种方法后孩子的情况依然没有改善，如果您已经束手无策了......那么，请让我们来帮助您！";
        $wx_uri = Config::getConfig("wx_uri");
        $url = "{$wx_uri}/lesson/justforshow?openid={$openid}&lessonid=173360386";

        $articles = array();
        $articles[] = new SimpleWxMsg($title, $img, $content, $url);

        WxApi::kefuNewsMsg($wxshop, $openid, $articles);
    }

    // 肿瘤方向报到成功后发图文消息 #5641
    public static function sendCancer(WxUser $wxuser) {
        $wxshop = $wxuser->wxshop;
        $openid = $wxuser->openid;

        $title = "诊后管理，和你在一起";
        $img = "https://photo.fangcunyisheng.com/8/b5/8b5a3e44e8a843d59a25d126c32f490b.jpeg";
        $content = "诊后管理能给患者带来哪些好处？诊后管理管哪些事情？谁负责我的诊后管理？如何与医生助理沟通？";
        $wx_uri = Config::getConfig("wx_uri");
        $url = "{$wx_uri}/lesson/justforshow?lessonid=562550516";

        $articles = array();
        $articles[] = new SimpleWxMsg($title, $img, $content, $url);

        WxApi::kefuNewsMsg($wxshop, $openid, $articles);
    }

    // 尝试发送客服消息:图文, 失败了发送模板消息
    public static function trySendKefuMewsMsg(WxUser $wxuser, $url, $title, $content, $img_url){
        $wxshop = $wxuser->wxshop;
        $openid = $wxuser->openid;

        $articles = array();
        $articles[] = new SimpleWxMsg($title, $img_url, $content, $url);

//        if (Picture::isExist($img_url) === false) {
//            $pushMsg = PushMsgService::sendNoticeToWxUserBySystem($wxuser, $title, $content, $url);
//        }else {
        //发送失败转模板消息
        $errcode = WxApi::kefuNewsMsg($wxshop, $openid, $articles);
        if($errcode != 0){
            $pushMsg = PushMsgService::sendNoticeToWxUserBySystem($wxuser, $title, $content, $url);
            if($pushMsg instanceof PushMsg === false) {
                return false;
            }
        } else {
            // 发送成功 也记一条流
            $pipetpl = PipeTplDao::getOneByObjtypeAndObjcode('PushMsg', 'bySystem');

            $row = [];
            $row["wxuserid"] = $wxuser->id;
            $row["userid"] = $wxuser->userid;
            $row["patientid"] = $wxuser->patientid;
            $row["doctorid"] = $wxuser->patient->doctorid;
            $row["pipetplid"] = $pipetpl->id;
            $row["objtype"] = 'WxUser';
            $row["objid"] = $wxuser->id;
            $default["objcode"] = 'bySystem';
            $row["content"] = "图文消息推送成功:" . $title;
            Pipe::createByBiz($row);
        }
        return true;
    }

    // 发送客服消息:图文
    public static function kefuNewsMsg(WxShop $wxshop, $touser, array $wxMsgBase4wxs) {
        $access_token = $wxshop->getAccessToken();
        $errcode = self::kefuNewsMsgImp($access_token, $touser, $wxMsgBase4wxs);
        if ($errcode == '40001' || $errcode == '42001') {

            // 重新获取一次,重发一次
            $wxshop->access_token = '';
            $access_token = $wxshop->getAccessToken();

            $errcode = self::kefuNewsMsgImp($access_token, $touser, $wxMsgBase4wxs);
        } elseif ($errcode == '-1') {
            $errcode = self::kefuNewsMsgImp($access_token, $touser, $wxMsgBase4wxs);
        }

        return $errcode;
    }

    // 发送客服消息:图文
    private static function kefuNewsMsgImp($access_token, $touser, array $wxMsgBase4wxs) {
        $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token={$access_token}";

        $touser = mb_convert_encoding($touser, 'ascii', 'utf-8');

        $fields = array();
        $fields['touser'] = $touser;
        $fields['msgtype'] = 'news';
        $fields['news'] = array();
        $fields['news']['articles'] = array();

        $i = 0;
        foreach ($wxMsgBase4wxs as $a) {
            $i++;

            if ($i > 8) {
                break;
            }

            $picurl = '';
            $picurl = $a->getPicUrl4wx();

            // echo "\n $i => ";
            // echo $picurl;
            // echo "\n";

            $arr = array();
            $arr['title'] = urlencode($a->getTitle4wx());
            $arr['description'] = urlencode($a->getContent4wx());
            $arr['url'] = urlencode($a->getUrl4wx());
            if ($picurl) {
                $arr['picurl'] = urlencode($picurl);
            }

            $fields['news']['articles'][] = $arr;
        }

        $fields = urldecode(json_encode($fields));

        $jsonStr = XHttpRequest::curl_postUrlContents($url, $fields, $err);
        $json = json_decode($jsonStr, true);

        // 缓存最后一条发送结果
        self::$lastJsonStr = $jsonStr;
        self::$last_errcode = $json['errcode'];
        self::$last_errmsg = $json['errmsg'];

        $logstr = " 发送图文消息 access_token:$access_token touser:$touser url:$url content:" . json_encode($arr) . " err:" . json_encode($err) . " ret:" .
            json_encode($json);
        Debug::trace($logstr);

        return $json['errcode'];
    }

    // 发送客服消息:模板消息
    public static function kefuTplMsg(WxShop $wxshop, $touser, $template_id, $url, $data) {
        $access_token = $wxshop->getAccessToken();
        $errcode = self::kefuTplMsgImp($access_token, $touser, $template_id, $url, $data);
        if ($errcode == '40001' || $errcode == '42001') {

            // 重新获取一次,重发一次
            $wxshop->access_token = '';
            $access_token = $wxshop->getAccessToken();

            $errcode = self::kefuTplMsgImp($access_token, $touser, $template_id, $url, $data);
        } elseif ($errcode == '-1') {
            $errcode = self::kefuTplMsgImp($access_token, $touser, $template_id, $url, $data);
        } else {
            // ...
        }

        return $errcode;
    }

    // 发送客服消息:模板消息
    private static function kefuTplMsgImp($access_token, $touser, $template_id, $url, $data) {
        $api = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token={$access_token}";

        $touser = mb_convert_encoding($touser, 'ascii', 'utf-8');

        if (is_array($data)) {
            $data = json_encode($data);
        }

        $str = <<< INPUTHTML
{
   "touser":"{$touser}",
   "template_id":"{$template_id}",
   "url":"{$url}",
   "data":{$data}
}
INPUTHTML;

        $jsonStr = XHttpRequest::curl_postUrlContents($api, $str, $err);
        $json = json_decode($jsonStr, true);

        // 缓存最后一条发送结果
        self::$lastJsonStr = $jsonStr;
        self::$last_errcode = $json['errcode'];
        self::$last_errmsg = $json['errmsg'];

        $logstr = " 发送模板消息 access_token:$access_token touser:$touser url:$url content:" . $str . " err:" . json_encode($err) . " ret:" . $jsonStr;
        Debug::trace($logstr);

        return $json['errcode'];
    }

    // 响应消息:文本
    public static function xiangyingTextMsg($FromUserName, $ToUserName, $content) {
        $CreateTime = time();
        $textTpl = "<xml>
        <ToUserName><![CDATA[%s]]></ToUserName>
        <FromUserName><![CDATA[%s]]></FromUserName>
        <CreateTime>%s</CreateTime>
        <MsgType><![CDATA[%s]]></MsgType>
        <Content><![CDATA[%s]]></Content>
        </xml>";

        return sprintf($textTpl, $ToUserName, $FromUserName, $CreateTime, $msgType = 'text', $content);
    }

    // 响应消息:图片
    public static function xiangyingImageMsg($FromUserName, $ToUserName, $mediaid) {
        $CreateTime = time();
        $textTpl = "<xml>
        <ToUserName><![CDATA[%s]]></ToUserName>
        <FromUserName><![CDATA[%s]]></FromUserName>
        <CreateTime>%s</CreateTime>
        <MsgType><![CDATA[%s]]></MsgType>
        <Image>
        <MediaId><![CDATA[%s]]></MediaId>
        </Image>
        </xml>";
        return sprintf($textTpl, $ToUserName, $FromUserName, $CreateTime, $msgType = 'image', $mediaid);
    }

    // 响应消息:图文
    public static function xiangyingNewsMsg($FromUserName, $ToUserName, array $wxMsgBase4wxs) {
        $CreateTime = time();
        $cnt = count($wxMsgBase4wxs);

        $str = "<xml>
<ToUserName><![CDATA[{$ToUserName}]]></ToUserName>
<FromUserName><![CDATA[{$FromUserName}]]></FromUserName>
<CreateTime>{$CreateTime}</CreateTime>
<MsgType><![CDATA[news]]></MsgType>
<ArticleCount>{$cnt}</ArticleCount>
<Articles>";

        $i = 0;
        foreach ($wxMsgBase4wxs as $a) {
            $i++;
            $title = $a->getTitle4wx();
            $picurl = $a->getPicUrl4wx();
            $content = $a->getContent4wx();
            $url = $a->getUrl4wx();

            $item = "\n<item>
<Title><![CDATA[{$title}]]></Title>
<Description><![CDATA[{$content}]]></Description>
<PicUrl><![CDATA[{$picurl}]]></PicUrl>
<Url><![CDATA[{$url}]]></Url>
</item>";
            $str .= $item;
        }

        $str .= "\n</Articles>\n</xml>";

        return $str;
    }

    /**
     * 创建二维码ticket
     * 每次创建二维码ticket需要提供一个开发者自行设定的参数（scene_id），分别介绍临时二维码和永久二维码的创建二维码ticket过程。
     *
     * @param $scene_id int
     *            场景值ID，临时二维码时为32位整型，永久二维码时最大值为1000
     * @param $type int
     *            二维码类型，0为临时,1为永久
     * @param $expire int
     *            该二维码有效时间，以秒为单位。 最大不超过1800。
     * @return string
     */
    public static function createQrcode($access_token, $scene_id, $type = 0, $expire = 1800) {
        if (!$access_token) {
            XContext::setValue("createQrcodeRet", WxErrorCode::getMsg(40001));
            return '';
        }

        $data = array();
        $data['action_info'] = array(
            'scene' => array(
                'scene_id' => $scene_id));
        $data['action_name'] = ($type == 0 ? 'QR_SCENE' : 'QR_LIMIT_SCENE');
        if ($type == 0)
            $data['expire_seconds'] = $expire;

        // $result = curlRequest(self::API_URL_PREFIX .
        // self::QRCODE_CREATE_URL . 'access_token=' .
        // $access_token,
        // self::jsonEncode($data), 'post');
        $result = XHttpRequest::curl_postUrlContents(self::API_URL_PREFIX . self::QRCODE_CREATE_URL . 'access_token=' . $access_token, self::jsonEncode($data),
            $err);
        if ($result) {
            $jsonArr = json_decode($result, true);
            if (!$jsonArr || (isset($jsonArr['errcode']) && $jsonArr['errcode'] > 0)) {
                Debug::trace("[ " . __METHOD__ . " ] " . $result);
                XContext::setValue("createQrcodeRet", WxErrorCode::getMsg($jsonArr['errcode']));
                return '';
            } else {
                return $jsonArr['ticket'];
            }
        }

        return '';
    }

    // 上传多媒体文件
    public static function uploadfile($access_token, $media, $type) {
        $url = "http://file.api.weixin.qq.com/cgi-bin/media/upload?access_token={$access_token}&type={$type}";

        $cmd = 'curl -F media=@' . $media . ' "' . $url . '"';
        $jsonStr = system($cmd);
        $json = json_decode($jsonStr, true);

        if (empty($json['errcode'])) {
            return $json;
        }

        return WxErrorCode::getMsg($json['errcode']);
    }

    public static function uploadimg($access_token, $filename, $type = "image") {
        $url = "https://api.weixin.qq.com/cgi-bin/media/upload?access_token={$access_token}&type={$type}";

        $cmd = 'curl -F media=@' . $filename . ' "' . $url . '"';
        exec($cmd, $ret, $out);

        Debug::trace("[ " . __METHOD__ . " ] " . $ret[0]);
        $data = json_decode($ret[0], true);

        if (empty($data['errcode'])) {
            return $data;
        }

        return WxErrorCode::getMsg($data['errcode']);
    }

    public static function uploadimgByUrl($access_token, $imgurl, $type = "image") {
        $userAgent = "Mozilla/5.0 (compatible; Baiduspider/2.0; +http://www.baidu.com/search/spider.html)";
        $httpRequest = XHttpRequest::getInstance();
        $httpRequest->setUserAgent($userAgent);

        $err = "";
        $content = @$httpRequest->getUrlContents($imgurl, $err);

        if (empty($content)) {
            return ErrCode::no_img_data;
        }

        $id = BeanFinder::get("IDGenerator")->getNextId();
        $tmpname = "/tmp/netpic_{$id}";
        file_put_contents($tmpname, $content);
        $imagetype = XUtility::checkImgType($tmpname);
        if (!$imagetype) {
            return ErrCode::error_img_type;
        }
        $tmpname1 = "/tmp/netpic_{$id}.{$imagetype}";
        copy($tmpname, $tmpname1);

        $result = self::uploadimg($access_token, $tmpname1, $type);
        @unlink($tmpname);
        @unlink($tmpname1);
        return $result;
    }

    // 上传素材
    public static function uploadnews($access_token, $articles, $thumb_media_id) {
        $url = "https://api.weixin.qq.com/cgi-bin/media/uploadnews?access_token={$access_token}";

        $fields = array();
        $fields['articles'] = array();

        if (is_array($articles)) {
            foreach ($articles as $a) {

                $arr = array();
                $arr['thumb_media_id'] = $thumb_media_id;
                $arr['author'] = urlencode("方寸儿童管理服务平台");
                $arr['title'] = urlencode($a->getTitle4wx());
                $arr['content_source_url'] = urlencode($a->getUrl4wx());
                $arr['content'] = urlencode($a->getContent4wx());
                $arr['digest'] = '';
                $arr['show_cover_pic'] = 1;

                $fields['articles'][] = $arr;
            }
        } else {
            $arr = array();
            $arr['thumb_media_id'] = $thumb_media_id;
            $arr['author'] = urlencode("方寸儿童管理服务平台");
            $arr['title'] = urlencode($articles->getTitle4wx());
            $arr['content_source_url'] = urlencode($articles->getUrl4wx());
            $arr['content'] = urlencode($articles->getContent4wx());
            $arr['digest'] = '';
            $arr['show_cover_pic'] = 1;

            $fields['articles'][] = $arr;
        }

        $fields = urldecode(json_encode($fields));

        $jsonStr = XHttpRequest::curl_postUrlContents($url, $fields, $err);
        $json = json_decode($jsonStr, true);

        if (empty($json['errcode'])) {
            return $json;
        }

        return WxErrorCode::getMsg($json['errcode']);
    }

    // 根据分组进行群发
    public static function massSend($access_token, $group_id, $media_id, $msgtype) {
        $url = "https://api.weixin.qq.com/cgi-bin/message/mass/sendall?access_token={$access_token}";

        $fields = array();
        $filter = array(
            "group_id" => $group_id);
        $mpnews = array(
            "media_id" => $media_id);
        $fields["filter"] = $filter;
        $fields["mpnews"] = $mpnews;
        $fields["msgtype"] = $msgtype;

        $fields = urldecode(json_encode($fields));

        $jsonStr = XHttpRequest::curl_postUrlContents($url, $fields, $err);
        $json = json_decode($jsonStr, true);

        return $json;
    }

    // 删除群发
    public static function massDelete($access_token, $msgid) {
        $url = "https://api.weixin.qq.com//cgi-bin/message/mass/delete?access_token={$access_token}";

        $fields = array();
        $fields['msgid'] = $msgid;

        $fields = urldecode(json_encode($fields));

        $jsonStr = XHttpRequest::curl_postUrlContents($url, $fields, $err);
        $json = json_decode($jsonStr, true);

        return $json;
    }

    /**
     * 将数组中的中文转换成json数据
     *
     * @param $arr
     * @return string
     */
    public static function jsonEncode($arr) {
        $parts = array();
        $is_list = false;
        // Find out if the given array is a numerical array
        $keys = array_keys($arr);
        $max_length = count($arr) - 1;
        if (($keys[0] === 0) && ($keys[$max_length] === $max_length)) {
            $is_list = true;
            for ($i = 0; $i < count($keys); $i++) {
                if ($i != $keys[$i]) { // A key fails at position check.
                    $is_list = false; // It is an associative array.
                    break;
                }
            }
        }
        foreach ($arr as $key => $value) {
            if (is_array($value)) { // Custom handling for arrays
                if ($is_list)
                    $parts[] = self::jsonEncode($value);
                else
                    $parts[] = '"' . $key . '":' . self::jsonEncode($value);
            } else {
                $str = '';
                if (!$is_list)
                    $str = '"' . $key . '":';
                // Custom handling for multiple data types
                if (is_numeric($value) && $value < 2000000000)
                    $str .= $value; // Numbers
                elseif ($value === false)
                    $str .= 'false'; // The booleans
                elseif ($value === true)
                    $str .= 'true';
                else
                    $str .= '"' . addslashes($value) . '"'; //
                $parts[] = $str;
            }
        }
        $json = implode(',', $parts);
        if ($is_list)
            return '[' . $json . ']'; // Return numerical JSON
        return '{' . $json . '}'; // Return associative JSON
    }

    // 创建微信分组
    public static function createGroup(WxShop $wxshop, $groupname) {
        $access_token = $wxshop->getAccessToken();
        $fields = array(
            "group" => array(
                "name" => $groupname));
        $fields = urldecode(json_encode($fields));

        $url = "https://api.weixin.qq.com/cgi-bin/groups/create?access_token={$access_token}";
        $jsonStr = XHttpRequest::curl_postUrlContents($url, $fields);
        $json = json_decode($jsonStr, true);
        return $json['group']['id'];
    }

    // 移动微信用户到微信分组
    public static function MvWxuserToGroup(WxUser $wxuser, $groupid) {
        $wxshop = $wxuser->wxshop;
        $wxuser->groupid = $groupid;
        $access_token = $wxshop->getAccessToken();
        $fields = array(
            "openid" => $wxuser->openid,
            "to_groupid" => $groupid);
        $fields = urldecode(json_encode($fields));

        $url = "https://api.weixin.qq.com/cgi-bin/groups/members/update?access_token={$access_token}";
        $jsonStr = XHttpRequest::curl_postUrlContents($url, $fields);
        Debug::trace("========MvWxuserToGroup[{$jsonStr}]==============");
        $json = json_decode($jsonStr, true);
        return $json['errmsg'];
    }

    // 为用户取消微信标签
    public static function DeleteGroup(WxUser $wxuser, $groupid) {
        $wxshop = $wxuser->wxshop;
        $wxuser->groupid = 0;
        $access_token = $wxshop->getAccessToken();
        $fields = array(
            "openid_list" => [
                "$wxuser->openid"],
            "tagid" => $groupid);
        $fields = urldecode(json_encode($fields));

        $url = "https://api.weixin.qq.com/cgi-bin/tags/members/batchuntagging?access_token={$access_token}";
        $jsonStr = XHttpRequest::curl_postUrlContents($url, $fields);
        Debug::trace("========MvWxuserToGroup[{$jsonStr}]==============");
        $json = json_decode($jsonStr, true);
        return $json['errmsg'];
    }

    // 获取当前公众号下所有的标签
    public static function getTags(WxShop $wxshop) {
        $access_token = $wxshop->getAccessToken();
        $url = "https://api.weixin.qq.com/cgi-bin/tags/get?access_token={$access_token}";
        $fields = [];
        $fields = urldecode(json_encode($fields));
        $jsonStr = XHttpRequest::curl_postUrlContents($url, $fields);
        Debug::trace("========getTags[{$jsonStr}]==============");
        $result = json_decode($jsonStr, true);
        return $result['tags'];
    }

    // 创建微信标签
    public static function createTag(WxShop $wxshop, $tagname) {
        $access_token = $wxshop->getAccessToken();
        $fields = [
            "tag" => [
                "name" => $tagname
            ]
        ];
        $fields = urldecode(json_encode($fields, JSON_UNESCAPED_UNICODE));
        $url = "https://api.weixin.qq.com/cgi-bin/tags/create?access_token={$access_token}";
        $jsonStr = XHttpRequest::curl_postUrlContents($url, $fields);
        Debug::trace("========createTag[{$jsonStr}]==============");
        $json = json_decode($jsonStr, true);
        return $json['tag']['id'];
    }

    // 删除微信标签
    public static function deleteTag(WxShop $wxshop, $tagid) {
        $access_token = $wxshop->getAccessToken();
        $fields = [
            "tag" => [
                "id" => $tagid
            ]
        ];
        $fields = urldecode(json_encode($fields));
        $url = "https://api.weixin.qq.com/cgi-bin/tags/delete?access_token={$access_token}";
        $jsonStr = XHttpRequest::curl_postUrlContents($url, $fields);
        Debug::trace("========deleteTag[{$jsonStr}]==============");
        $json = json_decode($jsonStr, true);
        return $json['errmsg'];
    }

    // 为一组用户打标签
    public static function batchTagging($wxshop, $wxusers, $tagid) {
        $openid_list = [];
        foreach ($wxusers as $wxuser) {
            $openid_list[] = $wxuser->openid;
            $wxuser->groupid = $tagid;
        }
        $access_token = $wxshop->getAccessToken();
        $url = "https://api.weixin.qq.com/cgi-bin/tags/members/batchtagging?access_token={$access_token}";
        $fields = [
            "openid_list" => $openid_list,
            "tagid" => $tagid];
        $fields = urldecode(json_encode($fields));
        $jsonStr = XHttpRequest::curl_postUrlContents($url, $fields);
        Debug::trace("========batchTagging[{$jsonStr}]==============");
        $json = json_decode($jsonStr, true);
        return $json['errmsg'];
    }

    // 为用户取消微信标签
    public static function batchUnTagging(WxShop $wxshop, $wxusers, $tagid) {
        $openid_list = [];
        foreach ($wxusers as $wxuser) {
            $openid_list[] = $wxuser->openid;
        }
        $access_token = $wxshop->getAccessToken();
        $fields = [
            "openid_list" => $openid_list,
            "tagid" => $tagid];
        $fields = urldecode(json_encode($fields));
        $url = "https://api.weixin.qq.com/cgi-bin/tags/members/batchuntagging?access_token={$access_token}";
        $jsonStr = XHttpRequest::curl_postUrlContents($url, $fields);
        Debug::trace("========batchUnTagging[{$jsonStr}]==============");
        $json = json_decode($jsonStr, true);
        return $json['errmsg'];
    }
}

// 数组的值做urlencode处理
function arrayToUrlencodeArray(array $data) {
    $arr = array();
    foreach ($data as $k => $v) {
        if (is_array($v)) {
            $arr[$k] = arrayToUrlencodeArray($v);
        } else {
            $arr[$k] = urlencode($v);
        }
    }
    return $arr;
}
