<?php

/**
 * DaoBase
 * @desc		 数据库访问对象基类
 * @remark		 依赖类: Entity , XEntity , Debug , BeanFinder , DBC , Config , DbExecuter
 * @copyright 	(c)2012 xwork.
 * @file		 DaoBase.class.php
 * @author 		shijianping <shijpcn@qq.com>
 * @date		 2012-02-26
 */
class DaoBase
{

    // 默认数据库名称
    private static $defaultdb_name = '';

    // 初始化,给外部修改的接口
    public static function init_defaultdb_name ($database = '') {
        self::$defaultdb_name = $database;
    }

    // 获取默认数据库
    public static function get_defaultdb_name () {
        return self::$defaultdb_name;
    }

    // 实体类名称，如Board, User
    protected $entityClassName;

    // 数据库名称,如果为空则读中心库(缺省库)
    protected $database;

    // 散列表的编号
    protected $tableno = 0;

    protected $pkeyName = 'id';

    // 缓存,提高性能
    protected $_dbExecuter;

    /*
     * 构造函数，传递实体名与散列表编号（默认为0） 现在可以支持多数据库了 1. 缺省情况,读写缺省库 2.
     * 如果一个实体存在一个固定的其他库里,则可以通过继承Dao来实现,或传参数 3. 如果是库散列的情况,则需要创建Dao时动态传$database参数
     */
    public function __construct ($entityClassName, $dbconf = array()) {
        $tableno = 0;
        $database = '';
        $pkeyName = 'id';

        // 向前兼容,这个参数曾经名称为 $tableno
        if (is_numeric($dbconf) && $dbconf) {
            $tableno = $dbconf; // 数字 : 表暂列后缀
        } elseif (is_string($dbconf) && $dbconf) {
            $database = $dbconf; // 字符串 : 库名
        } elseif (is_array($dbconf)) {
            if (isset($dbconf['tableno']) && $dbconf['tableno']) {
                $tableno = $dbconf['tableno'];
            }
            if (isset($dbconf['database']) && $dbconf['database']) {
                $database = $dbconf['database'];
            }
            if (isset($dbconf['pkeyName']) && $dbconf['pkeyName']) {
                $pkeyName = $dbconf['pkeyName'];
            }
            if (isset($dbconf['pkeyname']) && $dbconf['pkeyname']) {
                $pkeyName = $dbconf['pkeyname'];
            }
        }

        // 修正 $database
        $database = $database ? $database : DaoBase::get_defaultdb_name();

        $this->entityClassName = $entityClassName;
        $this->tableno = $tableno;
        $this->database = $database;
        $this->pkeyName = $pkeyName;

        $this->_dbExecuter = BeanFinder::get("DbExecuter", $database);
    }

    // 获得工作单元实例
    protected function getUnitOfWork () {
        return BeanFinder::get("UnitOfWork");
    }

    // 获取表名
    public function getTableName () {
        return self::getTableNameImp($this->entityClassName, $this->tableno, $this->database);
    }

    // 获取表名
    public static function getTableNameImp ($entityClassName, $tableno = 0, $database = '') {
        $tableNameCreator = BeanFinder::get("TableNameCreator");
        return $tableNameCreator->getTableName($entityClassName, $tableno, $database);
    }

