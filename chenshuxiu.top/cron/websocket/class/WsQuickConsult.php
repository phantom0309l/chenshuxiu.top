<?php
/**
 * Created by PhpStorm.
 * User: hades
 * Date: 2018/3/14
 * Time: 19:49
 */

class WsQuickConsult extends WsBase
{
    public function __construct($serv, $storage) {
        $this->serv = $serv;
        $this->storage = $storage;
    }

    // 给运营推送消息
    public function pushMessage($paramsData) {
        $this->data = $paramsData;

        $userids = $paramsData['userids'];

        foreach ($userids as $userid) {
            $fds = $this->storage->getFds($userid);
            if (!empty($fds)) {
                $fd = end($fds);
                $this->serv->push($fd, $this->jsonFormat());
                echo "推送消息：" . $userid . " [" . $fd . "]\n";
            }
        }

        return '';
    }
}