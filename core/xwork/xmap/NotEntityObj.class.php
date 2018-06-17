<?php
// 非实体对象,可继承,具有belongto功能
class NotEntityObj
{

    public $_keys;

    public $_cols;

    public $_belongtos;

    public $_objname;

    protected function init_belongtos () {
        $this->_belongtos = array();
    }

    public function __construct ($row, $belongtos = array(), $objname = "NotEntityObj") {
        $this->_keys = array_keys($row);
        $this->_cols = $row;
        $this->_objname = $objname;

        if (! empty($belongtos)) {
            $this->_belongtos = $belongtos;
        } else {
            $this->init_belongtos();
        }

        // 注册
        BeanFinder::get("UnitOfWork")->registerBelongtos($this, $this->_belongtos);
    }

    // 统一数据获取接口，如果键名错误，在开发环境中报错
    public function __get ($key) {
        $method = "get$key";
        if (method_exists($this, $method)) {
            return $this->$method();
        }

        if ($key == "objname") {
            return $this->_objname;
        }

        if (in_array($key, $this->_keys)) {
            return $this->_cols[$key];
        }

        // 检查belongto关系中是否存在此key
        $belongto = $this->_belongtos[strtolower($key)];
        if ($belongto) {
            $dao = new Dao($belongto["type"]);
            $idstr = $belongto["key"];
            return $dao->getById($this->$idstr);
        }

        DBC::requireTrue(in_array($key, $this->_keys), "$key not in _keys");
    }

    // 统一数据设置接口，一般没啥用途
    public function __set ($key, $value) {
        $method = "set$key";
        if (method_exists($this, $method)) {
            return $this->$method($value);
        }

        DBC::requireTrue(in_array($key, $this->_keys), "$key not in _keys");
        DBC::requireNotNull($value, "$key = null");

        return $this->_cols[$key] = $value;
    }

    // //////////////////////////////////////////////
    // 构造对象数组
    public static function createObjs ($rows, $belongtos = array(), $objname = "notnameobj") {
        $arr = array();
        foreach ($rows as $a) {
            $arr[] = new NotEntityObj($a, $belongtos, $objname);
        }

        return $arr;
    }
}