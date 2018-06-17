<?php

/**
 * UnitOfWork
 * @desc 		工作单元 UnitOfWork;实体Wrapper EntityWrapper
 * @remark		依赖类: XMemCached , BeanFinder , Config , DBC , DbExecuter, Entity, XEntity
 * @remark2		TODO by sjp 2011-09-08: 在工作单元提交后,需要将各个对象的状态都致好，以便重复提交也不出问题
 * @copyright	(c)2012 xwork.
 * @file		UnitOfWork.class.php
 * @author		shijianping <shijpcn@qq.com>
 * @date		2012-02-26
 */
class UnitOfWork
{

    const READ_ONLY = true;

    const MODIFY = false;

    // commitedCnt
    public static $commitedCnt = 0;

    // 缓存对象 Entity, 工作单元提交, 不会被清空, 真正的全局唯一
    // key的产生规则和 loadMap 一致
    private $entityMap;

    // 实体id生成的key作为键，缓存对象 $entityWrapper, 仅作用于一次工作单元提交
    private $loadMap;

    // sql语句作为键，缓存结果
    private $loadMap2;

    // 高级cache
    private $cache;

    // 可能会用到的对象的ids
    public $maybeIdsList;

    private $insertList;

    private $updateList;

    private $deleteList;

    private $isReadOnly = false;

    private $isReadOnlyBak = "nullbak";

    private $isCommited = false;

    // 收集信息用于生成Xunitofwork
    private $infoForXunitofwork = array();

    public function setInfoForXunitofwork ($k, $v) {
        $this->infoForXunitofwork[$k] = $v;
    }

    public function getInfoForXunitofwork ($k) {
        return $this->infoForXunitofwork[$k];
    }

    // /////////////////////////////////
    // reload 实验 begin TODO by sjp
    // 重新加载的对象
    private $reloadIds = array();

    // 只需要重新加载一次, Entity or XEntity
    public function needReload ($entity) {
        $key = $this->genKey($entity->id, $entity->getClassName(), $entity->getDatabaseName());
        if (empty($this->reloadIds[$key])) {
            return true;
        } else {
            return false;
        }
    }

    // 标记为重新加载过一次了, Entity or XEntity
    public function reloadOneTime ($entity) {
        $key = $this->genKey($entity->id, $entity->getClassName(), $entity->getDatabaseName());
        $this->reloadIds[$key] = $key;
    }

    // reload 实验 end
    // ////////////////////////////////*/
    public function __construct () {
        $this->init();
    }

    public function init () {
        if (self::$commitedCnt > 0) {
            Debug::plusplusUnitofworkId();
        }

        // 已处理过的Entity版本号重置
        $this->loadMap_entitys_resetVersion();

        $this->loadMap = array();
        $this->loadMap2 = array();
        $this->cache = BeanFinder::get("XMemCached");
        $this->insertList = array();
        $this->updateList = array();
        $this->deleteList = array();
        $this->maybeIdsList = array();
        $this->isCommited = false;
        $this->infoForXunitofwork = array();
        $this->reloadIds = array();
    }

    // 对象版本号重置
    private function loadMap_entitys_resetVersion () {
        if (empty($this->loadMap)) {
            return;
        }

        $cnt_all = count($this->entityMap);
        $cnt_reset = 0;
        $cnt_remove = 0;

        foreach ($this->loadMap as $key => $entityWrapper) {
            $entity = $entityWrapper->getEntity();

            // 清除, remove 的 Entity
            if ($entity->isRemoved()) {

                $key = $this->genKey($entity->id, $entity->getClassName(), $entity->getDatabaseName());
                unset($this->entityMap[$key]);

                $cnt_remove ++;
                continue;
            }

            $cnt_reset ++;

            // 版本号重置, 对象和数据库里的应该是一样了, 相当于加载过了
            $entity->resetOriginalVersion();
        }

        $logstr = "[-- loadMap_entitys_resetVersion : cnt_all= {$cnt_all} , cnt_reset= {$cnt_reset} , cnt_remove= {$cnt_remove} --]";
        Debug::sys($logstr);
    }

    // 产生loadmap key
    public function genKey ($id, $entityClassName = "", $database = '') {
        $key_prefix = Config::getConfig("key_prefix");
        $key = "[{$key_prefix}][{$database}][{$entityClassName}][{$id}]";
        // Debug::xworkdev("UnitOfWork::genKey => {$key}");
        return $key;
    }

