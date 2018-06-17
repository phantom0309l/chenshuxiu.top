<?php
/**
 * Created by PhpStorm.
 * User: hades
 * Date: 2018/3/15
 * Time: 11:19
 */

class WebSocketService
{
    /**
     * 运营推送消息模板的ename获取
     * @param $ename
     * @return array
     */
    public static function getUseridsByEnameOfAuditorPushMsgTpl($ename) {
        $userids = [];

        $auditorPushMsgTpl = AuditorPushMsgTplDao::getByEname($ename);

        if (false == $auditorPushMsgTpl instanceof AuditorPushMsgTpl) {
            Debug::warn("没有找到监控消息类型[ename:{$ename}]");
            return $userids;
        }

        $auditorPushMsgTplRefs = AuditorPushMsgTplRefDao::getListByAuditorPushMsgTplIdAndCan_ops($auditorPushMsgTpl->id, 1);

        foreach ($auditorPushMsgTplRefs as $auditorPushMsgTplRef) {
            // 取到运营
            $auditor = $auditorPushMsgTplRef->auditor;
            if ($auditor->isLeave()) {
                continue;
            }
            $userids[] = $auditor->userid;
        }

        return $userids;
    }


    /**
     * @param $title : 通知标题
     * @param $body : 通知内容
     * @param string $tag : 通知的标识标签，相同tag只会打开同一个通知窗口
     * @param array $data : 自定义数据
     * @param string $image : 通知的图像URL
     * @param null $icon : 通知的图标URL
     * @param null $sound : 自定义声音
     * @param bool $requireInteraction : 通知保持有效不自动关闭，默认false
     * @param array $options : 通知的设置选项（可选）
     * @return array
     */
    public static function getNotificationTpl($title, $body, $tag = "", $data = [], $image = "", $icon = null, $sound = null, $requireInteraction = false, $options = []) {
        $img_uri = Config::getConfig('img_uri');

        return [
            'title' => $title,
            'body' => $body,
            'tag' => $tag,
            'icon' => $icon ?? $img_uri . "/static/img/logonew.png",
            'image' => $image,
            'data' => $data,
            'sound' => $sound ?? $img_uri . "/v5/common/nofify_sound.mp3",
            'requireInteraction' => $requireInteraction,
            'options' => $options,
        ];
    }

    public static function push($class, $method, $data, $userids = []) {
        $data['userids'] = $userids;

        $params = [
            'class' => $class,
            'method' => $method,
            'data' => json_encode($data, JSON_UNESCAPED_UNICODE),
        ];

        $host = config::getConfig('websocket_http_host');
        $port = config::getConfig('websocket_http_port');
        $url = "http://{$host}:{$port}";

        // 创建一个新cURL资源
        $ch = curl_init();
        // 设置URL和相应的选项
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1); //设置为POST方式
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($ch);
        if (curl_errno($ch)) {
            Debug::warn('Curl error: ' . curl_error($ch));
        }
        curl_close($ch);
        Debug::trace($res);

        return $res;
    }
}