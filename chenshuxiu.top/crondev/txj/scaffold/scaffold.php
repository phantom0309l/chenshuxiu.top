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

class Scaffold
{
    protected $tableName = "";

    protected $entityName = "";

    protected $filedirectory = "entity";

    protected $fields = array();

    protected $subsysName = "Audit";

    public function __construct ($tableName, $entityName, $filedirectory) {
        //表名
        $this->tableName = $tableName;
        //实体名
        $this->entityName = $entityName;
        //实体文件和Dao文件存放位置
        $this->filedirectory = $filedirectory;

        $this->fields = $this->getFields();
    }

    public function doWork(){
        //创建Dao文件
        $this->createDaoFile();
        //创建实体文件
        $this->createEntityFile();
        //创建action
        $this->createAction();
        //创建tpl文件目录
        $this->createTplFilePath();
        //创建tpl文件
        $this->createTplFiles();
    }

    public function createAction () {
        $entityName = $this->entityName;
        $subsysName = $this->subsysName;
        $hostdir = ROOT_TOP_PATH . "/" . strtolower($subsysName) . "/action/";
        $createFileName = "{$entityName}MgrAction.php";
        $canCreate = $this->canCreateFile ($hostdir, $createFileName);
        if (false == $canCreate) {
            return;
        }

        $str = '<?php

class XXXMgrAction extends YYYBaseAction {

    // 构造函数，初始化了很多数据
    public function __construct() {
        parent::__construct ();
    }

    _LISTSTR_
    _ONESTR_
    _ADDSTR_
    _ADDPOSTSTR_
    _MODIFYSTR_
    _MODIFYPOSTSTR_
}
        ';

        $str = str_replace("XXX", $entityName, $str);
        $str = str_replace("YYY", $subsysName, $str);

        $listStr = $this->createListActionStr();
        $oneStr = $this->createOneActionStr();
        $addStr = $this->createAddActionStr();
        $addpostStr = $this->createAddPostActionStr();
        $modifyStr = $this->createModifyActionStr();
        $modifypostStr = $this->createModifyPostActionStr();

        $str = str_replace("_LISTSTR_", $listStr, $str);
        $str = str_replace("_ONESTR_", $oneStr, $str);
        $str = str_replace("_ADDSTR_", $addStr, $str);
        $str = str_replace("_ADDPOSTSTR_", $addpostStr, $str);
        $str = str_replace("_MODIFYSTR_", $modifyStr, $str);
        $str = str_replace("_MODIFYPOSTSTR_", $modifypostStr, $str);

        echo "\n";
        echo $filename = ROOT_TOP_PATH . "/" . strtolower($subsysName) . "/action/{$entityName}MgrAction.php";
        echo "\n";

        file_put_contents($filename, $str);
        return $str;
    }

    private function createListActionStr(){
        $str = '
    // 列表
    public function doList() {
        $pagesize = XRequest::getValue("pagesize", 20);
        $pagenum = XRequest::getValue("pagenum", 1);

        $_ID_ = XRequest::getValue("_ID_", 0);

        $cond = "";
        $bind = [];

        //id筛选
        if($_ID_ > 0){
            $cond .= " and id = :id ";
            $bind[":id"] = $_ID_;
        }

        //获得实体
        $sql = "select *
                    from _TABLENAME_
                    where 1 = 1 {$cond} order by id desc";
        $_ENTITYNAMELOWERFIRST_s = Dao::loadEntityList4Page("_ENTITYNAME_", $sql, $pagesize, $pagenum, $bind);
        XContext::setValue("_ENTITYNAMELOWERFIRST_s", $_ENTITYNAMELOWERFIRST_s);

        //获得分页
        $countSql = "select count(*)
                    from _TABLENAME_
                    where 1 = 1 {$cond} order by id desc";
        $cnt = Dao::queryValue($countSql, $bind);
        $url = "/_ENTITYNAMELOWER_mgr/list?_ID_={$_ID_}";
        $pagelink = PageLinkOfBack::create($cnt, $pagesize, $url);
        XContext::setValue("pagelink", $pagelink);

        XContext::setValue("_ID_", $_ID_);
        return self::SUCCESS;
    }';

        $entityName = $this->entityName;
        $entityid = strtolower($entityName) . "id";
        $str = str_replace("_ID_", $entityid, $str);
        $str = str_replace("_ENTITYNAME_", $entityName, $str);
        $str = str_replace("_ENTITYNAMELOWER_", strtolower($entityName), $str);
        $str = str_replace("_ENTITYNAMELOWERFIRST_", lcfirst($entityName), $str);

        $tableName = $this->tableName;
        $str = str_replace("_TABLENAME_", $tableName, $str);
        return $str;
    }

