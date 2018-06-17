<?php
// Dwx_pipeMgrAction
class Dwx_pipeMgrAction extends AuditBaseAction
{
    public function dolist () {
        $pagesize = XRequest::getValue("pagesize", 15);
        $pagenum = XRequest::getValue("pagenum", 1);

        $diseaseidstr = $this->getContextDiseaseidStr();

        $doctorid = XRequest::getValue('doctorid', 0);
        $doctor_name = XRequest::getValue('doctor_name', '');
        XContext::setValue('doctorid', $doctorid);
        XContext::setValue('doctor_name', $doctor_name);

//        // 下拉选择的医生
//        $sql = "select distinct a.*
//            from doctors a
//            inner join doctordiseaserefs b on b.doctorid = a.id
//            where b.diseaseid in ({$diseaseidstr}) ";
//        $select_doctors = Dao::loadEntityList('Doctor', $sql);
//        XContext::setValue('select_doctors', $select_doctors);

        // start
        $sql = "";
        $bind = [];

        if ($doctorid) {
            $sql .= "select a.*
                from doctors a 
                where id = :doctorid ";
            $bind[':doctorid'] = $doctorid;
        } else {
            $sql .= "select distinct a.*
                from
                (
                    select *
                    from dwx_pipes
                    order by createtime desc
                ) t
                inner join doctors a on a.id = t.doctorid
                inner join doctordiseaserefs b on b.doctorid = a.id
                where b.diseaseid in ({$diseaseidstr})
                group by t.doctorid
                order by a.is_new_pipe desc,t.createtime desc ";
        }

        $doctors = Dao::loadEntityList4Page('Doctor', $sql, $pagesize, $pagenum, $bind);

        $doctoralls = Dao::queryValues($sql, $bind);
        $cnt = count($doctoralls);
        $url = "/dwx_pipemgr/list?doctorid={$doctorid}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);

        XContext::setValue("pagelink", $pagelink);
        XContext::setValue('doctors', $doctors);

        return self::SUCCESS;
    }

    public function doReply () {
        $doctorid = XRequest::getValue('doctorid', 0);

        $doctor = Doctor::getById($doctorid);

        XContext::setValue('doctor', $doctor);

        return self::SUCCESS;
    }

    public function doPipeListHtml () {
        $doctorid = XRequest::getValue('doctorid', 0);
        $doctor = Doctor::getById($doctorid);
        DBC::requireNotEmpty($doctor, "医生不能为空");
        $page_size = XRequest::getValue('page_size', 5);
        $offsetpipetime = XRequest::getValue('offsetpipetime', '0000-00-00 00:00:00');

        $page_size = intval($page_size);

        $cond = "";
        $bind = [];

        $cond .= " and doctorid = :doctorid";
        $bind[':doctorid'] = $doctorid;

        if ($offsetpipetime != '0000-00-00 00:00:00') {
            $cond .= " and createtime < :createtime ";
            $bind[':createtime'] = $offsetpipetime;
        }

        $cond .= " order by createtime desc limit {$page_size}";

        $dwx_pipes = Dao::getEntityListByCond('Dwx_pipe', $cond, $bind);

        XContext::setValue('doctor', $doctor);
        XContext::setValue('offsetpipetime', $offsetpipetime);
        XContext::setValue('dwx_pipes', $dwx_pipes);

        return self::SUCCESS;
    }

    public function doAuditorToDoctorForDwxJson () {
        $auditor = $this->myauditor;
        $doctorid = XRequest::getValue('doctorid', 0);
        $doctor = Doctor::getById($doctorid);
        $content = XRequest::getValue('content', '');
        DBC::requireNotEmpty($content, "内容不能为空");
        DBC::requireNotEmpty($doctor, "医生不能为空");

        $appendarr["doctorid"] = $doctor->id;
        $appendarr["auditorid"] = $auditor->id;
        $appendarr["send_by_way"] = 'txtmsg';

        // 方寸管理端wxshopid = 2
        $cond = " and userid = :userid and wxshopid = 2 ";
        $bind = [
            ':userid' => $doctor->userid
        ];

        $wxusers = Dao::getEntityListByCond('WxUser', $cond, $bind);
        $wxuser_cnt = count($wxusers);
        if ($wxuser_cnt < 1) {
            echo "nowxuser";

            return self::BLANK;
        }
        $list = [];
        foreach ($wxusers as $wxuser) {
            $list[] = WechatMsg::sendmsg2wxuser_dwx($wxuser, $content, $appendarr);
        }

        $dwx_kefumsg = array_pop($list);
        if ($dwx_kefumsg instanceof Dwx_kefumsg) {
            // 创建dwx_pipe流
            Dwx_pipe::createByEntity($dwx_kefumsg);
        }

        // 运营回复之后，is_new_pipe设置为0
        $doctor->is_new_pipe = 0;

        return self::BLANK;
    }

    public function doSendAuditor2DoctorPicJson() {
        $doctorid = XRequest::getValue("doctorid", 0);
        $pictureids = XRequest::getValue('pictureids', []);

        $doctor = Doctor::getById($doctorid);

        $cond = " and userid = :userid and wxshopid = 2 ";
        $bind = [
            ':userid' => $doctor->userid
        ];
        $wxusers = Dao::getEntityListByCond('WxUser', $cond, $bind);

        if (true == empty($wxusers)) {
            $this->returnError("医生没有绑定方寸管理端");
        }

        $wxshop = WxShop::getById(2);
        $access_token = $wxshop->getAccessToken();
        foreach ($pictureids as $pictureid) {
            $picture = Picture::getById($pictureid);

            $filename = Config::getConfig('xphoto_path') . '/' . $picture->getFilePath();
            //$mediaidjson = WxApi::uploadimg($access_token, $filename);
            $mediaidjson = WxApi::uploadimgByUrl($access_token, $picture->getSrc());

            $dwx_picmsg = null;
            foreach ($wxusers as $wxuser) {
                $myauditor = $this->myauditor;
                $row = [];
                $row["wxuserid"] = $wxuser->id;
                $row["userid"] = $wxuser->userid;
                $row["doctorid"] = $doctorid;
                $row["auditorid"] = $myauditor->id;
                $row["pictureid"] = $pictureid;
                $row["remark"] = "运营发图片给医生";
                $dwx_picmsg = Dwx_picmsg::createByBiz($row);

                WxApi::kefuImageMsg($wxshop, $wxuser->openid, $mediaidjson['media_id']);
            }

            Dwx_pipe::createByEntity($dwx_picmsg);
        }

        $this->result = [
            'errno' => 1,
            'errmsg' => '发送成功',
            'data' => []
        ];

        return self::TEXTJSON;
    }
}