    // 自举，配置了cache
    public final function getById ($id, $needDBC = true) {
        $logkey = "{$this->database},{$this->entityClassName},{$id},{$needDBC}";

        Debug::xworkdev("DaoBase::getById [$logkey][0]");

        // null, 暂时容忍的, 可以延迟修复
        if (is_null($id)) {
            Debug::warn("[-- {$this->entityClassName}::getById [{$id}] is_null --]");
            return null;
        }

        // 空字符串, 暂时容忍的, 可以延迟修复
        if ($id === '') {
            Debug::warn("[-- {$this->entityClassName}::getById [{$id}] is empty str --]");
            return null;
        }

        // 非数字的情况, 如 'abc', array 等, 需要立刻修复
        if ($needDBC) {
            DBC::requireTrue(is_numeric($id), "[-- {$this->entityClassName}::getById [{$id}] not is_numeric --]");
        }

        // 0, 正常情况
        if ($id < 1) {
            return null;
        }

        // 从缓存里取一下试试
        $unitOfWork = $this->getUnitOfWork();
        $entity = $unitOfWork->getEntity($id, $this->entityClassName, $this->database);
        if ($entity instanceof EntityBase) {
            Debug::xworkdev("DaoBase::getById [$logkey][0-0]");
            return $entity;
        }

        $ids = array();

        // 在不散列的情况下，进行ids合并
        if ($this->tableno < 1) {
            $ids = $unitOfWork->getMaybeIds($this->entityClassName, $this->database);
            $unitOfWork->unsetMaybeIds($this->entityClassName, $this->database);
        }

        Debug::xworkdev("DaoBase::getById [$logkey][1]{" . implode(',', $ids) . "}");

        // 不散列或者只有一个id需求时
        if (empty($ids)) {
            Debug::xworkdev("DaoBase::getById [$logkey][1-0]");
            $tableName = $this->getTableName();
            $sql = "select * from {$tableName} where {$this->pkeyName}=:id ";
            $entity = $this->loadBySimpleType($sql, array(
                ":id" => $id));

            if (empty($entity)) {
                // TODO 应该是 warn
                Debug::sys("DaoBase::getById[$logkey][fail][1]");
            }

            return $entity;
        }

        // ids合并
        $ids[$id] = $id;

        Debug::xworkdev("DaoBase::getById [$logkey][2]{" . implode(',', $ids) . "}");

        // 合并后,如果还是只有一个id
        if (count($ids) == 1) {
            Debug::xworkdev("DaoBase::getById [$logkey][2-0]");
            $tableName = $this->getTableName();
            $sql = "select * from {$tableName} where {$this->pkeyName}=:id ";
            $entity = $this->loadBySimpleType($sql, array(
                ":id" => $id));

            if (empty($entity)) {
                // TODO 应该是 warn
                Debug::sys("DaoBase::getById[$logkey][fail][2]");
            }

            return $entity;
        }

        // maybeIds 加载
        $entitys = $this->getArrayByIds($ids);

        Debug::xworkdev("DaoBase::getById [$logkey][3] getArrayByIds => cnt=" . count($entitys));

        // 前面的逻辑必须保证实体已经被放进了loadMap
        $entity = $unitOfWork->getEntity($id, $this->entityClassName, $this->database);

        if (empty($entity)) {
            // TODO 应该是 warn
            Debug::sys("DaoBase::getById[$logkey][fail][3]");
        }

        return $entity;
    }

    // 通过id数组获得实体数组
    public final function getArrayByIds ($ids) {
        if (empty($ids)) {
            return array();
        }

        $inIds = array();

        // 先从缓存里取
        $unitOfWork = $this->getUnitOfWork();
        foreach ($ids as $id) {
            if (false == is_numeric($id)) {
                continue;
            }

            $entity = $unitOfWork->getEntity($id, $this->entityClassName, $this->database);
            if (empty($entity)) {
                $inIds[] = $id;
            }
        }

        Debug::xworkdev("DaoBase::getArrayByIds [{$this->entityClassName}][" . implode(',', $ids) . "]");

        if (count($inIds) > 0) {
            $cond = "and `{$this->pkeyName}` in(";
            $cond .= implode(",", $inIds);
            $cond .= ")";

            $tableName = $this->getTableName();
            $sql = "select * from {$tableName} where 1=1 {$cond}";
            $entitys = $this->loadArrayBySimpleType($sql); // 仅查询未缓存的实体
        }

        $entitys = $this->combinArray($ids); // 通过combinArray可以获得按照id排序的实体

        return $entitys;
    }

