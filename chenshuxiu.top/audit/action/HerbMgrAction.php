<?php
// HerbMgrAction
class HerbMgrAction extends AuditBaseAction
{

    public function doList () {
        $herbs = Dao::getEntityListByCond("Herb"," order by pinyin asc");

        $herbarr = array();
        foreach( $herbs as $herb ){
            $herbarr[substr($herb->py,0,1)][] = $herb;
        }

        XContext::setValue("herbarr", $herbarr);
        return self::SUCCESS;
    }

    public function doAdd () {
        return self::SUCCESS;
    }

    public function doAddPost () {
        $name = XRequest::getValue("name", '');

        $herb = Dao::getEntityByCond('Herb'," and name = :name",array(":name"=>$name));

        if( (false == $herb instanceof Herb ) && $name ){
            $row = array();
            $row["name"] = $name;
            $herb = Herb::createByBiz($row);

            $preMsg = $name."添加成功";
        }else{
            $preMsg = $name."有误或者已存在同名药物";
        }
        XContext::setJumpPath("/herbmgr/add?preMsg=" . urlencode($preMsg));
        return self::SUCCESS;
    }

    public function doModify () {
        $herbid = XRequest::getValue("herbid", 0);
        $herb = Herb::getById($herbid);

        XContext::setValue("herb", $herb);
        return self::SUCCESS;
    }

    public function doModifyPost () {
        $herbid = XRequest::getValue("herbid", 0);
        $name = XRequest::getValue("name", '');
        $pinyin = XRequest::getValue("pinyin", '');
        $py = XRequest::getValue("py", '');

        $herb_f = Dao::getEntityByCond('Herb'," and name = :name",array(":name"=>$name));
        $herb = Herb::getById($herbid);

        if( (false == $herb_f instanceof Herb ) && $name ){
            $herb->name = $name;
        }

        $herb->pinyin = $pinyin;
        $herb->py = $py;
        $preMsg = $name."修改成功";

        XContext::setJumpPath("/herbmgr/modify?herbid=" . $herbid . "&preMsg=" . urlencode($preMsg));
        return self::SUCCESS;
    }
}
