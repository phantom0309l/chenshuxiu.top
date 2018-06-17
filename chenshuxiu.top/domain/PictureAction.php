<?php

class PictureAction extends BaseAction
{

    // 测试图片上传
    public function doAddTest () {
        return self::SUCCESS;
    }

    // 接口新规范 for ipad
    public function doAddJson () {
        $response = array();
        $response['errno'] = 0;
        $response['errmsg'] = "图片上传成功";
        $response['query'] = array();
        $response['query']['imgfile'] = "...";

        $picture = Picture::uploadByApi();

        if ($picture instanceof Picture) {
            $response['data'] = JsonPicture::jsonArrayForIpad($picture);
        } else {
            $response['errno'] = $picture;
            $response['errmsg'] = ErrCode::desc($picture);
        }

        XContext::setValue("json", $response);
        return self::TEXTJSON;
    }
    // 接口新规范 for ipad
    public function doAddByUrlJson () {
        $url = XRequest::getValue("url", '');
        $response = array();
        $response['errno'] = 0;
        $response['errmsg'] = "图片上传成功";
        $response['query'] = array();
        $response['query']['url'] = $url;

        $picture = Picture::createByFetchWX($url);

        if ($picture instanceof Picture) {
            $response['data'] = JsonPicture::jsonArrayForIpad($picture);
        } else {
            $response['errno'] = $picture;
            $response['errmsg'] = ErrCode::desc($picture);
        }

        XContext::setValue("json", $response);
        return self::TEXTJSON;
    }

    // 客户端上传单张图片接口
    // http://api.xxxx.com/picture/upload
    // 参数 {
    // imgfile: 图片文件
    // objtype: 关联对象类型,如 User
    // objid: 关联对象id
    // type: 图片分类,如 face
    // width: 缩略图 宽 , 默认320
    // height: 缩略图 高 , 默认320
    // iscut: 缩略图是否裁剪:0 不裁剪, 1 裁剪 ; 默认 0 不裁剪
    // }
    // 返回值 {
    // 见debug
    // }
    public function doUpload () {
        // ini_set('display_errors', 1);
        $data = array();
        $data['status'] = ErrCode::ok;
        $data['errcode'] = 0;
        $data['errmsg'] = "上传成功";
        $data['objtype'] = $objtype = XRequest::getValue("objtype", 'App');
        $data['objid'] = $objid = XRequest::getValue("objid", 0);
        $data['type'] = $type = XRequest::getValue("type", '');

        $data['width'] = $width = XRequest::getValue("width", 320);
        $data['height'] = $height = XRequest::getValue("height", 320);
        $data['iscut'] = $iscut = XRequest::getValue("iscut", 0);

        $picture = Picture::uploadByApi($objtype, $objid, $type);

        if ($picture instanceof Picture) {
            $data['pictureid'] = $picture->id;
            $data['thumburl'] = $picture->getSrc($width, $height, $iscut);
            $data['picture'] = JsonPicture::jsonArray($picture, 100, 100, false, false);
        } else {
            $data['status'] = ErrCode::error;
            $data['errcode'] = $picture;
            $data['errmsg'] = ErrCode::desc($picture);
        }

        XContext::setValue("json", $data);
        return self::TEXTJSON;
    }

