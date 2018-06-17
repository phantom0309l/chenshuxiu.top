<?php

class NameCardHelper
{

    // 非多动症-名片-正面
    public static function namecard_front (DoctorWxShopRef $doctorWxShopRef) {
        $wxshop = $doctorWxShopRef->wxshop;
        $doctor = $doctorWxShopRef->doctor;
        $disease = $doctorWxShopRef->disease;

        $wxshop_name = $wxshop->name;
        $disease_name = $disease->name;

        $sql = 'select distinct doctorid
                from doctordiseaserefs
                where diseaseid in (2,3,4,6,18,20,22)';

        $doctorids = Dao::queryValues($sql);

        // 是否多疾病组
        if (in_array($doctor->id, $doctorids)) {
            $phone = "电话: 010-60641115";
        } else {
            $phone = "电话: 010-60648881";
        }

        $hospital_name = $doctor->hospital->name;
        $doctor_name = $doctor->name;
        $doctor_title = $doctor->title;

        // 加载背景图
        $filecontent = file_get_contents(ROOT_TOP_PATH . "/audit/action/namecard/namecard_front.png");
        $img = imagecreatefromstring($filecontent);

        // 医院名称, 医生名称, 职称 ----------begin----------

        // 方正兰亭准黑
        $font = ROOT_TOP_PATH . "/wwwroot/img/static/fonts/fzltzh_GBK1.00.TTF";
        $font_color = ImageColorAllocate($img, 0x20, 0x8b, 0x3a); // 字体颜色

        $strlen = mb_strlen($hospital_name);

        $font_size = 36;
        if ($strlen > 16) {
            $font_size = 24;
        } elseif ($strlen > 14) {
            $font_size = 28;
        } elseif ($strlen > 12) {
            $font_size = 30;
        }

        $x = 94;
        $y = 150;
        imagefttext($img, $font_size, 0, $x, $y, $font_color, $font, $hospital_name);

        $font_size = 38;
        $x = 94;
        $y = 272;
        imagefttext($img, $font_size, 0, $x, $y, $font_color, $font, $doctor_name);

        // 计算医生名宽度
        $w = self::cal_ttf_str_width($doctor_name, $font, $font_size);

        $font_size = 26;
        $x = 94 + $w + 64;
        $y = 272;
        imagefttext($img, $font_size, 0, $x, $y, $font_color, $font, $doctor_title);

        // 医院名称, 医生名称, 职称 ----------end----------

        // 医院logo ----------begin----------

        if ($doctor->hospital->qr_logo_picture instanceof Picture) {
            $photo_uri = $doctor->hospital->qr_logo_picture->getSrc();
            $filecontent = file_get_contents($photo_uri);
            $img_logo = imagecreatefromstring($filecontent);

            // 覆盖上logo, 位置 (300)
            imagecopy($img, $img_logo, 76, 300, 0, 0, 242, 242);

            // 销毁句柄
            imagedestroy($img_logo);
        }

        // 医院logo ----------end----------

        // 覆盖二维码 ----------begin----------

        $qrUrl = $doctorWxShopRef->getQrUrl();

        $qr_w = 284;
        $qr_h = 284;
        $qr_color = ImageColorAllocate($img, 0x20, 0x8b, 0x3a);
        $qr_bg_color = ImageColorAllocate($img, 0xff, 0xff, 0xff);

        $qr_image = self::qr_image($qrUrl, $qr_w, $qr_h, $qr_color, $qr_bg_color);

        // 覆盖上二维码, 位置 (706,112)
        imagecopy($img, $qr_image, 706, 112, 0, 0, $qr_w, $qr_h);

        // 销毁句柄
        imagedestroy($qr_image);

        // 覆盖二维码 ----------end----------

        // 方寸logo ----------begin----------

        $filecontent = file_get_contents(ROOT_TOP_PATH . "/audit/action/namecard/namecard_front_fangcunlogo_88_green.png");
        $img_fangcun_logo = imagecreatefromstring($filecontent);

        // 覆盖上fangcunlogo, 位置 (804, 208)
        imagecopy($img, $img_fangcun_logo, 804, 208, 0, 0, 88, 88);

        // 销毁句柄
        imagedestroy($img_fangcun_logo);

        // 方寸logo ----------end----------

        // 服务号名称, 电话 ----------begin----------

        // 方正兰亭准黑
        $font = ROOT_TOP_PATH . "/wwwroot/img/static/fonts/fzltzh_GBK1.00.TTF";
        $font_color = ImageColorAllocate($img, 0x20, 0x8b, 0x3a); // 字体颜色

        // 正常偏移20
        $y_offset = 20;

        if ($disease_name) {
            $y_offset = 0;
        }

        // 服务号
        $font_size = 22;

        // 文字下中心位置 848-460
        $x = self::cal_ttf_str_x(848, $wxshop_name, $font, $font_size);
        $y = 440 + $y_offset;
        imagefttext($img, $font_size, 0, $x, $y, $font_color, $font, $wxshop_name);

        // 疾病名
        if ($disease_name) {
            $font_size = 22;
            $x = self::cal_ttf_str_x(848, $disease_name, $font, $font_size);
            $y = 485;
            imagefttext($img, $font_size, 0, $x, $y, $font_color, $font, $disease_name);

            $y_offset = 45;
        }

        // 电话
        $font_size = 17;

        // 文字下中心位置 848-500
        $x = self::cal_ttf_str_x(848, $phone, $font, $font_size);
        $y = 480 + $y_offset;
        imagefttext($img, $font_size, 0, $x, $y, $font_color, $font, $phone);

        // 服务号名称,电话 ----------end----------

        $download_filename = "{$doctor->name}-{$doctor->code}-{$doctorWxShopRef->wxshopid}-{$doctorWxShopRef->diseaseid}-名片-正面.png";

        header("Cache-Control: max-age=1, s-maxage=1, no-cache, must-revalidate");
        header("Content-Disposition: attachment; filename=\"{$download_filename}\"");
        header("Content-type: image/png");

        echo self::dpi72to300($img);

        // 销毁句柄
        imagedestroy($img);
    }

