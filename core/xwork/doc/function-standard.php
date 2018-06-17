<?php
$str = '

h1. 1. domain层的新类和新函数, 包括类名和函数名修改和废弃

h1. 2. action层的类和do开头函数, 包括类名和函数名修改和废弃

h1. 3. 上线单 review 时, 需要审查是否增加了变更记录

h2. 4. 函数规范(完善中)

| 位置 | 类型 | 函数目的 | 例子 |  说明 |
| Dao | 静态 | getListBy 获取实体列表 | UserDao::getListByPatient ($patientid) | 需有复用价值, 只有一两次调用的, 可以在action里直接实现
        Dao::getEntityListByCond 或 Dao::loadEntityList |
| Dao | 静态 | getBy 获取实体 | UserDao::getByMobile($mobile) | 同上  Dao::getEntityByCond 或 Dao::loadEntity( |
| Dao | 静态 | getCnt 获取cnt | PatientDao::getPaitentCntOfDoctor($doctorid) | 同上 Dao::queryValue |
| Dao | 静态 | getXxxs 获取一维数组 | UserDao::getTestUserids() | 同上 Dao::queryValues |
| Dao | 静态 | getXxxArray 根据条件获取rows | PatientDao::getRptGroupbyMarketMonth() | 同上 Dao::queryRows |
| Entity | 静态 | createByXxx 构造型 | Patient::createByBiz | 复杂的修改函数,考虑迁移到 Service |
| Entity | 动态 | hasMany 型 | Patient->getUsers | 实体关系比较紧密, 动静结合, 调用 XxxDao::getList( |
| Entity | 动态 | hasOne 型 | Patient->getMasterPcard | 实体关系比较紧密 , 动静结合 |
| Entity | 动态 | getXxxCnt 型 | FitPageTpl->getFitPageTplItemCnt | 实体关系比较紧密 , 其他的例子 Patient->getSameNamePatientCnt |
| Entity | 动态 | isXxx 类型判断型 | User->isDoctor |  |
| Entity | 动态 | isXxx 逻辑判断型 | Auditor->isHasAuth | 须有封装价值 Patient->isSubscribe , Patient->hadRegisted
        关系不紧密, 考虑迁移 Patient->isNoDruging |
| Entity | 动态 | can 逻辑判断型 | Auditor->canHandleOptask | 例子不太好, 应该迁移到 OpTaskService |
| Entity | 动态 | get 实体属性加工型 | Patient->getSexStr | Patient->getOneMobile |
| Entity | 动态 | set 实体属性型 | Patient->setStatus | 复杂的修改函数,考虑迁移到 Service |
| Entity | 动态 | fixXxx 实体属性逻辑处理型 | Patient->fixIsactivity | 如果逻辑复杂,考虑迁移到 Service |
| Entity | 动态 | doXxx 逻辑处理型 | Patient->doDrug | Patient->stopDrug, 应该迁移到 DrugService |
| JsonXxxEntity | 静态 | jsonArray* 实体转换 jsonArray | JsonDoctor::jsonArray(Doctor $doctor) | 一个Entity对应一个单独的JsonEntity,
        函数严格以jsonArray开头, 数组字段和实体属性须保持一致 |
| CtrHelper | 静态 | getXxxEntityCtrArray | CtrHelper::getDiseaseCtrArray | 实体生成下拉框数据数组统一放在CtrHelper类 |
| CtrHelper | 静态 | toXxxEntityCtrArray | CtrHelper::toDoctorCtrArray($doctors) | 同上, 参数传 实体数组 |
| PushMsgService | 静态 | sendxxxToyyyByzzz | PushMsgService::sendTplMsgToWxUserByAuditor(xxx) | 给患者发消息,统一在这个service里,
        不要写在实体方法, 直接在action层调用 |
| Dwx_kefuMsgService | 静态 | sendxxxToyyyByzzz | Dwx_kefuMsgService::sendTxtMsgToDoctorBySystem(xxx) | 给方寸管理端医生发消息,统一在这个service里,
        不要写在实体方法,直接在action层调用 |
| DoctordbOplogService | 静态 | 记日志 | DoctordbOplogService::log | log($user, $doctor, $objtype, $objid, $content, $patientid) |

';