<?php
class WsMonitor extends WsBase {
    public function __construct($serv, $storage) {
	$this->serv = $serv;
	$this->storage = $storage;
    }
    public function getAllFds($paramsData) {
        $data = [];
        $data['allfds'] = $this->storage->getAllFds();
        //print_r($this->data);
        $this->data = $data;
        return $this->jsonFormat();
    }
}
