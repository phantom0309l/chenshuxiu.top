<?php
$str = <<<STRSTR

h1. sql编码规范

<pre>
v0.1 2016.8.15
v0.9 2016.8.22
</pre>

h2. 1. 一般规则

1.1 以下关键字要另起一行开始

<pre>
select
from
inner join
left join
where
group by
order by
limit
union
</pre>

1.2 关键字不允许大小写混写

1.2.1 可以关键字统一小写
<pre>
select from where and or group by order by limit union inner join left join
</pre>

1.2.2 也可以关键字统一大写
<pre>
SELECT FROM WHERE AND OR GROUP BY ORDER BY LIMIT UNION INNER JOIN LEFT JOIN
</pre>

1.2 不允许只写 join , 需要明确 inner join, left join

1.3 字段转名 需要 as

1.4 表别名 不要 as

1.5 子表别名 , 第一层 tt , 第二层 ttt , 类推

1.6 不要用函数, 除非经过评审; 特殊 left(); if();

1.7 and 条件 每3个, 需要换行并缩进4个空格

1.8 运算符两侧都要留一个空格

1.9 使用 in 代替 or

1.10 使用 >= 代替 >

1.11 子查询嵌套, 需要缩进4个空格

h2. 2. bind 模式

2.1 冒号模式
<pre>
$sql = "select * from patients where id=:id ";
//或
$cond = " and id=:id ";

$bind = array();
bind[':id'] = $patientid;
</pre>

2.2 有把握的id值,可以嵌套变量,包以大括号
<pre>
$sql = "select * from patients where id={$patient->id} ";
</pre>

h2. 3. like

<pre>
$sql = "select * from patients where name like :name ";
bind[':name'] = "%{$name}%";
</pre>

h2. 4. 单表查询

4.1 单个条件查询可以单行 (不出现滑动条为准)
<pre>
" select * from patients where id=:id  ";
</pre>

4.2 也可以多行
<pre>
"select *
from patients
where id=:id ";
</pre>

h2. 5. 联表查询

<pre>
"select distinct a.*
from patients a
inner join pcards b on b.patientid=a.id
inner join users c on c.patientid = a.id
inner join wxusers d on d.userid = c.id
where b.diseaseid = :diseaseid or d.doctorid = :doctorid
group by b.doctorid
order by a.id desc
limit 20 ";
</pre>

5.0 明确地为每个字段指定表名
5.1 表别名, a,b,c x,y,z , p,pc,u,w
5.2 联一个表加一行 , on 在同一行, on 的条件 就近原则
5.3 select
5.4 inner join 或 left join
5.5 where
5.6 group by
5.7 order by
5.8 limit

h2. 6. 联表修改
由联表查询修改而成

h2. 7. 子查询, 缩进4个空格

7.1 id in 模式
<pre>
select a.*
from patients
where id in
(
    select patientid from pcards where diseaseid=1
) and status=1
</pre>

7.2 子表联表,别名 tt
<pre>
select tt.*,b.id,b.name
from pcards a
inner join
(
    select id as patientid, name from patients
) tt on tt.patientid=a.patientid
inner join doctors b on b.id=a.doctorid
where 1=1
</pre>

h2. 8. 其他
<pre>
略
</pre>

STRSTR;
