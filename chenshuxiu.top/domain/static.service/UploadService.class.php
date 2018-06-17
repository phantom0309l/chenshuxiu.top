<?php

class UploadService
{
    const MAX_FILE_SIZE = 1024 * 1024 * 30;

    /*
    * @return {
               "data": {
                   "ext": "jpeg",
                   "filename": "4020e0c460df1e23d4d22aac2843d16a",
                   "size": 16533,
                   "url": "http://photo.fangcunyisheng.com/4/02/4020e0c460df1e23d4d22aac2843d16a.origin.jpeg",
                   "width": 160,
                   "height": 240
               },
               "errmsg": "",
               "errno": 0
           }
    */
    //上传图片
    public static function uploadPhoto($filename, $tmpname, $filesize, $dirname, $finalfilename='') {
        $upload_uri = Config::getConfig("upload_uri");
        if (empty($upload_uri)) {
            Debug::error("upload_uri config is null");
            return false;
        }
        $upload_uri .= "/photo";
        $filetype = "photo";
        return self::uploadImp($upload_uri, $tmpname, $filename, $filesize, $dirname, $filetype, $finalfilename);
    }

    //上传音频
    public static function uploadVoice($filename, $tmpname, $filesize, $dirname) {
        $upload_uri = Config::getConfig("upload_uri");
        if (empty($upload_uri)) {
            Debug::error("upload_uri config is null");
            return false;
        }
        $upload_uri .= "/media";
        $filetype = "voice";
        return self::uploadImp($upload_uri, $tmpname, $filename, $filesize, $dirname, $filetype);
    }

    //上传视频
    public static function uploadVideo($filename, $tmpname, $filesize, $dirname) {
        $upload_uri = Config::getConfig("upload_uri");
        if (empty($upload_uri)) {
            Debug::error("upload_uri config is null");
            return false;
        }
        $upload_uri .= "/media";
        $filetype = "video";
        return self::uploadImp($upload_uri, $tmpname, $filename, $filesize, $dirname, $filetype);
    }

    //上传通话录音
    public static function uploadMeeting($filename, $tmpname, $filesize, $dirname) {
        $upload_uri = Config::getConfig("upload_uri");
        if (empty($upload_uri)) {
            Debug::error("upload_uri config is null");
            return false;
        }
        $upload_uri .= "/media";
        $filetype = "meeting";
        return self::uploadImp($upload_uri, $tmpname, $filename, $filesize, $dirname, $filetype);
    }

    //dirname 不应该由client端决定，这里有此参数纯粹是为了在新存储服务宕机时可以沿用旧版的存储
    private static function uploadImp($upload_uri, $tmpname, $filename, $filesize, $dirname, $filetype, $finalfilename='') {
        if ($filesize > self::MAX_FILE_SIZE) {
            Debug::error("上传文件超过最大限制30M");
            return false;
        }
        $data = [];
        $data['file'] = new CURLFile($tmpname);
        $data['filename'] = $filename;
        $data['filesize'] = $filesize;
        $data['filetype'] = $filetype;
        $data['finalfilename'] = $finalfilename;

        $ch = curl_init();
        // 超时设置
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_URL, $upload_uri);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $content = curl_exec($ch);
        $errno = curl_errno($ch);
        //如果连接不上服务则使用旧版的上传接口
        //当静态服务稳定之后需要移除本处代码
        if ($errno == CURLE_COULDNT_CONNECT) {
            Debug::error(__METHOD__ . " $upload_uri can't connect to service, may be down");
            return UploadFile::upload($filename, $tmpname, $filesize, $dirname);
        } else if ($errno) {
            Debug::warn('Curl error: ' . curl_error($ch));
            return false;
        }
        curl_close($ch);
        Debug::trace(__METHOD__ . "content " . $content);
        $ret = json_decode($content, true);
        if (!is_array($ret)) {
            Debug::warn(__METHOD__ . " $upload_uri upload api's return is not a json string " . $content);
            return false;
        }
        if ($ret['errno'] != 0) {
            Debug::warn(__METHOD__ . " upload api's return err " . $ret['errmsg']);
            return false;
        }
        //这个ret其实有很多文件属性，这里由于历史系统在外层计算文件的相关属性
        //一期为了便于回滚操作（测试静态服务的稳定性），就不修改调用处的代码了
        return true;
    }
}