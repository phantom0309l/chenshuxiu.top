<?php
class WsDemo extends WsBase {
    public function __construct($serv, $storage) {
	$this->serv = $serv;
	$this->storage = $storage;
    }
    public function sayHello($paramsData) {
	//调试
	$uid = $paramsData['uid'] ?? '10022';
	$fds = $this->storage->getFds($uid);
	$msg = $paramsData['msg'] ?? 'hello fangcun';
        $this->data = $paramsData;
        $this->data['msg'] = $msg;
	foreach ($fds as $fd) {
	    $this->serv->push($fd, $this->jsonFormat());
	}
	return $this->jsonFormat();
    }
}
