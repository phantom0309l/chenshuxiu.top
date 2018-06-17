<?php
/**
 * Created by PhpStorm.
 * User: nigestream
 * Date: 2018/5/18
 * Time: 14:23
 */
class PictureEditorMgrAction extends AuditBaseAction {
    public function doBrush() {
        $pictureId = XRequest::getValue('pictureid');
        $picture = Picture::getById($pictureId);
        DBC::requireNotEmpty($picture, 'picture is null');
        XContext::setValue('picture', $picture);
        return self::SUCCESS;
    }
    public function doMosaic() {
        $pictureId = XRequest::getValue('pictureid');
        $picture = Picture::getById($pictureId);
        DBC::requireNotEmpty($picture, 'picture is null');
        XContext::setValue('picture', $picture);
        return self::SUCCESS;
    }
}