    // 非多动症-名片2-正面
    public static function namecard2_front (DoctorWxShopRef $doctorWxShopRef) {
        $wxshop = $doctorWxShopRef->wxshop;
        $doctor = $doctorWxShopRef->doctor;
        $disease = $doctorWxShopRef->disease;

        $wxshop_name = $wxshop->name;
        $disease_name = $disease->name;

        $sql = 'select distinct doctorid
                from doctordiseaserefs
                where diseaseid in (2,3,4,6,18,20,22)';

        $doctorids = Dao::queryValues($sql);

        // 是否多疾病组
        if (in_array($doctor->id, $doctorids)) {
            $phone = "010-60641115";
        } else {
            $phone = "010-60648881";
        }

        $hospital_name = $doctor->hospital->name;
        $doctor_name = $doctor->name;

        // 加载背景图
        $filecontent = file_get_contents(ROOT_TOP_PATH . "/audit/action/namecard/namecard2_front.png");
        $img = imagecreatefromstring($filecontent);

        // 覆盖二维码 ----------begin----------

        $qrUrl = $doctorWxShopRef->getQrUrl();

        $qr_w = 272;
        $qr_h = 272;
        $qr_color = ImageColorAllocate($img, 0x33, 0x33, 0x33);
        $qr_bg_color = ImageColorAllocate($img, 0xff, 0xff, 0xff);

        $qr_image = self::qr_image($qrUrl, $qr_w, $qr_h, $qr_color, $qr_bg_color);

        // 覆盖上二维码, 位置 (120, 220)
        imagecopy($img, $qr_image, 120, 220, 0, 0, $qr_w, $qr_h);

        // 销毁句柄
        imagedestroy($qr_image);

        // 覆盖二维码 ----------end----------

        // 方寸logo ----------begin----------

        $filecontent = file_get_contents(ROOT_TOP_PATH . "/audit/action/namecard/namecard_front_fangcunlogo_88_blue.png");
        $img_fangcun_logo = imagecreatefromstring($filecontent);

        // 覆盖上fangcunlogo, 位置 (212, 312)
        imagecopy($img, $img_fangcun_logo, 212, 312, 0, 0, 88, 88);

        // 销毁句柄
        imagedestroy($img_fangcun_logo);

        // 方寸logo ----------end----------

        // 服务号名称 ----------begin----------

        // 方正兰亭准黑
        $font = ROOT_TOP_PATH . "/wwwroot/img/static/fonts/fzltzh_GBK1.00.TTF";
        $font_color = ImageColorAllocate($img, 0x66, 0x66, 0x66); // 字体颜色

        $font_size = 22;

        // 文字下中心位置 256-195
        $x = self::cal_ttf_str_x(256, $wxshop_name, $font, $font_size);
        $y = 195;
        imagefttext($img, $font_size, 0, $x, $y, $font_color, $font, $wxshop_name);

        // 服务号名称 ----------end----------

        // 医院名称, 医生名 ----------begin----------

        // 医生名
        $font_size = 24;

        // 文字下中心位置 256-545
        $x = self::cal_ttf_str_x(256, $doctor_name, $font, $font_size);
        $y = 545;
        imagefttext($img, $font_size, 0, $x, $y, $font_color, $font, $doctor_name);

        // 医院名称, 字体大小变化
        $strlen = mb_strlen($hospital_name);

        $font_size = 24;
        if ($strlen > 16) {
            $font_size = 18;
        } elseif ($strlen > 14) {
            $font_size = 20;
        } elseif ($strlen > 12) {
            $font_size = 22;
        }

        // 文字下中心位置 256-597
        $x = self::cal_ttf_str_x(256, $hospital_name, $font, $font_size);
        $y = 597;
        imagefttext($img, $font_size, 0, $x, $y, $font_color, $font, $hospital_name);

        // 医院名称, 医生名 ----------end----------

        // 电话 ----------begin----------

        // 方正兰亭准黑
        $font = ROOT_TOP_PATH . "/wwwroot/img/static/fonts/fzltzh_GBK1.00.TTF";
        $font_color = ImageColorAllocate($img, 0x77, 0x77, 0x77); // 字体颜色

        // 电话
        $font_size = 24;
        $x = 680;
        $y = 597;
        imagefttext($img, $font_size, 0, $x, $y, $font_color, $font, $phone);

        // 电话 ----------end----------

        $download_filename = "{$doctor->name}-{$doctor->code}-{$doctorWxShopRef->wxshopid}-{$doctorWxShopRef->diseaseid}-名片2-正面.png";

        header("Cache-Control: max-age=1, s-maxage=1, no-cache, must-revalidate");
        header("Content-Disposition: attachment; filename=\"{$download_filename}\"");
        header("Content-type: image/png");

        echo self::dpi72to300($img);

        // 销毁句柄
        imagedestroy($img);
    }

    // 非多动症-名片-背面
    public static function namecard_back (DoctorWxShopRef $doctorWxShopRef) {
        $doctor = $doctorWxShopRef->doctor;

        $filecontent = file_get_contents(ROOT_TOP_PATH . "/audit/action/namecard/namecard_back.jpg");

        $download_filename = "{$doctor->name}-{$doctor->code}-{$doctorWxShopRef->wxshopid}-{$doctorWxShopRef->diseaseid}-名片-背面.png";

        header("Cache-Control: max-age=1, s-maxage=1, no-cache, must-revalidate");
        header("Content-Disposition: attachment; filename=\"{$download_filename}\"");
        header("Content-type: image/png");

        echo $filecontent;
    }

