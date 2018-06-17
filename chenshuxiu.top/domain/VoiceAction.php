<?php

class VoiceAction extends BaseAction
{

    // 测试图片上传
    public function doAddTest () {
        return self::SUCCESS;
    }

    public function doUpload () {
        if (! empty($_FILES["voicefile"]["tmp_name"])) {
            $tmpname = $_FILES["voicefile"]["tmp_name"];
            $realname = $_FILES["voicefile"]["name"];
            $type = preg_replace('/.*\.(.*[^\.].*)*/iU', '\\1', $realname); // 取得文件扩展名;
            if (! $type) {
                die(json_encode(array(
                    "msg" => "错误的音频格式")));
            }
            $id = BeanFinder::get("IDGenerator")->getNextId();

            $name = date("YmdHis");

            $filename = $name . ".$type";
            $filesize = $_FILES["voicefile"]["size"];
            $dirname = ROOT_TOP_PATH . "/wwwroot/voice/voices";

            $result = UploadService::uploadVoice($filename, $tmpname, $filesize, $dirname);
//            $result = UploadFile::upload($filename, $tmpname, $filesize, $dirname);
            if (! $result) {
                die(json_encode(array(
                    "msg" => "上传失败")));
            }

            $row = array();
            $row["name"] = $name;
            $row["ext"] = $type;
            $row["size"] = $filesize;
            $row["type"] = 'FromDoctor';
            $row["status"] = 1;

            $voice = Voice::createByBiz($row);

            $unitofwork = BeanFinder::get('UnitOfWork');
            $unitofwork->commit();

            header("Content-type: text/html");
            die(json_encode(array(
                "msg" => "上传成功",
                "voiceid" => $voice->id,
                "filename" => $filename,
                "tmpname" => $tmpname,
                "name" => $realname)));
        }
        header("Content-type: text/html");
        die(json_encode(array(
            "msg" => "请上传音频！")));
    }

}
