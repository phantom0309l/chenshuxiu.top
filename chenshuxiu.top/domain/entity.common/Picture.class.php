<?php
// Picture
// 图片,图片文件暂列在硬盘上, 将来可以改进到LevelDb等方案

// owner by sjp
// create by sjp
// review by sjp 20160628
class Picture extends Entity
{

    protected function init_keys() {
        $this->_keys = self::getKeysDefine();
    }

    public static function getKeysDefine() {
        return array(
            'picname',
            'picext',
            'width',
            'height',
            'size',
            'objtype',
            'objid',
            'type',
            'name',
            'pos',
            'status',
            'fromurl',
            'fromurlmd5');
    }

    protected function init_keys_lock() {
        $this->_keys_lock = array();
    }

    protected function init_belongtos() {
        $this->_belongtos = array();
        $this->_belongtos['obj'] = array(
            "type" => $this->objtype,
            "key" => "objid");
    }

    // 图片名字
    public function getFilePath() {
        return $this->picname . "." . $this->picext;
    }

    // 默认图, TODO rework
    public function isDefaultPicture() {
        return $this->picname == 'abc'; // 需要UI设计一个默认图片
    }

    // 获取默认图片id, 气泡, TODO rework
    public static function getDefaultPictureId() {
        return 123456; // 需要UI设计一个默认图片
    }

    // 获取默认图片,气泡
    public static function getDefaultPicture() {
        return self::getById(self::getDefaultPictureId());
    }

    // 获取图片url, 最常用的函数
    public function getSrc($width = 0, $height = 0, $iscut = false) {
        if ($width == 0) {
            $width = 1500;
        }
        if ($height == 0) {
            $height = 1500;
        }
        return $this->getSrcImg($this->picname, $this->picext, $width, $height, $iscut);
    }

    // 查看大图url,链接,用于app
    public function getBigSrc4App() {
        if ($this->width > 1500 && $this->height > 2000) {
            return $this->getSrc(750, 1334);
        }

        if ($this->width > 2000 && $this->height > 1500) {
            return $this->getSrc(1080, 720);
        }

        return $this->getSrc();
    }

    // 获取方图,用于app图片列表,限宽不限高
    public function getSrc4PhotoList($width = 0) {
        $height = $this->getHeightByWidth($width);
        return $this->getSrcImg($this->picname, $this->picext, $width, $height, false);
    }

    // 生成图片高度,等比缩放
    public function getHeightByWidth($width = 100) {
        return intval($width * $this->height / $this->width);
    }

    // 字符串: 宽 × 高, 用于显示
    public function getWidthHeight() {
        return $this->width . " × " . $this->height;
    }

    // 图片大小,SizeKB
    public function getSizeKB() {
        return floor($this->size / 1024);
    }

    // 图片大小，SizeMB
    public function getSizeMB() {
        return $this->size / 1024 / 1024;
    }

    // toJsonArray, 请先查看 JsonPicture
    public function toJsonArray() {
        return array(
            "pictureid" => $this->id,
            "url" => $this->getSrc(),
            "picname" => $this->picname,
            "picext" => $this->picext,
            "width" => $this->width,
            "height" => $this->height,
            "size" => $this->size,
            "objtype" => $this->objtype,
            "objid" => $this->objid,
            "type" => $this->type,
            "name" => $this->name,
            "pos" => $this->pos);
    }

