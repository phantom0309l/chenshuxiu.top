#!/usr/bin/env php
<?php
/*
 *@desc 在class目录下新增文件后需要执行generatorAssembly.cron.php 生成autoload字典文件
 *      同时重新启动websocket.php
 */

ini_set('memory_limit', "1024M");
include_once(dirname(__FILE__) . "/../../sys/PathDefine.php");
include_once(ROOT_TOP_PATH . "/cron/Assembly.php");
include_once(dirname(__FILE__) . "/storage.php");

Config::setConfigFile(ROOT_TOP_PATH . '/sys/config.php');

//初始化存储
$storage = Storage::getInstance();

$websocket_service_host = config::getConfig('websocket_service_host');
$websocket_service_port = config::getConfig('websocket_service_port');
$websocket_http_host = config::getConfig('websocket_http_host');
$websocket_http_port = config::getConfig('websocket_http_port');

$serv = new Swoole\Websocket\Server($websocket_service_host, $websocket_service_port);
$serv->set([
    'worker_num' => 4,
    'daemonize' => false,
    'max_request' => 1000,
    'dispatch_mode' => 2,
    'debug_mode' => 1
]);

$serv->on('open', function ($server, $req) {
    global $storage;
    $fd = $req->fd;
    $sid = $req->get['sid'] ?? $req->post['sid'];
    if (!empty($sid)) {
        $userid = getUserid($sid);
        if ($userid === false) {
            $resData["error"] = "Permission denied";
            $server->push($fd, json_encode($resData, JSON_UNESCAPED_UNICODE));
            $server->close($fd);
            return;
        }
        if (!$storage->setFd($userid, $fd)) {
            $resData["error"] = "超出最大连接数限制，请关闭一些不用的页面后重新刷新当前页";
            $server->push($fd, json_encode($resData, JSON_UNESCAPED_UNICODE));
            $server->close($fd);
            return;
        }
        echo "connection open: " . $userid . " [" . $fd, "]\n";
    } else {
        echo "connection open: " . $fd, "\n";
    }
});

$serv->on('message', function ($server, $frame) {
    global $storage;
    $reqData = json_decode($frame->data, true);
    //print_r($reqData);
    $sid = $reqData['sid'] ?? '';
    $userid = getUserid($sid);
    if ($userid === false) {
        $resData["error"] = "Permission denied";
        $server->push($frame->fd, json_encode($resData, JSON_UNESCAPED_UNICODE));
        $server->close($frame->fd);
        return;
    }
    if (!$storage->setFd($userid, $frame->fd)) {
        $resData["error"] = "超出最大连接数限制，请关闭一些不用的页面后重新刷新当前页";
        $server->push($frame->fd, json_encode($resData, JSON_UNESCAPED_UNICODE));
        $server->close($frame->fd);
        return;
    }

    $className = $reqData['class'] ?? '';
    $methodName = $reqData['method'] ?? '';
    $paramsData = $reqData['data'] ?? [];
    $paramsData['class'] = $className;
    $paramsData['method'] = $methodName;

    $resData = [
        'errno' => '',
        'errmsg' => '',
        'data' => [],
    ];
    if (!$className || !$methodName) {
        $resData['errno'] = -1;
        $resData["errmsg"] = "class or method is empty";
        $server->push($frame->fd, json_encode($resData, JSON_UNESCAPED_UNICODE));
        return;
    }
    $realname = getClassName($className);
    if (empty($className)) {
        $resData['errno'] = -1;
        $resData["errmsg"] = "class file not found";
        $server->push($frame->fd, json_encode($resData, JSON_UNESCAPED_UNICODE));
    }
    $ins = new $realname($server, $storage, $frame);
    $ins->$methodName($paramsData);
});

//需要将ip和port写入配置文件
$http_server = $serv->addListener($websocket_http_host, $websocket_http_port, SWOOLE_SOCK_TCP);
//默认为http协议
//注释打开后就变成tcp协议了
//$tcp_server->set(array());

//监听http请求
//curl -i 'http://fangcundev:9503'
$http_server->on('Request', function ($req, $response) {
    global $serv;
    global $storage;
    print_r($req);
    $className = $req->post['class'] ?? $req->get['class'] ?? '';
    $methodName = $req->post['method'] ?? $req->get['method'] ?? '';
    $paramsData = $req->post['data'] ?? $req->get['data'] ?? '';
    $paramsData = json_decode($paramsData, true) ?? [];
    if (!$className || !$methodName) {
        $response->end(json_encode(['errno' => -1, 'errmsg' => 'class or method name is empty']));
        return;
    }
    $paramsData['class'] = $className;
    $paramsData['method'] = $methodName;

    $realname = getClassName($className);
    if (empty($className)) {
        $response->end(json_encode(['errno' => -1, 'errmsg' => 'class file is not found']));
        return;
    }

    $response->end(json_encode(['errno' => 0, 'errmsg' => '尝试发送，不保证成功送达。']));

    $ins = new $realname($serv, $storage);
    $res = $ins->$methodName($paramsData);
});

$http_server->on('close', function ($server, $fd) {
    echo "http connection close: {$fd}\n";
});


$serv->on('close', function ($server, $fd) {
    global $storage;
    echo "socket connection close: " . $fd, "\n";
    $storage->delFd($fd);

});

$serv->start();


function getClassName(string $className) {
    global $lowerclasspath;
    $realname = $className;
    if (!empty($lowerclasspath)) {
        $realname = $lowerclasspath[strtolower($className)] ?? $className;
    }
    return $realname;
}

function getUserid(string $sid) {
    list($t, $value, $m) = explode("|x|", $sid);
    $m0 = XCookie::mcode($t, $value);
    if ($m0 == $m) {
        return $value;
    }
    return false;
}
