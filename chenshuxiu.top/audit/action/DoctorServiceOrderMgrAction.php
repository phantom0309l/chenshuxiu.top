<?php

class DoctorServiceOrderMgrAction extends AuditBaseAction {

    // 构造函数，初始化了很多数据
    public function __construct() {
        parent::__construct ();
    }

    // 列表按月
    public function doListMonth() {
        $pagesize = XRequest::getValue("pagesize", 60);
        $pagenum = XRequest::getValue("pagenum", 1);

        $is_menzhen = XRequest::getValue("is_menzhen", -1);
        $is_sign = XRequest::getValue("is_sign", -1);

        $doctorid = XRequest::getValue("doctorid", 0);
        $timestamp = time();
        $the_month_default = date('Y-m-01',strtotime(date('Y',$timestamp).'-'.(date('m',$timestamp)-1).'-01'));
        $the_month = XRequest::getValue("the_month", $the_month_default);

        $cond = "";
        $bind = [];

        if($doctorid > 0){
            $cond .= " and a.doctorid = :doctorid ";
            $bind[":doctorid"] = $doctorid;
        }

        if($the_month){
            $the_month = substr($the_month, 0, 7) . "-01";
            $cond .= " and a.the_month = :the_month ";
            $bind[":the_month"] = $the_month;
        }

        if($is_menzhen > -1){
            if($is_menzhen == 0){
                $cond .= " and b.menzhen_offset_daycnt = 0 ";
            }else{
                $cond .= " and b.menzhen_offset_daycnt > 0 ";
            }
        }

        if($is_sign > -1){
            $cond .= " and b.is_sign = :is_sign ";
            $bind[":is_sign"] = $is_sign;
        }
        //获得实体
        $sql = "select a.*
                    from doctorserviceorders a
                    inner join doctors b on b.id = a.doctorid
                    where 1 = 1 {$cond} group by a.doctorid, a.the_month order by a.id";
        $doctorServiceOrders = Dao::loadEntityList4Page("DoctorServiceOrder", $sql, $pagesize, $pagenum, $bind);
        XContext::setValue("doctorServiceOrders", $doctorServiceOrders);

        //获得分页
        $countSql = "select count(*) from (select a.id
                        from doctorserviceorders a
                        inner join doctors b on b.id = a.doctorid
                        where 1 = 1 {$cond} group by a.doctorid, a.the_month order by a.id)tt";
        $cnt = Dao::queryValue($countSql, $bind);
        $url = "/doctorserviceordermgr/listmonth?doctorid={$doctorid}&the_month={$the_month}&is_menzhen={$is_menzhen}&is_sign={$is_sign}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);
        XContext::setValue("pagelink", $pagelink);

        XContext::setValue("doctorid", $doctorid);
        XContext::setValue("the_month", $the_month);
        XContext::setValue("is_menzhen", $is_menzhen);
        XContext::setValue("is_sign", $is_sign);
        if ($this->mydisease instanceof Disease) {
            $doctors = DoctorDao::getListByDiseaseid($this->mydisease->id);
        }
        XContext::setValue('doctors', $doctors);
        return self::SUCCESS;
    }

    // 列表
    public function doList() {
        $pagesize = XRequest::getValue("pagesize", 200);
        $pagenum = XRequest::getValue("pagenum", 1);

        $doctorid = XRequest::getValue('doctorid', 0);
        $doctor_name = XRequest::getValue('doctor_name', '');
        XContext::setValue('doctorid', $doctorid);
        XContext::setValue('doctor_name', $doctor_name);

        $the_month = XRequest::getValue("the_month", "");

        $cond = "";
        $bind = [];

        if($doctorid > 0){
            $cond .= " and doctorid = :doctorid ";
            $bind[":doctorid"] = $doctorid;
        }

        if($the_month){
            $the_month = substr($the_month, 0, 7) . "-01";
            $cond .= " and the_month = :the_month ";
            $bind[":the_month"] = $the_month;
        }
        //获得实体
        $sql = "select *
                    from doctorserviceorders
                    where 1 = 1 {$cond} order by id";
        $doctorServiceOrders = Dao::loadEntityList4Page("DoctorServiceOrder", $sql, $pagesize, $pagenum, $bind);
        XContext::setValue("doctorServiceOrders", $doctorServiceOrders);

        //获得分页
        $countSql = "select count(*)
                    from doctorserviceorders
                    where 1 = 1 {$cond} order by id";
        $cnt = Dao::queryValue($countSql, $bind);
        $url = "/doctorserviceordermgr/list?doctorid={$doctorid}&the_month={$the_month}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);
        XContext::setValue("pagelink", $pagelink);

        if($doctorid > 0){
            $doctor = Doctor::getById($doctorid);
            XContext::setValue("doctor", $doctor);
        }
        XContext::setValue("the_month", $the_month);


        return self::SUCCESS;
    }

    public function doGenDoctorWithdrawOrderPost(){
        $doctorid = XRequest::getValue("doctorid", 0);
        $the_month = XRequest::getValue("the_month", "");
        $doctor = Doctor::getById($doctorid);
        DBC::requireTrue($doctor instanceof Doctor, "医生不存在");
        $doctorServiceOrders = DoctorServiceOrderDao::getListOfNeedRechargeByDoctorThe_month($doctor, $the_month);
        $amount = 0;

        //从[医生收益基金] 转账到 [医生账户]
        //基于doctorServiceOrders
        foreach($doctorServiceOrders as $a){
            if($a->is_recharge > 0){
                continue;
            }
            $amount += $a->amount;
            $a->recharge();
        }
        DBC::requireTrue($amount > 0, "提现金额不能小于0");

        //从[医生账户] 转账到 [医生收益提现支出]
        //基于doctorWithdrawOrder
        $account = $doctor->getAccount();
        $doctorWithdrawOrder = OrderService::processDoctorWithdraw($account, $amount, $this->myauditor);

        $preMsg = "已生成提现单" . XDateTime::now();
        XContext::setJumpPath("/doctorServiceOrderMgr/listmonth?the_month=" . $the_month . "&preMsg=" . urlencode($preMsg));
        return self::SUCCESS;
    }

}
