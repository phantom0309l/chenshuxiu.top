<?php
/*
 * 目标是产生如下文件: function __autoload($classname) { static $classpath = array(
 * //test "GroupTest" => "test/simpletest/test_case.php", "BeanFinder" =>
 * "class/dao/beanfinder.class.php", "StubIDGeneratorFactory" =>
 * "class/dao/idgenerator.class.php", ); if (!empty($classpath[$classname])) {
 * include_once(ROOT_TOP_PATH.$classpath[$classname]); } } ?>
 */

include_once (dirname(__FILE__) . "/File.class.php");

class GeneratorAssemblyProcess
{

    private $assemblyFileName = "";

    private $includepaths = array();

    private $notincludepaths = array();

    private $notIncludeStr = "";

    public function __construct ($assemblyFileName, $includepaths, $notincludepaths = array(), $notIncludeStr = ".svn") {
        $this->assemblyFileName = $assemblyFileName;
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
                if (empty($classes[$class]))
                    $classes[$class] = $filename;
                else {
                    echo "Repeatedly Class $class => $filename\n";
                }
            }
        }

        $this->generatorAssemblyFile($this->assemblyFileName, $classes);

        echo "\ngenerator assembly file successed!\n";
        echo "time= " . @date("Y-m-d H:i:s", time());
    }

    private function generatorAssemblyFile ($fileName, $classes) {
        $assemblyfile = new File($fileName);
        $assemblyfile->open("w+");
        $assemblyfile->write("<?php\n");
        $assemblyfile->write("    function __autoload(\$classname)\n");
        $assemblyfile->write("    {\n");
        $assemblyfile->write("        static \$classpath = array(\n");
        foreach ($classes as $key => $value) {
            $prefix = ROOT_TOP_PATH . "/";
            $value = substr_replace($value, "", 0, strlen($prefix));
            $assemblyfile->write("            \"$key\" => \"$value\",\n");
        }
        $assemblyfile->write("        );\n");
        $assemblyfile->write("        if (!empty(\$classpath[\$classname]))\n");
        $assemblyfile->write("        {\n");
        $assemblyfile->write("            include_once(ROOT_TOP_PATH.'/'.\$classpath[\$classname]);\n");
        $assemblyfile->write("        }\n");
        $assemblyfile->write("    }\n");

        // *
        // 生成小写路径
        $assemblyfile->write("\n    \$lowerclasspath = array(\n");
        foreach ($classes as $key => $value) {
            $assemblyfile->write("            \"" . strtolower($key) . "\" => \"$key\",\n");
        }
        $assemblyfile->write("      );\n");
        // */

        $assemblyfile->write("?>");
        $assemblyfile->close();
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
