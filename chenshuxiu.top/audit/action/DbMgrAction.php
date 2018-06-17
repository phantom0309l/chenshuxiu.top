<?php

class DbMgrAction extends AuditBaseAction
{

    // 菜单默认页
    public function doDefault () {
        return self::SUCCESS;
    }

    // 补充新增的AuditResource
    public function doFixAuditResources () {
        $includepath = ROOT_TOP_PATH . "/audit/action";

        echo '<pre>';

        $process = new Fix_auditresource($includepath);
        $process->dowork();

        $arr = $process->class_method_arr;

        $auditresources = Dao::getEntityListByCond('AuditResource');
        foreach ($auditresources as $a) {
            $str = strtolower("{$a->action}:{$a->method}");
            if (false == isset($arr[$str])) {
                echo "\n[{$a->id}] [{$a->createtime}] [{$a->action}/{$a->method}] [{$a->type}] [{$a->title}] ";
                echo "<a href='/auditresourcemgr/deletepost?auditresourceid={$a->id}' target='_blank'>delete</a>";
                // $a->remove();
            }

        }

        return self::blank;
    }

    // 数据完整性检查, 外键指向的实体is null
    public function doCheckXxidIsNull () {
        $dbfix = new Dbfix_check_xxid_isnull();
        $dbfix->initThreeArray();
        $table_arr = $dbfix->doCheckXxidIsNull();

        XContext::setValue('table_arr', $table_arr);
        return self::SUCCESS;
    }

    // 数据完整性检查, xxids, 某些值对应的Entity is null
    public function doCheckXxidsIsNull () {
        $dbfix = new Dbfix_check_xxid_isnull();
        $dbfix->initThreeArray();
        $result = $dbfix->doCheckXxidsIsNull();

        XContext::setValue('result', $result);
        return self::SUCCESS;
    }

    // 数据一致性完整性检查, objtype, objid
    public function doCheckObjtypeObjid () {
        $dbfix = new Dbfix_check_objtypeobjid_isnull();
        $result = $dbfix->doWork();

        XContext::setValue('result', $result);

        return self::SUCCESS;
    }

