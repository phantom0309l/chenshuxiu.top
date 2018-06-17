<?php
// Actelion_GiftMgrAction
class Actelion_GiftMgrAction extends AuditBaseAction
{

    public function doList () {
        $cond = "";
        $bind = [];

        $actelion_gifts = Dao::getEntityListByCond('Actelion_Gift', $cond, $bind);

        XContext::setValue('actelion_gifts', $actelion_gifts);

        return self::SUCCESS;
    }

    public function doOne () {
        return self::SUCCESS;
    }

    public function doAddOrModifyJson () {
        $actelion_giftid = XRequest::getValue('actelion_giftid', '');
        $title = XRequest::getValue('title', '');
        $jifen_price = XRequest::getValue('jifen_price', '');
        $pictureid = XRequest::getValue('pictureid', '');
        $init_cnt = XRequest::getValue('init_cnt', '');
        $remark = XRequest::getValue('remark', '');

        if ($actelion_giftid) {
            $actelion_gift = Actelion_Gift::getById($actelion_giftid);
            if ($actelion_gift instanceof Actelion_Gift) {
                $actelion_gift->title = $title;
                $actelion_gift->jifen_price = $jifen_price;
                if ($pictureid) {
                    $actelion_gift->set4lock('pictureid', $pictureid);
                }
                $actelion_gift->init_cnt = $init_cnt;
                $actelion_gift->remark = $remark;

                echo "modify-success";
            }
        } else {
            $row = [];
            $row['title'] = $title;
            $row['jifen_price'] = $jifen_price;
            $row['pictureid'] = $pictureid;
            $row['init_cnt'] = $init_cnt;
            $row['remark'] = $remark;
            $actelion_gift = Actelion_Gift::createByBiz($row);

            echo "add-success";
        }

        return self::BLANK;
    }

    public function doModifyPost () {
        return self::SUCCESS;
    }

}
