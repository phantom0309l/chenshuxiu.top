--
-- Table structure for table `idgenerator`
--

DROP TABLE IF EXISTS `idgenerator`;
CREATE TABLE `idgenerator` (
  `nextid` bigint(20) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/* initialize datas sql */
insert into idgenerator values(100000001);

CREATE TABLE `xunitofworks` (
  `id` bigint(20) unsigned NOT NULL,
  `version` int(11) unsigned NOT NULL DEFAULT '1',
  `createtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updatetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `server_ip` char(15) NOT NULL DEFAULT '' COMMENT 'server_ip',
  `client_ip` char(15) NOT NULL DEFAULT '' COMMENT 'client_ip',
  `dev_user` varchar(32) NOT NULL DEFAULT '' COMMENT '当前环境',
  `domain` varchar(32) NOT NULL DEFAULT '' COMMENT '主域名',
  `sub_domain` varchar(32) NOT NULL DEFAULT '' COMMENT '子域名',
  `action_name` varchar(64) NOT NULL DEFAULT '' COMMENT 'action',
  `method_name` varchar(64) NOT NULL DEFAULT '' COMMENT 'method',
  `cacheopen` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '是否启用缓存',
  `commit_load_cnt` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT 'load实体数目',
  `commit_insert_cnt` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT 'insert数目',
  `commit_update_cnt` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT 'update数目',
  `commit_delete_cnt` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT 'delete数目',
  `method_end` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '截止时间',
  `commit_end` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '截止时间',
  `page_end` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '截止时间',
  `url` text NOT NULL COMMENT 'url',
  `referer` text NOT NULL COMMENT 'referer',
  `cookie` text NOT NULL COMMENT 'cookie',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

CREATE TABLE `objlogs` (
  `id` bigint(20) unsigned NOT NULL,
  `version` int(11) unsigned NOT NULL DEFAULT '1',
  `createtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updatetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `xunitofworkid` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'unitofworkid',
  `type` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '0 insert, 1 update, 2 delete',
  `objtype` varchar(64) NOT NULL DEFAULT '' COMMENT 'objtype',
  `objid` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'objid',
  `objver` int(11) unsigned NOT NULL DEFAULT '1' COMMENT 'objver',
  `content` mediumtext NOT NULL COMMENT '内容',
  PRIMARY KEY (`id`),
  KEY `idx_objtype_objid` (`objtype`,`objid`),
  KEY `idx_xunitofworkid` (`xunitofworkid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;