    public function getMaybeIds ($entityClassName, $database = '') {
        Debug::xworkdev("UnitOfWork::getMaybeIds [$entityClassName][$database]");

        if (isset($this->maybeIdsList) && isset($this->maybeIdsList[$entityClassName]) && isset($this->maybeIdsList[$entityClassName][$database])) {
            return $this->maybeIdsList[$entityClassName][$database];
        } else {
            return array();
        }
    }

    public function unsetMaybeIds ($entityClassName, $database = '') {
        Debug::xworkdev("UnitOfWork::unsetMaybeIds [$entityClassName][$database]");

        if (isset($this->maybeIdsList) && isset($this->maybeIdsList[$entityClassName]) && isset($this->maybeIdsList[$entityClassName][$database])) {
            unset($this->maybeIdsList[$entityClassName][$database]);
        }
    }

    // 供 $this->registerEntity调用 和 供 NoEntityObj调用
    public function registerBelongtos ($obj, $belongtos = array()) {
        foreach ($belongtos as $belongto) {
            $entityClassName = $belongto['type']; // 类型
            $foreign_key = $belongto['key']; // 外键
            $database = isset($belongto['database']) ? $belongto['database'] : DaoBase::get_defaultdb_name(); // 数据库

            $id = $obj->$foreign_key;
            $key = $this->genKey($id, $entityClassName, $database);

            // 缓存belongto id
            if (! array_key_exists($key, (array) $this->loadMap) && $id != 0) {
                if (! isset($this->maybeIdsList[$entityClassName])) {
                    $this->maybeIdsList[$entityClassName] = array();
                }

                if (! isset($this->maybeIdsList[$entityClassName][$database])) {
                    $this->maybeIdsList[$entityClassName][$database] = array();
                }

                Debug::xworkdev("UnitOfWork::registerBelongtos [$entityClassName][$database][$id]");

                $this->maybeIdsList[$entityClassName][$database][$id] = $id;
            }
        }
    }

    // 工作单元提交一次以后,再修改实体时,如果实体没有在loadMap里,需要重新注册一次
    public function tryReRegisterIfNeed ($entity) {
        $key = $this->genKey($entity->id, $entity->getClassName(), $entity->getDatabaseName());

        // 未加载入 loadMap
        if (empty($this->loadMap[$key])) {
            $logstr = "[-- ReRegisterEntity[0] {$key} --]";
            Debug::sys($logstr);

            // 如果 entityMap[key]存在, 则可用
            if ($this->entityMap[$key]) {
                $entity_from_map = $this->entityMap[$key];

                // 不应该存在这种情况
                if ($entity_from_map->version != $entity->version) {
                    Debug::error("[-- ReRegisterEntity[1] {$key}->version [{$entity_from_map->version} <> {$entity->version}] --]");
                }

                // 重新注册
                $this->registerEntity($entity);
            } else {
                // 不应该存在这种情况
                Debug::error("[-- ReRegisterEntity[2] entityMap[{$key}] not exist --]");
            }
        }
    }

    // Entity or XEntity
    public function registerEntity (EntityBase $entity, $needAdd2Cached = true) {
        // 只读工作单元不能创建实体,有可能是半路开启的只读开关
        if ($this->isReadOnly) {
            DBC::requireTrue(false == $entity->isCreated(), "can't create " . $entity->getClassName() . " Entity in readOnly UnitOfWork");
        }

        // 生成key
        $key = $this->genKey($entity->id, $entity->getClassName(), $entity->getDatabaseName());

        // 加入 entityMap
        $this->entityMap[$key] = $entity;

        // 加入loadMap
        $this->loadMap[$key] = new EntityWrapper($entity, $this->isReadOnly);

        // 把可能加载的实体的ids,注册到$MaybeIdsList
        $this->registerBelongtos($entity, $entity->getBelongtos());

        $cacheOpen = Config::getConfig("cacheOpen", false);
        $entityCacheOpen = Config::getConfig("entityCacheOpen", false);

        // 高级cache
        if ($needAdd2Cached && ! empty($entity) && $entity->isCreated() == false && $cacheOpen && $entityCacheOpen) {
            $this->cache->set($key, $entity, false, Config::getConfig("entityCacheExpireTime", 3600));
        }
    }

    public function commitAndRelease () {
        $this->commit();
        BeanFinder::clearBean("UnitOfWork");
    }

    public function commitAndInit () {
        $this->commit();
        $this->init();
    }

    private $isNotNeedCommitAgain = false;

    public function commitAndSetNotNeedCommitAgain () {
        $this->commit();
        $this->setReadOnly4fast();
        $this->isNotNeedCommitAgain = true;
    }