    // 通过id数组获得实体数组,应该在所有需查实体都缓存在工作单元时使用
    protected function combinArray ($ids) {
        $entitys = array();
        foreach ($ids as $id) {
            $entity = $this->getById($id, false);
            if (! empty($entity)) {
                $entitys[] = $entity;
            }
        }

        return $entitys;
    }

    // 通过条件查询单个实体
    public final function getByCond ($cond, $bind = array()) {
        $tableName = $this->getTableName();
        $sql = "select * from {$tableName} where 1=1 {$cond}";
        return $this->load($sql, $bind);
    }

    // 通过条件查询实体数组
    public final function getArrayByCond ($cond = "", $bind = array()) {
        $tableName = $this->getTableName();
        $sql = "select * from {$tableName} where 1=1 {$cond}";
        return $this->loadArray($sql, $bind);
    }

    // 通过条件查询实体数组，带翻页接口
    public final function getArrayByCond4Page ($cond, $pagesize, $pagenum, $bind = array()) {
        DBC::requireTrue(is_numeric($pagesize), "$pagesize not is number");
        DBC::requireTrue(is_numeric($pagenum), "$pagenum not is number");
        $tableName = $this->getTableName();
        $sql = "select * from {$tableName} where 1=1 {$cond} ";
        return $this->loadArray4Page($sql, $pagesize, $pagenum, $bind);
    }

    // 获得计数sql语句的前半部分
    public final function getCountSqlOfCond ($cond) {
        $tableName = $this->getTableName();
        return "select count(*) from {$tableName} where 1=1 {$cond} ";
    }

    // 查询单个实体,如果需要缓冲sql结果,通过完整的sql语句,先查id，再加载
    public final function load ($sql, $bind = array()) {
        if (Config::getConfig("cacheOpen", false) && Config::getConfig("idListCacheOpen")) {
            $id = DaoBase::queryValueWithCache($sql, $bind);
            if (empty($id)) {
                return null;
            }
            return $this->getById($id);
        } else {
            return $this->loadBySimpleType($sql, $bind);
        }
    }

    // 查询单个实体,通过完整的sql语句,简单的方式,不检查loadMap2，不检查cache,承担了getById的出口
    public final function loadBySimpleType ($sql, $bind = array()) {
        // 修正sql 补 limit 1
        $str1 = stristr($sql, "limit");
        $str2 = stristr($sql, ";");
        if (empty($str1) && empty($str2)) {
            $sql .= " limit 1 ";
        }

        $rs = $this->_dbExecuter->query($sql, $bind);
        if (empty($rs)) {
            return null;
        }

        return $this->row2Object($rs[0]);
    }

    // 查询实体数组,如果需要memcache，则通过完整的sql语句,先查ids,然后再用 in 加载
    public final function loadArray ($sql, $bind = array()) {
        if (Config::getConfig("cacheOpen", false) && Config::getConfig("idListCacheOpen")) {
            $ids = DaoBase::queryValuesWithCache($sql, $bind, $expireTime = 0, $this->database);
            return $this->getArrayByIds($ids);
        } else {
            return $this->loadArrayBySimpleType($sql, $bind);
        }
    }

    // 查询实体数组,通过完整的sql语句,简单的方式,不检查loadMap2，不检查cache,承担了getArrayByIds的出口
    public final function loadArrayBySimpleType ($sql, $bind = array()) {
        $arrayEntities = array();
        $rs = $this->_dbExecuter->query($sql, $bind);
        foreach ($rs as $row) {
            $arrayEntities[] = $this->row2Object($row);
        }
        return $arrayEntities;
    }

    // 通过完整的sql语句查询实体数组, 带翻页功能
    public final function loadArray4Page ($sql, $pagesize, $pagenum, $bind = array()) {
        $offset = ($pagenum - 1) * $pagesize;
        $sql = DbExecuter::limit($sql, $offset, $pagesize);
        return $this->loadArray($sql, $bind);
    }

