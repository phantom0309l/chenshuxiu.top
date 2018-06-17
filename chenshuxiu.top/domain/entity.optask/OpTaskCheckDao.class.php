<?php

/*
 * OpTaskCheckDao
 */

class OpTaskCheckDao extends Dao
{
    //
    public static function getListByAuditorIdAndTheDate ($auditor_id, $theDate) {
        $cond = ' AND auditor_id=:auditor_id AND thedate=:theDate';
        $bind = [];
        $bind[':auditor_id'] = $auditor_id;
        $bind[':theDate'] = $theDate;

        return Dao::getEntityListByCond('OpTaskCheck', $cond, $bind);
    }

    public static function getListByAuditorIdAndTimeSlot ($auditor_id, $startTime, $endTime) {
        $cond = ' AND auditor_id=:auditor_id AND (createtime BETWEEN :startTime AND :endTime)';
        $bind = [];
        $bind[':auditor_id'] = $auditor_id;
        $bind[':startTime'] = $startTime;
        $bind[':endTime'] = $endTime;

        return Dao::getEntityListByCond('OpTaskCheck', $cond, $bind);
    }

    public static function getListByAuditorIdAndTheDateSlot ($auditor_id, $startTime, $endTime) {
        $cond = ' AND auditor_id=:auditor_id AND (thedate BETWEEN :startTime AND :endTime)';
        $bind = [];
        $bind[':auditor_id'] = $auditor_id;
        $bind[':startTime'] = $startTime;
        $bind[':endTime'] = $endTime;

        return Dao::getEntityListByCond('OpTaskCheck', $cond, $bind);
    }

    public static function getList () {
        return Dao::getEntityListByCond('OpTaskCheck');
    }

    public static function getFirstByAuditorAndTimeSlot  ($auditor_id, $startTime, $endTime) {
        $cond = 'AND auditor_id=:auditor_id 
                 AND thedate BETWEEN :startTime AND :endTime
                 ORDER BY createtime DESC LIMIT 1
                 ';
        $bind = [];
        $bind[':auditor_id'] = $auditor_id;
        $bind[':startTime'] = $startTime;
        $bind[':endTime'] = $endTime;

        return Dao::getEntityByCond('OpTaskCheck', $cond, $bind);
    }


    // 获取运营在指定时间段内 xquestion 被评测为 option 的 optaskcheck 数量
    public static function getCntByAuditorAndTimeSlotAndQuestionAndOption ($auditorid, $startTime, $endTime, $xquestionid, $xoptionid) {
        $sql = "
            SELECT count(*) as value,f.content as name FROM optaskchecks a
              LEFT JOIN optaskchecktpls g ON a.optaskchecktplid=g.id
              LEFT JOIN xanswersheets b ON a.xanswersheetid = b.id
              LEFT JOIN xanswers c ON c.xanswersheetid = a.xanswersheetid
              LEFT JOIN xansweroptionrefs d ON c.id = d.xanswerid
              LEFT JOIN xquestionsheets e ON b.xquestionsheetid = e.id
              LEFT JOIN xquestions f ON f.xquestionsheetid = g.xquestionsheetid
            WHERE
              a.auditor_id = :auditorid
              AND (a.thedate BETWEEN :startTime AND :endTime)
              AND a.status = 1 AND f.id = :xquestionid
              AND d.xoptionid = :xoptionid
        ";

        $bind = [];
        $bind[':auditorid'] = $auditorid;
        $bind[':startTime'] = $startTime;
        $bind[':endTime'] = $endTime;
        $bind[':xquestionid'] = $xquestionid;
        $bind[':xoptionid'] = $xoptionid;

        $result = Dao::queryRow($sql,$bind);
        return $result;
    }
}