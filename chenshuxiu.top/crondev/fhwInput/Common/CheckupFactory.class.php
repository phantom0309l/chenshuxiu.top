<?php
include_once(dirname(__FILE__) . "/../Checkup/EDSS.class.php");
include_once(dirname(__FILE__) . "/../Checkup/Fazuoshi.class.php");
include_once(dirname(__FILE__) . "/../Checkup/Xuechanggui.class.php");
include_once(dirname(__FILE__) . "/../Checkup/Gangongneng.class.php");
include_once(dirname(__FILE__) . "/../Checkup/Shengongneng.class.php");
include_once(dirname(__FILE__) . "/../Checkup/ENA.class.php");
include_once(dirname(__FILE__) . "/../Checkup/Jiazhuangxian.class.php");
include_once(dirname(__FILE__) . "/../Checkup/Kanghekangti.class.php");
include_once(dirname(__FILE__) . "/../Checkup/Shuitongdao.class.php");
include_once(dirname(__FILE__) . "/../Checkup/Naojiye.class.php");
include_once(dirname(__FILE__) . "/../Checkup/Yingxiang.class.php");
include_once(dirname(__FILE__) . "/../Checkup/Zhenduan.class.php");
include_once(dirname(__FILE__) . "/../Checkup/Yizhu.class.php");
include_once(dirname(__FILE__) . "/../Checkup/Zhiliao.class.php");

class CheckupFactory
{
    public static function produceEDSS()
    {
        return new EDSS();
    }

    public static function produceFazuoshi()
    {
        return new Fazuoshi();
    }

    public static function produceXuechanggui()
    {
        return new Xuechanggui();
    }

    public static function produceGangongneng()
    {
        return new Gangongneng();
    }

    public static function produceShengongneng()
    {
        return new Shengongneng();
    }

    public static function produceENA()
    {
        return new ENA();
    }

    public static function produceJiazhuangxian()
    {
        return new Jiazhuangxian();
    }

    public static function produceKanghekangti()
    {
        return new Kanghekangti();
    }

    public static function produceShuitongdao()
    {
        return new Shuitongdao();
    }

    public static function produceNaojiye()
    {
        return new Naojiye();
    }

    public static function produceYingxiang()
    {
        return new Yingxiang();
    }

    public static function produceZhenduan()
    {
        return new Zhenduan();
    }

    public static function produceYizhu()
    {
        return new Yizhu();
    }

    public static function produceZhiliao(){
        return new Zhiliao();
    }
}
