
==== 20000101 - 20161101 ====
select count(*) from statdb.objlogs where xunitofworkid >= 946656000000000000 and xunitofworkid < 1477929600000000000
====+++====
select count(*) from xobjlogs200001 

==== 20000101 - 20000111 ====
select count(*) from statdb.objlogs where xunitofworkid >= 946656000000000000 and xunitofworkid < 947520000000000000
insert into xobjlogs200001 (`id`,`version`,`createtime`,`updatetime`,`randno`,`xunitofworkid`,`type`,`objtype`,`objid`,`objver`,`content`,`randno_fix`) select id, version, createtime, updatetime, 200001, xunitofworkid, type, objtype, objid, objver,content,'' as randno_fix from statdb.objlogs where xunitofworkid >= 946656000000000000 and xunitofworkid < 947520000000000000
select count(*) from xobjlogs200001 

==== 20000111 - 20000121 ====
select count(*) from statdb.objlogs where xunitofworkid >= 947520000000000000 and xunitofworkid < 948384000000000000
insert into xobjlogs200001 (`id`,`version`,`createtime`,`updatetime`,`randno`,`xunitofworkid`,`type`,`objtype`,`objid`,`objver`,`content`,`randno_fix`) select id, version, createtime, updatetime, 200001, xunitofworkid, type, objtype, objid, objver,content,'' as randno_fix from statdb.objlogs where xunitofworkid >= 947520000000000000 and xunitofworkid < 948384000000000000
select count(*) from xobjlogs200001 

==== 20000121 - 20161101 ====
select count(*) from statdb.objlogs where xunitofworkid >= 948384000000000000 and xunitofworkid < 1477929600000000000
insert into xobjlogs200001 (`id`,`version`,`createtime`,`updatetime`,`randno`,`xunitofworkid`,`type`,`objtype`,`objid`,`objver`,`content`,`randno_fix`) select id, version, createtime, updatetime, 200001, xunitofworkid, type, objtype, objid, objver,content,'' as randno_fix from statdb.objlogs where xunitofworkid >= 948384000000000000 and xunitofworkid < 1477929600000000000
select count(*) from xobjlogs200001 


==== 20161101 - 20161201 ====
select count(*) from statdb.objlogs where xunitofworkid >= 1477929600000000000 and xunitofworkid < 1480521600000000000
====+++====
select count(*) from xobjlogs201611 

==== 20161101 - 20161111 ====
select count(*) from statdb.objlogs where xunitofworkid >= 1477929600000000000 and xunitofworkid < 1478793600000000000
insert into xobjlogs201611 (`id`,`version`,`createtime`,`updatetime`,`randno`,`xunitofworkid`,`type`,`objtype`,`objid`,`objver`,`content`,`randno_fix`) select id, version, createtime, updatetime, 201611, xunitofworkid, type, objtype, objid, objver,content,'' as randno_fix from statdb.objlogs where xunitofworkid >= 1477929600000000000 and xunitofworkid < 1478793600000000000
select count(*) from xobjlogs201611 

==== 20161111 - 20161121 ====
select count(*) from statdb.objlogs where xunitofworkid >= 1478793600000000000 and xunitofworkid < 1479657600000000000
insert into xobjlogs201611 (`id`,`version`,`createtime`,`updatetime`,`randno`,`xunitofworkid`,`type`,`objtype`,`objid`,`objver`,`content`,`randno_fix`) select id, version, createtime, updatetime, 201611, xunitofworkid, type, objtype, objid, objver,content,'' as randno_fix from statdb.objlogs where xunitofworkid >= 1478793600000000000 and xunitofworkid < 1479657600000000000
select count(*) from xobjlogs201611 

==== 20161121 - 20161201 ====
select count(*) from statdb.objlogs where xunitofworkid >= 1479657600000000000 and xunitofworkid < 1480521600000000000
insert into xobjlogs201611 (`id`,`version`,`createtime`,`updatetime`,`randno`,`xunitofworkid`,`type`,`objtype`,`objid`,`objver`,`content`,`randno_fix`) select id, version, createtime, updatetime, 201611, xunitofworkid, type, objtype, objid, objver,content,'' as randno_fix from statdb.objlogs where xunitofworkid >= 1479657600000000000 and xunitofworkid < 1480521600000000000
select count(*) from xobjlogs201611 


