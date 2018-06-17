<?php
function SayHello($server, $frame, $userid, $data, $resData) {
    $res = "";
    $resData['data'] = "你好，方寸医生 $userid";
    $server->push($frame->fd, json_encode($resData, JSON_UNESCAPED_UNICODE));
    return $res;
}
