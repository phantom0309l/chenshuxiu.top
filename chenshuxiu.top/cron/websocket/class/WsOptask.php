<?php

class WsOpTask extends WsBase
{
    public function __construct($serv, $storage, $frame) {
        $this->serv = $serv;
        $this->storage = $storage;
        $this->frame = $frame;
    }

    //Important!! 死循环会导致connection 不会close
    //在前端循环拉取
    public function getNewOptaskCnt($paramsData) {
        //while (true) {
        $sql = "SELECT COUNT(*) FROM optasks WHERE STATUS=0 AND diseaseid=1";
        $cnt = Dao::queryValue($sql);
        $res = "";
        $this->data = $paramsData;
        $this->data['cnt'] = $cnt;
        $this->serv->push($this->frame->fd, $this->jsonFormat());
        //print_r($this->storage->getAllFds());
        //sleep(5);
        //}
        return $res;
    }
}