==== 20161201 - 20170101 ====
select count(*) from statdb.objlogs where xunitofworkid >= 1480521600000000000 and xunitofworkid < 1483200000000000000
====+++====
select count(*) from xobjlogs201612 

==== 20161201 - 20161211 ====
select count(*) from statdb.objlogs where xunitofworkid >= 1480521600000000000 and xunitofworkid < 1481385600000000000
insert into xobjlogs201612 (`id`,`version`,`createtime`,`updatetime`,`randno`,`xunitofworkid`,`type`,`objtype`,`objid`,`objver`,`content`,`randno_fix`) select id, version, createtime, updatetime, 201612, xunitofworkid, type, objtype, objid, objver,content,'' as randno_fix from statdb.objlogs where xunitofworkid >= 1480521600000000000 and xunitofworkid < 1481385600000000000
select count(*) from xobjlogs201612 

==== 20161211 - 20161221 ====
select count(*) from statdb.objlogs where xunitofworkid >= 1481385600000000000 and xunitofworkid < 1482249600000000000
insert into xobjlogs201612 (`id`,`version`,`createtime`,`updatetime`,`randno`,`xunitofworkid`,`type`,`objtype`,`objid`,`objver`,`content`,`randno_fix`) select id, version, createtime, updatetime, 201612, xunitofworkid, type, objtype, objid, objver,content,'' as randno_fix from statdb.objlogs where xunitofworkid >= 1481385600000000000 and xunitofworkid < 1482249600000000000
select count(*) from xobjlogs201612 

==== 20161221 - 20170101 ====
select count(*) from statdb.objlogs where xunitofworkid >= 1482249600000000000 and xunitofworkid < 1483200000000000000
insert into xobjlogs201612 (`id`,`version`,`createtime`,`updatetime`,`randno`,`xunitofworkid`,`type`,`objtype`,`objid`,`objver`,`content`,`randno_fix`) select id, version, createtime, updatetime, 201612, xunitofworkid, type, objtype, objid, objver,content,'' as randno_fix from statdb.objlogs where xunitofworkid >= 1482249600000000000 and xunitofworkid < 1483200000000000000
select count(*) from xobjlogs201612 


==== 20170101 - 20170201 ====
select count(*) from statdb.objlogs where xunitofworkid >= 1483200000000000000 and xunitofworkid < 1485878400000000000
====+++====
select count(*) from xobjlogs201701 

==== 20170101 - 20170111 ====
select count(*) from statdb.objlogs where xunitofworkid >= 1483200000000000000 and xunitofworkid < 1484064000000000000
insert into xobjlogs201701 (`id`,`version`,`createtime`,`updatetime`,`randno`,`xunitofworkid`,`type`,`objtype`,`objid`,`objver`,`content`,`randno_fix`) select id, version, createtime, updatetime, 201701, xunitofworkid, type, objtype, objid, objver,content,'' as randno_fix from statdb.objlogs where xunitofworkid >= 1483200000000000000 and xunitofworkid < 1484064000000000000
select count(*) from xobjlogs201701 

==== 20170111 - 20170121 ====
select count(*) from statdb.objlogs where xunitofworkid >= 1484064000000000000 and xunitofworkid < 1484928000000000000
insert into xobjlogs201701 (`id`,`version`,`createtime`,`updatetime`,`randno`,`xunitofworkid`,`type`,`objtype`,`objid`,`objver`,`content`,`randno_fix`) select id, version, createtime, updatetime, 201701, xunitofworkid, type, objtype, objid, objver,content,'' as randno_fix from statdb.objlogs where xunitofworkid >= 1484064000000000000 and xunitofworkid < 1484928000000000000
select count(*) from xobjlogs201701 

==== 20170121 - 20170201 ====
select count(*) from statdb.objlogs where xunitofworkid >= 1484928000000000000 and xunitofworkid < 1485878400000000000
insert into xobjlogs201701 (`id`,`version`,`createtime`,`updatetime`,`randno`,`xunitofworkid`,`type`,`objtype`,`objid`,`objver`,`content`,`randno_fix`) select id, version, createtime, updatetime, 201701, xunitofworkid, type, objtype, objid, objver,content,'' as randno_fix from statdb.objlogs where xunitofworkid >= 1484928000000000000 and xunitofworkid < 1485878400000000000
select count(*) from xobjlogs201701 