    // $box_width : 相框宽度
    // $box_height : 相框高度
    // $iscut : 是否裁剪
    // $isfill : 此参数仅对非裁剪(等比缩放)有效: 1 缩略图比相框大,覆盖相框; 0 缩略图比相框小,相框包住缩略图;
    public function toJsonArrayThumb($box_width = 0, $box_height = 0, $iscut = true, $isfill = false) {
        $box_width_bak = $box_width;
        $box_height_bak = $box_height;

        // 原图
        if ($box_width == 0) {
            $thumb_width = $this->width;
            $thumb_height = $this->height;
        } elseif ($box_height == 0) { // 裁成方图
            $thumb_width = $box_width;
            $thumb_height = $box_width;
        } else { // 裁剪或等比缩放

            // 先复制
            $thumb_width = $box_width;
            $thumb_height = $box_height;

            // 裁剪
            if ($iscut) {
                // 啥也不做
            } else { // 不裁剪,等比缩放

                // 充满模式,放大相框
                if ($isfill) {
                    // 相框相比图片要宽,宽度充满
                    if ($box_width / $box_height > $this->width / $this->height) {

                        // 相框高度加大
                        $box_height = floor($this->height / $this->width * $box_width);

                        // 宽度充满
                        $thumb_width = $box_width;
                        // 缩略图高度也加大
                        $thumb_height = floor($this->height / $this->width * $box_width);
                    } else { // 相框相比图片要窄,高度充满

                        // 相框宽度加大
                        $box_width = floor($this->width / $this->height * $box_height);

                        // 高度充满
                        $thumb_height = $box_height;
                        // 缩略图宽度也加大
                        $thumb_width = floor($this->width / $this->height * $box_height);
                    }
                } else { // 内嵌模式, 框的尺寸不变
                    // 相框相比图片要宽,高度充满
                    if ($box_width / $box_height > $this->width / $this->height) {

                        // 高度充满
                        $thumb_height = $box_height;
                        // 缩略图宽度变小
                        $thumb_width = floor($this->width / $this->height * $box_height);
                    } else { // 相框相比图片要窄,宽度充满

                        // 宽度充满
                        $thumb_width = $box_width;
                        // 缩略图高度变小
                        $thumb_height = floor($this->height / $this->width * $box_width);
                    }
                }
            }

            // 修正缩略图尺寸,宽度
            if ($thumb_width > $this->width) {
                $thumb_width = $this->width;
            }

            // 修正缩略图尺寸,高度
            if ($thumb_height > $this->height) {
                $thumb_height = $this->height;
            }
        }

        return array(
            "thumb_url" => $this->getSrc($box_width, $box_height, $iscut),
            "thumb_width" => $thumb_width,
            "thumb_height" => $thumb_height);
    }

    // ====================================
    // --------------- static ---------------
    // ====================================

    // $row = array();
    // $row["picname"] = $picname;
    // $row["picext"] = $picext;
    // $row["width"] = $width;
    // $row["height"] = $height;
    // $row["size"] = $size;
    // $row["objtype"] = $objtype;
    // $row["objid"] = $objid;
    // $row["type"] = $type;
    // $row["name"] = $name;
    // $row["pos"] = $pos;
    // $row["status"] = $status;
    public static function createByBiz($row) {
        DBC::requireNotEmpty($row, "Picture::createByBiz row cannot empty");

        $default = array();
        $default['objtype'] = '';
        $default['objid'] = 0;
        $default['type'] = 0;
        $default['name'] = '';
        $default['pos'] = 0;
        $default['status'] = 1;
        $default['fromurl'] = "";
        $default['fromurlmd5'] = "";

        $row += $default;

        return new self($row);
    }

    // 网络抓取图片
    public static function createByFetch($picurl) {
        $picurl = trim($picurl);
        if (empty($picurl)) {
            return ErrCode::no_img_data;
        }

        $picture = Picture::getByFromUrl($picurl);
        if ($picture instanceof Picture) {
            return $picture;
        }

        $userAgent = "Mozilla/5.0 (compatible; Baiduspider/2.0; +http://www.baidu.com/search/spider.html)";
        $httpRequest = XHttpRequest::getInstance();
        $httpRequest->setUserAgent($userAgent);

        $err = "";
        $content = @$httpRequest->getUrlContents($picurl, $err);

        if (empty($content)) {
            return ErrCode::no_img_data;
        }

        $id = BeanFinder::get("IDGenerator")->getNextId();
        $tmpname_bak = $tmpname = "/tmp/netpic_{$id}";
        file_put_contents($tmpname, $content);

        $picture = self::uploadByTmpName($tmpname);

        if ($picture instanceof Picture) {
            $picture->fromurl = $picurl;
            $picture->fromurlmd5 = md5($picurl);
        }

        @unlink($tmpname_bak);

        return $picture;
    }

    // 抓取微信图片
    public static function createByFetchWXOfMediaid($media_id) {
        $wxshop = WxShop::getById(1);
        $access_token = $wxshop->getAccessToken();
        $picurl = "https://api.weixin.qq.com/cgi-bin/media/get?access_token=" . $access_token . "&media_id=" . $media_id;
        return self::createByFetchWX($picurl);
    }

    // 抓取微信图片,TODO rework 函数重复
    public static function createByFetchWX($picurl) {
        Debug::trace("picurl：{$picurl}");

        $picurl = trim($picurl);
        if (empty($picurl)) {
            return ErrCode::no_img_data;
        }

        $picture = Picture::getByFromUrl($picurl);
        if ($picture instanceof Picture) {
            // return $picture;
        }

        $content = file_get_contents($picurl);
        if (empty($content)) {
            return ErrCode::no_img_data;
        }

        $id = BeanFinder::get("IDGenerator")->getNextId();
        $tmpname_bak = $tmpname = "/tmp/netpic_{$id}";
        file_put_contents($tmpname, $content);

        $picture = self::uploadByTmpName($tmpname);

        if ($picture instanceof Picture) {
            $picture->fromurl = $picurl;
            $picture->fromurlmd5 = md5($picurl);
        }

        @unlink($tmpname_bak);
        return $picture;
    }

