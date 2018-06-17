<?php
// YiShiMgrAction
class YiShiMgrAction extends AuditBaseAction
{

    public function doList () {
        $yishis = Dao::getEntityListByCond('YiShi');
        XContext::setValue("yishis", $yishis);
        return self::SUCCESS;
    }

    public function doAdd () {
        return self::SUCCESS;
    }

    public function doAddPost () {
        $type = XRequest::getValue("type");
        $name = XRequest::getValue("name");
        $mobile = XRequest::getValue("mobile");
        $password= XRequest::getValue("password");
        $hospital_name= XRequest::getValue("hospital_name");
        $department_name= XRequest::getValue("department_name");
        DBC::requireNotEmpty($type,"星号为必填项");
        DBC::requireNotEmpty($name,"星号为必填项");
        DBC::requireNotEmpty($mobile,"星号为必填项");
        DBC::requireNotEmpty($password,"星号为必填项");
        DBC::requireNotEmpty($hospital_name,"星号为必填项");
        DBC::requireNotEmpty($department_name,"星号为必填项");

        $row = array();

        $row["type"] = $type;
        $row["name"] = $name;
        $row["mobile"] = $mobile;
        $row["password"] = $password;
        $row["hospital_name"] = $hospital_name;
        $row["department_name"] = $department_name;

        $yishi = YiShi::createByBiz($row);

        XContext::setJumpPath("/yishimgr/list");
        return self::SUCCESS;
    }

    public function doModify () {
        $yishiid = XRequest::getValue("yishiid", 0);
        $yishi = YiShi::getById($yishiid);

        DBC::requireNotEmpty($yishi, "医师不存在{$yishiid}");

        XContext::setValue('yishi', $yishi);
        return self::SUCCESS;
    }

    public function doModifyPost () {
        $yishiid = XRequest::getValue("yishiid",0);
        $type = XRequest::getValue("type");
        $name = XRequest::getValue("name");
        $mobile = XRequest::getValue("mobile");
        $password = XRequest::getValue("password");
        $hospital_name = XRequest::getValue("hospital_name");
        $department_name = XRequest::getValue("department_name");

        DBC::requireNotEmpty($type,"星号为必填项");
        DBC::requireNotEmpty($name,"星号为必填项");
        DBC::requireNotEmpty($mobile,"星号为必填项");
        DBC::requireNotEmpty($password,"星号为必填项");
        DBC::requireNotEmpty($hospital_name,"星号为必填项");
        DBC::requireNotEmpty($department_name,"星号为必填项");

        $yishi = YiShi::getById($yishiid);
        DBC::requireNotEmpty($yishi, "医师不存在{$yishiid}");

        $yishi->id = $yishiid;
        $yishi->type = $type;
        $yishi->name = $name;
        $yishi->mobile = $mobile;
        $yishi->password = $password;
        $yishi->hospital_name = $hospital_name;
        $yishi->department_name = $department_name;

        XContext::setJumpPath("/yishimgr/list");

        return self::SUCCESS;
    }

    public function doDeletePost () {
        $yishiid = XRequest::getValue("yishiid");
        $yishi = YiShi::getById($yishiid);
        $yishi->remove();

        XContext::setJumpPath("/yishimgr/list");
        return self::SUCCESS;
    }
}