    private function createOneActionStr(){
        $str = '
    // 详情页
    public function doOne () {
        $_ID_ = XRequest::getValue("_ID_", 0);

        $_ENTITYNAMELOWERFIRST_ = _ENTITYNAME_::getById($_ID_);

        XContext::setValue("_ENTITYNAMELOWERFIRST_", $_ENTITYNAMELOWERFIRST_);
        return self::SUCCESS;
    }';

        $entityName = $this->entityName;
        $entityid = strtolower($entityName) . "id";
        $str = str_replace("_ID_", $entityid, $str);
        $str = str_replace("_ENTITYNAME_", $entityName, $str);
        $str = str_replace("_ENTITYNAMELOWERFIRST_", lcfirst($entityName), $str);
        return $str;
    }

    private function createAddActionStr(){
        $str = '
    public function doAdd () {
        return self::SUCCESS;
    }';
        return $str;
    }

    private function createAddPostActionStr(){
        $str = '
    public function doAddPost () {

        _XRequest_

        $row = array();
        _RowStr_

        _ENTITYNAME_::createByBiz($row);

        XContext::setJumpPath("/_ENTITYNAMELOWER_mgr/list");
        return self::SUCCESS;
    }';
        $entityName = $this->entityName;
        $str = str_replace("_ENTITYNAME_", $entityName, $str);
        $str = str_replace("_ENTITYNAMELOWER_", strtolower($entityName), $str);

        $XRequestStr = $this->createXRequestStr();
        $str = str_replace("_XRequest_", $XRequestStr, $str);

        $rowStr = $this->createRowStr();
        $str = str_replace("_RowStr_", $rowStr, $str);

        return $str;
    }

    private function createModifyActionStr(){
        $str = '
    public function doModify () {
        $_ID_ = XRequest::getValue("_ID_", 0);

        $_ENTITYNAMELOWERFIRST_ = _ENTITYNAME_::getById($_ID_);
        DBC::requireTrue($_ENTITYNAMELOWERFIRST_ instanceof _ENTITYNAME_, "_ENTITYNAMELOWERFIRST_不存在:{$_ID_}");
        XContext::setValue("_ENTITYNAMELOWERFIRST_", $_ENTITYNAMELOWERFIRST_);

        return self::SUCCESS;
    }';

        $entityName = $this->entityName;
        $entityid = strtolower($entityName) . "id";
        $str = str_replace("_ID_", $entityid, $str);
        $str = str_replace("_ENTITYNAME_", $entityName, $str);
        $str = str_replace("_ENTITYNAMELOWERFIRST_", lcfirst($entityName), $str);
        return $str;
    }

    private function createModifyPostActionStr(){
        $str = '
    // 修改提交
    public function doModifyPost () {
        $_ID_ = XRequest::getValue("_ID_", 0);
        _XRequest_
        $_ENTITYNAMELOWERFIRST_ = _ENTITYNAME_::getById($_ID_);
        DBC::requireTrue($_ENTITYNAMELOWERFIRST_ instanceof _ENTITYNAME_, "_ENTITYNAMELOWERFIRST_不存在:{$_ID_}");

        _ModifyEntityStr_
        $preMsg = "修改已保存 " . XDateTime::now();
        XContext::setJumpPath("/_ENTITYNAMELOWER_mgr/modify?_ID_=" . $_ID_ . "&preMsg=" . urlencode($preMsg));
        return self::SUCCESS;
    }';

        $entityName = $this->entityName;
        $entityid = strtolower($entityName) . "id";
        $str = str_replace("_ID_", $entityid, $str);
        $str = str_replace("_ENTITYNAME_", $entityName, $str);
        $str = str_replace("_ENTITYNAMELOWER_", strtolower($entityName), $str);
        $str = str_replace("_ENTITYNAMELOWERFIRST_", lcfirst($entityName), $str);

        $XRequestStr = $this->createXRequestStr();
        $str = str_replace("_XRequest_", $XRequestStr, $str);

        $modifyEntityStr = $this->createModifyEntityStr();
        $str = str_replace("_ModifyEntityStr_", $modifyEntityStr, $str);

        return $str;
    }

