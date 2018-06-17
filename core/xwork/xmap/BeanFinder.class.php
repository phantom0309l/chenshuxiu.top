<?php

/**
 * BeanFinder
 * @desc		实例工厂,实例注册表
 * @remark		依赖类: 多个框架类
 * @copyright 	(c)2012 xwork.
 * @file		BeanFinder.class.php
 * @author		shijianping <shijpcn@qq.com>
 * @date		2012-02-26
 */
class BeanFinder
{
    // 工厂
    private static $factorys = array();
    // 实例缓存
    private static $interfaceArray = array();

    // iswidget,UnitOfWork 需要在widget中重新开启一个新的
    private static $isWidget = false;

    public static function startWidget () {
        self::$interfaceArray["Widget_UnitOfWork"] = new UnitOfWork();
        self::$isWidget = true;
    }

    public static function stopWidget () {
        unset(self::$interfaceArray["Widget_UnitOfWork"]);
        self::$isWidget = false;
    }

    // 注册工厂
    public static function registerFactory ($factory) {
        self::$factorys[] = $factory;
    }
    // 注册实例
    public static function register ($interface, $object) {
        self::$interfaceArray[$interface] = $object;
    }
    // 清理工厂与实例缓存
    public static function clear () {
        self::$factorys = array();
        self::$interfaceArray = array();
    }
    // 判断工厂与实例缓存是否都为空，若是则返回真
    public static function isClear () {
        return empty(self::$factorys) && empty(self::$interfaceArray);
    }
    // 清除某种类型实例的缓存
    public static function clearBean ($typeOfBean) {
        self::$interfaceArray = array_diff_key(self::$interfaceArray, array(
            $typeOfBean => ""));
    }

    // 获得某种类型的实例，如 工作单元(UnitOfWork) 数据库执行器(DbExecuter) ID生成器(IDGenerator)
    // memcache缓存(XMemCached)
    public static function get ($typeOfBean, $fix = "") {

        // 拦截一下Widget
        if ($typeOfBean == "UnitOfWork" && self::$isWidget) {
            $typeOfBean = "Widget_UnitOfWork";
        }

        // 拦截DbExecuter,修正$fix
        if ($typeOfBean == "DbExecuter" && empty($fix)) {
            $fix = DaoBase::get_defaultdb_name();
        }

        $typeOfBeanFix = $typeOfBean . $fix;

        // 如果注册表里有则直接返回
        if (array_key_exists($typeOfBeanFix, self::$interfaceArray)) {
            return self::$interfaceArray[$typeOfBeanFix];
        }

        if ($typeOfBean == "UnitOfWork") {
            self::$interfaceArray["UnitOfWork"] = new UnitOfWork();
            return self::$interfaceArray["UnitOfWork"];
        }

        // 如有不同规则,请在具体项目继承和注册
        if ($typeOfBean == "TableNameCreator") {
            self::$interfaceArray["TableNameCreator"] = new TableNameCreator();
            return self::$interfaceArray["TableNameCreator"];
        }

        // 如有不同规则,请在具体项目继承和注册
        if ($typeOfBean == "DbMgr") {
            self::$interfaceArray["DbMgr"] = new DbMgr();
            return self::$interfaceArray["DbMgr"];
        }

        // 支持多个DbExecuter,但必须是固定的几个库,而不是动态的n多个库
        if ($typeOfBean == "DbExecuter") {
            self::InitDbExecuter($fix);
            return self::$interfaceArray[$typeOfBeanFix];
        }

        if ($typeOfBean == "IDGenerator") {
            self::$interfaceArray["IDGenerator"] = new IDGeneratorByDb();
            return self::$interfaceArray["IDGenerator"];
        }

        if ($typeOfBean == "XMemCached") {
            self::InitXMemCached();
            return self::$interfaceArray["XMemCached"];
        }

        foreach (self::$factorys as $factory) {
            $object = $factory->get($typeOfBean);
            if (isset($object))
                return $object;
        }

        throw new SystemException("class $typeOfBean not found!");
    }

    // TODO by sjp 2010-11-28:重新初始化数据库, 配置文件中的固定库
    public static function InitDbExecuter ($database = "") {
        $dbe = BeanFinder::get("DbMgr")->getDbexecuter($database);
        self::register("DbExecuter{$database}", $dbe);
    }

    // 初始化memcache
    private static function InitXMemCached () {
        if (Config::getConfig("cacheOpen", false)) {
            $str = "[-- cacheOpen = true --]";

            self::register("XMemCached", XMemCached::getOneInstance());
        } else {
            $str = "[-- cacheOpen = false --]";

            self::register("XMemCached", new NullMemCached());
        }

        Debug::addNotice($str);
        Debug::sys($str);
    }

    // 手工启动数据库链接
    public static function setupManual_Db ($host, $database, $username, $password) {
        if (self::isClear()) {
            $dataSource = new MysqlDataSource($host, $database, $username, $password);
            self::register("DbExecuter{$database}", new DbExecuter($dataSource, $database));
            self::register("IDGenerator", new IDGeneratorByDb());
        }
    }
}