    // 非多动症-名片2-背面
    public static function namecard2_back (DoctorWxShopRef $doctorWxShopRef) {
        $doctor = $doctorWxShopRef->doctor;

        $filecontent = file_get_contents(ROOT_TOP_PATH . "/audit/action/namecard/namecard2_back.png");

        $download_filename = "{$doctor->name}-{$doctor->code}-{$doctorWxShopRef->wxshopid}-{$doctorWxShopRef->diseaseid}-名片2-背面.png";

        header("Cache-Control: max-age=1, s-maxage=1, no-cache, must-revalidate");
        header("Content-Disposition: attachment; filename=\"{$download_filename}\"");
        header("Content-type: image/png");

        echo $filecontent;
    }

    // 非多动症-名片2-背面-cancer
    public static function namecard2_back_cancer (DoctorWxShopRef $doctorWxShopRef) {
        $doctor = $doctorWxShopRef->doctor;

        $filecontent = file_get_contents(ROOT_TOP_PATH . "/audit/action/namecard/namecard2_back_cancer.png");

        $download_filename = "{$doctor->name}-{$doctor->code}-{$doctorWxShopRef->wxshopid}-{$doctorWxShopRef->diseaseid}-名片2-背面-cancer.png";

        header("Cache-Control: max-age=1, s-maxage=1, no-cache, must-revalidate");
        header("Content-Disposition: attachment; filename=\"{$download_filename}\"");
        header("Content-type: image/png");

        echo $filecontent;
    }

    // Adhd-名片-绿色-正面
    public static function adhd_namecard_front (DoctorWxShopRef $doctorWxShopRef, $color_str = 'green') {
        $wxshop = $doctorWxShopRef->wxshop;
        $doctor = $doctorWxShopRef->doctor;

        $hospital_name = $doctor->hospital->name;
        $doctor_name = $doctor->name;
        $doctor_title = $doctor->title;
        $doctor_title = "医生";

        // 加载背景图
        $filecontent = file_get_contents(ROOT_TOP_PATH . "/audit/action/namecard/adhd_namecard_front_{$color_str}.png");
        $img = imagecreatefromstring($filecontent);

        // 医院名称, 医生名称, 职称 ----------begin----------

        // 方正兰亭准黑
        $font = ROOT_TOP_PATH . "/wwwroot/img/static/fonts/fzltzh_GBK1.00.TTF";

        if ($color_str == 'green') {
            $font_color = ImageColorAllocate($img, 0x21, 0x8b, 0x3b); // 绿色
        } elseif ($color_str == 'blue') {
            $font_color = ImageColorAllocate($img, 0x30, 0x99, 0x96); // 蓝色
        }

        $font_size = 36;
        $x = 94;
        $y = 150;
        imagefttext($img, $font_size, 0, $x, $y, $font_color, $font, $hospital_name);

        $font_size = 38;
        $x = 94;
        $y = 272;
        imagefttext($img, $font_size, 0, $x, $y, $font_color, $font, $doctor_name);

        // 计算医生名宽度
        $w = self::cal_ttf_str_width($doctor_name, $font, $font_size);

        // $doctor_title = "医生";

        $font_size = 26;
        $x = 94 + $w + 40;
        $y = 272;
        imagefttext($img, $font_size, 0, $x, $y, $font_color, $font, $doctor_title);

        // 医院名称, 医生名称, 职称 ----------end----------

        // 医院logo ----------begin----------

        if ($doctor->hospital->qr_logo_picture instanceof Picture) {
            $photo_uri = $doctor->hospital->qr_logo_picture->getSrc();
            $filecontent = file_get_contents($photo_uri);
            $img_logo = imagecreatefromstring($filecontent);

            // 覆盖上logo, 位置 (300)
            imagecopy($img, $img_logo, 76, 300, 0, 0, 242, 242);

            // 销毁句柄
            imagedestroy($img_logo);
        }

        // 医院logo ----------end----------

        header("Cache-Control: max-age=1, s-maxage=1, no-cache, must-revalidate");
        header("Content-Disposition: attachment; filename=\"{$doctor->name}-{$doctor->code}-adhd-名片-正面-{$color_str}.png\"");
        header("Content-type: image/png");

        echo self::dpi72to300($img);

        // 销毁句柄
        imagedestroy($img);
    }

