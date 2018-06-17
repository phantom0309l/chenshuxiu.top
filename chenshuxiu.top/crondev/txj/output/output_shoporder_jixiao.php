<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once (dirname(__FILE__) . "/../../../sys/PathDefine.php");
include_once (ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

// Debug::$debug = 'Dev';

class Output_shoporder_jixiao
{

    public function dowork () {

        $auditor_arr = [10025, 10032, 10042, 10036, 10027, 10055];
        foreach($auditor_arr as $auditorid){
        $unitofwork = BeanFinder::get("UnitOfWork");
        $auditor = Auditor::getById($auditorid);

        echo "======市场[{$auditor->name}]开始导出======\n";

        $date_arr = [
            ["2017-10-02", "2017-10-08"],
            ["2017-10-09", "2017-10-15"],
            ["2017-10-16", "2017-10-22"],
            ["2017-10-23", "2017-10-29"],
            ["2017-10-30", "2017-11-05"],
            ["2017-11-06", "2017-11-12"],
            ["2017-11-13", "2017-11-19"],
            ["2017-11-20", "2017-11-26"],
            ["2017-11-27", "2017-12-03"],
            ["2017-12-04", "2017-12-10"],
            ["2017-12-11", "2017-12-17"],
            ["2017-12-18", "2017-12-24"],
            ["2017-12-25", "2017-12-31"],
        ];

        //以周的维度汇总
        //单数
        $w_cnt_0 = 0;
        $w_cnt_1 = 0;
        $w_cnt_2 = 0;
        $w_cnt_3 = 0;
        $w_cnt_4 = 0;
        $w_cnt_5 = 0;
        $w_cnt_6 = 0;
        $w_cnt_7 = 0;
        $w_cnt_8 = 0;
        $w_cnt_9 = 0;
        $w_cnt_10 = 0;
        $w_cnt_11 = 0;
        $w_cnt_12 = 0;

        //首单
        $w_firstcnt_0 = 0;
        $w_firstcnt_1 = 0;
        $w_firstcnt_2 = 0;
        $w_firstcnt_3 = 0;
        $w_firstcnt_4 = 0;
        $w_firstcnt_5 = 0;
        $w_firstcnt_6 = 0;
        $w_firstcnt_7 = 0;
        $w_firstcnt_8 = 0;
        $w_firstcnt_9 = 0;
        $w_firstcnt_10 = 0;
        $w_firstcnt_11 = 0;
        $w_firstcnt_12 = 0;

        //金额
        $w_amount_0 = 0;
        $w_amount_1 = 0;
        $w_amount_2 = 0;
        $w_amount_3 = 0;
        $w_amount_4 = 0;
        $w_amount_5 = 0;
        $w_amount_6 = 0;
        $w_amount_7 = 0;
        $w_amount_8 = 0;
        $w_amount_9 = 0;
        $w_amount_10 = 0;
        $w_amount_11 = 0;
        $w_amount_12 = 0;

        $sql = "select id from doctors where auditorid_market = :auditorid_market";
        $bind = array();
        $bind[":auditorid_market"] = $auditorid;
        $ids = Dao::queryValues($sql, $bind);
        $i = 0;
        //要合并的行
        $needMergeRowIndexArr = array();
        $data = array();
        $row_index = 2;

        //编号
        $num = 0;

        foreach ($ids as $id) {
            $i ++;
            if ($i > 50) {
                $i = 0;
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
            }
            $needMergeRowIndexArr[] = array($row_index, $row_index + 2);
            $row_index = $row_index + 3;
            echo "[{$id}]\n";
            $doctor = Doctor::getById($id);
            $index = 1;
            $name = $doctor->name;
            $menzhen_pass_date = $doctor->menzhen_pass_date;
            $cnt_arr = array($index, $name, $menzhen_pass_date, "单数");
            $cnt_all = 0;

            $first_cnt_arr = array($index, $name, $menzhen_pass_date, "首单");
            $first_cnt_all = 0;

            $amount_arr = array($index, $name, $menzhen_pass_date, "金额");
            $amount_all = 0;
            foreach($date_arr as $n => $item){
                //单数
                $cnt = $this->getShopOrderCnt($doctor, $item);
                $cnt_arr[] = $cnt;
                $cnt_all += $cnt;
                $w_cnt = "w_cnt_{$n}";
                $$w_cnt += $cnt;

                //首单
                $first_cnt = $this->getShopOrderFirstCnt($doctor, $item);
                $first_cnt_arr[] = $first_cnt;
                $first_cnt_all += $first_cnt;
                $w_firstcnt = "w_firstcnt_{$n}";
                $$w_firstcnt += $first_cnt;

                //金额
                $amount = $this->getShopOrderAmount($doctor, $item);
                $amount_yuan = sprintf("%.2f", $amount / 100);
                $amount_arr[] = $amount_yuan;
                $amount_all += $amount;
                $w_amount = "w_amount_{$n}";
                $$w_amount += $amount_yuan;
            }
            //为0的数据不显示
            if($amount_all <= 0){
                continue;
            }
            $num++;
            $cnt_arr[] = $cnt_all;
            $first_cnt_arr[] = $first_cnt_all;
            $amount_all_yuan = sprintf("%.2f", $amount_all / 100);
            $amount_arr[] = $amount_all_yuan;

            //因为有跳过的医生，修改编号
            $cnt_arr[0] = $num;
            $first_cnt_arr[0] = $num;
            $amount_arr[0] = $num;

            $data[] = $cnt_arr;
            $data[] = $first_cnt_arr;
            $data[] = $amount_arr;
        }

        //追加最后的总计
        $w_cnt_all_last = $w_cnt_0 + $w_cnt_1 + $w_cnt_2 + $w_cnt_3 + $w_cnt_4 + $w_cnt_5 + $w_cnt_6 + $w_cnt_7 + $w_cnt_8 + $w_cnt_9 + $w_cnt_10 + $w_cnt_11 + $w_cnt_12;
        $w_cnt_all = array($num+1, "总计", "", "单数", $w_cnt_0, $w_cnt_1, $w_cnt_2, $w_cnt_3, $w_cnt_4, $w_cnt_5, $w_cnt_6, $w_cnt_7, $w_cnt_8, $w_cnt_9, $w_cnt_10, $w_cnt_11, $w_cnt_12, $w_cnt_all_last);

        $w_firstcnt_all_last = $w_firstcnt_0 + $w_firstcnt_1 + $w_firstcnt_2 + $w_firstcnt_3 + $w_firstcnt_4 + $w_firstcnt_5 + $w_firstcnt_6 + $w_firstcnt_7 + $w_firstcnt_8 + $w_firstcnt_9 + $w_firstcnt_10 + $w_firstcnt_11 + $w_firstcnt_12;
        $w_firstcnt_all = array($num+1, "总计", "", "首单", $w_firstcnt_0, $w_firstcnt_1, $w_firstcnt_2, $w_firstcnt_3, $w_firstcnt_4, $w_firstcnt_5, $w_firstcnt_6, $w_firstcnt_7, $w_firstcnt_8, $w_firstcnt_9, $w_firstcnt_10, $w_firstcnt_11, $w_firstcnt_12, $w_firstcnt_all_last);

        $w_amount_all_last = $w_amount_0 + $w_amount_1 + $w_amount_2 + $w_amount_3 + $w_amount_4 + $w_amount_5 + $w_amount_6 + $w_amount_7 + $w_amount_8 + $w_amount_9 + $w_amount_10 + $w_amount_11 + $w_amount_12;
        $w_amount_all = array($num+1, "总计", "", "金额", $w_amount_0, $w_amount_1, $w_amount_2, $w_amount_3, $w_amount_4, $w_amount_5, $w_amount_6, $w_amount_7, $w_amount_8, $w_amount_9, $w_amount_10, $w_amount_11, $w_amount_12, $w_amount_all_last);

        $data[] = $w_cnt_all;
        $data[] = $w_firstcnt_all;
        $data[] = $w_amount_all;

        $needMergeRowIndexArr[] = array($row_index, $row_index + 2);

        //要合并的列
        $needMergeColIndexArr = array(0,1,2);

        $headarr = array(
            "编号",
            "医生",
            "开通时间",
            "类别",
            "10/2-10/8",
            "10/9-10/15",
            "10/16-10/22",
            "10/23-10/29",
            "10/30-11/5",
            "11/6-11/12",
            "11/13-11/19",
            "11/20-11/26",
            "11/27-12/3",
            "12/4-12/10",
            "12/11-12/17",
            "12/18-12/24",
            "12/25-12/31",
            "总计",
        );
        //ExcelUtil::createHasMergeCellsForWeb($data, $headarr, $needMergeRowIndexArr, $needMergeColIndexArr);
        ExcelUtil::createHasMergeCellsForCron($data, $headarr, "/home/taoxiaojin/scale/shoporder/output_shoporder_jixiao_{$auditor->name}.xlsx", $needMergeRowIndexArr, $needMergeColIndexArr);
        $unitofwork->commitAndInit();
        }
    }

    private function getShopOrderCnt($doctor, $item){
        $the_doctorid = $doctor->id;
        $start_date = $item[0];
        $end_date = $item[1];
        $end_date = date("Y-m-d", strtotime($item[1])+86400*1);

        $sql = "select count(id) as id
                    from shoporders
                    where the_doctorid=:the_doctorid and time_pay > :start_date and time_pay < :end_date
                    and is_pay=1 and type = 'chufang' and (amount - refund_amount > 1000)";
        $bind = array();
        $bind[":the_doctorid"] = $the_doctorid;
        $bind[":start_date"] = $start_date;
        $bind[":end_date"] = $end_date;
        $cnt = 0 + Dao::queryValue($sql, $bind);
        return $cnt;
    }

    private function getShopOrderFirstCnt($doctor, $item){
        $the_doctorid = $doctor->id;
        $start_date = $item[0];
        $end_date = $item[1];
        $end_date = date("Y-m-d", strtotime($item[1])+86400*1);

        $sql = "select count(tt1.id) from (
                    select * from (
                        select * from shoporders where is_pay=1 and type = 'chufang' order by time_pay asc
                    )tt group by patientid
                )tt1 where tt1.the_doctorid=:the_doctorid and tt1.time_pay > :start_date and tt1.time_pay < :end_date
                and (tt1.amount - tt1.refund_amount > 1000)";
        $bind = array();
        $bind[":the_doctorid"] = $the_doctorid;
        $bind[":start_date"] = $start_date;
        $bind[":end_date"] = $end_date;
        $cnt = 0 + Dao::queryValue($sql, $bind);
        return $cnt;
    }

    private function getShopOrderAmount($doctor, $item){
        $the_doctorid = $doctor->id;
        $start_date = $item[0];
        $end_date = $item[1];
        $end_date = date("Y-m-d", strtotime($item[1])+86400*1);

        $sql = "select sum(amount - refund_amount)
                    from shoporders
                    where the_doctorid=:the_doctorid and time_pay > :start_date and time_pay < :end_date
                    and is_pay=1 and type = 'chufang' and (amount - refund_amount > 1000)";
        $bind = array();
        $bind[":the_doctorid"] = $the_doctorid;
        $bind[":start_date"] = $start_date;
        $bind[":end_date"] = $end_date;
        $amount = 0 + Dao::queryValue($sql, $bind);
        return $amount;
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Output_shoporder_jixiao.php]=====");

$process = new Output_shoporder_jixiao();
$process->dowork();

Debug::trace("=====[cron][end][Output_shoporder_jixiao.php]=====");
//Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
