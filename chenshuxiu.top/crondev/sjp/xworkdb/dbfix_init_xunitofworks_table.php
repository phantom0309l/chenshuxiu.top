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

class Dbfix_init_xunitofworks_table
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

        foreach ($tablenos as $tableno) {
            echo "\n drop table xunitofworks{$tableno};";
            echo "\nCREATE TABLE `xunitofworks{$tableno}` (
  `id` bigint(20) unsigned NOT NULL,
  `version` int(11) unsigned NOT NULL DEFAULT '1',
  `createtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updatetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `randno` int(11) unsigned NOT NULL DEFAULT '0',
  `server_ip` char(15) CHARACTER SET utf8mb4 DEFAULT NULL,
  `client_ip` char(15) CHARACTER SET utf8mb4 DEFAULT NULL,
  `dev_user` varchar(32) CHARACTER SET utf8mb4 DEFAULT NULL,
  `domain` varchar(32) CHARACTER SET utf8mb4 DEFAULT NULL,
  `sub_domain` varchar(32) CHARACTER SET utf8mb4 DEFAULT NULL,
  `action_name` varchar(64) CHARACTER SET utf8mb4 DEFAULT NULL,
  `method_name` varchar(64) CHARACTER SET utf8mb4 DEFAULT NULL,
  `cacheopen` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '是否启用缓存',
  `commit_load_cnt` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT 'load实体数目',
  `commit_insert_cnt` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT 'insert数目',
  `commit_update_cnt` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT 'update数目',
  `commit_delete_cnt` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT 'delete数目',
  `method_end` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '截止时间',
  `commit_end` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '截止时间',
  `page_end` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '截止时间',
  `url` text CHARACTER SET utf8mb4,
  `referer` text CHARACTER SET utf8mb4,
  `cookie` text CHARACTER SET utf8mb4,
  `posts` text CHARACTER SET utf8mb4,
  PRIMARY KEY (`id`),
  KEY `idx_createtime` (`createtime`),
  KEY `idx_client_ip` (`client_ip`),
  KEY `idx_dev_user` (`dev_user`),
  KEY `idx_sub_domain` (`sub_domain`),
  KEY `idx_action_name_method_name` (`action_name`,`method_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;";
        }
    }
}

$process = new Dbfix_init_xunitofworks_table();
$process->dowork();
