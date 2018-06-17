<?php
class Storage {
    const FD_STRLEN = 100;
    private $table = NULL;
    private static $instance = NULL;
    private function __construct() {
        //初始化table内存
        $this->table = new swoole_table(2<<15);
        $this->table->column('fd', swoole_table::TYPE_STRING, self::FD_STRLEN);//1,2,4,8
        $this->table->create();
    }

    private function __clone() {
    }

    public static function getInstance() {
        if (self::$instance === NULL) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    //全遍历
    public function isExist($fd) {
        foreach ($this->getAllFds as $uid => $fds) {
            if (in_array($fd, $fds)) {
                return true;
            }
        }
        return false;
    }

    public function getAllFds() {
        $allFds = [];
        foreach ($this->table as $uid => $row) {
            $allFds[$uid] = explode(',', $row['fd']);
        }
        return $allFds;
    }

    public function getFds($uid) {
        $fds = $this->table->get($uid);
        if ($fds === false) {
            return [];
        }
        return explode(',', $fds['fd']);
    }

    public function setFd($uid, $fd) {
        $fds = $this->getFds($uid);
        if (in_array($fd, $fds)) {
            return true;
        }
        $fds[] = $fd;
        $fdstr = implode(',', $fds);
        if (strlen($fdstr) > self::FD_STRLEN) {
            //echo "超出fd_strlen长度限制\n";
            return false;
        }
        return $this->table->set($uid, ['fd' => $fdstr]);
    }

    public function delFd($fd) {
        foreach ($this->getAllFds() as $uid => $fds) {
            if (in_array($fd, $fds)) {
                $cnt = count($fds);
                if ($cnt == 1) {
                    $this->table->del($uid);
                } else if ($cnt > 1) {
                    foreach ($fds as  $k => $one) {
                        if ($one == $fd) {
                            unset($fds[$k]);
                        }
                    }
                    $this->table->set($uid, ['fd' => implode(',', $fds)]);
                    return true;
                } else {
                    //有key无value
                    return true;
                }
            }
        }
        return true;
    }
}