    private function createModifyEntityStr(){
        $fields = $this->fields;
        $entityName = $this->entityName;
        $result = "";
        foreach($fields as $field => $default){
            $str = '$_ENTITYNAMELOWERFIRST_->_FIELD_ = $_FIELD_;';
            $str = str_replace("_FIELD_", $field, $str);
            $str = str_replace("_ENTITYNAMELOWERFIRST_", lcfirst($entityName), $str);
            $result .= "{$str}\n        ";
        }
        return $result;
    }

    private function createRowStr(){
        $fields = $this->fields;
        $result = "";
        foreach($fields as $field => $default){
            $str = '$row["_FIELD_"] = $_FIELD_;';
            $str = str_replace("_FIELD_", $field, $str);
            $result .= "{$str}\n        ";
        }
        return $result;
    }

    private function createXRequestStr(){
        $fields = $this->fields;
        $result = "";
        foreach($fields as $field => $default){
            $str = '$_FIELD_ = XRequest::getValue("_FIELD_", _DEFAULT_);';
            $str = str_replace("_FIELD_", $field, $str);
            $str = str_replace("_DEFAULT_", $default, $str);
            $result .= "{$str}\n        ";
        }
        return $result;
    }

    private function getFields(){
        $tableName = $this->tableName;
        $arr = $this->table2array($tableName);
        $result = array();
        foreach ($arr as $a) {
            $field = $a['field'];
            if ($field == "id" || $field == "version" || $field == "createtime" || $field == "updatetime") {
                continue;
            }

            $default = $a['default'] === null ? '""' : $a['default'];
            $result[$field] = $default;
        }
        return $result;
    }

    private function getFieldTypes(){
        $tableName = $this->tableName;
        $arr = $this->table2array($tableName);
        $result = array();
        foreach ($arr as $a) {
            $field = $a['field'];
            $result[$field] = $a['type'];
        }
        return $result;
    }

    public function createTplFilePath(){
        $subsysName = $this->subsysName;
        $entityName = $this->entityName;
        $tplPath = ROOT_TOP_PATH . "/" . strtolower($subsysName) . "/tpl/" . strtolower($entityName) . "mgr";

        if (! is_dir($tplPath)) {
            mkdir($tplPath, 0775);
        }
    }

    public function createTplFiles(){
        $this->createAddTpl();
        $this->createModifyTpl();
        $this->createOneTpl();
        $this->createListTpl();
    }

    private function createAddTpl(){
        $subsysName = $this->subsysName;
        $entityName = $this->entityName;
        $hostdir = ROOT_TOP_PATH . "/" . strtolower($subsysName) . "/tpl/" . strtolower($entityName) . "mgr/";
        $createFileName = "add.tpl.php";
        $canCreate = $this->canCreateFile ($hostdir, $createFileName);
        if (false == $canCreate) {
            return;
        }
        $str = file_get_contents("add.tpl.php");
        $str = str_replace("_ENTITYNAMELOWER_", strtolower($entityName), $str);

        $trStr = $this->createAddTrStr();
        $str = str_replace("_TRSTR_", $trStr, $str);

        echo "\n";
        echo $filename = ROOT_TOP_PATH . "/" . strtolower($subsysName) . "/tpl/" . strtolower($entityName) . "mgr/add.tpl.php";
        echo "\n";

        file_put_contents($filename, $str);
    }

