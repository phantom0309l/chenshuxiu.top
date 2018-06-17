<?php
// XCode
// 编号生成器

// owner by sjp
// create by sjp
// review by sjp 20160628

class XCode
{
    // 获取下一个code
    public static function getNextCode ($codename = 'userxcode') {
        $dbExecuter = BeanFinder::get("DbExecuter");
        $dbExecuter->saveNeedRwSplit();
        $dbExecuter->unNeedRwSplit();

        BeanFinder::get("DbExecuter")->executeNoQuery(" update xcodes set nextcode=LAST_INSERT_ID(nextcode+1) where codename='{$codename}' ");
        $nextcode = BeanFinder::get("DbExecuter")->queryValue("select LAST_INSERT_ID() as nextcode");

        $dbExecuter->restoreRwSplit();

        return ($nextcode - 1);
    }
}