==== 20170201 - 20170301 ====
select count(*) from statdb.objlogs where xunitofworkid >= 1485878400000000000 and xunitofworkid < 1488297600000000000
====+++====
select count(*) from xobjlogs201702 

==== 20170201 - 20170211 ====
select count(*) from statdb.objlogs where xunitofworkid >= 1485878400000000000 and xunitofworkid < 1486742400000000000
insert into xobjlogs201702 (`id`,`version`,`createtime`,`updatetime`,`randno`,`xunitofworkid`,`type`,`objtype`,`objid`,`objver`,`content`,`randno_fix`) select id, version, createtime, updatetime, 201702, xunitofworkid, type, objtype, objid, objver,content,'' as randno_fix from statdb.objlogs where xunitofworkid >= 1485878400000000000 and xunitofworkid < 1486742400000000000
select count(*) from xobjlogs201702 

==== 20170211 - 20170221 ====
select count(*) from statdb.objlogs where xunitofworkid >= 1486742400000000000 and xunitofworkid < 1487606400000000000
insert into xobjlogs201702 (`id`,`version`,`createtime`,`updatetime`,`randno`,`xunitofworkid`,`type`,`objtype`,`objid`,`objver`,`content`,`randno_fix`) select id, version, createtime, updatetime, 201702, xunitofworkid, type, objtype, objid, objver,content,'' as randno_fix from statdb.objlogs where xunitofworkid >= 1486742400000000000 and xunitofworkid < 1487606400000000000
select count(*) from xobjlogs201702 

==== 20170221 - 20170301 ====
select count(*) from statdb.objlogs where xunitofworkid >= 1487606400000000000 and xunitofworkid < 1488297600000000000
insert into xobjlogs201702 (`id`,`version`,`createtime`,`updatetime`,`randno`,`xunitofworkid`,`type`,`objtype`,`objid`,`objver`,`content`,`randno_fix`) select id, version, createtime, updatetime, 201702, xunitofworkid, type, objtype, objid, objver,content,'' as randno_fix from statdb.objlogs where xunitofworkid >= 1487606400000000000 and xunitofworkid < 1488297600000000000
select count(*) from xobjlogs201702 


==== 20170301 - 20170401 ====
select count(*) from statdb.objlogs where xunitofworkid >= 1488297600000000000 and xunitofworkid < 1490976000000000000
====+++====
select count(*) from xobjlogs201703 

==== 20170301 - 20170311 ====
select count(*) from statdb.objlogs where xunitofworkid >= 1488297600000000000 and xunitofworkid < 1489161600000000000
insert into xobjlogs201703 (`id`,`version`,`createtime`,`updatetime`,`randno`,`xunitofworkid`,`type`,`objtype`,`objid`,`objver`,`content`,`randno_fix`) select id, version, createtime, updatetime, 201703, xunitofworkid, type, objtype, objid, objver,content,'' as randno_fix from statdb.objlogs where xunitofworkid >= 1488297600000000000 and xunitofworkid < 1489161600000000000
select count(*) from xobjlogs201703 

==== 20170311 - 20170321 ====
select count(*) from statdb.objlogs where xunitofworkid >= 1489161600000000000 and xunitofworkid < 1490025600000000000
insert into xobjlogs201703 (`id`,`version`,`createtime`,`updatetime`,`randno`,`xunitofworkid`,`type`,`objtype`,`objid`,`objver`,`content`,`randno_fix`) select id, version, createtime, updatetime, 201703, xunitofworkid, type, objtype, objid, objver,content,'' as randno_fix from statdb.objlogs where xunitofworkid >= 1489161600000000000 and xunitofworkid < 1490025600000000000
select count(*) from xobjlogs201703 

==== 20170321 - 20170401 ====
select count(*) from statdb.objlogs where xunitofworkid >= 1490025600000000000 and xunitofworkid < 1490976000000000000
insert into xobjlogs201703 (`id`,`version`,`createtime`,`updatetime`,`randno`,`xunitofworkid`,`type`,`objtype`,`objid`,`objver`,`content`,`randno_fix`) select id, version, createtime, updatetime, 201703, xunitofworkid, type, objtype, objid, objver,content,'' as randno_fix from statdb.objlogs where xunitofworkid >= 1490025600000000000 and xunitofworkid < 1490976000000000000
select count(*) from xobjlogs201703 


