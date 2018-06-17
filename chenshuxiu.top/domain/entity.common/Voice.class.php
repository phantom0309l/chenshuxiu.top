<?php

/*
 * Voice
 */
class Voice extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'pictureid',  // pictureid
            'title',  // 标题
            'content',  // 简介
            'name',  // 文件名
            'ext',  // 文件后缀
            'size',  // 文件大小
            'type',  // 文件来源类型
            'status'); // 状态

    }

    protected function init_keys_lock () {
        $this->_keys_lock = array();
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
        $this->_belongtos["picture"] = array(
            "type" => "Picture",
            "key" => "pictureid");
    }

    // $row = array();
    // $row["name"] = $name;
    // $row["ext"] = $ext;
    // $row["size"] = $size;
    // $row["type"] = $type;
    // $row["status"] = $status;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "Voice::createByBiz row cannot empty");

        $default = array();
        $default['pictureid'] = 0;
        $default['title'] = '';
        $default['content'] = '';
        $default['name'] = '';
        $default['ext'] = '';
        $default['size'] = 0;
        $default['type'] = '';
        $default['status'] = 1;

        $row += $default;

        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================

    public function getUrl () {
        $voice_uri = Config::getConfig("voice_uri");
        return $voice_uri . "/voices/" . $this->name . "." . $this->ext;
    }

    public function getMp3Url() {
        $voice_uri = Config::getConfig("voice_uri");
        return $voice_uri . "/voices/mp3/" . $this->name . ".mp3";
    }

    public function getSizeKB () {
        return floor($this->size / 1024);
    }

    // ====================================
    // ----------- static method ----------
    // ====================================

    // 抓取用户发送的语音
    public static function createByFetch ($url, $wxuserid) {
        $url = trim($url);
        if (empty($url)) {
            return ErrCode::no_voice_data;
        }

        $content = self::getVoiceData($url);
        if (empty($content)) {
            return ErrCode::no_voice_data;
        }

        preg_match('/\w\/(\w+)/i', $content["content_type"], $extmatches);
        $ext = $extmatches[1];
        $name = $wxuserid . "_" . date("YmdHis");
        $href = $name . ".{$ext}";
        $dirname = ROOT_TOP_PATH . "/wwwroot/voice/voices/";
        if (! file_exists($dirname)) {
            mkdir($dirname, 0777, true);
        }
        $tmpname = "/tmp/$href";
        file_put_contents($tmpname, $content['mediaBody']);

        $size = filesize($tmpname);
        $result = UploadService::uploadVoice($href, $tmpname, $size, $dirname);
        @unlink($tmpname);
        if (!$result) {
            return false;
        }
        $row = array();
        $row['name'] = $name;
        $row['ext'] = $ext;
        $row['size'] = $size;
        $row['type'] = 'FromWxuser';
        $voice = self::createByBiz($row);
        return $voice;
    }

    private static function getVoiceData ($url) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_NOBODY, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $package = curl_exec($ch);
        $httpinfo = curl_getinfo($ch);

        curl_close($ch);
        return array_merge(array(
            'mediaBody' => $package), $httpinfo);
    }
}
