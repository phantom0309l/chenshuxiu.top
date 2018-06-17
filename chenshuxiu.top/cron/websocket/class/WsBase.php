<?php
class WsBase {
    protected $serv = null;
    protected $storage = null;
    protected $frame = null;
    protected $errno = 0;
    protected $errmsg = '';
    protected $data = [];

    protected function jsonFormat() {
        return json_encode([
            'errno' => $this->errno,
            'errmsg' => $this->errmsg,
            'data' => $this->data,
        ], JSON_UNESCAPED_UNICODE);    
    }
}