==== 20170401 - 20170501 ====
select count(*) from statdb.objlogs where xunitofworkid >= 1490976000000000000 and xunitofworkid < 1493568000000000000
====+++====
select count(*) from xobjlogs201704 

==== 20170401 - 20170411 ====
select count(*) from statdb.objlogs where xunitofworkid >= 1490976000000000000 and xunitofworkid < 1491840000000000000
insert into xobjlogs201704 (`id`,`version`,`createtime`,`updatetime`,`randno`,`xunitofworkid`,`type`,`objtype`,`objid`,`objver`,`content`,`randno_fix`) select id, version, createtime, updatetime, 201704, xunitofworkid, type, objtype, objid, objver,content,'' as randno_fix from statdb.objlogs where xunitofworkid >= 1490976000000000000 and xunitofworkid < 1491840000000000000
select count(*) from xobjlogs201704 

==== 20170411 - 20170421 ====
select count(*) from statdb.objlogs where xunitofworkid >= 1491840000000000000 and xunitofworkid < 1492704000000000000
insert into xobjlogs201704 (`id`,`version`,`createtime`,`updatetime`,`randno`,`xunitofworkid`,`type`,`objtype`,`objid`,`objver`,`content`,`randno_fix`) select id, version, createtime, updatetime, 201704, xunitofworkid, type, objtype, objid, objver,content,'' as randno_fix from statdb.objlogs where xunitofworkid >= 1491840000000000000 and xunitofworkid < 1492704000000000000
select count(*) from xobjlogs201704 

==== 20170421 - 20170501 ====
select count(*) from statdb.objlogs where xunitofworkid >= 1492704000000000000 and xunitofworkid < 1493568000000000000
insert into xobjlogs201704 (`id`,`version`,`createtime`,`updatetime`,`randno`,`xunitofworkid`,`type`,`objtype`,`objid`,`objver`,`content`,`randno_fix`) select id, version, createtime, updatetime, 201704, xunitofworkid, type, objtype, objid, objver,content,'' as randno_fix from statdb.objlogs where xunitofworkid >= 1492704000000000000 and xunitofworkid < 1493568000000000000
select count(*) from xobjlogs201704 


==== 20170501 - 20170601 ====
select count(*) from statdb.objlogs where xunitofworkid >= 1493568000000000000 and xunitofworkid < 1496246400000000000
====+++====
select count(*) from xobjlogs201705 

==== 20170501 - 20170511 ====
select count(*) from statdb.objlogs where xunitofworkid >= 1493568000000000000 and xunitofworkid < 1494432000000000000
insert into xobjlogs201705 (`id`,`version`,`createtime`,`updatetime`,`randno`,`xunitofworkid`,`type`,`objtype`,`objid`,`objver`,`content`,`randno_fix`) select id, version, createtime, updatetime, 201705, xunitofworkid, type, objtype, objid, objver,content,'' as randno_fix from statdb.objlogs where xunitofworkid >= 1493568000000000000 and xunitofworkid < 1494432000000000000
select count(*) from xobjlogs201705 

==== 20170511 - 20170521 ====
select count(*) from statdb.objlogs where xunitofworkid >= 1494432000000000000 and xunitofworkid < 1495296000000000000
insert into xobjlogs201705 (`id`,`version`,`createtime`,`updatetime`,`randno`,`xunitofworkid`,`type`,`objtype`,`objid`,`objver`,`content`,`randno_fix`) select id, version, createtime, updatetime, 201705, xunitofworkid, type, objtype, objid, objver,content,'' as randno_fix from statdb.objlogs where xunitofworkid >= 1494432000000000000 and xunitofworkid < 1495296000000000000
select count(*) from xobjlogs201705 

==== 20170521 - 20170601 ====
select count(*) from statdb.objlogs where xunitofworkid >= 1495296000000000000 and xunitofworkid < 1496246400000000000
insert into xobjlogs201705 (`id`,`version`,`createtime`,`updatetime`,`randno`,`xunitofworkid`,`type`,`objtype`,`objid`,`objver`,`content`,`randno_fix`) select id, version, createtime, updatetime, 201705, xunitofworkid, type, objtype, objid, objver,content,'' as randno_fix from statdb.objlogs where xunitofworkid >= 1495296000000000000 and xunitofworkid < 1496246400000000000
select count(*) from xobjlogs201705 