    public function commit () {
        if ($this->isNotNeedCommitAgain) {
            Debug::sys("[-- isNotNeedCommitAgain --]");
            return;
        }

        // 恢复一下状态,也许被人更改过
        $this->resetReadOnly();

        Debug::sys("[-- commit beg [" . count($this->loadMap) . "] --]");
        DBC::requireTrue(! $this->isCommited, "can't commited twice.");

        Debug::sys("[-- makeThreeList --]");
        $this->makeThreeList();
        $database_sqls = array();

        $insertCnt = count($this->insertList);
        $updateCnt = count($this->updateList);
        $deleteCnt = count($this->deleteList);

        Debug::sys("[-- threeList [{$insertCnt}] [{$updateCnt}] [{$deleteCnt}] --]");

        Debug::sys("[-- insertList --]");
        $this->insert($this->insertList, $database_sqls);

        Debug::sys("[-- updateList --]");
        $this->update($this->updateList, $database_sqls);

        Debug::sys("[-- deleteList --]");
        $this->delete($this->deleteList, $database_sqls);

        $this->setInfoForXunitofwork('commit_load_cnt', count($this->loadMap));
        $this->setInfoForXunitofwork('commit_insert_cnt', count($this->insertList));
        $this->setInfoForXunitofwork('commit_update_cnt', count($this->updateList));
        $this->setInfoForXunitofwork('commit_delete_cnt', count($this->deleteList));

        $cnt = 0;
        foreach ($database_sqls as $sqls) {
            $cnt += count($sqls);
        }

        if ($this->isReadOnly) {
            $this->checkReadOnly($database_sqls);
            $cnt = - 1;
        } elseif ($cnt > 0) {
            $this->doCommit($database_sqls);
        }

        // 已提交
        $this->isCommited = true;

        $this->setInfoForXunitofwork('commit_end', Debug::mark_commit_end());

        // 尝试记录 XUnitOfWork
        Debug::tryCreateXUnitOfWork($this);

        // 工作单元提交数目
        self::$commitedCnt ++;

        Debug::sys("[-- commit end [$cnt] --]");
    }

    public function isReadOnly () {
        return $this->isReadOnly;
    }

    public function unsetCommited() {
        $this->isCommited = false;
    }

    public function isCommited () {
        return $this->isCommited;
    }

    public function makeThreeList () {
        foreach ($this->loadMap as $entityWrapper) {
            $entity = $entityWrapper->getEntity();

            // 新建并删除,则跳过
            if ($entity->isRemoved() && $entity->isCreated()) {
                continue;
            }

            if ($entity->isRemoved()) {
                // 删除
                $this->deleteList[] = $entity;
            } elseif ($entity->isCreated()) {
                // 新建
                $this->insertList[] = $entity;
            } elseif (count($diff = $entityWrapper->getDiff()) > 0) {
                // 有变动的
                $entity->setDirty();

                $entity->setDirtyKeys($diff);
                $this->updateList[] = $entity;
            }
        }
        // 后进先出
        $this->deleteList = array_reverse($this->deleteList);
    }

    // 需要解决跨数据的事务
    protected function doCommit (array $database_sqls) {
        Debug::sys("[-- doCommit [00] --]");

        // 有几个库需要同时更改?
        $database_cnt = count($database_sqls);
        $database0 = "";
        $sqls0 = array();

        // 有更新语句,需要记录下来log
        Config::setConfig("mustXworklog", true);
        try {
            foreach ($database_sqls as $database => $sqls) {
                $sqlcnt = count($sqls);
                Debug::sys("[-- doCommit [{$database}] [begin] [$sqlcnt] --]");
                $database0 = $database;
                $sqls0 = $sqls;
                $this->commitImp($sqls, $database);
                Debug::sys("[-- doCommit [{$database}] [end] [$sqlcnt] --]");
            }
        } catch (Exception $e) {

            // 如果跨库则可以双提交
            if ($database_cnt > 1) {
                Debug::error("[-- database_cnt = $database_cnt ; break! --]");
                throw $e;
            }

            // 如果不跨库, 如果异常原因为执行超时，重新连接数据库一次
            if (preg_match("/gone away/i", $e->getMessage()) > 0) {
                Debug::warn("[-- DbExecuter:{$database0}:reConnection --]");
                $dbExec = BeanFinder::get("DbExecuter", $database0);
                $dbExec->reConnection();
                $this->commitImp($sqls0, $database0);
            } else {
                throw $e;
            }
        }

        // 新建对象,设置为已插入
        foreach ($this->insertList as $entity) {
            $entity->setCreated();
        }
        // 更新对象,清高级cache
        foreach ($this->updateList as $entity) {
            $this->removeFromCached($entity);
        }
        // 删除对象,清高级cache
        foreach ($this->deleteList as $entity) {
            $this->removeFromCached($entity);
        }

        Debug::sys("[-- doCommit [11] --]");
    }