    // Adhd-名片-绿色-背面
    public static function adhd_namecard_back (DoctorWxShopRef $doctorWxShopRef, $color_str = 'green') {
        $wxshop = $doctorWxShopRef->wxshop;
        $doctor = $doctorWxShopRef->doctor;

        // 加载背景图
        $filecontent = file_get_contents(ROOT_TOP_PATH . "/audit/action/namecard/adhd_namecard_back_{$color_str}.png");
        $img = imagecreatefromstring($filecontent);

        // 覆盖二维码 ----------begin----------

        $qrUrl = $doctorWxShopRef->getQrUrl();

        $qr_w = 222;
        $qr_h = 222;
        $qr_bg_color = ImageColorAllocate($img, 0xff, 0xff, 0xff);

        if ($color_str == 'green') {
            $qr_color = ImageColorAllocate($img, 0x21, 0x8b, 0x3b); // 绿色
        } elseif ($color_str == 'blue') {
            $qr_color = ImageColorAllocate($img, 0x30, 0x99, 0x96); // 蓝色
        }

        $qr_image = self::qr_image($qrUrl, $qr_w, $qr_h, $qr_color, $qr_bg_color);

        // 覆盖上二维码, 位置 (430,139)
        imagecopy($img, $qr_image, 430, 139, 0, 0, $qr_w, $qr_h);

        // 销毁句柄
        imagedestroy($qr_image);

        // 覆盖二维码 ----------end----------

        // 方寸logo ----------begin----------

        $filecontent = file_get_contents(ROOT_TOP_PATH . "/audit/action/namecard/namecard_front_fangcunlogo_68_{$color_str}.png");
        $img_fangcun_logo = imagecreatefromstring($filecontent);

        // 覆盖上fangcunlogo, 位置 (507,216)
        imagecopy($img, $img_fangcun_logo, 507, 216, 0, 0, 68, 68);

        // 销毁句柄
        imagedestroy($img_fangcun_logo);

        // 方寸logo ----------end----------

        header("Cache-Control: max-age=1, s-maxage=1, no-cache, must-revalidate");
        header("Content-Disposition: attachment; filename=\"{$doctor->name}-{$doctor->code}-adhd-名片-背面-{$color_str}.png\"");
        header("Content-type: image/png");

        echo self::dpi72to300($img);

        // 销毁句柄
        imagedestroy($img);
    }

    // Adhd-名片-绿色-背面-复杂
    public static function adhd_namecard_back_fix (DoctorWxShopRef $doctorWxShopRef, $color_str = 'green') {
        $wxshop = $doctorWxShopRef->wxshop;
        $doctor = $doctorWxShopRef->doctor;

        $doctor_name = $doctor->name;

        // 加载背景图
        $filecontent = file_get_contents(ROOT_TOP_PATH . "/audit/action/namecard/adhd_namecard_back_{$color_str}_fix.png");
        $img = imagecreatefromstring($filecontent);

        // 覆盖二维码 ----------begin----------

        $qrUrl = $doctorWxShopRef->getQrUrl();

        $qr_w = 218;
        $qr_h = 218;
        $qr_bg_color = ImageColorAllocate($img, 0xff, 0xff, 0xff);

        if ($color_str == 'green') {
            $qr_color = ImageColorAllocate($img, 0x21, 0x8b, 0x3b); // 绿色
        } elseif ($color_str == 'blue') {
            $qr_color = ImageColorAllocate($img, 0x30, 0x99, 0x96); // 蓝色
        }

        $qr_image = self::qr_image($qrUrl, $qr_w, $qr_h, $qr_color, $qr_bg_color);

        // 覆盖上二维码, 位置 (437,128)
        imagecopy($img, $qr_image, 437, 128, 0, 0, $qr_w, $qr_h);

        // 销毁句柄
        imagedestroy($qr_image);

        // 覆盖二维码 ----------end----------

        // 方寸logo ----------begin----------

        $filecontent = file_get_contents(ROOT_TOP_PATH . "/audit/action/namecard/namecard_front_fangcunlogo_68_{$color_str}.png");
        $img_fangcun_logo = imagecreatefromstring($filecontent);

        // 覆盖上fangcunlogo, 位置 (512,203)
        imagecopy($img, $img_fangcun_logo, 512, 203, 0, 0, 68, 68);

        // 销毁句柄
        imagedestroy($img_fangcun_logo);

        // 方寸logo ----------end----------

        // 某医生多动症院外管理平台 ----------begin----------

        // 方正兰亭准黑
        $font = ROOT_TOP_PATH . "/wwwroot/img/static/fonts/fzltzh_GBK1.00.TTF";

        if ($color_str == 'green') {
            $font_color = ImageColorAllocate($img, 0x21, 0x8b, 0x3b); // 绿色
        } elseif ($color_str == 'blue') {
            $font_color = ImageColorAllocate($img, 0x30, 0x99, 0x96); // 蓝色
        }

        $font_size = 24;

        $name_len = mb_strlen($doctor_name);
        $first_name_len = 1;
        if ($name_len > 3) {
            $first_name_len = 2;
        }

        $doctor_str1 = mb_substr($doctor_name, 0, $first_name_len) . "医生多动症院外管理平台";

        // 计算医生名宽度
        $w = self::cal_ttf_str_width($doctor_str1, $font, $font_size);

        $x = 545 - $w / 2;
        $y = 110;

        imagefttext($img, $font_size, 0, $x, $y, $font_color, $font, $doctor_str1);

        // 某医生多动症院外管理平台 ----------end----------

        // 4. 某医生助理对孩子进行院外管理观察 ----------begin----------

        $font_color = ImageColorAllocate($img, 0x20, 0x46, 0x29); // 深绿色

        $font_size = 21;

        $name_len = mb_strlen($doctor_name);
        $first_name_len = 1;
        if ($name_len > 3) {
            $first_name_len = 2;
        }

        $doctor_str2 = "4. " . mb_substr($doctor_name, 0, $first_name_len) . "医生助理对孩子进行院外管理观察";

        $x = 272;
        $y = 534;

        imagefttext($img, $font_size, 0, $x, $y, $font_color, $font, $doctor_str2);

        // 4. 某医生助理对孩子进行院外管理观察 ----------end----------

        header("Cache-Control: max-age=1, s-maxage=1, no-cache, must-revalidate");
        header("Content-Disposition: attachment; filename=\"{$doctor->name}-{$doctor->code}-adhd-名片-背面-{$color_str}-fix.png\"");
        header("Content-type: image/png");

        echo self::dpi72to300($img);

        // 销毁句柄
        imagedestroy($img);
    }

