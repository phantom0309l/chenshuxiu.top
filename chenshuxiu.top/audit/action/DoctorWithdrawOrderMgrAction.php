<?php

class DoctorWithdrawOrderMgrAction extends AuditBaseAction {

    // 构造函数，初始化了很多数据
    public function __construct() {
        parent::__construct ();
    }

    // 列表
    public function doList() {
        $pagesize = XRequest::getValue("pagesize", 300);
        $pagenum = XRequest::getValue("pagenum", 1);

        $doctorid = XRequest::getValue('doctorid', 0);
        $doctor_name = XRequest::getValue('doctor_name', '');
        XContext::setValue('doctorid', $doctorid);
        XContext::setValue('doctor_name', $doctor_name);

        $cond = "";
        $bind = [];

        if($doctorid > 0){
            $cond .= " and doctorid = :doctorid ";
            $bind[":doctorid"] = $doctorid;
        }

        //获得实体
        $sql = "select *
                    from doctorwithdraworders
                    where 1 = 1 {$cond} order by id";
        $doctorWithdrawOrders = Dao::loadEntityList4Page("DoctorWithdrawOrder", $sql, $pagesize, $pagenum, $bind);
        XContext::setValue("doctorWithdrawOrders", $doctorWithdrawOrders);

        //获得分页
        $countSql = "select count(*)
                    from doctorwithdraworders
                    where 1 = 1 {$cond} order by id";
        $cnt = Dao::queryValue($countSql, $bind);
        $url = "/doctorwithdrawordermgr/list?doctorid={$doctorid}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);
        XContext::setValue("pagelink", $pagelink);


        return self::SUCCESS;
    }

    public function doPassJson(){
        $doctorwithdraworderid = XRequest::getValue("doctorwithdraworderid", 0);
        $remark = XRequest::getValue("remark", "");

        $doctorWithdrawOrder = DoctorWithdrawOrder::getById($doctorwithdraworderid);
        $doctorWithdrawOrder->pass();
        $doctorWithdrawOrder->remark = $remark;
        echo "ok";
        return self::BLANK;
    }
}
