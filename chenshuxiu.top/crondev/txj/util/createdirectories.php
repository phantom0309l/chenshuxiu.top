<?php

$arr = array(
    "wx",
    "admin",
    "audit",
    "dm",
    "doctor",
    "www"
);
foreach( $arr as $a ){
    $dir = "../../wwwroot/img/v5/page/{$a}";
    $tpl = "../../{$a}/tpl";
    //到此已经生成wx子域名目录
    createDir( $dir );
    echo "{$tpl}====\n";
    createActionDirByTpl( $tpl, $dir );

}

function createActionDirByTpl( $tpl, $basedir ){
    $handler = opendir( $tpl );
    while( ($filename = readdir($handler)) !== false )  {
        if($filename != "." && $filename != ".." && is_dir("{$tpl}/{$filename}") ) {
            //生成action层次的目录
            createDir( "{$basedir}/{$filename}" );
            createPapeDirByTpl("{$tpl}/{$filename}", "{$basedir}/{$filename}");
        }
    }
    closedir($handler);
}

function createPapeDirByTpl( $tpl, $basedir ){
    $handler = opendir( $tpl );
    while( ($filename = readdir($handler)) !== false )  {
        if($filename != "." && $filename != ".." && is_file("{$tpl}/{$filename}") ) {
            if( strpos($filename,"_") === 0 ){
                continue;
            }
            $arr = explode( ".", $filename );
            $shortname = $arr[0];
            //生成页面对应的page目录
            createDir( "{$basedir}/{$shortname}" );
            createFilesOfPapeDir($shortname, "{$basedir}/{$shortname}");
        }
    }
    closedir($handler);
}

function createFilesOfPapeDir($pagename, $basedir){
    createDir( $basedir."/img" );
    createFile("{$basedir}/{$pagename}.js");
    createFile("{$basedir}/{$pagename}.css");
}

function createDir( $dir ){
    if (! file_exists($dir)){
        mkdir($dir, 0777, true); // 不存在则创建;
        echo "done: {$dir}\n";
    }else{
        echo "has done: {$dir}\n";
    }
}

function createFile( $fileFullName ){
    if (! file_exists($fileFullName)){
        $arr = explode("/", $fileFullName);
        $name = array_pop( $arr );
        $myfile = fopen($fileFullName, "w") or die("Unable to open file!");
        fwrite($myfile, $name);
        fclose($myfile);
        echo "done: {$fileFullName}\n";
    }else{
        echo "has done: {$fileFullName}\n";
    }
}
