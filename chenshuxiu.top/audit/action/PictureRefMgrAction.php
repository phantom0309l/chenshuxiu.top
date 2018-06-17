<?php
// PictureRef
class PictureRefMgrAction extends AuditBaseAction
{

    public function doAddPicRefPost () {
        $objid = XRequest::getValue('objid', 0);
        $objtype = XRequest::getValue('objtype', '');
        $pictureid = XRequest::getValue('pictureid', 0);

        $obj = $objtype::getById($objid);

        $pictureref = PictureRef::createByObj($obj, $pictureid);

        $preMsg = "图片添加成功 " . XDateTime::now();
        $objtype = strtolower($objtype);
        XContext::setJumpPath("/{$objtype}mgr/modify?{$objtype}id={$objid}&preMsg=" . urlencode($preMsg));
        return self::SUCCESS;
    }

    public function doDeletePicRefPost () {
        $picturerefid = XRequest::getValue('picturerefid', 0);
        $objid = XRequest::getValue('objid', 0);
        $objtype = XRequest::getValue('objtype', '');

        $pictureref = PictureRef::getById($picturerefid);
        $pictureref->remove();

        $preMsg = "图片删除成功 " . XDateTime::now();
        $objtype = strtolower($objtype);
        XContext::setJumpPath("/{$objtype}mgr/modify?{$objtype}id={$objid}&preMsg=" . urlencode($preMsg));
        return self::SUCCESS;
    }
}
