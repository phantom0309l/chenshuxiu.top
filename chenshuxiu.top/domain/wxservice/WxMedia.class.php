<?php

class WxMedia
{

    public function getMediaId ($obj, $access_token, $objcode, $imgprev, $imgs) {
        $mediaid = 0;
        $entityClass = get_class($obj);
        $media = MediaDao::getOneByObj3($entityClass, $obj->id, $objcode);
        if ($media instanceof Media) {
            $now = time();
            $created_at = $media->created_at;
            if ($now - $created_at < 259000) {
                $mediaid = $media->media_id;
            } else {
                $mediajson = $this->getMediaReturnJson($access_token, $obj, $imgprev);
                if (empty($mediajson['errcode'])) {
                    $media->media_id = $mediajson['media_id'];
                    $media->media_type = $mediajson['type'];
                    $media->created_at = $mediajson['created_at'];
                    $mediaid = $mediajson['media_id'];
                }
            }
        } else {
            $this->mergeImg($imgs, $obj, $imgprev);
            $mediajson = $this->getMediaReturnJson($access_token, $obj, $imgprev);

            if (! empty($mediajson)) {
                $row = array();
                $row['media_id'] = $mediajson['media_id'];
                $row['media_type'] = $mediajson['type'];
                $row['created_at'] = $mediajson['created_at'];
                $row['objtype'] = get_class($obj);
                $row['objid'] = $obj->id;
                $row['objcode'] = $objcode;
                Media::createByBiz($row);
                $mediaid = $mediajson['media_id'];
            }
        }
        return $mediaid;
    }

    private function getMediaReturnJson ($access_token, $obj, $prev) {
        // {"type":"TYPE","media_id":"MEDIA_ID","created_at":123456789}
        $arr = array();
        $filename = $this->getFinalUrl($obj, $prev);
        $mediaidjson = WxApi::uploadimg($access_token, $filename);
        if (empty($mediaidjson['errcode'])) {
            $arr = $mediaidjson;
        }
        return $arr;
    }

    private function getFinalUrl ($obj, $prev) {
        return "/home/xdata/xphoto/qrcode/{$prev}_{$obj->id}.jpg";
    }

    public function getQrCodePicture ($obj, $pcode, $prev) {
        $wxshop = WxShop::getById(3);
        $access_token = $wxshop->getAccessToken();

        // 断是否已有分享二维码
        $picture = null;
        $qr = WxQrcode::getByPcodeObj($wxshop->id, $pcode, $obj);

        if (empty($qr)) {
            $scene_str = "{$prev}{$obj->id}";
            $row = array();
            $row["wxshopid"] = $wxshop->id;
            $row["action_name"] = "QR_LIMIT_STR_SCENE";
            $row["scene_str"] = $scene_str;
            $row["pcode"] = $pcode;
            $row["objtype"] = get_class($obj);
            $row["objid"] = $obj->id;
            $qr = WxQrcode::createByBiz($row);

            $ticket = WxApi::getQrTicket($access_token, $scene_str);
            $qr->ticket = $ticket;
            $qr->url = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=" . "{$ticket}";
            // 取生成的二维码图片
            $picture = Picture::createByFetchWX($qr->url);
            $qr->pictureid = $picture->id;
        } else {
            $picture = $qr->picture;
        }
        return $picture;
    }

    private function mergeImg ($imgs, $obj, $prev) {
        list ($max_width, $max_height) = getimagesize($imgs['dst']);
        $canvas = imagecreatetruecolor($max_width, $max_height);

        $dst_im = imagecreatefrompng($imgs['dst']);
        imagecopy($canvas, $dst_im, 0, 0, 0, 0, $max_width, $max_height);
        imagedestroy($dst_im);

        $srcs = $imgs['src'];
        foreach ($srcs as $src) {
            $url = $src['url'];
            $left = $src['left'];
            $top = $src['top'];
            $fw = $src['fw'];
            $fh = $src['fh'];
            $src_info = getimagesize($url);
            $fileType = $src_info[2];
            if ($fileType == 2) {
                // 原图是 jpg 类型
                $src_im = imagecreatefromjpeg($url);
            } else
                if ($fileType == 3) {
                    // 原图是 png 类型
                    $src_im = imagecreatefrompng($url);
                } else {
                    // 无法识别的类型
                    $src_im = imagecreatefrompng($url);
                }

            $tempcanvas = imagecreatetruecolor($fw, $fh);
            imagecopyresampled($tempcanvas, $src_im, 0, 0, 0, 0, $fw, $fh, $src_info['0'], $src_info['1']);

            imagecopy($canvas, $tempcanvas, $left, $top, 0, 0, $fw, $fh);
            imagedestroy($src_im);
            imagedestroy($tempcanvas);
        }
        // imagefttext("Image", "Font Size", "Rotate Text", "Left Position",
        // "Top Position", "Font Color", "Font Name", "Text To Print");
        // $fontsrc = ROOT_TOP_PATH . "/wwwroot/img/static/fonts/dq.otf";
        // $content = "测试";
        // $red = imagecolorallocate($canvas, 150,0,0);
        // imagefttext($canvas, 30, 0, 40, 154, $red, $fontsrc, $content);
        $finalUrl = $this->getFinalUrl($obj, $prev);
        imagejpeg($canvas, $finalUrl);
        imagedestroy($canvas);
    }
}