    // 实际提交
    protected function commitImp (array $sqls, $database = "") {
        if (empty($sqls)) {
            return;
        }

        // 尽量避免生成DbExecuter对象
        $dbExec = BeanFinder::get("DbExecuter", $database);
        $dbExec->beginTransaction();

        try {
            foreach ($sqls as $sql) {
                $affectedRowCnt = $dbExec->executeNoQuery($sql['sql'], $sql['param']);

                // 乐观离线锁的检查, 只检查update语句, delete语句有可能删除不到东西
                $update_need_check_version = Config::getConfig("update_need_check_version", false);
                if ($affectedRowCnt < 1 && preg_match("/update\s/i", $sql['sql']) > 0 && $update_need_check_version) {
                    $echoSql = DbExecuter::buildSql($sql['sql'], $sql['param']);
                    $logstr = "[-- 并发冲突导致更新失败,sql={$database}:{$echoSql} --]";
                    Debug::error($logstr);
                    throw new DbException("并发冲突,请稍候重试.", 0);
                }
            }

            $dbExec->commit();
        } catch (Exception $ex) {
            Debug::error("[-- DbExecuter:$database:rollBack --]");
            $rollBackRet = $dbExec->rollBack();
            throw $ex;
        }
    }

    protected function insert ($insertList, &$database_sqls) {
        foreach ($insertList as $entity) {
            $this->mergeSqls($database_sqls, $entity->getInsertCommand(), $entity->getDatabaseName());
        }
    }

    protected function update ($updateList, &$database_sqls) {
        foreach ($updateList as $entity) {
            $this->mergeSqls($database_sqls, $entity->getUpdateCommand(), $entity->getDatabaseName());
        }
    }

    protected function delete ($deleteList, &$database_sqls) {
        foreach ($deleteList as $entity) {
            $this->mergeSqls($database_sqls, $entity->getDeleteCommand(), $entity->getDatabaseName());
        }
    }

    protected function mergeSqls (&$database_sqls, $sqls, $entityDatabase) {
        foreach ($sqls as $sql) {
            // 存在跨库事务的问题
            $database = (isset($sql['database']) && $sql['database']) ? $sql['database'] : $entityDatabase;

            // 避免notice
            if (! isset($database_sqls[$database])) {
                $database_sqls[$database] = array();
            }

            $database_sqls[$database][] = $sql;
        }
    }

    public function getEntity ($id, $entityClassName = "", $database = '') {
        $key = $this->genKey($id, $entityClassName, $database);

        // 检查 loadMap
        if (array_key_exists($key, (array) $this->loadMap)) {
            return $this->loadMap[$key]->getEntity();
        }

        // 检查 entityMap => loadMap
        if (array_key_exists($key, (array) $this->entityMap)) {

            $entity = $this->entityMap[$key];

            Debug::sys("[-- entityMap=>loadMap {$key} [{$entity->version}] --]");

            $this->registerEntity($entity);

            return $entity;
        }

        // Map中不存在，则试试cache
        $entity = null;
        if (Config::getConfig("cacheOpen", false) && Config::getConfig("entityCacheOpen", false)) {
            $entity = $this->cache->get($key);

            // cache => entityMap, loacMap
            if ($entity) {
                $this->registerEntity($entity, false);
            }
        }

        return $entity;
    }

    public function getEntityDiff ($id, $entityClassName = "", $database = '') {
        // 准备废弃
        throw new Exception("UnitOfWork::getEntityDiff() ready delete");
        $key = $this->genKey($id, $entityClassName, $database);
        if (array_key_exists($key, (array) $this->loadMap)) {
            return $this->loadMap[$key]->getDiff();
        }
        return array();
    }

    // 调试用途
    public function getLoadMap2 () {
        return $this->loadMap2;
    }

    public function registerQueryRet ($sql_key, $ret, $needAdd2Cached = false, $expireTime = 600) {
        if (empty($ret)) {
            return;
        }
        $key = Config::getConfig("key_prefix") . '_' . md5($sql_key);
        $this->loadMap2[$key] = $ret;

        // 这里需要注意:$ret为empty的时候,不进行cache,loadMap2却可能存null
        if ($needAdd2Cached && Config::getConfig("cacheOpen", false)) {
            $this->cache->set($key, $ret, false, $expireTime);
        }
    }

