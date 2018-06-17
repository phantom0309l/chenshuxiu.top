<?php
class CuiBaodaoBase
{

    public $obj = null;

    public function __construct ($obj) {
        $this->obj = $obj;
    }
    public function dowork () {
        $begintime = XDateTime::now();
        $obj = $this->obj;
        $config = $obj->getConfig();
        $starttime = $config["starttime"];
        $endtime = $config["endtime"];
        $typestr = $config["typestr"];
        $content = $config["content"];

        $unitofwork = BeanFinder::get("UnitOfWork");

        $ids = $this->getWxuserIds($starttime, $endtime);
        $i = 0;
        foreach ($ids as $id) {
            $i ++;
            if ($i >= 50) {
                $i = 0;
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
            }

            $wxuser = WxUser::getById($id);
            if( method_exists($obj, 'filter') && $obj->filter($wxuser, $typestr) ){
                continue;
            }
            $send_content = $obj->getSendContent($wxuser);
            $this->sendmsg($wxuser, $send_content);
            $this->createComment($wxuser, $typestr, $content);
        }

        $unitofwork->commitAndInit();

        return count($ids);
    }
    public function getWxuserIds($starttime, $endtime){
        $bind = [];
        $bind[":starttime"] = $starttime;
        $bind[":endtime"] = $endtime;
        $sql = "select a.id
                from wxusers a
                inner join users b on b.id = a.userid
                where a.subscribe=1 and a.wxshopid=1 and a.ref_pcode='DoctorCard'
                and a.createtime>= :starttime and a.createtime < :endtime
                and b.patientid=0";
        return Dao::queryValues($sql, $bind);
    }

    public function sendmsg($wxuser, $content){
        if( $wxuser instanceof WxUser ){
            PushMsgService::sendTxtMsgToWxUserBySystem($wxuser, $content);
        }
    }

    public function createComment ($wxuser, $typestr, $content) {
        if( false == $wxuser instanceof WxUser ){
            return;
        }
        $row = array();
        $row['wxuserid'] = $wxuser->id;
        $row['userid'] = $wxuser->userid;
        $row["doctorid"] = $wxuser->doctorid;
        $row['objtype'] = "WxUser";
        $row['objid'] = $wxuser->id;
        $row['typestr'] = $typestr;
        $row['content'] = $content;
        Comment::createByBiz($row);
    }

}