    // 实体转换,可以继承
    protected function row2Object ($row) {
        $row = array_change_key_case($row, CASE_LOWER);

        $id = $row[$this->pkeyName];

        // 必须检查工作单元里是否有了，避免clone体的出现,这个是个核心的思想
        $unitOfWork = $this->getUnitOfWork();
        $entity = $unitOfWork->getEntity($id, $this->entityClassName, $this->database);
        if ($entity instanceof EntityBase) {
            return $entity;
        }

        // fix bad data
        foreach ($row as $k => $v) {
            if (is_null($v)) {
                $row[$k] = "";
            }

            if (is_string($v)) {
                $v = str_ireplace("\'", '’', $v);
                $v = str_ireplace("'", '’', $v);
            }
        }

        $dbconf = array();
        $dbconf['tableno'] = $this->tableno;
        $dbconf['database'] = $this->database;

        $entity = new $this->entityClassName($row, 0, false, $dbconf);

        return $entity;
    }

    // /////////////////////////////////////////////////////////////
    // 共用方法
    // 生成insert命令 , Entity or XEntity
    public static function getInsertCommand (EntityBase $entity) {
        $tableName = self::getTableNameImp($entity->getClassName(), $entity->tableno, $entity->getDatabaseName());
        $row = $entity->toArray();

        $columns = array();
        $bindColumns = array();
        $bindValues = array();
        foreach ($row as $column => $value) {
            // 单引号替换为中文引号
            if (is_string($value)) {
                $value = str_ireplace("\'", '’', $value);
                $value = str_ireplace("'", '’', $value);
            }

            $columns[] = "`{$column}`";
            $bindColumns[] = ":" . $column;
            $bindValues[":" . $column] = $value;
        }

        $columns = implode(",", $columns);
        $bindColumns = implode(",", $bindColumns);

        $sqls[] = array(
            'database' => $entity->getDatabaseName(),
            'sql' => "insert into {$tableName} ({$columns}) values ({$bindColumns})",
            'param' => $bindValues);

        // 修补,如果不能保证肯定能更新数据请不要覆写此函数
        $sqlsFix = $entity->getInsertSqlsFix();

        // 并集
        $sqls = array_merge($sqls, $sqlsFix);

        // 框架, XObjLog
        $sqlsFix2 = self::tryXObjLogInsertSqls($entity, $entity->get_keys());
        $sqls = array_merge($sqls, $sqlsFix2);

        return $sqls;
    }

    // 记录XObjLog, 需要建表xworkdb.xobjlogs, 需要的Entity重载 notXObjLog
    protected static function tryXObjLogInsertSqls (EntityBase $entity, $dirtyKeys = array()) {
        $sqls = array();

        // 关闭本请求的 xunitofwork 记录
        if (Debug::$xunitofwork_create_close) {
            return $sqls;
        }

        // xworkdbOpen 开启
        if (false == Config::getConfig("xworkdbOpen", false)) {
            return $sqls;
        }

        // 实体是否接受XObjLog, XObjLog 自己返回true
        if ($entity->notXObjLog()) {
            return $sqls;
        }

        // version, updatetime 有单独字段记录
        $arr = array();
        foreach ($dirtyKeys as $key) {
            if (in_array($key, array(
                'version',
                'updatetime'))) {
                continue;
            }
            $arr[$key] = $entity->getCol($key);
        }

        $type = 0; // insert
        if ($entity->isRemoved()) {
            $type = 2; // remove
        } elseif ($entity->version > 1) {
            $type = 1; // update
        }

        // Debug::sys("[-- tryXObjLogInsertSqls 1 --]");
        $xunitofworkid = Debug::getUnitofworkId();
        // Debug::sys("[-- tryXObjLogInsertSqls 2 {$xunitofworkid} --]");

        $randno = XUnitOfWork::getTablenoByXunitofworkid($xunitofworkid);
        $content = json_encode($arr, JSON_UNESCAPED_UNICODE);

        $row = array();
        $row["randno"] = $randno;
        $row["xunitofworkid"] = $xunitofworkid;
        $row["type"] = $type;
        $row["objtype"] = $objtype = get_class($entity);
        $row["objid"] = $objid = $entity->id;
        $row["objver"] = $entity->version;
        $row["content"] = $content;
        $row["randno_fix"] = XObjLog::getTablenoByObjtypeObjid($objtype, $objid);

        // 生成双份散列
        // 按日期散列
        $dbconf = [];
        $dbconf['database'] = 'xworkdb';
        $dbconf['tableno'] = $randno;
        $xobjlog1 = XObjLog::createByBiz($row, $dbconf);

        $row["id"] = $xobjlog1->id;

        // 按objtype:objid散列
        $dbconf = [];
        $dbconf['database'] = 'xworkdb';
        $dbconf['tableno'] = $row["randno_fix"];
        $xobjlog2 = XObjLog::createByBiz($row, $dbconf);

        $sqls1 = $xobjlog1->getInsertCommand();

        $sqls2 = $xobjlog2->getInsertCommand();

        return array_merge($sqls1, $sqls2);
    }