    public function getQueryRet ($sql_key, $needCheckCached = false) {
        $key = Config::getConfig("key_prefix") . '_' . md5($sql_key);
        if (array_key_exists($key, (array) $this->loadMap2)) {
            return $this->loadMap2[$key];
        }

        $ret = NULL;
        if ($needCheckCached && Config::getConfig("cacheOpen", false)) {
            $ret = $this->cache->get($key);
            if (empty($ret))
                $this->loadMap2[$key] = $ret;
        }

        return $ret;
    }

    public function getInsertList () {
        return $this->insertList;
    }

    public function getUpdateList () {
        return $this->updateList;
    }

    public function getDeleteList () {
        return $this->deleteList;
    }

    public function remove4Test ($removeReservedId) {
        foreach ($this->loadMap as $entityWrapper) {
            $entityWrapper->getEntity()->remove();
        }
    }

    private function removeFromCached ($entity) {
        $key = $this->genKey($entity->id, $entity->getClassName(), $entity->getDatabaseName());

        $result = $this->cache->delete($key);
        if ($result == false) {
            throw new SystemException("remove entity from cache failed.");
        }
    }

    // =================================
    // 设置isReadOnly,会导致接下来加载的数据不再缓存snap,不再进行diff比较
    public function setReadOnly ($isReadOnly = true) {
        // 保存最原始的值
        if ($this->isReadOnlyBak == "nullbak") {
            $this->isReadOnlyBak = $this->isReadOnly;
        }
        $this->isReadOnly = $isReadOnly;
    }

    // 备份当前的isReadOnly的值
    public function bakupReadOnly () {
        $this->isReadOnlyBak = $this->isReadOnly;
    }

    // 恢复isReadOnly的值,如果是nullbak,则不进行恢复
    public function resetReadOnly () {
        if ($this->isReadOnlyBak != "nullbak") {
            $this->isReadOnly = $this->isReadOnlyBak;
        }
    }

    // 检查是否存在更新语句
    private function checkReadOnly (array $database_sqls) {
        if (count($database_sqls) != 0) {
            throw new SystemException("changes occur in readonly UnitOfWork.");
        }
    }

    // =================================

    // 将当前工作单元设为只读以提高速度,高级优化方案,一定要清楚调用后的后果
    public static function setReadOnly4fast () {
        $unitOfWork = BeanFinder::get("UnitOfWork");
        $unitOfWork->setReadOnly(true);
        Debug::sys("[-- UnitOfWork::setReadOnly4fast --]");
    }

    // 将当前工作单元设为非只读
    public static function setNotReadOnly () {
        $unitOfWork = BeanFinder::get("UnitOfWork");
        $unitOfWork->setReadOnly(false);
        Debug::sys("[-- UnitOfWork::setNotReadOnly --]");
    }
}

class EntityWrapper
{

    private $snap;

    private $entity;

    public function __construct ($entity, $isReadOnly = false) {
        $this->entity = $entity;
        if ($isReadOnly || $entity->isReadOnly()) {
            $this->snap = array();
        } else {
            $this->snap = $this->arrayToSnap($entity->toArray());
        }
    }

    public function isCreated () {
        return $this->entity->isCreated();
    }

    public function isRemoved () {
        return $this->entity->isRemoved();
    }

    public function getEntity () {
        return $this->entity;
    }

    public function getDiff () {
        // 没有快照则没有diff
        if (empty($this->snap)) {
            return array();
        }

        $array = $this->entity->toArray();
        $newSnap = $this->arrayToSnap($array);

        // 性能优化尝试,也许会更差 TODO by sjp
        $md50 = md5(serialize($newSnap));
        $md51 = md5(serialize($this->snap));
        if ($md50 == $md51) {
            return array();
        }

        // $diff = array();
        $diff = array_diff_assoc($this->snap, $newSnap);
        return array_keys($diff);
    }

    // 快照数据用md5会节省内存,但会影响性能,应该在0.2毫秒这个量级
    // 需要权衡具体的情况来定
    private function arrayToSnap ($array) {
        return $array;
        // return $this-> arrayToMd5s($array);
    }

    private function arrayToMd5s ($array) {
        $md5s = array();
        foreach ($array as $key => $value) {
            $md5s[$key] = md5($value);
        }
        return $md5s;
    }
}