    // 数据一致性检查
    public function doCheckSqls () {

        $rows = array();
        $arr = array();
        $arr['title'] = "wxusers: 微信用户,没生成user";
        $arr['expectcnt'] = 0;
        $arr['sql'] = "select count(a.id) as cnt
from wxusers a
left join users b on a.userid=b.id
where b.id is null";
        $rows[] = $arr;

        $arr = array();
        $arr['title'] = "users: 用户, 非微信用户的数目";
        $arr['expectcnt'] = x;
        $arr['sql'] = "select count(a.id) as cnt
from users a
left join wxusers b on a.id=b.userid
where b.id is null";
        $rows[] = $arr;

        $arr = array();
        $arr['title'] = "users: 用户, 没有patient的数目(没报到)";
        $arr['expectcnt'] = x; // 564;
        $arr['sql'] = "select count(a.id) as cnt
from users a
left join patients b on a.patientid=b.id
where b.id is null";
        $rows[] = $arr;

        $arr = array();
        $arr['title'] = "users: 用户, patient被审核拒绝的数目";
        $arr['expectcnt'] = x;
        $arr['sql'] = "select count(a.id) as cnt
from users a
inner join patients b on a.patientid=b.id
where b.status=0 and b.auditstatus=2";
        $rows[] = $arr;

        $arr = array();
        $arr['title'] = "patients: 患者,已经解绑的patient的数目(合并)";
        $arr['expectcnt'] = x;
        $arr['sql'] = "select count(a.id) as cnt
from patients a
left join users b on a.id=b.patientid
where b.id is null";
        $rows[] = $arr;

        $arr = array();
        $arr['title'] = "xanswers: 答案, 没有答卷的数目";
        $arr['expectcnt'] = 0; // 442;
        $arr['sql'] = "select count(a.id) as cnt
from xanswers a
left join xanswersheets b on a.xanswersheetid=b.id
where b.id is null";
        $rows[] = $arr;

        $arr = array();
        $arr['title'] = "xanswersheets: 答卷, 没有答案的数目";
        $arr['expectcnt'] = 0; // 51
        $arr['sql'] = "select count(distinct a.id) as cnt
from xanswersheets a
left join xanswers b on b.xanswersheetid=a.id
where b.id is null";
        $rows[] = $arr;

        $arr = array();
        $arr['title'] = "xanswersheets: 答卷, patientid=0 的数目 (没报到?)";
        $arr['expectcnt'] = 0;
        $arr['sql'] = "select count(id) as cnt
from xanswersheets
where patientid=0";
        $rows[] = $arr;

        $arr = array();
        $arr['title'] = "xanswersheets: 答卷, userid=0 的数目";
        $arr['expectcnt'] = 0;
        $arr['sql'] = "select count(id) as cnt
from xanswersheets
where userid=0";
        $rows[] = $arr;

        $arr = array();
        $arr['title'] = "xanswersheets: 答卷, patientid=0 and userid=0 的数目";
        $arr['expectcnt'] = 0;
        $arr['sql'] = "select count(id) as cnt
from xanswersheets
where patientid=0 and userid=0";
        $rows[] = $arr;

        $arr = array();
        $arr['title'] = "scheduletpls: 出诊表, doctorid=0 的数目";
        $arr['expectcnt'] = 0;
        $arr['sql'] = "select count(id) as cnt
from scheduletpls
where doctorid=0";
        $rows[] = $arr;

        $arr = array();
        $arr['title'] = "patientnotes: 健康日记, patientid=0 的数目 (想法修)";
        $arr['expectcnt'] = 0;
        $arr['sql'] = "select count(id) as cnt
from patientnotes
where patientid=0";
        $rows[] = $arr;

        $arr = array();
        $arr['title'] = "patientnotes: 健康日记, userid=0 的数目";
        $arr['expectcnt'] = 0;
        $arr['sql'] = "select count(id) as cnt
from patientnotes
where userid=0";
        $rows[] = $arr;

        $arr = array();
        $arr['title'] = "pipes: 流, patientid=0 的数目 (想法修)";
        $arr['expectcnt'] = 0;
        $arr['sql'] = "select count(id) as cnt
from pipes
where patientid=0";
        $rows[] = $arr;

        $arr = array();
        $arr['title'] = "pipes: 流, userid=0 的数目";
        $arr['expectcnt'] = 0;
        $arr['sql'] = "select count(id) as cnt
from pipes
where userid=0";
        $rows[] = $arr;

        $arr = array();
        $arr['title'] = "pipes: 流, userid=0 and patientid=0 的数目(垃圾数据?)";
        $arr['expectcnt'] = 0;
        $arr['sql'] = "select count(id) as cnt
from pipes
where userid=0 and patientid=0";
        $rows[] = $arr;

        $arr = array();
        $arr['title'] = "pipes: 流, userid>0 and patientid=0 的数目";
        $arr['expectcnt'] = 0;
        $arr['sql'] = "select count(id) as cnt
from pipes
where userid>0 and patientid=0";
        $rows[] = $arr;

        $arr = array();
        $arr['title'] = "pipes: 流, userid=0 and patientid>0 的数目";
        $arr['expectcnt'] = 0;
        $arr['sql'] = "select count(id) as cnt
from pipes
where userid=0 and patientid>0";
        $rows[] = $arr;

        $arr = array();
        $arr['title'] = "pushmsgs: 推送消息, userid=0 的数目";
        $arr['expectcnt'] = 0;
        $arr['sql'] = "select count(id) as cnt
from pushmsgs
where userid=0";
        $rows[] = $arr;

        $arr = array();
        $arr['title'] = "pushmsgs: 推送消息, patientid=0 的数目";
        $arr['expectcnt'] = 0;
        $arr['sql'] = "select count(id) as cnt
from pushmsgs
where patientid=0";
        $rows[] = $arr;

        $arr = array();
        $arr['title'] = "pushmsgs: 推送消息, userid=0 and patientid=0 的数目(垃圾数据?)";
        $arr['expectcnt'] = 0;
        $arr['sql'] = "select count(id) as cnt
from pushmsgs
where userid=0 and patientid=0";
        $rows[] = $arr;

        $arr = array();
        $arr['title'] = "pushmsgs: 推送消息, userid>0 and patientid=0 的数目";
        $arr['expectcnt'] = 0;
        $arr['sql'] = "select count(id) as cnt
from pushmsgs
where userid>0 and patientid=0";
        $rows[] = $arr;

        $arr = array();
        $arr['title'] = "pushmsgs: 推送消息, userid=0 and patientid>0 的数目";
        $arr['expectcnt'] = 0;
        $arr['sql'] = "select count(id) as cnt
from pushmsgs
where userid=0 and patientid>0";
        $rows[] = $arr;

        $arr = array();
        $arr['title'] = "wxpicmsgs: 图片消息, patientid=0 的数目 (没有报到?)";
        $arr['expectcnt'] = 0;
        $arr['sql'] = "select count(id) as cnt
from wxpicmsgs
where patientid=0";
        $rows[] = $arr;

        $arr = array();
        $arr['title'] = "wxpicmsgs: 图片消息, userid=0 的数目";
        $arr['expectcnt'] = 0;
        $arr['sql'] = "select count(id) as cnt
from wxpicmsgs
where userid=0";
        $rows[] = $arr;

        $arr = array();
        $arr['title'] = "wxpicmsgs: 图片消息, userid=0 and patientid=0 的数目(垃圾数据?)";
        $arr['expectcnt'] = 0;
        $arr['sql'] = "select count(id) as cnt
from wxpicmsgs
where userid=0 and patientid=0";
        $rows[] = $arr;

        $arr = array();
        $arr['title'] = "wxtxtmsgs: 文本消息, patientid=0 的数目 (没有报到?)";
        $arr['expectcnt'] = 0;
        $arr['sql'] = "select count(id) as cnt
from wxtxtmsgs
where patientid=0";
        $rows[] = $arr;

        $arr = array();
        $arr['title'] = "wxtxtmsgs: 文本消息, userid=0 的数目";
        $arr['expectcnt'] = 0;
        $arr['sql'] = "select count(id) as cnt
from wxtxtmsgs
where userid=0";
        $rows[] = $arr;

        $arr = array();
        $arr['title'] = "wxtxtmsgs: 文本消息, userid=0 and patientid=0 的数目(垃圾数据?)";
        $arr['expectcnt'] = 0;
        $arr['sql'] = "select count(id) as cnt
from wxtxtmsgs
where userid=0 and patientid=0";
        $rows[] = $arr;

        // 循环执行sql
        foreach ($rows as $i => $row) {
            $sql = $row['sql'];
            $row['cnt'] = Dao::queryValue($sql, []);
            $rows[$i] = $row;
        }

        XContext::setValue("rows", $rows);

        return self::SUCCESS;
    }
}