==== 20170601 - 20170701 ====
select count(*) from statdb.objlogs where xunitofworkid >= 1496246400000000000 and xunitofworkid < 1498838400000000000
====+++====
select count(*) from xobjlogs201706 

==== 20170601 - 20170611 ====
select count(*) from statdb.objlogs where xunitofworkid >= 1496246400000000000 and xunitofworkid < 1497110400000000000
insert into xobjlogs201706 (`id`,`version`,`createtime`,`updatetime`,`randno`,`xunitofworkid`,`type`,`objtype`,`objid`,`objver`,`content`,`randno_fix`) select id, version, createtime, updatetime, 201706, xunitofworkid, type, objtype, objid, objver,content,'' as randno_fix from statdb.objlogs where xunitofworkid >= 1496246400000000000 and xunitofworkid < 1497110400000000000
select count(*) from xobjlogs201706 

==== 20170611 - 20170621 ====
select count(*) from statdb.objlogs where xunitofworkid >= 1497110400000000000 and xunitofworkid < 1497974400000000000
insert into xobjlogs201706 (`id`,`version`,`createtime`,`updatetime`,`randno`,`xunitofworkid`,`type`,`objtype`,`objid`,`objver`,`content`,`randno_fix`) select id, version, createtime, updatetime, 201706, xunitofworkid, type, objtype, objid, objver,content,'' as randno_fix from statdb.objlogs where xunitofworkid >= 1497110400000000000 and xunitofworkid < 1497974400000000000
select count(*) from xobjlogs201706 

==== 20170621 - 20170701 ====
select count(*) from statdb.objlogs where xunitofworkid >= 1497974400000000000 and xunitofworkid < 1498838400000000000
insert into xobjlogs201706 (`id`,`version`,`createtime`,`updatetime`,`randno`,`xunitofworkid`,`type`,`objtype`,`objid`,`objver`,`content`,`randno_fix`) select id, version, createtime, updatetime, 201706, xunitofworkid, type, objtype, objid, objver,content,'' as randno_fix from statdb.objlogs where xunitofworkid >= 1497974400000000000 and xunitofworkid < 1498838400000000000
select count(*) from xobjlogs201706 


==== 20170701 - 20170801 ====
select count(*) from statdb.objlogs where xunitofworkid >= 1498838400000000000 and xunitofworkid < 1501516800000000000
====+++====
select count(*) from xobjlogs201707 

==== 20170701 - 20170711 ====
select count(*) from statdb.objlogs where xunitofworkid >= 1498838400000000000 and xunitofworkid < 1499702400000000000
insert into xobjlogs201707 (`id`,`version`,`createtime`,`updatetime`,`randno`,`xunitofworkid`,`type`,`objtype`,`objid`,`objver`,`content`,`randno_fix`) select id, version, createtime, updatetime, 201707, xunitofworkid, type, objtype, objid, objver,content,'' as randno_fix from statdb.objlogs where xunitofworkid >= 1498838400000000000 and xunitofworkid < 1499702400000000000
select count(*) from xobjlogs201707 

==== 20170711 - 20170721 ====
select count(*) from statdb.objlogs where xunitofworkid >= 1499702400000000000 and xunitofworkid < 1500566400000000000
insert into xobjlogs201707 (`id`,`version`,`createtime`,`updatetime`,`randno`,`xunitofworkid`,`type`,`objtype`,`objid`,`objver`,`content`,`randno_fix`) select id, version, createtime, updatetime, 201707, xunitofworkid, type, objtype, objid, objver,content,'' as randno_fix from statdb.objlogs where xunitofworkid >= 1499702400000000000 and xunitofworkid < 1500566400000000000
select count(*) from xobjlogs201707 

==== 20170721 - 20170801 ====
select count(*) from statdb.objlogs where xunitofworkid >= 1500566400000000000 and xunitofworkid < 1501516800000000000
insert into xobjlogs201707 (`id`,`version`,`createtime`,`updatetime`,`randno`,`xunitofworkid`,`type`,`objtype`,`objid`,`objver`,`content`,`randno_fix`) select id, version, createtime, updatetime, 201707, xunitofworkid, type, objtype, objid, objver,content,'' as randno_fix from statdb.objlogs where xunitofworkid >= 1500566400000000000 and xunitofworkid < 1501516800000000000
select count(*) from xobjlogs201707 


