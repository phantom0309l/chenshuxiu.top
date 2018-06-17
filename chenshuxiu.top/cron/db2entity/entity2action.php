<?php
include_once (dirname(__FILE__) . "/../../sys/PathDefine.php");

$includepaths = array();
$includepaths[] = ROOT_TOP_PATH . "/domain/entity.fbt";

$notincludepaths = array();

$process = new Entity2ActionProcess($includepaths, $notincludepaths);
$process->dowork();

class Entity2ActionProcess
{

    private $includepaths = array();

    private $notincludepaths = array();

    private $notIncludeStr = "";

    private $subsysName = "Admin";

    public function __construct ($includepaths, $notincludepaths = array(), $notIncludeStr = ".svn") {
        $this->includepaths = $includepaths;
        $this->notincludepaths = $notincludepaths;
        $this->notIncludeStr = $notIncludeStr;
    }

    public function dowork () {
        $paths = $this->includepaths;
        $classes = array();

        foreach ($paths as $path) {
            $files = $this->findFiles($path);
            foreach ($this->findClasses($files) as $class => $filename) {
                $classes[] = $class;
            }
        }

        $this->workArray($classes);

        echo "\ngenerator action file successed!\n";
        echo "time= " . @date("Y-m-d H:i:s", time());
    }

    private function workArray ($classes) {
        foreach ($classes as $className) {
            $this->workOne($className);
        }
    }

    private function workOne ($className) {
        echo "\n $className";
        $subsysName = $this->subsysName;

        $str = '<?php

class XXXMgrAction extends YYYBaseAction {

    // 构造函数，初始化了很多数据
    public function __construct() {
        parent::__construct ();
    }

    // 列表
    public function doList() {
        $xxxs = Dao::getEntityListByCond ( "XXX" );

        XContext::setValue ( "xxxs", $xxxs );
        return self::SUCCESS;
    }

    // 详情
    public function doOne() {
        $xxxid = XRequest::getValue ( "xxxid" , 0 );

        $xxx = XXX::getById ( $xxxid );

        XContext::setValue ( "xxx", $xxx );
        return self::SUCCESS;
    }
}
        ';

        $str = str_replace("XXX", $className, $str);
        $str = str_replace("xxx", strtolower($className), $str);
        $str = str_replace("YYY", $subsysName, $str);

        echo "\n";
        echo $filename = ROOT_TOP_PATH . "/cron/db2entity/action.new/{$className}MgrAction.php";
        echo "\n";

        file_put_contents($filename, $str);

        $tplPath = ROOT_TOP_PATH . "/" . strtolower($subsysName) . "/tpl/" . strtolower($className) . "mgr";

        if (! is_dir($tplPath)) {
            mkdir($tplPath, 0775);
        }

        $filename = $tplPath . "/list.tpl.php";
        if (! file_exists($filename)) {
            touch($filename);
            chmod($filename, 0664);
        }

        $filename = $tplPath . "/one.tpl.php";
        if (! file_exists($filename)) {
            touch($filename);
            chmod($filename, 0664);
        }

        return $str;
    }

    // 递归函数
    private function findFiles ($dirname) {
        $filelist = array();
        $currentfilelist = scandir($dirname);
        foreach ($currentfilelist as $file) {
            if ($file == "." || $file == "..")
                continue;
            $file = "$dirname/$file";

            if (is_dir($file) && array_search($file, $this->notincludepaths) === FALSE && strstr($file, $this->notIncludeStr) === false) {
                foreach ($this->findFiles($file) as $tmpFile) {
                    $filelist[] = $tmpFile;
                }
                continue;
            }
            if (preg_match("/.+\.php$/", $file))
                $filelist[] = $file;
        }
        return $filelist;
    }

    private function findClasses ($files) {
        $classes = array();
        foreach ($files as $file) {
            foreach ($this->findClassFromAFile($file) as $class) {
                if (empty($classes[$class]))
                    $classes[$class] = $file;
                else
                    echo "Repeatedly Class $class => $file\n";
            }
        }
        return $classes;
    }

    private function findClassFromAFile ($file) {
        $classes = array();
        $lines = file($file);
        foreach ($lines as $line) {
            if (preg_match("/^\s*class\s+(\S+)\s*/", $line, $match)) {
                $classes[] = $match[1];
            }
            if (preg_match("/^\s*abstract\s*class\s+(\S+)\s*/", $line, $match)) {
                $classes[] = $match[1];
            }
            if (preg_match("/^\s*interface\s+(\S+)\s*/", $line, $match)) {
                $classes[] = $match[1];
            }
        }
        return $classes;
    }
}
