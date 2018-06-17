<?php

$str = <<<STRSTR

h1. [ xwork编码规范 ]

<pre>
2016.3.1 by sjp
</pre>

h1. 1. 黄金定律: 永远遵循同一套编码规范

1.1 不管有多少人共同参与同一项目，一定要确保每一行代码都像是同一个人编写的。
1.2 不言而喻
1.3 名正
1.4 言顺
1.5 在其位,谋其政 (过犹不及)
1.6 不在其位,不谋其政 (一二三原则, 去除重复代码!!!)
1.7 与其不逊也，宁固!
1.8 矫枉必须过正!
1.9 勿以恶小而为之
1.10 勿以善小而不为

h1. 2. 阅读并理解[xwork简明手册] 即 [xwork-guide.txt.php] 先认同,再改进。

h1. 3. 文件,在其位 (文件放到正确的文件夹目录)

3.1 实体类
3.2 实体Dao类
3.3 action类
3.4 tpl 模板
3.5 cron 脚本
3.6 img (js/css/jpg等)

h1. 4. 文件名,名正言顺

4.1 类文件名 = 类名.class.php
4.2 模板名 = 小写的方法名.tpl.php
4.3 模板碎片命名: 下划线开头,纯小写,如 _xxx.php
4.4 cron脚本的命名 : 纯小写字母,下划线分隔,如 dbfix_wxuserid_userid_patientid.php

h1.  5. 类名,名正言顺,名副其实 (名字正确,读起来顺口)(代码整洁之道-第二章)

5.0 类名需要评审.
5.0.1 必须认真对待每一个实体名,变量名,函数名;就像给自己的儿子起名字一样谨慎.
5.1 名字关乎抽象,是顶层设计,是软件开发第一重要的事.名字就是架构,名字是项目的技术积累!
5.2 首字母大写,驼峰式
5.3 entity类,如 Paper
5.4 action类,如 PaperMgrAction
5.5 ref类,如 DiseasePaperRef
5.6 名词或名词短语,一个类一个抽象.
5.7 简单,好记,可读,无歧义.
5.8 避讳原则,可搜索.
5.9 相关原则,PaperTpl 和 Paper
5.10 发现更好的名字,及时更改,全面更改.
5.11 明确是王道!为别人考虑,为团队考虑,为未来考虑.
5.12 不重要的抽象,不要占据好名字.
5.13 一些基础实体,User,Picture,Account,AccountItem,AccountTrans,

h1.  6. 表名

6.1 框架要求:实体名,全小写+s

h1. 7. 实体类的属性/表的字段,名正言顺

7.0 属性名/字段名需要评审.
7.1 全小写
7.2 实体默认属性 id,version,createtime,updatetime
7.3 一些其他约定字段: xcode,objtype,objid,objcode,status,remark,auditstatus,auditorid,auditremark
7.4 几个常用字段: code,type,name,ename,title,content,brief,tip,url,pos,mobile,phone,username,password 等
7.5 如果需要记录用户主动修改时间,modifytime ;
7.6 外键 userid,patientid等
7.7 is , is_ 开头的字段,bool 类型
7.8 str 后缀的字段,字符串 类型,字段可能将来要建成表
7.9 cnt 后缀的字段,整数 类型,xx数目,缓存字段
7.10 积累我们的常用字段库

h1. 8. 类的对象/实例

8.1 第一规则: 类名首字母小写.
8.2 例子 User : $user , UserLessonRef : $userLessonRef
8.3 如果发现这样的变量，一定是对象. 举例: 张小鹏一定是张鹏的儿子 ,而不应该是李亮的儿子!
8.4 如果是纯数据数组,不是实体的实例对象,不能用$user; 改成 $userJson 或 $userData 或 $userRow 或 $row
8.5 下划线开头, $_user, 用于这些场景: a) 避免冲突, b) 循环 c) 模板碎片 等
8.6 my 开头 $myuser,$mypatient,$myauditor , 非驼峰式
8.7 the 开头 $theUser, 驼峰式
8.8 不言而喻,不能误导

h1. 9. 数组变量

9.1 类的对象数组, 对象名+s, $users,
9.2 一些局部数组变量, $array, $arr, $list
9.3 对象的纯数据形式,见8.4
9.4 数组的循环: $i, $k, $v, $a, $_user

h1. 10. get 和 post 的变量

10.1 patientid 全小写
10.2 和字段名一致

h1. 11. 实体类Dao函数名

11.0 需要评审
11.1 一个实体类dao的函数就是一个查询接口
11.2 尽量少,封装有复用价值的查询
11.3 约定习惯优于特立独行
11.4 PatientDao::getByName
11.5 PatientDao::getListByWord($word='')
11.6 PaperDao::getListByUser(User $user)
11.7 PaperDao::getListByUser($userid) // 也算正确
11.8 PaperDao::getListByUser($userid,$status=1) // 参数默认值
11.9 PatientMedicineRefDao::getByPatientidMedicineid ($patientid,$medicineid) // getOfPatientByMedicineid
11.10 EntityXX::getById
11.11 Dao::getEntityListByCond('Patient',$cond,$bind);
11.12 Dao::getEntityListByCond4Page('Patient',$pagesize, $pagenum,$cond,$bind);
11.13 PatientMedicineRefDao::getListByPatient_Open #许喆发明的规范

h1. 12. 实体类函数名

12.0 需要评审
12.1 实体类的函数就是领域逻辑
12.2 当页面上有变量判断时,考虑封装函数;出现重复代码时,考虑封装函数.
12.3 Patient::pass
12.4 Patient::refuse($reason='')
12.5 User::isTest
12.6 User::validatePassword($password)
12.7 belongto 解决了主要实体关系
12.8 User::getWxUsers()
12.9 User::getMasterWxUser($wxshopid=0)

h1. 13. action函数

13.0 需要评审
13.1 一个action函数对应一个功能
13.2 action函数封装事务逻辑
13.3 约定习惯优于特立独行
13.4 PatientMgr::doList() 列表页
13.5 PatientMgr::doOne() 详情页
13.6 PatientMgr::doAdd() 新建页
13.7 PatientMgr::doAddPost() 新建提交,跳转到 doOne 或 doModify
13.8 PatientMgr::doModify() 修改页
13.9 PatientMgr::doModifyPost() 修改提交,跳转到 doOne 或 doModify
13.10 PatientMgr::doListHtml() 或 Patient::doOneHtml() ,ajax 的 get模式,返回html
13.11 PatientMgr::doModifyJson(), ajax 的 post模式
13.12 PatientMgr::doOneJson(), ajax 的 get模式,返回数据
13.13 PatientMgr::listImp($a,$b); 非功能性实现函数,imp后缀
13.14 前置条件检查
13.15 权限检查

h1. 14. 模板碎片

14.1 下划线前缀 _one.php
14.2 不加 tpl
14.3 下划线分隔
14.4 纯小写
14.5 特殊 _WxTxtMsg.php
14.6 模板中遇到 if-else 或 foreach 考虑切碎片

h1. 15. 类所有者制

h1. 16. 子系统所有者制

h1. 17. 拒绝魔法变量

h1. 18. 格式化代码

STRSTR;
