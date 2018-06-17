<?php

/*
 * XUnitOfWork
 */
class XUnitOfWork extends Entity
{

    protected function init_database () {
        $this->database = 'xworkdb';
    }

    public function notXObjLog () {
        return true;
    }

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'randno',  // randno
            'server_ip',  // server_ip
            'client_ip',  // client_ip
            'dev_user',  // 当前环境
            'domain',  // 主域名
            'sub_domain',  // 子域名
            'action_name',  // action
            'method_name',  // method
            'cacheopen',  // 是否启用缓存
                         // 这个字段,暂时用于标识是否微信群消息 11: from=singlemessage,
                         // 12: from=groupmessage, 13: from=timeline
            'commit_load_cnt',  // load实体数目
            'commit_insert_cnt',  // insert数目
            'commit_update_cnt',  // update数目
            'commit_delete_cnt',  // delete数目
            'method_end',  // 耗时，微秒
            'commit_end',  // 耗时，微秒
            'page_end',  // 耗时，微秒
            'url',  // url
            'referer',  // referer
            'cookie',  // cookie
            'posts'); // posts
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array();
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
    }

    // $row = array();
    // $row["randno"] = $randno;
    // $row["server_ip"] = $server_ip;
    // $row["client_ip"] = $client_ip;
    // $row["dev_user"] = $dev_user;
    // $row["domain"] = $domain;
    // $row["sub_domain"] = $sub_domain;
    // $row["action_name"] = $action_name;
    // $row["method_name"] = $method_name;
    // $row["cacheopen"] = $cacheopen;
    // $row["commit_load_cnt"] = $commit_load_cnt;
    // $row["commit_insert_cnt"] = $commit_insert_cnt;
    // $row["commit_update_cnt"] = $commit_update_cnt;
    // $row["commit_delete_cnt"] = $commit_delete_cnt;
    // $row["method_end"] = $method_end;
    // $row["commit_end"] = $commit_end;
    // $row["page_end"] = $page_end;
    // $row["url"] = $url;
    // $row["referer"] = $referer;
    // $row["cookie"] = $cookie;
    // $row["posts"] = $posts;
    public static function createByBiz ($row, $dbconf = []) {
        DBC::requireNotEmpty($row, "XUnitOfWork::createByBiz row cannot empty");
        foreach ($row as $key => $one) {
            if ($one === null) {
                $row[$key] = '';
            }
        }

        $default = array();
        $default["randno"] = 0;
        $default["server_ip"] = '';
        $default["client_ip"] = '';
        $default["dev_user"] = '';
        $default["domain"] = '';
        $default["sub_domain"] = '';
        $default["action_name"] = '';
        $default["method_name"] = '';
        $default["cacheopen"] = 0;
        $default["commit_load_cnt"] = 0;
        $default["commit_insert_cnt"] = 0;
        $default["commit_update_cnt"] = 0;
        $default["commit_delete_cnt"] = 0;
        $default["method_end"] = 0;
        $default["commit_end"] = 0;
        $default["page_end"] = 0;
        $default["url"] = '';
        $default["referer"] = '';
        $default["cookie"] = '';
        $default["posts"] = '';

        $row += $default;
        return new self($row, $dbconf);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    // 获取相关实体 = insert + update + delete
    public function getXObjLogs () {
        return XObjLogDao::getListByXunitofworkid($this->id);
    }

    // ====================================
    // ----------- static method ----------
    // ====================================
    // 生成 tableno
    public static function getTablenoByXunitofworkid ($xunitofworkid) {
        return date('Ym', substr($xunitofworkid, 0, 10));
    }
}