    private function createAddTrStr(){
        $fields = $this->fields;
        $types = $this->getFieldTypes();
        $result = "";
        $str1 = '<tr>
                    <td>_FIELD_</td>
                    <td>
                        <input type="text" name="_FIELD_" value="" />
                    </td>
                </tr>';
        $str2 = '<tr>
                    <td>_FIELD_</td>
                    <td>
                        <textarea rows="10" cols="120" name="_FIELD_"></textarea>
                    </td>
                </tr>';
        foreach($fields as $field => $default){
            $type = $types[$field];
            if($type == 'text'){
                $str = $str2;
            }else{
                $str = $str1;
            }
            $str = str_replace("_FIELD_", $field, $str);
            $result .= "{$str}\n                ";
        }
        return $result;
    }

    private function createModifyTpl(){
        $subsysName = $this->subsysName;
        $entityName = $this->entityName;

        $hostdir = ROOT_TOP_PATH . "/" . strtolower($subsysName) . "/tpl/" . strtolower($entityName) . "mgr/";
        $createFileName = "modify.tpl.php";
        $canCreate = $this->canCreateFile ($hostdir, $createFileName);
        if (false == $canCreate) {
            return;
        }
        $str = file_get_contents("modify.tpl.php");
        $entityid = strtolower($entityName) . "id";
        $str = str_replace("_ID_", $entityid, $str);
        $str = str_replace("_ENTITYNAMELOWER_", strtolower($entityName), $str);
        $str = str_replace("_ENTITYNAMELOWERFIRST_", lcfirst($entityName), $str);

        $trStr = $this->createModifyTrStr();
        $str = str_replace("_TRSTR_", $trStr, $str);

        echo "\n";
        echo $filename = ROOT_TOP_PATH . "/" . strtolower($subsysName) . "/tpl/" . strtolower($entityName) . "mgr/modify.tpl.php";
        echo "\n";

        file_put_contents($filename, $str);
    }

    private function createModifyTrStr(){
        $entityName = $this->entityName;
        $fields = $this->fields;
        $types = $this->getFieldTypes();
        $result = "";
        $str1 = '<tr>
                    <td>_FIELD_</td>
                    <td>
                        <input type="text" name="_FIELD_" value="<?= $_ENTITYNAMELOWERFIRST_->_FIELD_ ?>" />
                    </td>
                </tr>';
        $str2 = '<tr>
                    <td>_FIELD_</td>
                    <td>
                        <textarea rows="10" cols="120" name="_FIELD_"><?= $_ENTITYNAMELOWERFIRST_->_FIELD_ ?></textarea>
                    </td>
                </tr>';
        foreach($fields as $field => $default){
            $type = $types[$field];
            if($type == 'text'){
                $str = $str2;
            }else{
                $str = $str1;
            }
            $str = str_replace("_FIELD_", $field, $str);
            $str = str_replace("_ENTITYNAMELOWERFIRST_", lcfirst($entityName), $str);
            $result .= "{$str}\n                ";
        }
        return $result;
    }

    private function createOneTpl(){
        $subsysName = $this->subsysName;
        $entityName = $this->entityName;

        $hostdir = ROOT_TOP_PATH . "/" . strtolower($subsysName) . "/tpl/" . strtolower($entityName) . "mgr/";
        $createFileName = "one.tpl.php";
        $canCreate = $this->canCreateFile ($hostdir, $createFileName);
        if (false == $canCreate) {
            return;
        }
        $str = file_get_contents("modify.tpl.php");
        $entityid = strtolower($entityName) . "id";
        $str = str_replace("_ID_", $entityid, $str);
        $str = str_replace("_ENTITYNAMELOWER_", strtolower($entityName), $str);
        $str = str_replace("_ENTITYNAMELOWERFIRST_", lcfirst($entityName), $str);

        $trStr = $this->createModifyTrStr();
        $str = str_replace("_TRSTR_", $trStr, $str);

        echo "\n";
        echo $filename = ROOT_TOP_PATH . "/" . strtolower($subsysName) . "/tpl/" . strtolower($entityName) . "mgr/one.tpl.php";
        echo "\n";

        file_put_contents($filename, $str);
    }