==== 20170801 - 20170901 ====
select count(*) from statdb.objlogs where xunitofworkid >= 1501516800000000000 and xunitofworkid < 1504195200000000000
====+++====
select count(*) from xobjlogs201708 

==== 20170801 - 20170811 ====
select count(*) from statdb.objlogs where xunitofworkid >= 1501516800000000000 and xunitofworkid < 1502380800000000000
insert into xobjlogs201708 (`id`,`version`,`createtime`,`updatetime`,`randno`,`xunitofworkid`,`type`,`objtype`,`objid`,`objver`,`content`,`randno_fix`) select id, version, createtime, updatetime, 201708, xunitofworkid, type, objtype, objid, objver,content,'' as randno_fix from statdb.objlogs where xunitofworkid >= 1501516800000000000 and xunitofworkid < 1502380800000000000
select count(*) from xobjlogs201708 

==== 20170811 - 20170821 ====
select count(*) from statdb.objlogs where xunitofworkid >= 1502380800000000000 and xunitofworkid < 1503244800000000000
insert into xobjlogs201708 (`id`,`version`,`createtime`,`updatetime`,`randno`,`xunitofworkid`,`type`,`objtype`,`objid`,`objver`,`content`,`randno_fix`) select id, version, createtime, updatetime, 201708, xunitofworkid, type, objtype, objid, objver,content,'' as randno_fix from statdb.objlogs where xunitofworkid >= 1502380800000000000 and xunitofworkid < 1503244800000000000
select count(*) from xobjlogs201708 

==== 20170821 - 20170901 ====
select count(*) from statdb.objlogs where xunitofworkid >= 1503244800000000000 and xunitofworkid < 1504195200000000000
insert into xobjlogs201708 (`id`,`version`,`createtime`,`updatetime`,`randno`,`xunitofworkid`,`type`,`objtype`,`objid`,`objver`,`content`,`randno_fix`) select id, version, createtime, updatetime, 201708, xunitofworkid, type, objtype, objid, objver,content,'' as randno_fix from statdb.objlogs where xunitofworkid >= 1503244800000000000 and xunitofworkid < 1504195200000000000
select count(*) from xobjlogs201708 


==== 20170901 - 20171001 ====
select count(*) from statdb.objlogs where xunitofworkid >= 1504195200000000000 and xunitofworkid < 1506787200000000000
====+++====
select count(*) from xobjlogs201709 

==== 20170901 - 20170911 ====
select count(*) from statdb.objlogs where xunitofworkid >= 1504195200000000000 and xunitofworkid < 1505059200000000000
insert into xobjlogs201709 (`id`,`version`,`createtime`,`updatetime`,`randno`,`xunitofworkid`,`type`,`objtype`,`objid`,`objver`,`content`,`randno_fix`) select id, version, createtime, updatetime, 201709, xunitofworkid, type, objtype, objid, objver,content,'' as randno_fix from statdb.objlogs where xunitofworkid >= 1504195200000000000 and xunitofworkid < 1505059200000000000
select count(*) from xobjlogs201709 

==== 20170911 - 20170921 ====
select count(*) from statdb.objlogs where xunitofworkid >= 1505059200000000000 and xunitofworkid < 1505923200000000000
insert into xobjlogs201709 (`id`,`version`,`createtime`,`updatetime`,`randno`,`xunitofworkid`,`type`,`objtype`,`objid`,`objver`,`content`,`randno_fix`) select id, version, createtime, updatetime, 201709, xunitofworkid, type, objtype, objid, objver,content,'' as randno_fix from statdb.objlogs where xunitofworkid >= 1505059200000000000 and xunitofworkid < 1505923200000000000
select count(*) from xobjlogs201709 

==== 20170921 - 20171001 ====
select count(*) from statdb.objlogs where xunitofworkid >= 1505923200000000000 and xunitofworkid < 1506787200000000000
insert into xobjlogs201709 (`id`,`version`,`createtime`,`updatetime`,`randno`,`xunitofworkid`,`type`,`objtype`,`objid`,`objver`,`content`,`randno_fix`) select id, version, createtime, updatetime, 201709, xunitofworkid, type, objtype, objid, objver,content,'' as randno_fix from statdb.objlogs where xunitofworkid >= 1505923200000000000 and xunitofworkid < 1506787200000000000
select count(*) from xobjlogs201709 


