<?php

class ChufangOplogService
{
    //处方系统操作日志
    public static function log($prescriptionid, Yishi $yishi, $content) {
        $row = array();
        $row['prescriptionid'] = $prescriptionid;
        $row['yishiid'] = $yishi->id;
        $row['yishi_name'] = $yishi->name;
        $row['content'] = $content;

        $chufangOplog = ChufangOplog::createByBiz($row);
        return $chufangOplog;
    }
}