    private function createOneTrStr(){
        $entityName = $this->entityName;
        $fields = $this->fields;
        $types = $this->getFieldTypes();
        $result = "";
        $str1 = '<tr>
                    <td>_FIELD_</td>
                    <td>
                        <span><?= $_ENTITYNAMELOWERFIRST_->_FIELD_ ?></span>
                    </td>
                </tr>';
        $str2 = '<tr>
                    <td>_FIELD_</td>
                    <td>
                        <p><?= $_ENTITYNAMELOWERFIRST_->_FIELD_ ?></p>
                    </td>
                </tr>';
        foreach($fields as $field => $default){
            $type = $types[$field];
            if($type == 'text'){
                $str = $str2;
            }else{
                $str = $str1;
            }
            $str = str_replace("_FIELD_", $field, $str);
            $str = str_replace("_ENTITYNAMELOWERFIRST_", lcfirst($entityName), $str);
            $result .= "{$str}\n                ";
        }
        return $result;
    }

    private function createListTpl(){
        $subsysName = $this->subsysName;
        $entityName = $this->entityName;

        $hostdir = ROOT_TOP_PATH . "/" . strtolower($subsysName) . "/tpl/" . strtolower($entityName) . "mgr/";
        $createFileName = "list.tpl.php";
        $canCreate = $this->canCreateFile ($hostdir, $createFileName);
        if (false == $canCreate) {
            return;
        }
        $str = file_get_contents("list.tpl.php");
        $entityid = strtolower($entityName) . "id";
        $str = str_replace("_ID_", $entityid, $str);
        $str = str_replace("_ENTITYNAMELOWER_", strtolower($entityName), $str);
        $str = str_replace("_ENTITYNAMELOWERFIRST_", lcfirst($entityName), $str);

        $theadStr = $this->createListTheadStr();
        $str = str_replace("_THEADSTR_", $theadStr, $str);

        $tbodyStr = $this->createListTbodyStr();
        $str = str_replace("_TBODYSTR_", $tbodyStr, $str);

        echo "\n";
        echo $filename = ROOT_TOP_PATH . "/" . strtolower($subsysName) . "/tpl/" . strtolower($entityName) . "mgr/list.tpl.php";
        echo "\n";

        file_put_contents($filename, $str);
    }

    private function createListTheadStr(){
        $fields = $this->fields;
        $result = "";
        foreach($fields as $field => $default){
            $str = '<td>_FIELD_</td>';
            $str = str_replace("_FIELD_", $field, $str);
            $result .= "{$str}\n                    ";
        }
        return $result;
    }

    private function createListTbodyStr(){
        $fields = $this->fields;
        $result = "";
        foreach($fields as $field => $default){
            $str = '<td><?= $a->_FIELD_ ?></td>';
            $str = str_replace("_FIELD_", $field, $str);
            $result .= "{$str}\n                    ";
        }
        return $result;
    }

    public function createDaoFile () {
        $entityName = $this->entityName;
        $filedirectory = $this->filedirectory;

        $hostdir = ROOT_TOP_PATH . "/domain/{$filedirectory}/";
        $createFileName = "{$entityName}Dao.class.php";
        $canCreate = $this->canCreateFile ($hostdir, $createFileName);
        if (false == $canCreate) {
            return;
        }

        $str = '<?php
    /*
     * _EntityName_Dao
     */
    class _EntityName_Dao extends Dao {

    }';

        $str = str_replace("_EntityName_", $entityName, $str);
        echo "\n";
        echo $filename = ROOT_TOP_PATH . "/domain/{$filedirectory}/{$entityName}Dao.class.php";
        echo "\n";

        file_put_contents($filename, $str);
        return $str;
    }