    // adhd-桌牌-横版
    public static function adhd_zhuopai_heng (DoctorWxShopRef $doctorWxShopRef) {
        $wxshop = $doctorWxShopRef->wxshop;
        $doctor = $doctorWxShopRef->doctor;

        $hospital_name = $doctor->hospital->name;
        $doctor_name = $doctor->name;
        $doctor_title = $doctor->title;
        $doctor_title = "医生";

        // $doctor_name = "姚献花";
        // $doctor_title = "主任医师 教授";
        // $hospital_name = "河南中医药大学第一附属医院";

        $filecontent = file_get_contents(ROOT_TOP_PATH . "/audit/action/namecard/adhd_zhuopai_heng.png");
        $img = imagecreatefromstring($filecontent);

        // 写上医院和医生名字 ----------begin----------

        $font = ROOT_TOP_PATH . "/wwwroot/img/static/fonts/msyh.ttf";
        $font_color = imagecolorallocate($img, 0x2f, 0x7d, 0xb3); // 字体颜色
        $font_size = 56;
        $x = 172;
        $y = 478;
        imagefttext($img, $font_size, 0, $x, $y, $font_color, $font, $hospital_name);

        $font_color = imagecolorallocate($img, 0x33, 0x33, 0x33); // 字体颜色
        $font_size = 48;
        $x = 172;
        $y = 608;
        imagefttext($img, $font_size, 0, $x, $y, $font_color, $font, $doctor_name);

        $w = self::cal_ttf_str_width($doctor_name, $font, $font_size);

        $font_color = imagecolorallocate($img, 0x55, 0x55, 0x55); // 字体颜色
        $font_size = 36;

        $x = $x + $w + 64;
        $y = 608;

        imagefttext($img, $font_size, 0, $x, $y, $font_color, $font, $doctor_title);

        // 写上医院和医生名字 ----------end----------

        // 覆盖二维码 ----------begin----------
        $qrUrl = $doctorWxShopRef->getQrUrl();

        $qr_w = 390;
        $qr_h = 390;
        $qr_color = ImageColorAllocate($img, 0x33, 0x33, 0x33);
        $qr_bg_color = ImageColorAllocate($img, 0xff, 0xff, 0xff);

        $qr_image = self::qr_image($qrUrl, $qr_w, $qr_h, $qr_color, $qr_bg_color);

        // 覆盖上二维码, 位置 (204,710)
        imagecopy($img, $qr_image, 204, 710, 0, 0, $qr_w, $qr_h);

        // 销毁句柄
        imagedestroy($qr_image);

        // 覆盖二维码 ----------end----------

        header("Cache-Control: max-age=1, s-maxage=1, no-cache, must-revalidate");
        header("Content-Disposition: attachment; filename=\"{$doctor->name}-{$doctor->code}-桌牌-横.png\"");
        header("Content-type: image/png");

        echo self::dpi72to300($img);

        // 销毁句柄
        imagedestroy($img);
    }

    // 非多动症-桌牌-竖版
    public static function zhuopai_shu (DoctorWxShopRef $doctorWxShopRef) {
        $wxshop = $doctorWxShopRef->wxshop;
        $doctor = $doctorWxShopRef->doctor;

        $hospital_name = $doctor->hospital->name;
        $doctor_name = $doctor->name;
        $doctor_title = $doctor->title;

        $wxshop_name = $wxshop->name;
        $wxshop_title = "{$wxshop_name}平台";

        // $doctor_name = "金欣俐";
        // $doctor_title = "主任医师 教授";
        // $hospital_name = "延吉市中医院";

        $filecontent = file_get_contents(ROOT_TOP_PATH . "/audit/action/namecard/zhuopai_shu.png");
        $img = imagecreatefromstring($filecontent);

        // 写上医院和医生名字, 服务号名称 ----------begin----------

        $font = ROOT_TOP_PATH . "/wwwroot/img/static/fonts/msyh.ttf";
        $font_color = imagecolorallocate($img, 0x1e, 0x8a, 0xcb); // 字体颜色, 蓝色
        $center_x = 739; // 中线

        // 医院名
        $font_size = 46;
        $x = self::cal_ttf_str_x($center_x, $hospital_name, $font, $font_size);
        $y = 432;
        imagefttext($img, $font_size, 0, $x, $y, $font_color, $font, $hospital_name);

        // 医生名
        $font_size = 45;
        $x = self::cal_ttf_str_x($center_x, $doctor_name, $font, $font_size);
        $y = 536;
        imagefttext($img, $font_size, 0, $x, $y, $font_color, $font, $doctor_name);

        // 二维码下面的服务号名称
        $font_size = 45;
        $x = self::cal_ttf_str_x($center_x, $wxshop_name, $font, $font_size);
        $y = 1156;
        imagefttext($img, $font_size, 0, $x, $y, $font_color, $font, $wxshop_name);

        // 顶部的服务号名称
        $font_color = imagecolorallocate($img, 0x33, 0x33, 0x33); // 字体颜色, 黑色
        $font_size = 55;
        $x = self::cal_ttf_str_x($center_x, $wxshop_title, $font, $font_size);
        $y = 220;
        imagefttext($img, $font_size, 0, $x, $y, $font_color, $font, $wxshop_title);

        // 顶部的服务号名称
        $font_color = imagecolorallocate($img, 0xff, 0xff, 0xff); // 字体颜色, 白色
        $font_size = 26;
        $str = "【{$wxshop_name}】微信号, 点击主页面";
        $x = 570;
        $y = 1922;
        imagefttext($img, $font_size, 0, $x, $y, $font_color, $font, $str);

        // 写上医院和医生名字, 服务号名称 ----------end----------

        // 覆盖二维码 ----------begin----------
        $qrUrl = $doctorWxShopRef->getQrUrl();

        $qr_w = 376;
        $qr_h = 376;
        $qr_color = ImageColorAllocate($img, 0x33, 0x33, 0x33);
        $qr_bg_color = ImageColorAllocate($img, 0xff, 0xff, 0xff);

        $qr_image = self::qr_image($qrUrl, $qr_w, $qr_h, $qr_color, $qr_bg_color);

        // 覆盖上二维码, 位置 (550,640)
        imagecopy($img, $qr_image, 550, 640, 0, 0, $qr_w, $qr_h);

        // 销毁句柄
        imagedestroy($qr_image);

        // 覆盖二维码 ----------end----------

        // 方寸logo ----------begin----------

        $filecontent = file_get_contents(ROOT_TOP_PATH . "/audit/action/namecard/namecard_front_fangcunlogo_110_blue.png");
        $img_fangcun_logo = imagecreatefromstring($filecontent);

        // 覆盖上fangcunlogo, 位置 (683, 773)
        imagecopy($img, $img_fangcun_logo, 683, 773, 0, 0, 110, 110);

        // 销毁句柄
        imagedestroy($img_fangcun_logo);

        // 方寸logo ----------end----------

        header("Cache-Control: max-age=1, s-maxage=1, no-cache, must-revalidate");
        header("Content-Disposition: attachment; filename=\"{$doctor->name}-{$doctor->code}-桌牌-竖.png\"");
        header("Content-type: image/png");

        echo self::dpi72to300($img);

        // 销毁句柄
        imagedestroy($img);
    }