    // js 上传图片
    public function doUploadImagePost () {
        // ni_set('display_errors', 1);
        if (! empty($_FILES["imgurl"]["tmp_name"])) {
            $tmpname = $_FILES["imgurl"]["tmp_name"];
            $realname = $_FILES["imgurl"]["name"];
            $type = XUtility::checkImgType($tmpname);
            if (! $type) {
                die(json_encode(array(
                    "msg" => "错误的图片格式")));
            }
            $id = BeanFinder::get("IDGenerator")->getNextId();
            $md5 = md5(file_get_contents($tmpname));
            $firstdir = substr($md5, 0, 1);
            $seconddir = substr($md5, 1, 2);
            $filename = $md5 . ".$type";
            $filesize = $_FILES["imgurl"]["size"];
            $imgdir = PHOTO_PATH . "/$firstdir/$seconddir/";
            $finalfilename = '';

            $result = UploadService::uploadPhoto($filename, $tmpname, $filesize, $imgdir, $finalfilename);
            if ($result === false || $result['errno'] != 0) {
                echo json_encode(['msg'=>"上传失败 ". $result['errmsg']], JSON_UNESCAPED_UNICODE);
                exit();
            }

//            $result = UploadFile::upload($filename, $tmpname, $filesize, $imgdir);
//            if (! $result) {
//                die(json_encode(array(
//                    "msg" => "图片上传失败")));
//            }

            // ebug::errorlog(print_r($result,true));

            $t = new ThumbHandler();
            $t->setSrcImg($tmpname);
            // //小图
            // $t->setDstImg($imgdir.$md5.".s.".$type);
            // $t->createImg(100,100);
            // //中图
            // $t->setDstImg($imgdir.$md5.".m.".$type);
            // $t->createImg(450,450);

            $ww = XRequest::getValue('w', 100);
            $hh = XRequest::getValue('h', 100);
            $isCut = XRequest::getValue('isCut', 0);
            $cutStr = $isCut ? '-' : '_';

            // 小图
            // $t->setDstImg($imgdir.$md5.".100.".$type);
            // $t->createCutImg(100);
            // //中图
            // $t->setDstImg($imgdir.$md5.".m.".$type);
            // $t->createCutImg(450);

            $photo_uri = XContext::getValue("photo_uri");
            $picname = "$firstdir/$seconddir/$md5";
            // $imgurl = $img_uri."/$firstdir/$seconddir/$filename";
            $thumb = $photo_uri . "/$firstdir/$seconddir/$md5.{$ww}{$cutStr}{$hh}.$type";

            $row = array();
            $row["picname"] = $picname;
            $row["picext"] = $type;
            $row["width"] = $t->src_w;
            $row["height"] = $t->src_h;
            $row["size"] = $filesize;
            $row["name"] = $realname;

            $row["objtype"] = XRequest::getValue('objtype', '');
            $row["objid"] = XRequest::getValue('objid', 0);
            $row["type"] = XRequest::getValue('type', "");

            $picture = Picture::createByBiz($row);

            $unitofwork = BeanFinder::get('UnitOfWork');
            $unitofwork->commit();

            header("Content-type: text/html");
            $fromWeditor = XRequest::getValue('fromWeditor', 0);
            if ($fromWeditor) {
                die($picture->getSrc());
            } else {
                die(
                json_encode(
                    array(
                        "pictureid" => $picture->id,
                        "thumb" => $thumb,
                        "name" => $realname)));
            }
        }
        header("Content-type: text/html");
        die(json_encode(array(
            "msg" => "请上传图片！！")));
    }

    public function doUploadWordImagePost () {
        // ini_set('display_errors', 1);
        if (! empty($_FILES["upfile"]["tmp_name"])) {
            $tmpname = $_FILES["upfile"]["tmp_name"];
            $realname = $_FILES["upfile"]["name"];
            $type = XUtility::checkImgType($tmpname);
            if (! $type) {
                die(json_encode(array(
                    "msg" => "错误的图片格式")));
            }
            $id = BeanFinder::get("IDGenerator")->getNextId();
            $md5 = md5(file_get_contents($tmpname));
            $firstdir = substr($md5, 0, 1);
            $seconddir = substr($md5, 1, 2);
            $filename = $md5 . ".$type";
            $filesize = $_FILES["upfile"]["size"];
            $imgdir = PHOTO_PATH . "/$firstdir/$seconddir/";

            $result = UploadService::uploadPhoto($filename, $tmpname, $filesize, $imgdir);

//            $result = UploadFile::upload($filename, $tmpname, $filesize, $imgdir);
            if (! $result) {
                die(json_encode(array(
                    "msg" => "图片上传失败")));
            }
            $t = new ThumbHandler();
            $t->setSrcImg($tmpname);
            // //小图
            // $t->setDstImg($imgdir.$md5.".s.".$type);
            // $t->createImg(100,100);
            // //中图
            // $t->setDstImg($imgdir.$md5.".m.".$type);
            // $t->createImg(450,450);

            $ww = XRequest::getValue('w', 540);
            $hh = XRequest::getValue('h', 800);
            $isCut = XRequest::getValue('isCut', 0);
            $cutStr = $isCut ? '-' : '_';

            // 小图
            // $t->setDstImg($imgdir.$md5.".100.".$type);
            // $t->createCutImg(100);
            // //中图
            // $t->setDstImg($imgdir.$md5.".m.".$type);
            // $t->createCutImg(450);

            $photo_uri = XContext::getValue("photo_uri");
            $picname = "$firstdir/$seconddir/$md5";
            // $upfile = $img_uri."/$firstdir/$seconddir/$filename";
            $thumb = $photo_uri . "/$firstdir/$seconddir/$md5.{$ww}{$cutStr}{$hh}.$type";

            $row = array();
            $row["picname"] = $picname;
            $row["picext"] = $type;
            $row["width"] = $t->src_w;
            $row["height"] = $t->src_h;
            $row["size"] = $filesize;
            $row["name"] = $realname;

            $row["objtype"] = XRequest::getValue('objtype', '');
            $row["objid"] = XRequest::getValue('objid', 0);

            $picture = Picture::createByBiz($row);

            $unitofwork = BeanFinder::get('UnitOfWork');
            $unitofwork->commit();

            $callback = $_GET['callback'];
            $info = array(
                "originalName" => $realname,
                "name" => $filename,
                "url" => $thumb,
                "size" => $filesize,
                "type" => $type,
                "state" => "SUCCESS");

            /**
             * 返回数据
             */
            if ($callback) {
                echo '<script>' . $callback . '(' . json_encode($info) . ')</script>';
            } else {
                echo json_encode($info);
            }
            exit();
        }
        die(json_encode(array(
            "msg" => "请上传图片！")));
    }
}