    // 图片上传服务 by Api
    public static function uploadByApi($objtype = '', $objid = 0, $type = 'Api') {
        if (!empty($_FILES["imgfile"]["tmp_name"])) {
            $tmpname = $_FILES["imgfile"]["tmp_name"];
            $realname = $_FILES["imgfile"]["name"];

            return self::uploadByTmpname($tmpname, $realname, $objtype, $objid, $type);
        } else {
            return ErrCode::no_img_data;
        }
    }

    // 图片上传服务base64 by Api
    public static function uploadBase64ByApi($objtype = '', $objid = 0, $type = 'Api') {
        $base64_img = XRequest::getUnSafeValue('imgfile');
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_img, $result)) {
            $imagetype = $result[2];
            $tmpname = '/tmp/' . date('YmdHis_') . '.' . $imagetype;
            if (file_put_contents($tmpname, base64_decode(str_replace($result[1], '', $base64_img)))) {
                $ret = self::uploadByTmpname($tmpname, "", $objtype, $objid, $type);
                @unlink($tmpname);
                return $ret;
            } else {
                return ErrCode::upload_img_fail;

            }
        } else {
            //文件错误
            return ErrCode::no_img_data;
        }
    }

    // 图片上传服务
    public static function uploadByTmpname($tmpname, $realname = "", $objtype = '', $objid = 0, $type = 'NetPic') {
        if (empty($tmpname)) {
            return ErrCode::no_img_data;
        }

        $realname = "";
        $imagetype = XUtility::checkImgType($tmpname);
        if (!$imagetype) {
            return ErrCode::error_img_type;
        }

        $md5 = md5(file_get_contents($tmpname));
        $firstdir = substr($md5, 0, 1);
        $seconddir = substr($md5, 1, 2);
        $filename = $md5 . ".$imagetype";
        $filesize = filesize($tmpname);
        $imgdir = PHOTO_PATH . "/$firstdir/$seconddir";

        $result = UploadService::uploadPhoto($filename, $tmpname, $filesize, $imgdir);
//        $result = UploadFile::upload($filename, $tmpname, $filesize, $imgdir);
        if (!$result) {
            return ErrCode::upload_img_fail;
        }
        $t = new ThumbHandler();
        $t->setSrcImg($tmpname);
        $row = array();
        $row["picname"] = "$firstdir/$seconddir/$md5";
        $row["picext"] = $imagetype;
        $row["width"] = $t->src_w;
        $row["height"] = $t->src_h;
        $row["size"] = $filesize;
        $row["name"] = $filename;

        $row["objtype"] = $objtype;
        $row["objid"] = $objid;
        $row["type"] = $type;

        $picture = Picture::createByBiz($row);

        return $picture;
    }

    // /////////////////////////////
    // 静态查询方法
    // ///////////////////////////

    // getByFromUrl
    public static function getByFromUrl($fromurl) {
        $fromurl = trim($fromurl);
        $fromurlmd5 = md5($fromurl);
        return self::getByFromUrlmd5($fromurlmd5);
    }

    // getByFromUrlmd5
    public static function getByFromUrlmd5($fromurlmd5) {
        $cond = " AND fromurlmd5=:fromurlmd5 ";
        $bind = [
            ':fromurlmd5' => $fromurlmd5];
        return Dao::getEntityByCond('Picture', $cond, $bind);
    }

    // 生成图片url
    public static function getSrcImg($picname, $picext, $width = 0, $height = 0, $iscut = false) {
        if ($width == 0) {
            return Config::getConfig("photo_uri") . "/" . $picname . '.' . $picext;
        } elseif ($height == 0) {
            return Config::getConfig("photo_uri") . "/" . $picname . ".{$width}." . $picext;
        } elseif ($iscut) {
            return Config::getConfig("photo_uri") . "/" . $picname . ".{$width}-{$height}." . $picext;
        } else {
            return Config::getConfig("photo_uri") . "/" . $picname . ".{$width}_{$height}." . $picext;
        }
    }

    // 根据url判断图片是否真是存在
    public static function isExist ($img_url) {
        $result = false;
        $defaultStream = stream_context_get_default();
        stream_context_set_default(
            array(
                'http' => [
                    'method' => 'HEAD'
                ]
            )
        );

        if(get_headers($img_url) !== false) {
            $result = true;
        }
        stream_context_get_default($defaultStream);
        return $result;
    }
}