    // adhd-桌牌-竖版
    public static function adhd_zhuopai_shu (DoctorWxShopRef $doctorWxShopRef) {
        $wxshop = $doctorWxShopRef->wxshop;
        $doctor = $doctorWxShopRef->doctor;

        $hospital_name = $doctor->hospital->name;
        $doctor_name = $doctor->name;
        $doctor_title = $doctor->title;

        // $doctor_name = "金欣俐";
        // $doctor_title = "主任医师 教授";
        // $hospital_name = "延吉市中医院";

        $filecontent = file_get_contents(ROOT_TOP_PATH . "/audit/action/namecard/adhd_zhuopai_shu.png");
        $img = imagecreatefromstring($filecontent);

        // 写上医院和医生名字 ----------begin----------

        $font = ROOT_TOP_PATH . "/wwwroot/img/static/fonts/msyh.ttf";
        $font_color = imagecolorallocate($img, 0x1e, 0x8a, 0xcb); // 字体颜色
        $center_x = 739; // 中线

        $font_size = 46;
        $x = self::cal_ttf_str_x($center_x, $hospital_name, $font, $font_size);
        $y = 432;
        imagefttext($img, $font_size, 0, $x, $y, $font_color, $font, $hospital_name);

        $font_size = 45;
        $x = self::cal_ttf_str_x($center_x, $doctor_name, $font, $font_size);
        $y = 536;
        imagefttext($img, $font_size, 0, $x, $y, $font_color, $font, $doctor_name);

        // 写上医院和医生名字 ----------end----------

        // 覆盖二维码 ----------begin----------
        $qrUrl = $doctorWxShopRef->getQrUrl();

        $qr_w = 376;
        $qr_h = 376;
        $qr_color = ImageColorAllocate($img, 0x33, 0x33, 0x33);
        $qr_bg_color = ImageColorAllocate($img, 0xff, 0xff, 0xff);

        $qr_image = self::qr_image($qrUrl, $qr_w, $qr_h, $qr_color, $qr_bg_color);

        // 覆盖上二维码, 位置 (550,640)
        imagecopy($img, $qr_image, 550, 640, 0, 0, $qr_w, $qr_h);

        // 销毁句柄
        imagedestroy($qr_image);

        // 覆盖二维码 ----------end----------

        // 方寸logo ----------begin----------

        $filecontent = file_get_contents(ROOT_TOP_PATH . "/audit/action/namecard/namecard_front_fangcunlogo_110_blue.png");
        $img_fangcun_logo = imagecreatefromstring($filecontent);

        // 覆盖上fangcunlogo, 位置 (683, 773)
        imagecopy($img, $img_fangcun_logo, 683, 773, 0, 0, 110, 110);

        // 销毁句柄
        imagedestroy($img_fangcun_logo);

        // 方寸logo ----------end----------

        header("Cache-Control: max-age=1, s-maxage=1, no-cache, must-revalidate");
        header("Content-Disposition: attachment; filename=\"{$doctor->name}-{$doctor->code}-桌牌-竖.png\"");
        header("Content-type: image/png");

        echo self::dpi72to300($img);

        // 销毁句柄
        imagedestroy($img);
    }

