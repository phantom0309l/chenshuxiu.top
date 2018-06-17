<?php

class WxTaskMedia
{

    public function getMediaId ($wxuser, $wxtaskitem, $access_token) {
        $mediaid = 0;
        $media = MediaDao::getOneByObj3("WxTaskItem", $wxtaskitem->id, "WxTask[listen]");
        if ($media instanceof Media) {
            $now = time();
            $created_at = $media->created_at;
            if ($now - $created_at < 259000) {
                $mediaid = $media->media_id;
            } else {
                $mediajson = $this->getMediaReturnJson($access_token, $wxtaskitem);
                if (empty($mediajson['errcode'])) {
                    $media->media_id = $mediajson['media_id'];
                    $media->media_type = $mediajson['type'];
                    $media->created_at = $mediajson['created_at'];
                    $mediaid = $mediajson['media_id'];
                }
            }
        } else {
            $this->createFinalImg($wxuser, $wxtaskitem);
            $mediajson = $this->getMediaReturnJson($access_token, $wxtaskitem);

            if (! empty($mediajson)) {
                $row = array();
                $row['media_id'] = $mediajson['media_id'];
                $row['media_type'] = $mediajson['type'];
                $row['created_at'] = $mediajson['created_at'];
                $row['objtype'] = "WxTaskItem";
                $row['objid'] = $wxtaskitem->id;
                $row['objcode'] = "WxTask[listen]";
                Media::createByBiz($row);
                $mediaid = $mediajson['media_id'];
            }
        }
        return $mediaid;
    }

    private function createFinalImg ($wxuser, $wxtaskitem) {
        $cnt = $wxuser->getWxTaskCnt("listen");
        // 第三期直接用的背景图
        if ($cnt > 2) {
            return;
        }
        $bgpicture = $wxtaskitem->wxtasktplitem->picture;
        if ($cnt == 2) {
            $bgpicture = $wxtaskitem->wxtasktplitem->picture1;
        }
        $picture = $this->getQrCodePicture($wxuser);
        // 接最终图
        $imgs = array(
            'dst' => $bgpicture->getSrc(),
            'src' => array(
                array(
                    "url" => $picture->getSrc(),
                    "fw" => 198,
                    "fh" => 198,
                    "left" => 140,
                    "top" => 986)));

        if ($wxuser->headimgpictureid > 0) {
            $headarr = array(
                "url" => $wxuser->headimgpicture->getSrc(),
                "fw" => 114,
                "fh" => 114,
                "left" => 318,
                "top" => 252);
            $imgs['src'][] = $headarr;
        }
        $this->mergeImg($imgs, $wxtaskitem);
    }

    private function getQrCodePicture ($wxuser) {
        $wxshop = WxShop::getById(3);
        $access_token = $wxshop->getAccessToken();

        // 断是否已有分享二维码
        $picture = null;
        $qr = WxQrcode::getByPcodeObj($wxshop->id, "WxTask", $wxuser);

        if (empty($qr)) {
            $scene_str = "WxTask_{$wxuser->id}";
            $row = array();
            $row["wxshopid"] = $wxshop->id;
            $row["action_name"] = "QR_LIMIT_STR_SCENE";
            $row["scene_str"] = $scene_str;
            $row["pcode"] = "WxTask";
            $row["objtype"] = get_class($wxuser);
            $row["objid"] = $wxuser->id;
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

    private function getMediaReturnJson ($access_token, $wxtaskitem) {
        // {"type":"TYPE","media_id":"MEDIA_ID","created_at":123456789}
        $arr = array();
        $filename = $this->getFinalUrl($wxtaskitem);
        $mediaidjson = WxApi::uploadimg($access_token, $filename);
        if (empty($mediaidjson['errcode'])) {
            $arr = $mediaidjson;
        }
        return $arr;
    }

    private function getFinalUrl ($wxtaskitem) {
        $cnt = $wxtaskitem->wxuser->getWxTaskCnt("listen");
        // 第三期直接用的背景图
        if ($cnt > 2) {
            $picture = $wxtaskitem->wxtasktplitem->picture2;
            $picname = $picture->picname;
            $picext = $picture->picext;
            return "/home/xdata/xphoto/{$picname}.{$picext}";
        } else {
            return "/home/xdata/xphoto/qrcode/wtlisten_{$wxtaskitem->id}.jpg";
        }
    }

    private function mergeImg ($imgs, $wxtaskitem) {
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
        $finalUrl = $this->getFinalUrl($wxtaskitem);
        imagejpeg($canvas, $finalUrl);
        imagedestroy($canvas);
    }
}
