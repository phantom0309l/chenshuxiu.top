<?php

class WxQrcodeMgrAction extends AuditBaseAction
{

    public function doAdd () {
        $qrcodeurl = XRequest::getValue("qrcodeurl", "");
        XContext::setValue("qrcodeurl", $qrcodeurl);
        return self::SUCCESS;
    }

    public function doAddPost () {
        $wxshopid = XRequest::getValue("wxshopid", 0);
        $pcode = XRequest::getValue("pcode", "");
        $wxuserid = XRequest::getValue("wxuserid", 0);
        $scene_pre = XRequest::getValue("scene_pre", "");

        $wxshop = WxShop::getById($wxshopid);
        $wxuser = WxUser::getById($wxuserid);
        $access_token = $wxshop->getAccessToken();

        $scene_str = "{$scene_pre}{$wxuserid}";
        $qr = WxQrcode::getByPcodeObj($wxshopid, $pcode, $wxuser);

        if (empty($qr)) {
            $row = array();
            $row["wxshopid"] = $wxshop->id;
            $row["action_name"] = "QR_LIMIT_STR_SCENE";
            $row["scene_str"] = $scene_str;
            $row["pcode"] = $pcode;
            $row["objtype"] = get_class($wxuser);
            $row["objid"] = $wxuser->id;
            $qr = WxQrcode::createByBiz($row);

            $ticket = WxApi::getQrTicket($access_token, $scene_str);
            $qr->ticket = $ticket;
            $qr->url = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=" . "{$ticket}";
            // 取生成的二维码图片
            $picture = Picture::createByFetchWX($qr->url);
            $qr->pictureid = $picture->id;
        }

        $preMsg = "修改已保存 " . XDateTime::now();
        XContext::setJumpPath("/wxqrcodemgr/add?preMsg=" . urlencode($preMsg) . "&qrcodeurl=" . $qr->url);
        return self::SUCCESS;
    }

    // 添加医生提交
    public function doAddDoctorPost () {
        $wxshopid = XRequest::getValue("wxshopid", 0);
        $doctorid = XRequest::getValue("doctorid", 0);
        $pcode = XRequest::getValue("pcode", "");
        $scene_str = XRequest::getValue("scene_str", "");

        $wxshop = WxShop::getById($wxshopid);
        $doctor = WxUser::getById($doctorid);
        $access_token = $wxshop->getAccessToken();
        $qr = WxQrcode::getByPcodeObj($wxshopid, $pcode, $doctor);

        if (empty($qr)) {
            $row = array();
            $row["wxshopid"] = $wxshop->id;
            $row["action_name"] = "QR_LIMIT_STR_SCENE";
            $row["scene_str"] = $scene_str;
            $row["pcode"] = $pcode;
            $row["objtype"] = get_class($doctor);
            $row["objid"] = $doctor->id;
            $qr = WxQrcode::createByBiz($row);

            $ticket = WxApi::getQrTicket($access_token, $scene_str);
            $qr->ticket = $ticket;
            $qr->url = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=" . "{$ticket}";
            // 取生成的二维码图片
            $picture = Picture::createByFetchWX($qr->url);
            $qr->pictureid = $picture->id;
        }

        $preMsg = "修改已保存 " . XDateTime::now();
        XContext::setJumpPath("/wxqrcodemgr/add?preMsg=" . urlencode($preMsg) . "&qrcodeurl=" . $qr->url);
        return self::SUCCESS;
    }

}