    // 礼来向日葵桌卡
    public static function lilly_zhuoka (Doctor_hezuo $doctor_hezuo, $hospital_name) {
        $doctor = $doctor_hezuo->doctor;
        $doctor_name = $doctor->name . " 医生";

        // 2018*1451 (lilly_zhuoka.png), 背景图有点小问题, 右边有个白边
        $filecontent = file_get_contents(ROOT_TOP_PATH . "/audit/action/namecard/lilly_zhuoka.png");
        $img = imagecreatefromstring($filecontent);

        // 写上医院和医生名字 ----------begin----------
        $font = ROOT_TOP_PATH . "/wwwroot/img/static/fonts/msyhbd.ttf";
        $font_color = imagecolorallocate($img, 0xf7, 0xfb, 0xf9); // 字体颜色
        $font_size = 48;
        if (mb_strlen($hospital_name) > 16) {
            $font_size = 36;
        } elseif (mb_strlen($hospital_name) > 14) {
            $font_size = 42;
        }

        imagefttext($img, $font_size, 0, 486, 560, $font_color, $font, $hospital_name);

        $font = ROOT_TOP_PATH . "/wwwroot/img/static/fonts/msyh.ttf";

        // 根据字数,偏移位置
        $strlen = mb_strlen($doctor_name) - 6;

        imagefttext($img, 32, 0, 1440 - $strlen * 16, 560, $font_color, $font, $doctor_name);
        // 因为要裁剪缩放, 故意加粗了字体
        imagefttext($img, 32, 0, 1441 - $strlen * 16, 561, $font_color, $font, $doctor_name);

        // 写上医院和医生名字 ----------end----------

        // 覆盖二维码 ----------begin----------
        $wxshop = WxShop::getById(1);
        $doctorWxShopRef = DoctorWxShopRefDao::getByDoctoridWxShopidDiseaseid($doctor->id, $wxshop->id, 0);
        if (false == $doctorWxShopRef instanceof DoctorWxShopRef) {
            echo "doctorWxShopRef[{$doctor->id}, 1] is null ";
            exit();
        }
        $qrUrl = $doctorWxShopRef->getQrUrl();

        $qr_w = 345;
        $qr_h = 345;
        $qr_color = ImageColorAllocate($img, 0x81, 0x47, 0x1c);
        $qr_bg_color = ImageColorAllocate($img, 0xff, 0xfb, 0xf0);

        $qr_image = self::qr_image($qrUrl, $qr_w, $qr_h, $qr_color, $qr_bg_color);

        // 覆盖上二维码, 位置 (1390,696)
        imagecopy($img, $qr_image, 1390, 696, 0, 0, $qr_w, $qr_h);

        // 销毁句柄
        imagedestroy($qr_image);

        // 新尺寸 2012*1438 => 1240*886
        // 原图进行了适当裁剪, 上边裁剪了13像素, 右边裁剪了6像素
        $new_image = imagecreatetruecolor(1240, 886);
        imagecopyresized($new_image, $img, 0, 0, 0, 13, 1240, 886, 2012, 1438);

        // 覆盖二维码 ----------end----------

        header("Cache-Control: max-age=1, s-maxage=1, no-cache, must-revalidate");
        header("Content-Disposition: attachment; filename=\"{$doctor->name}-{$doctor->code}-桌卡.png\"");
        header("Content-type: image/png");

        // echo self::dpi72to300($img);
        echo self::dpi72to300($new_image);

        // 销毁句柄
        imagedestroy($img);
        imagedestroy($new_image);
    }

    // 礼来向日葵,患者卡,发给患者
    public static function lilly_patient_page_back (Doctor_hezuo $doctor_hezuo, $hospital_name) {
        $doctor = $doctor_hezuo->doctor;
        $doctor_name = $doctor->name . " 医生";

        $filecontent = file_get_contents(ROOT_TOP_PATH . "/audit/action/namecard/lilly_patient_page_back.png");
        $img = imagecreatefromstring($filecontent);

        // 写上医院和医生名字 ----------begin----------

        $font = ROOT_TOP_PATH . "/wwwroot/img/static/fonts/msyh.ttf";
        $font_color = imagecolorallocate($img, 0x7a, 0x3b, 0x0c); // 字体颜色

        $font_size = 64;
        $strlen = mb_strlen($doctor_name) - 1;
        $font_width_half = (int) (64 * $strlen * 1.35 / 2 + 68 / 4) - 10;

        imagefttext($img, $font_size, 0, 1476 - $font_width_half, 3230, $font_color, $font, $doctor_name);

        $font_size = 64;
        $strlen = mb_strlen($hospital_name);
        $font_width_half = (int) (64 * $strlen * 1.35 / 2) - 10;

        imagefttext($img, $font_size, 0, 1476 - $font_width_half, 3370, $font_color, $font, $hospital_name);

        // 写上医院和医生名字 ----------end----------

        // 覆盖二维码 ----------begin----------
        $wxshop = WxShop::getById(1);
        $doctorWxShopRef = DoctorWxShopRefDao::getByDoctoridWxShopidDiseaseid($doctor->id, $wxshop->id, 0);
        if (false == $doctorWxShopRef instanceof DoctorWxShopRef) {
            echo "doctorWxShopRef[{$doctor->id}, 1] is null ";
            exit();
        }
        $qrUrl = $doctorWxShopRef->getQrUrl();

        $qr_w = 552;
        $qr_h = 552;
        $qr_color = ImageColorAllocate($img, 0x81, 0x47, 0x1c);
        $qr_bg_color = ImageColorAllocate($img, 0xff, 0xfb, 0xf0);

        $qr_image = self::qr_image($qrUrl, $qr_w, $qr_h, $qr_color, $qr_bg_color);

        // 覆盖上二维码, 位置 (1204,2544)
        imagecopy($img, $qr_image, 1204, 2544, 0, 0, $qr_w, $qr_h);

        // 销毁句柄
        imagedestroy($qr_image);

        // 覆盖二维码 ----------end----------

        header("Cache-Control: max-age=1, s-maxage=1, no-cache, must-revalidate");
        header("Content-Disposition: attachment; filename=\"{$doctor->name}-{$doctor->code}-患者卡.png\"");
        header("Content-type: image/png");

        echo self::dpi72to300($img);

        // 销毁句柄
        imagedestroy($img);
    }

