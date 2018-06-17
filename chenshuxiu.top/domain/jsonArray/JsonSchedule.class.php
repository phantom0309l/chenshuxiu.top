<?php

class JsonSchedule
{
    // jsonArray
    public static function jsonArray (Schedule $schedule) {
        $arr = array();

        $arr["scheduleid"] = $schedule->id;
        $arr["scheduletplid"] = $schedule->scheduletplid;
        $arr["thedate"] = $schedule->thedate;
        $arr["daypart"] = $schedule->getDay_partValue();
        $arr["daypartstr"] = $schedule->getDaypartStr();
        $arr["tkttype"] = $schedule->tkttype;
        $arr["tkttypestr"] = $schedule->getTkttypeStr();
        $arr["maxcnt"] = $schedule->maxcnt;
        $arr["cnt"] = RevisitTktDao::getCntByScheduleidDoctorid($schedule->id, $schedule->doctorid);
        $arr["status"] = $schedule->status;

        return $arr;
    }

    // jsonArrayForDwx
    public static function jsonArrayForDwx (Schedule $schedule) {
        $cnt = RevisitTktDao::getCntByScheduleidDoctorid($schedule->id, $schedule->doctorid);
        $maxcnt = $schedule->maxcnt;
        $desc = "{$cnt} / {$maxcnt}";
        if ($cnt >= $maxcnt && $maxcnt != 0) {
            $desc .= " 已满";
        }

        $theday = date("d", strtotime($schedule->thedate)) . "日";
        $theday .= " " . XDateTime::get_chinese_weekdayImp(strtotime($schedule->thedate));
        if ($schedule->thedate == date("Y-m-d")) {
            $theday .= " 今日";
        }

        $arr = array();

        $arr["scheduleid"] = $schedule->id;
        $arr["scheduletplid"] = $schedule->scheduletplid;
        $arr["thedate"] = $schedule->thedate;
        $arr["theday"] = $theday;
        $arr["daypart"] = $schedule->getDay_partValue();
        $arr["daypartstr"] = $schedule->getDaypartStr();
        $arr["max_cnt"] = $maxcnt;
        $arr["cnt"] = $cnt;
        $arr["desc"] = $desc;
        $arr["status"] = $schedule->status;

        return $arr;
    }

    // jsonArrayForIpad
    public static function jsonArrayForIpad (Schedule $schedule) {
        $tmp = array();

        $tmp['scheduleid'] = $schedule->id;

        $thedatetime = strtotime($schedule->thedate);
        $tmp['year'] = date('Y', $thedatetime);
        $tmp['month'] = date('m', $thedatetime);
        $tmp['day'] = date('d', $thedatetime);

        $cnt = 0;
        $maxcnt = 0;

        if (1 == $schedule->status) {
            $cnt = RevisitTktDao::getCntByScheduleidDoctorid($schedule->id, $schedule->doctorid);
            $maxcnt = $schedule->maxcnt;
        }

        $tmp['cnt'] = $cnt;
        $tmp['maxcnt'] = $maxcnt;

        return $tmp;
    }
}
