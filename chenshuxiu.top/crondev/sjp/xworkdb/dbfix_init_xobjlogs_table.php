<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "3048M");
include_once (dirname(__FILE__) . "/../../../sys/PathDefine.php");
include_once (ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

class Dbfix_init_xobjlogs_table
{

    public function dowork () {
        $tablenos = [];
        $tablenos[] = '201611';
        $tablenos[] = '201612';
        for ($i = 1; $i < 13; $i ++) {
            $tablenos[] = "2017" . sprintf("%02d", $i);
        }

        for ($i = 1; $i < 13; $i ++) {
            $tablenos[] = "2018" . sprintf("%02d", $i);
        }

        for ($i = 0; $i < 256; $i ++) {
            $tablenos[] = 1000 + $i;
        }

        foreach ($tablenos as $tableno) {
            echo "\n drop table xobjlogs{$tableno};";
            echo "\n CREATE TABLE `xobjlogs{$tableno}` (
  `id` bigint(20) unsigned NOT NULL,
  `version` int(11) unsigned NOT NULL DEFAULT '1',
  `createtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updatetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `randno` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '日期暂列',
  `xunitofworkid` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'unitofworkid',
  `type` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '0 insert, 1 update, 2 delete',
  `objtype` varchar(64) NOT NULL DEFAULT '' COMMENT 'objtype',
  `objid` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'objid',
  `objver` int(11) unsigned NOT NULL DEFAULT '1' COMMENT 'objver',
  `content` mediumtext NOT NULL COMMENT '内容',
  `randno_fix` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'md5暂列',
  PRIMARY KEY (`id`),
  KEY `idx_xunitofworkid` (`xunitofworkid`),
  KEY `idx_objtype_objid` (`objtype`,`objid`),
  KEY `idx_randno_fix` (`randno_fix`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;";
        }
    }
}

$process = new Dbfix_init_xobjlogs_table();
$process->dowork();