    // 礼来向日葵, 患者卡, 发给患者 20170919新版
    public static function lilly_patient_page_back_20170919 (Doctor_hezuo $doctor_hezuo, $hospital_name) {
        $doctor = $doctor_hezuo->doctor;
        $doctor_name = $doctor->name . " 医生";

        $filecontent = file_get_contents(ROOT_TOP_PATH . "/audit/action/namecard/lilly_patient_page_back_20170919.png");
        $img = imagecreatefromstring($filecontent);

        // 写上医院和医生名字 ----------begin----------

        $font = ROOT_TOP_PATH . "/wwwroot/img/static/fonts/msyh.ttf";
        $font_color = imagecolorallocate($img, 0x7a, 0x3b, 0x0c); // 字体颜色

        $font_size = 36;

        // 计算医生名宽度
        $w = self::cal_ttf_str_width($doctor_name, $font, $font_size);
        $x = 2632 - $w / 2;
        $y = 2000;

        imagefttext($img, $font_size, 0, $x, $y, $font_color, $font, $doctor_name);
        imagefttext($img, $font_size, 0, $x + 1, $y, $font_color, $font, $doctor_name);

        $w = self::cal_ttf_str_width($hospital_name, $font, $font_size);
        $x = 2632 - $w / 2;
        $y = 2080;

        imagefttext($img, $font_size, 0, $x, $y, $font_color, $font, $hospital_name);
        imagefttext($img, $font_size, 0, $x + 1, $y, $font_color, $font, $hospital_name);

        // 写上医院和医生名字 ----------end----------

        // 覆盖二维码 ----------begin----------
        $wxshop = WxShop::getById(1);
        $doctorWxShopRef = DoctorWxShopRefDao::getByDoctoridWxShopidDiseaseid($doctor->id, $wxshop->id, 0);
        if (false == $doctorWxShopRef instanceof DoctorWxShopRef) {
            echo "doctorWxShopRef[{$doctor->id}, 1] is null ";
            exit();
        }
        $qrUrl = $doctorWxShopRef->getQrUrl();

        $qr_w = 380;
        $qr_h = 380;
        $qr_color = ImageColorAllocate($img, 0x81, 0x47, 0x1c);
        $qr_bg_color = ImageColorAllocate($img, 0xff, 0xfb, 0xf0);

        $qr_image = self::qr_image($qrUrl, $qr_w, $qr_h, $qr_color, $qr_bg_color);

        // 覆盖上二维码
        imagecopy($img, $qr_image, 2442, 1530, 0, 0, $qr_w, $qr_h);

        // 销毁句柄
        imagedestroy($qr_image);

        // 覆盖二维码 ----------end----------

        header("Cache-Control: max-age=1, s-maxage=1, no-cache, must-revalidate");
        header("Content-Disposition: attachment; filename=\"{$doctor->name}-{$doctor->code}-患者卡-0919.png\"");
        header("Content-type: image/png");

        echo self::dpi72to300($img);

        // 销毁句柄
        imagedestroy($img);
    }

    // 裁边,并拉伸的二维码
    private static function qr_image ($qrUrl, $dst_w, $dst_h, $dst_qr_color, $dst_qr_bg_color) {
        $filecontent = file_get_contents($qrUrl);
        $src_image = imagecreatefromstring($filecontent);

        for ($i = 1; $i < 430; $i ++) {
            for ($j = 1; $j < 430; $j ++) {
                $rgb = imagecolorat($src_image, $i, $j);

                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;

                // echo "<br/>[{$i}][{$j}] = x ({$r},{$g},{$b}) {$rgb} ";

                if ($r < 30 && $g < 30 && $b < 30) {
                    // 纯黑色 和 接近黑色
                    imageFilledRectangle($src_image, $i, $j, $i, $j, $dst_qr_color);
                } elseif ($r > 200 && $g > 200 && $b > 200) {
                    // 纯白色 和 接近白色
                    imageFilledRectangle($src_image, $i, $j, $i, $j, $dst_qr_bg_color);
                } else {
                    // 不存在
                }
            }
        }

        // 裁边,并拉伸
        $dst_qr_image = imagecreate($dst_w, $dst_h);
        imagecopyresampled($dst_qr_image, $src_image, 0, 0, 30, 30, $dst_w, $dst_h, 370, 370);

        imagedestroy($src_image);

        return $dst_qr_image;
    }

    // 计算文字区宽度
    private static function cal_ttf_str_width ($str, $font, $font_size) {
        // 左下x、y, 右下,右上, 左上
        $fontarea = ImageTTFBBox($font_size, 0, $font, $str);
        return ($fontarea[2] - $fontarea[0]);
    }

    // 计算左下角x, 根据文字区下边中心点x
    private static function cal_ttf_str_x ($x, $str, $font, $font_size) {
        // 左下x、y, 右下,右上, 左上
        $fontarea = ImageTTFBBox($font_size, 0, $font, $str);
        return $x - ($fontarea[2] - $fontarea[0]) / 2;
    }

    // 参数是句柄
    private static function dpi72to300 ($img) {
        $filename = "/tmp/namecard_tmp";
        imagepng($img, $filename);

        $file_content = file_get_contents($filename);

        // 数据块长度为9
        $len = pack("N", 9);
        // 数据块类型标志为pHYs
        $sign = pack("A*", "pHYs");
        // X方向和Y方向的分辨率均为300DPI（1像素/英寸=39.37像素/米），单位为米（0为未知，1为米）
        $data = pack("NNC", 300 * 39.37, 300 * 39.37, 0x01);
        // CRC检验码由数据块符号和数据域计算得到
        $checksum = pack("N", crc32($sign . $data));
        $phys = $len . $sign . $data . $checksum;

        $pos = strpos($file_content, "pHYs");
        if ($pos > 0) {
            // 修改pHYs数据块
            $file_content = substr_replace($file_content, $phys, $pos - 4, 21);
        } else {
            // IHDR结束位置（PNG头固定长度为8，IHDR固定长度为25）
            $pos = 33;
            // 将pHYs数据块插入到IHDR之后
            $file_content = substr_replace($file_content, $phys, $pos, 0);
        }

        return $file_content;
    }
}
