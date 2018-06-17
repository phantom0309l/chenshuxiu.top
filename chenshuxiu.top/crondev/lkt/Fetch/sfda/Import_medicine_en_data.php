<?php
/**
 * Created by PhpStorm.
 * User: hades
 * Date: 2017/5/3
 * Time: 11:18
 */
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once(dirname(__FILE__) . "/../../../../sys/PathDefine.php");
include_once(ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

echo "\n\n-----begin----- " . XDateTime::now() . "\n\n";

$filenames = [
    "sfda_medicines_en.txt",
];
foreach ($filenames as $filename) {
    $file_path = "sfda_data/" . $filename;
    if (file_exists($file_path)) {
        $file = file($file_path);
        echo "读取文件 " . $file_path;
        echo "\n";
        foreach ($file as $line) {  // 按行读取
            $unitofwork = BeanFinder::get("UnitOfWork");
            $row = json_decode($line, true);
            $sfda_id = $row["sfda_id"] + 1000000;
            $sfda_medicine = Sfda_medicineDao::getBySfdaid($sfda_id);
            if (false == $sfda_medicine instanceof Sfda_medicine) {
                if (isset($row['isimport'])) {
                    $row['is_en'] = $row['isimport'];
                    unset($row['isimport']);
                }
                $row["sfda_id"] = $sfda_id;
                $medicine = Sfda_medicine::createByBiz($row);
                echo "插入 " . $medicine->name_common;
                echo "\n";
            } else {
                echo "sfda_id：{$sfda_id} 重复，跳过\n";
                continue;
            }
            $unitofwork->commitAndInit();
        }
    }
}