    // /////////////////////////////////////////////////////////////
    // 通用方法-代替-DbExecuter
    // queryValue 单值
    // queryValues 单列
    // queryRow 单行
    // queryRows 多行多列
    // executeNoQuery 非查询

    // 返回单值, 没有cache
    public static function queryValue ($sql, $bind = array(), $database = "") {
        return BeanFinder::get("DbExecuter", $database)->queryValue($sql, $bind);
    }

    // 返回单列, 没有cache
    public static function queryValues ($sql, $bind = array(), $database = "") {
        return BeanFinder::get("DbExecuter", $database)->queryValues($sql, $bind);
    }

    // 返回单行, 没有cache
    public static function queryRow ($sql, $bind = array(), $database = "") {
        $rows = BeanFinder::get("DbExecuter", $database)->query($sql, $bind);
        if (is_array($rows) && is_array($rows[0])) {
            return $rows[0];
        } else {
            return array();
        }
    }

    // 返回多行, 没有cache
    public static function queryRows ($sql, $bind = array(), $database = "") {
        return BeanFinder::get("DbExecuter", $database)->query($sql, $bind);
    }

    // 执行非查询语句，不存在有没有cache
    public static function executeNoQuery ($sql, $bind = array(), $database = "") {
        return BeanFinder::get("DbExecuter", $database)->executeNoQuery($sql, $bind);
    }

    // 通过sql语句查询第一行第一列的值，中间经过sql缓存
    // 需要调用者保证语句的正确性，主要是为了查询id，同时加cache
    public static function queryValueWithCache ($sql, $bind = array(), $expireTime = 0, $database = "") {
        // $sql_key = $sql."+".serialize($bind);
        $sql_key = $echoSql = DbExecuter::buildSql($sql, $bind);
        $str = $sql_key = "[queryValueWithCache][beg][$database] -- $sql_key";
        Debug::sys($str);
        $hit = "1";

        $unitOfWork = BeanFinder::get("UnitOfWork");
        $value = $unitOfWork->getQueryRet($sql_key, true);
        if (empty($value)) {
            $hit = "0";
            $value = BeanFinder::get("DbExecuter", $database)->queryValue($sql, $bind);
            if ($expireTime < 1) {
                $expireTime = Config::getConfig("idListCacheExpireTime", 600);
            }
            $unitOfWork->registerQueryRet($sql_key, $value, true, $expireTime);
        }

        $setfix = "";
        if ($hit == "0" && ! empty($value)) {
            $setfix = "and set value ";
        }

        $str = "[queryValueWithCache][end][$database] hit=$hit $setfix--";
        Debug::sys($str);
        return $value;
    }

