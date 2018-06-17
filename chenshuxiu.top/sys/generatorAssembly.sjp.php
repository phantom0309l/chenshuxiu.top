<?php
include_once 'PathDefine.php';
include_once (ROOT_TOP_PATH . "/../core/xutil/GeneratorAssemblyProcess.class.php");

$assemblyFileName = ROOT_TOP_PATH . "/cron/sjpAssembly.php";

$includepaths = array();
// $includepaths[] = ROOT_TOP_PATH . "/../core";
$includepaths[] = ROOT_TOP_PATH . "/domain";

$notincludepaths = array();
$notincludepaths[] = ROOT_TOP_PATH . "/../core/util/simpletest";
$notincludepaths[] = ROOT_TOP_PATH . "/../core/tools";
$notincludepaths[] = ROOT_TOP_PATH . "/domain/entity.clan";
$notincludepaths[] = ROOT_TOP_PATH . "/domain/library";
$notincludepaths[] = ROOT_TOP_PATH . "/domain/task";
$notincludepaths[] = ROOT_TOP_PATH . "/domain/tpl";
$notincludepaths[] = ROOT_TOP_PATH . "/domain/wenda.entity";
$notincludepaths[] = ROOT_TOP_PATH . "/domain/wenda.obj";
$notincludepaths[] = ROOT_TOP_PATH . "/domain/third.party/service.mipush";
$notincludepaths[] = ROOT_TOP_PATH . "/domain/third.party/service.umengpush";
$notincludepaths[] = ROOT_TOP_PATH . "/domain/third.party/TencentYoutuyun";
$notincludepaths[] = ROOT_TOP_PATH . "/domain/third.party/WxpayAPI_php_v3";
$notincludepaths[] = ROOT_TOP_PATH . "/domain/third.party/yuntongxun_php_v2.7";

$notincludepaths[] = ROOT_TOP_PATH . "/cron/bak";
$notincludepaths[] = ROOT_TOP_PATH . "/cron/db2entity";

$process = new CheckProcess($assemblyFileName, $includepaths, $notincludepaths);
$process->dowork();

class CheckProcess
{

    private $assemblyFileName = "";

    private $includepaths = array();

    private $notincludepaths = array();

    private $notIncludeStr = "";

    private $func_cnt = 0;

    private $result = array();

    public function __construct ($assemblyFileName, $includepaths, $notincludepaths = array(), $notIncludeStr = ".svn") {
        $this->assemblyFileName = $assemblyFileName;
        $this->includepaths = $includepaths;
        $this->notincludepaths = $notincludepaths;
        $this->notIncludeStr = $notIncludeStr;
    }

    public function dowork () {

        echo "\n=========begin==========\n";

        $paths = $this->includepaths;

        $classes = array();
        $functions = array();

        foreach ($paths as $path) {
            $files = $this->findFiles($path);
            foreach ($this->findClasses($files) as $class => $filename) {
                if (empty($classes[$class])) {
                    $classes[$class] = $filename;
                    // echo "\n{$class} => $filename";
                    $functions[$class] = $this->findFunctions($class, $filename);
                } else {
                    echo "Repeatedly Class $class => $filename\n";
                }
            }
        }

        echo "== [ func_cnt=";
        echo $this->func_cnt;
        echo " ]==\n";

        $files = $this->findFiles(ROOT_TOP_PATH);
        // $files = $this->findFiles($path);

        $file_cnt = count($files);

        echo "== [ file_cnt=";
        echo $file_cnt;
        echo " ]==\n";

        foreach ($files as $i => $file) {
            echo "\n {$i} / {$file_cnt} ";
            echo $file;
            $this->checkFileFunction($file, $functions);
        }

        ksort($this->result);

        echo "\n\n=========[no_call][begin]==========\n";

        foreach ($this->result as $k => $arr) {
            if (count($arr) < 1) {
                echo "\n$k";
            }
        }

        echo "\n\n=========[no_call][end]==========\n";

        echo "\n\n=========[yes_call][begin]==========\n";

        foreach ($this->result as $k => $arr) {
            foreach ($arr as $f) {
                echo "\n{$k} => {$f}";
            }
        }

        echo "\n\n=========[yes_call][end]==========\n";

        echo "\n\n=========end==========\n";
    }

    private function checkFileFunction ($file, $functions) {

        $lines = file($file);

        foreach ($lines as $line) {
            // echo "=";
            foreach ($functions as $class => $funcs) {
                // echo $class;
                foreach ($funcs as $k => $vs) {
                    // echo "-";
                    foreach ($vs as $v) {
                        // echo ".";
                        // echo "\n$class => $k => $v | ";

                        $pattern = '';
                        $patternFix = '';
                        $sss = "";

                        if ($k == 'public_static') {
                            $pattern = "/({$class}::{$v})/i";
                            $patternFix = "/(self::{$v})/i";

                            $sss = "{$class}::{$v}";
                        }

                        if ($k == 'public') {
                            $pattern = "/(->{$v})/i";

                            $sss = "{$class}->{$v}";
                        }

                        if (false == is_array($this->result[$sss])) {
                            $this->result[$sss] = array();
                        }

                        if (preg_match($pattern, $line, $match)) {
                            $str = $match[1];
                            $this->result[$sss][] = $file;

                            echo ".";
                        }

                        // 检测 self::xxx
                        if ($patternFix && strpos(strtolower($file), strtolower($class) . ".") > 0) {
                            if (preg_match($patternFix, $line, $match)) {
                                $str = $match[1];
                                $this->result[$sss][] = $file;
                                echo "+";
                            }
                        }

                    }
                }
            }
        }

        if (false == empty($this->result)) {
            // print_r($this->result);
            // exit();
        }
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

    private function findFunctions ($class, $file) {
        $functions = array();

        $lines = file($file);

        foreach ($lines as $line) {
            if (preg_match("/^\s*public\s*static\s*function\s+(\S+)\s*/", $line, $match)) {
                $str = $match[1];
                $str = $this->trimFunctionName($str);
                if ($str != '__construct' && $str != 'getKeysDefine') {
                    $functions['public_static'][] = $str;

                    $this->func_cnt ++;
                }
            }

            // if (preg_match("/^\s*public\s*function\s+(\S+)\s*/", $line,
            // $match)) {
            // $str = $match[1];
            // $str = $this->trimFunctionName($str);

            // if ($str != '__construct') {
            // $functions['public'][] = $str;

            // $this->func_cnt ++;
            // }
            // }

            // if (preg_match("/^\s*protected\s*static\s*function\s+(\S+)\s*/",
            // $line, $match)) {
            // $str = $match[1];
            // $str = $this->trimFunctionName($str);
            // // $functions['protected_static'][] = $str;
            // }

            // if (preg_match("/^\s*private\s*static\s*function\s+(\S+)\s*/",
            // $line, $match)) {
            // $str = $match[1];
            // $str = $this->trimFunctionName($str);
            // // $functions['private_static'][] = $str;
            // }

            // if (preg_match("/^\s*protected\s*function\s+(\S+)\s*/", $line,
            // $match)) {
            // $str = $match[1];
            // $str = $this->trimFunctionName($str);
            // // $functions['protected'][] = $str;
            // }

            // if (preg_match("/^\s*private\s*function\s+(\S+)\s*/", $line,
            // $match)) {
            // $str = $match[1];
            // $str = $this->trimFunctionName($str);
            // // $functions['private'][] = $str;
            // }
        }

        return $functions;
    }

    private function trimFunctionName ($str) {
        $str = str_replace('(', '', $str);
        $str = str_replace(')', '', $str);
        $str = trim($str);
        return $str;
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
}