<?php

class CacheMgrAction extends AuditBaseAction
{
    public function doOne() {
        return self::SUCCESS;
    }

    public function doGetValueJson() {
        $key = XRequest::getValue('key', '');
        DBC::requireNotEmpty($key, 'key is null');
        //DBC::requireTrue(preg_match('/^(\*)+$/', $key) == 0, "key could not be $key");
        $db = XRequest::getValue('db', 0);
        $redis = XRedis::getConnect();
        $redis->select($db);
        $keys = [];
        $it = NULL;
        $max_num = 100;
        do {
            // Scan for some keys
            $arr_keys = $redis->scan($it,  $key);

            // Redis may return empty results, so protect against that
            if ($arr_keys !== FALSE) {
                foreach($arr_keys as $str_key) {
                    $keys[$str_key] = $str_key;
                }
                if (count($keys) >= $max_num) {
                    break;
                }
            }
        } while ($it > 0);
        $keys = array_values($keys);

        $keys_slice = array_slice($keys, 0, $max_num);
        $vals = $redis->mget($keys_slice);
        $ttls = [];
        foreach ($keys_slice as $k) {
            $ttls[] = $redis->ttl($k);
        }

        for ($i=0;$i<count($keys_slice);$i++) {
            $data[] = [
                'key' => $keys_slice[$i],
                'val' => $vals[$i],
                'ttl' => $ttls[$i],
            ];
        }

        $this->result['data'] = $data;

        return self::TEXTJSON;
    }

    public function doDeleteValueJson() {
        $key = XRequest::getValue('key', '');
        DBC::requireNotEmpty($key, 'key is null');
        $db = XRequest::getValue('db', '');
        DBC::requireTrue($db !== '', 'db is null');
        $redis = XRedis::getConnect();
        $redis->select($db);
        $ret = $redis->del($key);
        $this->result['data'] = ['ret' => $ret];
        return self::TEXTJSON;
    }

    public function doClearDbJson() {
        $dbs = XRequest::getValue('dbs', '');
        DBC::requireNotEmpty($dbs, 'database is null');
        $redis = XRedis::getConnect();
        foreach ($dbs as $db) {
            $redis->select($db);
            $redis->flushdb();
        }
        $this->result['data'] = ['ret' => '已成功清空db'];
        return self::TEXTJSON;
    }
}