    // 通过sql语句查询第一列的值，中间经过sql缓存
    // 需要调用者保证语句的正确性，主要是为了查询ids，同时加cache
    public static function queryValuesWithCache ($sql, $bind = array(), $expireTime = 0, $database = "") {
        // $sql_key = $sql."+".serialize($bind);
        $sql_key = $echoSql = DbExecuter::buildSql($sql, $bind);
        $str = $sql_key = "[queryValuesWithCache][beg][$database] -- $sql_key";
        Debug::sys($str);
        $hit = "1";

        $unitOfWork = BeanFinder::get("UnitOfWork");
        $values = $unitOfWork->getQueryRet($sql_key, true);
        if (empty($values)) {
            $hit = "0";
            $values = BeanFinder::get("DbExecuter", $database)->queryValues($sql, $bind);
            if ($expireTime < 1) {
                $expireTime = Config::getConfig("idListCacheExpireTime", 600);
            }
            $unitOfWork->registerQueryRet($sql_key, $values, true, $expireTime);
        }

        $setfix = "";
        if ($hit == "0" && ! empty($values)) {
            $setfix = "and set values ";
        }

        $str = "[queryValuesWithCache][end][$database] hit=$hit $setfix--";
        Debug::sys($str);
        return $values;
    }

    // 通过sql语句查询第一行，中间经过sql缓存
    // 需要调用者保证语句的正确性，同时加cache
    public static function queryRowWithCache ($sql, $bind = array(), $expireTime = 0, $database = "") {
        $sql_key = $echoSql = DbExecuter::buildSql($sql, $bind);
        $str = $sql_key = "[queryRowWithCache][beg][$database] -- $sql_key";
        Debug::sys($str);
        $hit = "1";

        $unitOfWork = BeanFinder::get("UnitOfWork");
        $value = $unitOfWork->getQueryRet($sql_key, true);
        if (empty($value)) {
            $hit = "0";
            $rows = BeanFinder::get("DbExecuter", $database)->query($sql, $bind);
            if (is_array($rows) && is_array($rows[0])) {
                $value = $rows[0];
            } else {
                $value = array();
            }

            if ($expireTime < 1) {
                $expireTime = Config::getConfig("idListCacheExpireTime", 600);
            }
            $unitOfWork->registerQueryRet($sql_key, $value, true, $expireTime);
        }

        $setfix = "";
        if ($hit == "0" && ! empty($value)) {
            $setfix = "and set value ";
        }

        $str = "[queryRowWithCache][end][$database] hit=$hit $setfix--";
        Debug::sys($str);
        return $value;
    }

    // 通过sql语句查询多行，中间经过sql缓存
    // 需要调用者保证语句的正确性，同时加cache
    public static function queryRowsWithCache ($sql, $bind = array(), $expireTime = 0, $database = "") {
        $sql_key = $echoSql = DbExecuter::buildSql($sql, $bind);
        $str = $sql_key = "[queryRowsWithCache][beg][$database] -- $sql_key";
        Debug::sys($str);
        $hit = "1";

        $unitOfWork = BeanFinder::get("UnitOfWork");
        $value = $unitOfWork->getQueryRet($sql_key, true);
        if (empty($value)) {
            $hit = "0";
            $value = $rows = BeanFinder::get("DbExecuter", $database)->query($sql, $bind);

            if ($expireTime < 1) {
                $expireTime = Config::getConfig("idListCacheExpireTime", 600);
            }
            $unitOfWork->registerQueryRet($sql_key, $value, true, $expireTime);
        }

        $setfix = "";
        if ($hit == "0" && ! empty($value)) {
            $setfix = "and set value ";
        }

        $str = "[queryRowsWithCache][end][$database] hit=$hit $setfix--";
        Debug::sys($str);
        return $value;
    }

    // 提取实体数组的ids
    public static function entitys2ids ($entitys) {
        $ids = array();
        foreach ($entitys as $a) {
            $ids[] = $a->id;
        }

        return $ids;
    }
}
