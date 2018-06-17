<?php

class Fix_auditresource
{

    private $includepath = '';

    private $class = '';

    public $class_method_arr = array();

    public function __construct ($includepath) {
        $this->includepath = $includepath;
    }

    public function dowork () {
        echo "[Fix_auditresource] begin ===== \n<br/>";
        $files = $this->findFiles($this->includepath);

        foreach ($files as $file) {
            foreach ($this->findClassFromAFile($file) as $class) {
                $class = strtolower($class);
                $this->class = $class;
//                 echo "[##{$class}##]\n<br/>";
                $functions = $this->findFunctions($file);
//                 print_r($functions);
//                 echo '<br/>';
            }
        }
        echo "[Fix_auditresource] end ===== \n<br/>";
    }

    private function findFunctions ($file) {
        $functions = array();

        $lines = file($file);
        $unitofwork = BeanFinder::get("UnitOfWork");

        foreach ($lines as $line) {
            if (preg_match("/^\s*public\s*function\s+do(\S+)\s*/", $line, $match)) {
                $str = $match[1];
                $str = $this->trimFunctionName($str);
                if ($str != '__construct' && $str != 'getKeysDefine') {
                    $str = strtolower($str);
                    $functions[] = $str;

                    $this->class_method_arr[$this->class . ":" . $str] = array(
                        'action' => $this->class,
                        'method' => $str);

                    $auditresource = AuditResourceDao::getByActionMethod($this->class, $str);
                    if ($auditresource instanceof AuditResource) {
                        continue;
                    }
                    echo $this->class . ' ' . $str . "\n<br/>";

                    $row = array();
                    $row["title"] = "/{$this->class}/{$str}";
                    $row["action"] = $this->class;
                    $row["method"] = $str;
                    $row["target"] = "/{$this->class}/{$str}";

                    $auditresource = AuditResource::createByBiz($row);
                }
            }

        }
        $unitofwork->commitAndInit();

        return $functions;
    }

    private function trimFunctionName ($str) {
        $str = str_replace('(', '', $str);
        $str = str_replace(')', '', $str);
        $str = str_replace('{', '', $str);
        $str = trim($str);
        return $str;
    }

    private function findClassFromAFile ($file) {
        $classes = array();
        $lines = file($file);
        foreach ($lines as $line) {
            if (preg_match("/^\s*class\s+(\S+)Action\s*/", $line, $match)) {
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

    // 递归函数
    private function findFiles ($dirname) {
        $filelist = array();
        $currentfilelist = scandir($dirname);
        foreach ($currentfilelist as $file) {
            if ($file == "." || $file == "..")
                continue;
            $file = "$dirname/$file";

            if (is_dir($file)) {
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
}