    public function createEntityFile () {
        $tableName = $this->tableName;
        $entityName = $this->entityName;
        $filedirectory = $this->filedirectory;

        $hostdir = ROOT_TOP_PATH . "/domain/{$filedirectory}/";
        $createFileName = "{$entityName}.class.php";
        $canCreate = $this->canCreateFile ($hostdir, $createFileName);
        if (false == $canCreate) {
            return;
        }

        $fields = $this->table2array($tableName);
        $str = '<?php
    /*
     * _EntityName_
     */
    class _EntityName_ extends Entity
    {
        protected function init_keys()
        {
            $this->_keys = self::getKeysDefine();
        }

        public static function getKeysDefine()
        {
            return  array(
            _KEYS_
            );
        }

        protected function init_keys_lock()
        {
            $this->_keys_lock = array(_KEY_LOCK_);
        }

        protected function init_belongtos()
        {
            $this->_belongtos = array();_BELONGTO_
        }

        // $row = array(); _ROW_
        public static function createByBiz($row)
        {
            DBC::requireNotEmpty($row,"_EntityName_::createByBiz row cannot empty");

            $default = array();_DEFAULT_

            $row += $default;
            return new self($row);
        }

        // ====================================
        // ------------ obj method ------------
        // ====================================

        // ====================================
        // ----------- static method ----------
        // ====================================

    }
    ';

        echo $entityName;

        $str = str_replace("_EntityName_", $entityName, $str);

        // 替换 _KEYS_
        $str_keys = "";
        // 替换 _KEY_LOCK_
        $str_keys_lock = '';
        // 替换 _BELONGTO_
        $str_belongto = '';
        // 替换 _ROW_
        $str_row = "";
        // 替换 _DEFAULT_
        $default_str = "";

        $i = 0;
        foreach ($fields as $a) {

            $field = $a['field'];
            $type = $a['type'];
            $comment = $a['comment'];

            if ($field == "id" || $field == "version" || $field == "createtime" || $field == "updatetime") {
                continue;
            }

            if ($i > 0) {
                $str_keys .= "\n        ,";
            }

            $str_keys .= "'$field'    //$comment";

            $isBigint = 0;
            $isInt = 0;

            if (strpos($type, 'int') === 0 || strpos($type, 'int') > 0) {
                $isInt = 1;
            }

            if (strpos($type, 'bigint') === 0) {
                $isBigint = 1;
            }

            echo "\n $type $isBigint $isInt  ";

            if ($isBigint) {
                $str_keys_lock .= "'$field' ,";

                echo $fieldEntityName = substr($field, 0, strlen($field) - 2);
                $str_belongto .= "\n    ";
                $str_belongto .= '$this->_belongtos["' . $fieldEntityName . '"] = array ("type" => "' . ucfirst($fieldEntityName) . '", "key" => "' . $field . '" );';

            }

            if ($isInt) {
                $default_str .= "\n             ";
                $default_str .= '$default["' . $field . '"] = ' . " 0;";
            } else {
                $default_str .= "\n             ";
                $default_str .= '$default["' . $field . '"] = ' . "'';";
            }

            $str_row .= "\n";
            $str_row .= ('    // $row["' . $field . '"] = $' . $field . ';');

            $i ++;
        }

        echo "\n";
        echo $str_belongto;
        echo "\n";
        echo $str_keys_lock;
        echo "\n";

        $str = str_replace("_KEYS_", $str_keys, $str);
        $str = str_replace("_KEY_LOCK_", $str_keys_lock, $str);
        $str = str_replace("_BELONGTO_", $str_belongto, $str);
        $str = str_replace("_ROW_", $str_row, $str);
        $str = str_replace("_DEFAULT_", $default_str, $str);

        echo "\n";
        echo $filename = ROOT_TOP_PATH . "/domain/{$filedirectory}/{$entityName}.class.php";
        echo "\n";

        file_put_contents($filename, $str);
        return $str;
    }

    // 导出表定义
    private function table2array ($tableName) {
        $dbExecuter = BeanFinder::get("DbExecuter");

        // 提取字段
        $sql = "show full fields from `$tableName`";
        $rows = $dbExecuter->query($sql);

        return $rows;
    }

    private function canCreateFile ($dir, $createFileName) {
        $canCreate = true;
        $filenames = scandir($dir);
        foreach ($filenames as $name) {
            if ($name == $createFileName) {
                echo "已经生成过{$createFileName}文件";
                echo "\n";
                $canCreate = false;
                break;
            }
        }
        return $canCreate;
    }

}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Scaffold.php]=====");
print_r($argv);
$tableName = $argv[1];
$entityName = $argv[2];
$filedirectory = $argv[3];
if (empty($filedirectory)) {
    $filedirectory = "entity";
}

$a = new Scaffold($tableName, $entityName, $filedirectory);
$a->doWork();

Debug::trace("=====[cron][end][Scaffold.php]=====");

echo "\n-----end----- " . XDateTime::now();