==== 20171001 - 20171101 ====
select count(*) from statdb.objlogs where xunitofworkid >= 1506787200000000000 and xunitofworkid < 1509465600000000000
====+++====
select count(*) from xobjlogs201710 

==== 20171001 - 20171011 ====
select count(*) from statdb.objlogs where xunitofworkid >= 1506787200000000000 and xunitofworkid < 1507651200000000000
insert into xobjlogs201710 (`id`,`version`,`createtime`,`updatetime`,`randno`,`xunitofworkid`,`type`,`objtype`,`objid`,`objver`,`content`,`randno_fix`) select id, version, createtime, updatetime, 201710, xunitofworkid, type, objtype, objid, objver,content,'' as randno_fix from statdb.objlogs where xunitofworkid >= 1506787200000000000 and xunitofworkid < 1507651200000000000
select count(*) from xobjlogs201710 

==== 20171011 - 20171021 ====
select count(*) from statdb.objlogs where xunitofworkid >= 1507651200000000000 and xunitofworkid < 1508515200000000000
insert into xobjlogs201710 (`id`,`version`,`createtime`,`updatetime`,`randno`,`xunitofworkid`,`type`,`objtype`,`objid`,`objver`,`content`,`randno_fix`) select id, version, createtime, updatetime, 201710, xunitofworkid, type, objtype, objid, objver,content,'' as randno_fix from statdb.objlogs where xunitofworkid >= 1507651200000000000 and xunitofworkid < 1508515200000000000
select count(*) from xobjlogs201710 

==== 20171021 - 20171101 ====
select count(*) from statdb.objlogs where xunitofworkid >= 1508515200000000000 and xunitofworkid < 1509465600000000000
insert into xobjlogs201710 (`id`,`version`,`createtime`,`updatetime`,`randno`,`xunitofworkid`,`type`,`objtype`,`objid`,`objver`,`content`,`randno_fix`) select id, version, createtime, updatetime, 201710, xunitofworkid, type, objtype, objid, objver,content,'' as randno_fix from statdb.objlogs where xunitofworkid >= 1508515200000000000 and xunitofworkid < 1509465600000000000
select count(*) from xobjlogs201710 


==== 20171101 - 20171201 ====
select count(*) from statdb.objlogs where xunitofworkid >= 1509465600000000000 and xunitofworkid < 1512057600000000000
====+++====
select count(*) from xobjlogs201711 

==== 20171101 - 20171111 ====
select count(*) from statdb.objlogs where xunitofworkid >= 1509465600000000000 and xunitofworkid < 1510329600000000000
insert into xobjlogs201711 (`id`,`version`,`createtime`,`updatetime`,`randno`,`xunitofworkid`,`type`,`objtype`,`objid`,`objver`,`content`,`randno_fix`) select id, version, createtime, updatetime, 201711, xunitofworkid, type, objtype, objid, objver,content,'' as randno_fix from statdb.objlogs where xunitofworkid >= 1509465600000000000 and xunitofworkid < 1510329600000000000
select count(*) from xobjlogs201711 

==== 20171111 - 20171121 ====
select count(*) from statdb.objlogs where xunitofworkid >= 1510329600000000000 and xunitofworkid < 1511193600000000000
insert into xobjlogs201711 (`id`,`version`,`createtime`,`updatetime`,`randno`,`xunitofworkid`,`type`,`objtype`,`objid`,`objver`,`content`,`randno_fix`) select id, version, createtime, updatetime, 201711, xunitofworkid, type, objtype, objid, objver,content,'' as randno_fix from statdb.objlogs where xunitofworkid >= 1510329600000000000 and xunitofworkid < 1511193600000000000
select count(*) from xobjlogs201711 

==== 20171121 - 20171201 ====
select count(*) from statdb.objlogs where xunitofworkid >= 1511193600000000000 and xunitofworkid < 1512057600000000000
insert into xobjlogs201711 (`id`,`version`,`createtime`,`updatetime`,`randno`,`xunitofworkid`,`type`,`objtype`,`objid`,`objver`,`content`,`randno_fix`) select id, version, createtime, updatetime, 201711, xunitofworkid, type, objtype, objid, objver,content,'' as randno_fix from statdb.objlogs where xunitofworkid >= 1511193600000000000 and xunitofworkid < 1512057600000000000
select count(*) from xobjlogs201711 



