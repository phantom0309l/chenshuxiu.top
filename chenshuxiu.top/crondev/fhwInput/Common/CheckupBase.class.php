<?php
interface CheckupBase
{
    //初始化数据
    public function init(array $a);

    //构造sheets
    public function createSheets(array $a);

    //获取patient和user
    public function setPatientAndMyselfUser();

    //创建checkup
    public function createCheckup();

    //创建revisitrecord
    public function createRevisitRecord();

    //创建XanserSheet
    public function createXanswerSheet();
}
