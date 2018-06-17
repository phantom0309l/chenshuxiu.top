<?php

/*
 * Guest
 */
class Guest extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'openid',  // 用户的标识，对当前公众号唯一
            'objtype',  // objtype
            'objid',  // objid
            'unionid',  // 只有在用户将公众号绑定到微信开放平台帐号后，才会出现该字段。
            'nickname',  // 昵称
            'sex',  // 性别, 值为1时是男性，值为2时是女性，值为0时是未知
            'language',  // 语言
            'headimgurl',  // 头像
            'city',  // 城市
            'province',  // 省
            'country',  // 国家
            'status',  // 状态
            'remark'); // 备注
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array();
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
        $this->_belongtos["obj"] = array(
            "type" => $this->objtype,
            "key" => "objid");
    }

    public function getSharedByMe () {
        return Guest_schulteDao::getByFromguestid($this->id);
    }

    public function getLastScore () {
        $a = Guest_schulterecordDao::getLastByOpenid($this->openid);
        return $a->time / 1000;
    }

    public function getLastRole () {
        $a = Guest_schulterecordDao::getLastByOpenid($this->openid);
        $role = "家长";
        if ($a->role == 1) {
            $role = "孩子";
        }
        return $role;
    }

    public function getToptime () {
        $toptime = $this->obj->toptime;
        $toptime1 = $this->obj->toptime1;
        $min = min($toptime, $toptime1);

        if ($min > 0) {
            return $min;
        } else {
            return max($toptime, $toptime1);
        }
    }

    public function getScorePosition () {
        $time = 21;
        $a = Guest_schulterecordDao::getLastByOpenid($this->openid);
        if ($a instanceof Guest_schulterecord) {
            $time = $a->time;
        }
        $num = Guest_schulteDao::getScorePosition($time);

        return $num;
    }

    public function getScorePosition1 () {
        $time = 21;
        $a = Guest_schulterecordDao::getLastByOpenid($this->openid);
        if ($a instanceof Guest_schulterecord) {
            $time = $a->time;
        }
        $num = Guest_schulteDao::getScorePosition1($time);

        return $num;
    }

    // $row = array();
    // $row["openid"] = $openid;
    // $row["objtype"] = $objtype;
    // $row["objid"] = $objid;
    // $row["unionid"] = $unionid;
    // $row["nickname"] = $nickname;
    // $row["sex"] = $sex;
    // $row["language"] = $language;
    // $row["headimgurl"] = $headimgurl;
    // $row["city"] = $city;
    // $row["province"] = $province;
    // $row["country"] = $country;
    // $row["status"] = $status;
    // $row["remark"] = $remark;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "Guest::createByBiz row cannot empty");

        $default = array();
        $default["openid"] = '';
        $default["objtype"] = '';
        $default["objid"] = 0;
        $default["unionid"] = '';
        $default["nickname"] = '';
        $default["sex"] = 0;
        $default["language"] = '';
        $default["headimgurl"] = '';
        $default["city"] = '';
        $default["province"] = '';
        $default["country"] = '';
        $default["status"] = 1;
        $default["remark"] = '';

        $row += $default;
        return new self($row);
    }

    // /////////////////////////////
    // 静态查询方法
    // ////////////////////////////
}
