<?php
/*
 * Video
 */
class Video extends Entity
{

    protected function init_keys () {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine () {
        return array(
            'pictureid',  // 封面
            'url',  // 网址
            'urlmd5',  // urlmd5
            'title',  // 标题
            'content',  // 介绍
            'name', //文件名
            'ext',  // 后缀
            'size',  // 文件大小
            'minute_cnt'); // 时长(分钟)
    }

    protected function init_keys_lock () {
        $this->_keys_lock = array(
            'pictureid');
    }

    protected function init_belongtos () {
        $this->_belongtos = array();
        $this->_belongtos["picture"] = array(
            "type" => "Picture",
            "key" => "pictureid");
    }

    // $row = array();
    // $row["pictureid"] = $pictureid;
    // $row["url"] = $url;
    // $row["urlmd5"] = $urlmd5;
    // $row["title"] = $title;
    // $row["content"] = $content;
    // $row["name"] = name;
    // $row["ext"] = $ext;
    // $row["size"] = $size;
    // $row["minute_cnt"] = $minute_cnt;
    public static function createByBiz ($row) {
        DBC::requireNotEmpty($row, "Video::createByBiz row cannot empty");

        $default = [];
        $default["pictureid"] = 0;
        $default["url"] = '';
        $default["urlmd5"] = '';
        $default["title"] = '';
        $default["name"] = '';
        $default["content"] = '';
        $default["ext"] = '';
        $default["size"] = 0;
        $default["minute_cnt"] = 0;

        $row += $default;
        return new self($row);
    }

    // ====================================
    // ------------ obj method ------------
    // ====================================
    public function getUrl () {
        $voice_uri = Config::getConfig("voice_uri");
        return $voice_uri . "/videos/" . $this->name . "." . $this->ext;
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
            return ErrCode::no_video_data;
        }

        $content = self::getVoiceData($url);
        if (empty($content)) {
            return ErrCode::no_video_data;
        }

        preg_match('/\w\/(\w+)/i', $content["content_type"], $extmatches);
        $ext = $extmatches[1];
        $name = $wxuserid . "_" . date("YmdHis");
        $href = $name . ".{$ext}";
        $dirname = ROOT_TOP_PATH . "/wwwroot/voice/videos/";
        if (! file_exists($dirname)) {
            mkdir($dirname, 0777, true);
        }
        $tmpname = "/tmp/$href";
        file_put_contents($tmpname, $content['mediaBody']);

        $size = filesize($tmpname);
        $result = UploadService::uploadVideo($href, $tmpname, $size, $dirname);
        @unlink($tmpname);
        if (!$result) {
            return false;
        }
        $row = [];
        $row['name'] = $name;
        $row['ext'] = $ext;
        $row['size'] = $size;
        $row['type'] = 'FromWxuser';
        $video = self::createByBiz($row);
        return $video;
    }

    private static function getVoiceData ($url) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_NOBODY, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $package = curl_exec($ch);
        $httpinfo = curl_getinfo($ch);
        if (curl_errno($ch)) {
            Debug::warn('Curl error: ' . curl_error($ch));
        }

        curl_close($ch);
        return array_merge(['mediaBody' => $package], $httpinfo);
    }
}
